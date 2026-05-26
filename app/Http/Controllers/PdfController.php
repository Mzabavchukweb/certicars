<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarImage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        // ╔════════════════════════════════════════════════════════════════════╗
        // ║  TEMP PDF IMAGE DIAGNOSTICS — remove after production verification ║
        // ║                                                                    ║
        // ║  Logs the exact failure point of PDF image decoration/embedding.   ║
        // ║  Every image candidate is logged through pdf.diag.image with a     ║
        // ║  shared report_id so one PDF generation can be traced end-to-end.  ║
        // ║  Public R2 URLs are public anyway → safe to log verbatim. No       ║
        // ║  credentials, signed tokens or auth headers are emitted.           ║
        // ╚════════════════════════════════════════════════════════════════════╝
        $reportId = (string) Str::uuid();
        $diagStats = ['candidates' => 0, 'success' => 0, 'failed' => 0, 'cached' => 0, 'tmp_created' => 0];
        Log::info('pdf.diag.start', [
            'report_id'   => $reportId,
            'car_id'      => $car->id,
            'slug'        => $car->slug,
            'is_s3'       => $isS3,
            'public_base' => $publicBase,
            'public_base_set' => !empty($publicBase),
            'curl_loaded' => function_exists('curl_init'),
            'tmp_dir'     => sys_get_temp_dir(),
            'tmp_writable'=> is_writable(sys_get_temp_dir()),
        ]);
        // ╚════════════════════════════════════════════════════════════════════╝

        // DomPDF identifies image types by FILE EXTENSION (isRemoteEnabled=false reads
        // straight from disk). tempnam() returns an extension-less path, which made
        // every embedded photo silently fall back to DomPDF's broken-image X. So we
        // preserve the source path's extension on the temp file. Falls back to .jpg
        // for sources without an extension (rare; covers safety).
        $fetchToTmp = function (string $bytes, string $sourcePath) use (&$tmpFiles, $reportId, &$diagStats): string {
            $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION) ?: 'jpg');
            // Whitelist to formats DomPDF actually handles; default to jpg otherwise.
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'], true)) {
                $ext = 'jpg';
            }
            $tmp = sys_get_temp_dir() . '/certicars_pdf_' . bin2hex(random_bytes(8)) . '.' . $ext;
            $writeRet = file_put_contents($tmp, $bytes);
            $tmpFiles[] = $tmp;

            // ─── TEMP PDF IMAGE DIAGNOSTICS ───
            $magic = strlen($bytes) >= 4 ? bin2hex(substr($bytes, 0, 4)) : '';
            $magicKind = match (true) {
                str_starts_with($magic, 'ffd8ff')  => 'jpeg',
                str_starts_with($magic, '89504e47') => 'png',
                str_starts_with($magic, '52494646') => 'webp/riff',
                str_starts_with($magic, '47494638') => 'gif',
                default                             => 'unknown',
            };
            $diagStats['tmp_created']++;
            Log::info('pdf.diag.tmp_write', [
                'report_id'      => $reportId,
                'source_path'    => $sourcePath,
                'tmp_path'       => $tmp,
                'tmp_ext'        => $ext,
                'write_ret'      => $writeRet,
                'write_success'  => $writeRet !== false,
                'file_exists'    => is_file($tmp),
                'file_size'      => is_file($tmp) ? filesize($tmp) : 0,
                'is_readable'    => is_readable($tmp),
                'magic_kind'     => $magicKind,
                'magic_hex_head' => $magic,
                'bytes_len'      => strlen($bytes),
            ]);
            // ─── /TEMP PDF IMAGE DIAGNOSTICS ───

            return $tmp;
        };

        $fetchHttp = function (string $url) use ($reportId): array {
            // Returns ['body' => string|null, 'diag' => array] so the caller can log
            // every step without re-running cURL.
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 3,
                CURLOPT_USERAGENT      => 'CertiCarsPDF/1.0',
            ]);
            $body         = curl_exec($ch);
            $status       = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType  = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $totalTime    = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
            $primaryIp    = curl_getinfo($ch, CURLINFO_PRIMARY_IP);
            $errno        = curl_errno($ch);
            $errmsg       = curl_error($ch);
            curl_close($ch);

            $ok = ($body !== false && $status === 200 && is_string($body) && strlen($body) > 0);
            return [
                'body' => $ok ? $body : null,
                'diag' => [
                    'http_status'   => $status,
                    'content_type'  => $contentType,
                    'body_length'   => $body !== false && is_string($body) ? strlen($body) : 0,
                    'total_time_s'  => $totalTime,
                    'primary_ip'    => $primaryIp,
                    'curl_errno'    => $errno,
                    'curl_error'    => $errmsg,
                    'ok'            => $ok,
                ],
            ];
        };

        // Image type is passed in so the diagnostic log can tell hero/gallery/damage apart.
        $decorateOne = function (string $path, string $imageType = 'unknown')
            use ($isS3, $publicBase, &$pathCache, $fetchToTmp, $fetchHttp, $reportId, &$diagStats, $car): ?string {
            // ─── TEMP PDF IMAGE DIAGNOSTICS ───
            $diagStats['candidates']++;
            $diagBase = [
                'report_id'  => $reportId,
                'car_id'     => $car->id,
                'image_type' => $imageType,
                'db_path'    => $path,
            ];
            // ─── /TEMP PDF IMAGE DIAGNOSTICS ───

            if (!$path) {
                Log::info('pdf.diag.image.skip', $diagBase + ['reason' => 'empty_path']);
                $diagStats['failed']++;
                return null;
            }
            if (str_starts_with($path, 'http')) {
                Log::info('pdf.diag.image.skip', $diagBase + ['reason' => 'absolute_url_unsupported']);
                $diagStats['failed']++;
                return null;
            }
            if (isset($pathCache[$path])) {
                Log::info('pdf.diag.image.cache_hit', $diagBase + ['cached_tmp' => $pathCache[$path]]);
                $diagStats['cached']++;
                return $pathCache[$path];
            }

            try {
                if ($isS3) {
                    // Preferred path: HTTP fetch from the public R2 URL — credential-free.
                    if ($publicBase) {
                        $url = $publicBase . '/' . ltrim($path, '/');
                        Log::info('pdf.diag.image.http_attempt', $diagBase + ['url' => $url]);
                        $result = $fetchHttp($url);
                        Log::info('pdf.diag.image.http_result', $diagBase + ['url' => $url] + $result['diag']);
                        if ($result['body'] !== null) {
                            $tmp = $fetchToTmp($result['body'], $path);
                            $diagStats['success']++;
                            Log::info('pdf.diag.image.pdf_src_assigned', $diagBase + [
                                'fetch_method' => 'http',
                                'tmp'          => $tmp,
                            ]);
                            return $pathCache[$path] = $tmp;
                        }
                        Log::warning('pdf.diag.image.http_failed', $diagBase + ['url' => $url] + $result['diag']);
                    } else {
                        Log::info('pdf.diag.image.http_skipped', $diagBase + ['reason' => 'public_base_not_configured']);
                    }
                    // Fallback: SDK download (private buckets / no public URL).
                    Log::info('pdf.diag.image.sdk_attempt', $diagBase);
                    $exists = Storage::disk('public')->exists($path);
                    Log::info('pdf.diag.image.sdk_exists', $diagBase + ['exists' => $exists]);
                    if (!$exists) {
                        $diagStats['failed']++;
                        return null;
                    }
                    $body = Storage::disk('public')->get($path);
                    $tmp = $fetchToTmp((string) $body, $path);
                    $diagStats['success']++;
                    Log::info('pdf.diag.image.pdf_src_assigned', $diagBase + [
                        'fetch_method' => 'sdk',
                        'tmp'          => $tmp,
                    ]);
                    return $pathCache[$path] = $tmp;
                }
                // Local disk: use the on-disk path directly (DomPDF reads it as a file).
                Log::info('pdf.diag.image.local_attempt', $diagBase);
                if (!Storage::disk('public')->exists($path)) {
                    Log::warning('pdf.diag.image.local_missing', $diagBase);
                    $diagStats['failed']++;
                    return null;
                }
                $local = Storage::disk('public')->path($path);
                $diagStats['success']++;
                Log::info('pdf.diag.image.pdf_src_assigned', $diagBase + [
                    'fetch_method' => 'local',
                    'tmp'          => $local,
                    'file_exists'  => is_file($local),
                    'file_size'    => is_file($local) ? filesize($local) : 0,
                    'is_readable'  => is_readable($local),
                ]);
                return $pathCache[$path] = $local;
            } catch (\Throwable $e) {
                Log::warning('pdf.diag.image.exception', $diagBase + [
                    'exception' => get_class($e),
                    'message'   => $e->getMessage(),
                ]);
                $diagStats['failed']++;
                return null;
            }
        };

        // Image-type tag is forwarded so the log knows whether each candidate is hero,
        // gallery, damage or per-damage photo (helps spotting class-specific failures).
        $decorateAs = function (string $type) use ($decorateOne) {
            return function (CarImage $img) use ($decorateOne, $type) {
                $local = $decorateOne((string) $img->path, $type);
                if ($local !== null) {
                    $img->setAttribute('pdf_src', $local);
                }
            };
        };

        // Eloquent relations are separate in-memory collections, so each one must be
        // decorated individually for pdf_src to surface in every Blade loop.
        $car->images->each($decorateAs('all_images'));
        $car->galleryImages->each($decorateAs('gallery'));
        $car->damageImages->each($decorateAs('damage_aggregate'));
        // Per-damage photos linked through CarDamage::photos() (PR #7) plus the
        // CarDamage::image_path main photo (which lives on the damage row, not in
        // CarImage). Same fetch-to-temp pattern via the shared $decorateOne helper.
        $car->damages->each(function ($d) use ($decorateAs, $decorateOne) {
            if ($d->photos) {
                $d->photos->each($decorateAs('damage_photo'));
            }
            $local = $decorateOne((string) $d->image_path, 'damage_main');
            if ($local !== null) {
                $d->setAttribute('pdf_image_src', $local);
            }
        });

        // ─── TEMP PDF IMAGE DIAGNOSTICS ───
        Log::info('pdf.diag.images_done', [
            'report_id'   => $reportId,
            'candidates'  => $diagStats['candidates'],
            'success'     => $diagStats['success'],
            'cached'      => $diagStats['cached'],
            'failed'      => $diagStats['failed'],
            'tmp_created' => $diagStats['tmp_created'],
            'tmp_files'   => array_map(fn($p) => ['path' => $p, 'exists' => is_file($p), 'size' => is_file($p) ? filesize($p) : 0], $tmpFiles),
        ]);
        // ─── /TEMP PDF IMAGE DIAGNOSTICS ───

        $filename = 'CertiCars-' . $car->identifier . '-' . $car->slug . '.pdf';

        $pdfCompleted = false;
        $pdfException = null;
        try {
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
            $pdfCompleted = true;
        } catch (\Throwable $e) {
            $pdfException = $e;
            throw $e;
        } finally {
            // ─── TEMP PDF IMAGE DIAGNOSTICS ───
            Log::info('pdf.diag.end', [
                'report_id'    => $reportId,
                'car_id'       => $car->id,
                'candidates'   => $diagStats['candidates'],
                'success'      => $diagStats['success'],
                'failed'       => $diagStats['failed'],
                'tmp_created'  => $diagStats['tmp_created'],
                'pdf_completed'=> $pdfCompleted,
                'pdf_size'     => $pdfCompleted ? strlen($pdfContent ?? '') : 0,
                'pdf_exception'=> $pdfException ? get_class($pdfException) : null,
                'pdf_message'  => $pdfException ? $pdfException->getMessage() : null,
            ]);
            // ─── /TEMP PDF IMAGE DIAGNOSTICS ───
        }

        foreach ($tmpFiles as $tmp) {
            @unlink($tmp);
        }

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            // Echo the diagnostic report id back so the user can correlate the response
            // with the Railway log entries. Header is informational; no auth data.
            'X-PDF-Report-Id' => $reportId,
        ]);
    }
}
