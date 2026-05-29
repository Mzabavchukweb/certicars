<?php

namespace App\PdfBrochure;

/**
 * Successfully-embedded image, ready to drop into the brochure HTML.
 *
 * Holds the base64 `data:` URI plus enough metadata for the renderer + logs
 * to make sense of what shipped. There is no "broken" sibling DTO — failure
 * is represented by `null` returned from the embedder so the view branch is
 * literally `@if ($img)`.
 */
final class EmbeddedImage
{
    public function __construct(
        public readonly string $dataUri,
        public readonly string $sourcePath,
        public readonly string $context,
        public readonly int $width,
        public readonly int $height,
        public readonly int $bytes,
    ) {}
}
