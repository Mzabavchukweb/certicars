<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Skleja klatki 360° w arkusze, żeby strona auta pobierała kilka plików
 * zamiast 60.
 *
 *   preview.jpg   — wszystkie klatki w 320 px, siatka PREVIEW_COLS kolumn.
 *                   Ok. 300 KB: po kliknięciu auto obraca się od razu.
 *   sheet_NN.jpg  — po PER_SHEET klatek w pełnej rozdzielczości, jedna pod
 *                   drugą. Dociągają się w tle i podmieniają podgląd.
 *
 * Wejście: katalog lokalny z plikami frame_001.jpg … frame_NNN.jpg.
 * Wynik: meta zapisywana w cars.{side}_frames_meta (patrz Car::frameSprites()).
 */
class FrameSpriteBuilder
{
    public const PER_SHEET    = 6;
    public const PREVIEW_W    = 320;
    public const PREVIEW_COLS = 10;
    public const SHEET_Q      = 4; // ffmpeg q:v, jak klatki
    public const PREVIEW_Q    = 5;

    public function build(string $ffmpeg, string $localDir, int $count, string $framesDir): array
    {
        if ($count < 2) {
            throw new RuntimeException('Za mało klatek na arkusze.');
        }
        $first = $localDir . DIRECTORY_SEPARATOR . 'frame_001.jpg';
        $size = @getimagesize($first);
        if (!$size) {
            throw new RuntimeException('Nie da się odczytać rozmiaru klatki.');
        }
        [$w, $h] = $size;

        $out = $localDir . DIRECTORY_SEPARATOR . 'sprites';
        if (!is_dir($out) && !mkdir($out, 0700, true) && !is_dir($out)) {
            throw new RuntimeException('Nie da się utworzyć katalogu arkuszy.');
        }

        $input = $localDir . DIRECTORY_SEPARATOR . 'frame_%03d.jpg';

        // Arkusze pełnej rozdzielczości: PER_SHEET klatek jedna pod drugą.
        $this->run([
            $ffmpeg, '-y', '-loglevel', 'error', '-framerate', '1',
            '-i', $input, '-frames:v', (string) (int) ceil($count / self::PER_SHEET),
            '-vf', 'tile=1x' . self::PER_SHEET,
            '-q:v', (string) self::SHEET_Q,
            $out . DIRECTORY_SEPARATOR . 'sheet_%02d.jpg',
        ]);

        // Podgląd: wszystkie klatki w jednym małym pliku.
        $rows = (int) ceil($count / self::PREVIEW_COLS);
        $this->run([
            $ffmpeg, '-y', '-loglevel', 'error', '-framerate', '1',
            '-i', $input,
            '-vf', sprintf('scale=%d:-2,tile=%dx%d', self::PREVIEW_W, self::PREVIEW_COLS, $rows),
            '-frames:v', '1', '-q:v', (string) self::PREVIEW_Q,
            $out . DIRECTORY_SEPARATOR . 'preview.jpg',
        ]);

        $sheets = glob($out . DIRECTORY_SEPARATOR . 'sheet_*.jpg') ?: [];
        sort($sheets, SORT_NATURAL);
        $expected = (int) ceil($count / self::PER_SHEET);
        if (count($sheets) !== $expected || !is_file($out . DIRECTORY_SEPARATOR . 'preview.jpg')) {
            throw new RuntimeException('ffmpeg nie utworzył kompletu arkuszy (' . count($sheets) . '/' . $expected . ').');
        }
        $pv = getimagesize($out . DIRECTORY_SEPARATOR . 'preview.jpg');
        $ph = (int) round($pv[1] / $rows);

        $disk = Storage::disk('public');
        // Nazwy plików są stałe, więc wersja w URL (?v=) omija stary cache po
        // wgraniu nowego filmu; stąd też można cache'ować "na zawsze".
        $opts = ['CacheControl' => 'public, max-age=31536000, immutable', 'ContentType' => 'image/jpeg'];
        $names = [];
        foreach ($sheets as $path) {
            $name = basename($path);
            $this->put($disk, $framesDir . '/' . $name, $path, $opts);
            $names[] = $name;
        }
        $this->put($disk, $framesDir . '/preview.jpg', $out . DIRECTORY_SEPARATOR . 'preview.jpg', $opts);

        foreach (glob($out . DIRECTORY_SEPARATOR . '*') ?: [] as $f) @unlink($f);
        @rmdir($out);

        return [
            'v'       => (string) time(),
            'n'       => $count,
            'w'       => $w,
            'h'       => $h,
            'per'     => self::PER_SHEET,
            'sheets'  => $names,
            'preview' => 'preview.jpg',
            'pcols'   => self::PREVIEW_COLS,
            'pw'      => self::PREVIEW_W,
            'ph'      => $ph,
        ];
    }

    private function put($disk, string $remote, string $local, array $opts): void
    {
        $stream = fopen($local, 'rb');
        try {
            if (!$disk->put($remote, $stream, $opts)) {
                throw new RuntimeException("Nie udało się wysłać {$remote}.");
            }
        } finally {
            if (is_resource($stream)) fclose($stream);
        }
    }

    private function run(array $cmd): void
    {
        $p = new Process($cmd);
        $p->setTimeout(300);
        $p->run();
        if (!$p->isSuccessful()) {
            throw new RuntimeException('ffmpeg (arkusze): ' . mb_substr($p->getErrorOutput(), 0, 500));
        }
    }
}
