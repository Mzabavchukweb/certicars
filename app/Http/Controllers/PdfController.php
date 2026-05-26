<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarImage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    public function generate(Car $car)
    {
        $isAdmin = auth()->user()?->is_admin;

        if (($car->status !== 'active' || $car->is_sold) && !$isAdmin) {
            abort(404);
        }

        if (! $car->has_certicheck && ! $isAdmin) {
            abort(404);
        }

        $car->load('brand', 'images', 'galleryImages', 'damageImages', 'damages.photos', 'tireSets.tires');

        // Convert image paths to local filesystem paths for dompdf (isRemoteEnabled=false prevents SSRF).
        // For S3/R2, download each image to a temp file; clean up after the PDF is built.
        // Cache by path so the same physical file isn't fetched twice when it appears in
        // multiple relations (images / galleryImages / damageImages / damages.photos).
        $tmpFiles = [];
        $isS3 = config('filesystems.disks.public.driver') === 's3';
        // R2 objects are publicly readable via the configured public URL — HTTP fetch
        // there is credential-free and bypasses any S3-SDK permission issues that have
        // previously left photo placeholders in the PDF. Falls back to SDK if no URL
        // is configured (e.g. private S3 buckets).
        $publicBase = $isS3 ? rtrim((string) config('filesystems.disks.public.url'), '/') : null;
        $pathCache = [];

        $fetchToTmp = function (string $bytes) use (&$tmpFiles): string {
            $tmp = tempnam(sys_get_temp_dir(), 'certicars_pdf_');
            file_put_contents($tmp, $bytes);
            $tmpFiles[] = $tmp;
            return $tmp;
        };

        $fetchHttp = function (string $url): ?string {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 3,
                CURLOPT_USERAGENT      => 'CertiCarsPDF/1.0',
            ]);
            $body   = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return ($body !== false && $status === 200 && is_string($body) && strlen($body) > 0)
                ? $body : null;
        };

        $decorateOne = function (string $path) use ($isS3, $publicBase, &$pathCache, $fetchToTmp, $fetchHttp): ?string {
            if (!$path || str_starts_with($path, 'http')) return null;
            if (isset($pathCache[$path])) return $pathCache[$path];
            try {
                if ($isS3) {
                    // Preferred path: HTTP fetch from the public R2 URL — credential-free.
                    if ($publicBase) {
                        $url = $publicBase . '/' . ltrim($path, '/');
                        $bytes = $fetchHttp($url);
                        if ($bytes !== null) {
                            return $pathCache[$path] = $fetchToTmp($bytes);
                        }
                    }
                    // Fallback: SDK download (private buckets / no public URL).
                    if (!Storage::disk('public')->exists($path)) return null;
                    return $pathCache[$path] = $fetchToTmp(Storage::disk('public')->get($path));
                }
                // Local disk: use the on-disk path directly (DomPDF reads it as a file).
                if (!Storage::disk('public')->exists($path)) return null;
                return $pathCache[$path] = Storage::disk('public')->path($path);
            } catch (\Throwable $e) {
                Log::warning('PDF image fetch failed for '.$path.': '.$e->getMessage());
                return null;
            }
        };

        $decorate = function (CarImage $img) use ($decorateOne) {
            $local = $decorateOne((string) $img->path);
            if ($local !== null) {
                $img->setAttribute('pdf_src', $local);
            }
        };

        // Eloquent relations are separate in-memory collections, so each one must be
        // decorated individually for pdf_src to surface in every Blade loop.
        $car->images->each($decorate);
        $car->galleryImages->each($decorate);
        $car->damageImages->each($decorate);
        // Per-damage photos linked through CarDamage::photos() (PR #7) plus the
        // CarDamage::image_path main photo (which lives on the damage row, not in
        // CarImage). Same fetch-to-temp pattern via the shared $decorateOne helper.
        $car->damages->each(function ($d) use ($decorate, $decorateOne) {
            if ($d->photos) {
                $d->photos->each($decorate);
            }
            $local = $decorateOne((string) $d->image_path);
            if ($local !== null) {
                $d->setAttribute('pdf_image_src', $local);
            }
        });

        $filename = 'CertiCars-' . $car->identifier . '-' . $car->slug . '.pdf';

        $pdfContent = Pdf::loadView('pdf.brochure', compact('car'))
            ->setPaper('a4')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false)
            // Enable the in-template <script type="text/php"> callback that stamps
            // "Strona X / Y" page numbers; the brochure view sources only trusted
            // server-side strings so no user input crosses this boundary.
            ->setOption('isPhpEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans')
            ->output();

        foreach ($tmpFiles as $tmp) {
            @unlink($tmp);
        }

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
