<?php

namespace App\PdfBrochure;

/**
 * Thrown when the Chromium renderer fails. The controller catches this and
 * returns a 500 rather than ship a corrupt PDF — there is intentionally no
 * fallback renderer (DomPDF was removed because its image pipeline could
 * never be made reliable).
 */
class BrochureRenderException extends \RuntimeException
{
}
