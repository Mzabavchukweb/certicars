<?php

namespace App\Console\Commands;

use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Illuminate\Console\Command;

class SetR2Cors extends Command
{
    protected $signature = 'r2:set-cors';
    protected $description = 'Configure CORS on the R2 bucket so Pannellum XHR can load panoramas cross-origin';

    public function handle(): int
    {
        $driver = config('filesystems.disks.public.driver', 'local');

        if ($driver !== 's3') {
            $this->info("Disk driver is '{$driver}' — skipping R2 CORS setup.");
            return self::SUCCESS;
        }

        $disk   = config('filesystems.disks.public');
        $origin = rtrim((string) config('app.url'), '/');

        $client = new S3Client([
            'version'                 => 'latest',
            'region'                  => $disk['region'] ?? 'auto',
            'endpoint'                => $disk['endpoint'],
            'use_path_style_endpoint' => $disk['use_path_style_endpoint'] ?? false,
            'credentials'             => [
                'key'    => $disk['key'],
                'secret' => $disk['secret'],
            ],
        ]);

        try {
            $client->putBucketCors([
                'Bucket'            => $disk['bucket'],
                'CORSConfiguration' => [
                    'CORSRules' => [
                        [
                            'AllowedHeaders' => ['*'],
                            'AllowedMethods' => ['GET', 'HEAD'],
                            'AllowedOrigins' => [$origin],
                            'MaxAgeSeconds'  => 3600,
                        ],
                    ],
                ],
            ]);
            $this->info("R2 CORS set: GET/HEAD allowed from {$origin} on bucket '{$disk['bucket']}'.");
            return self::SUCCESS;
        } catch (AwsException $e) {
            $this->error('r2:set-cors failed: ' . $e->getAwsErrorMessage() ?: $e->getMessage());
            return self::FAILURE;
        }
    }
}
