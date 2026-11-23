<?php

namespace App\Support;

use App\Models\PublicProjectReport;
use DOMDocument;
use Dompdf\Dompdf;
use Dompdf\Options;
use DOMXPath;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Process\Process;
use ZipArchive;

class ReportDocumentRenderer
{
    private const MAX_SOURCE_BYTES = 20 * 1024 * 1024;

    private const PROCESS_TIMEOUT = 30;

    private const MAX_DOCX_TEXT_BYTES = 1_000_000;

    public function pageCount(PublicProjectReport $report): int
    {
        [$pdfPath, $temporaryDirectory] = $this->preparePdf($report);

        try {
            return $this->readPageCount($pdfPath);
        } finally {
            $this->removeTemporaryDirectory($temporaryDirectory);
        }
    }

    public function previewPageCount(PublicProjectReport $report): int
    {
        return max(1, intdiv($this->pageCount($report), 2));
    }

    public function previewImage(PublicProjectReport $report, int $page): string
    {
        [$pdfPath, $temporaryDirectory] = $this->preparePdf($report);

        try {
            $pageCount = $this->readPageCount($pdfPath);
            abort_unless($page >= 1 && $page <= max(1, intdiv($pageCount, 2)), 404);

            $prefix = $temporaryDirectory.DIRECTORY_SEPARATOR.'preview';
            $this->run([
                'pdftoppm',
                '-f', (string) $page,
                '-l', (string) $page,
                '-singlefile',
                '-png',
                '-scale-to', '1400',
                $pdfPath,
                $prefix,
            ]);

            $imagePath = $prefix.'.png';
            if (! is_file($imagePath)) {
                throw new RuntimeException('The report preview image could not be generated.');
            }

            if ($pageCount === 1) {
                $this->cropSinglePagePreview($imagePath);
            }

            $image = file_get_contents($imagePath);
            if ($image === false) {
                throw new RuntimeException('The report preview image could not be read.');
            }

            return $image;
        } finally {
            $this->removeTemporaryDirectory($temporaryDirectory);
        }
    }

    public function inlineResponse(PublicProjectReport $report): StreamedResponse
    {
        [$pdfPath, $temporaryDirectory] = $this->preparePdf($report);

        return response()->stream(function () use ($pdfPath, $temporaryDirectory) {
            try {
                $stream = fopen($pdfPath, 'rb');
                if ($stream === false) {
                    throw new RuntimeException('The report could not be opened.');
                }

                try {
                    if (fpassthru($stream) === false) {
                        throw new RuntimeException('The report could not be streamed.');
                    }
                } finally {
                    fclose($stream);
                }
            } finally {
                $this->removeTemporaryDirectory($temporaryDirectory);
            }
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="rapport.pdf"',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
        ]);
    }

    private function preparePdf(PublicProjectReport $report): array
    {
        $disk = Storage::disk('local');
        $root = realpath($disk->path(''));
        $source = realpath($disk->path($report->path));

        if ($root === false || $source === false || ! is_file($source)
            || ! str_starts_with($source, $root.DIRECTORY_SEPARATOR)) {
            abort(404);
        }
        $sourceSize = filesize($source);
        if ($sourceSize === false || $sourceSize > self::MAX_SOURCE_BYTES) {
            abort(413);
        }

        $extension = strtolower(pathinfo($report->original_name, PATHINFO_EXTENSION));
        if (! in_array($extension, ['pdf', 'docx'], true)) {
            abort(404);
        }

        $temporaryDirectory = tempnam(sys_get_temp_dir(), 'exposis-report-');
        if ($temporaryDirectory === false || ! unlink($temporaryDirectory)
            || ! mkdir($temporaryDirectory, 0700)) {
            throw new RuntimeException('A temporary report workspace could not be created.');
        }

        if ($extension === 'pdf') {
            return [$source, $temporaryDirectory];
        }

        try {
            $pdfPath = $temporaryDirectory.DIRECTORY_SEPARATOR.'document.pdf';
            $this->convertDocxToPdf($source, $pdfPath);

            return [$pdfPath, $temporaryDirectory];
        } catch (\Throwable $exception) {
            $this->removeTemporaryDirectory($temporaryDirectory);

            throw $exception;
        }
    }

    private function readPageCount(string $pdfPath): int
    {
        $output = $this->run(['pdfinfo', $pdfPath]);
        if (! preg_match('/^Pages:\s+([1-9]\d*)\s*$/m', $output, $matches)) {
            throw new RuntimeException('The report page count could not be determined.');
        }

        return (int) $matches[1];
    }

    private function convertDocxToPdf(string $sourcePath, string $pdfPath): void
    {
        $archive = new ZipArchive;
        if ($archive->open($sourcePath) !== true) {
            throw new RuntimeException('The DOCX report could not be opened.');
        }

        try {
            $documentStat = $archive->statName('word/document.xml');
            if ($documentStat === false || ($documentStat['size'] ?? 0) > self::MAX_DOCX_TEXT_BYTES * 4) {
                throw new RuntimeException('The DOCX report is too large to preview.');
            }

            $document = $archive->getFromName('word/document.xml');
            if ($document === false || strlen($document) > self::MAX_DOCX_TEXT_BYTES * 4) {
                throw new RuntimeException('The DOCX report is too large to preview.');
            }

            $xml = new DOMDocument;
            $previousErrorMode = libxml_use_internal_errors(true);
            try {
                $loaded = $xml->loadXML($document, LIBXML_NONET | LIBXML_COMPACT);
            } finally {
                libxml_clear_errors();
                libxml_use_internal_errors($previousErrorMode);
            }
            if (! $loaded) {
                throw new RuntimeException('The DOCX report could not be read.');
            }

            $xpath = new DOMXPath($xml);
            $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            $paragraphs = $xpath->query('//w:body//w:p');
            if ($paragraphs === false) {
                throw new RuntimeException('The DOCX report content could not be read.');
            }

            $html = [];
            $textLength = 0;
            foreach ($paragraphs as $paragraph) {
                $parts = $xpath->query(
                    './/w:r[not(ancestor::w:del) and not(ancestor::w:moveFrom) and not(w:rPr/w:vanish) and not(w:rPr/w:webHidden)]/*[self::w:t or self::w:tab or self::w:br or self::w:cr]',
                    $paragraph,
                );
                if ($parts === false) {
                    continue;
                }

                $text = '';
                foreach ($parts as $part) {
                    $text .= match ($part->localName) {
                        'tab' => "\t",
                        'br', 'cr' => "\n",
                        default => $part->textContent,
                    };
                }
                if (trim($text) === '') {
                    continue;
                }

                $textLength += strlen($text);
                if ($textLength > self::MAX_DOCX_TEXT_BYTES) {
                    throw new RuntimeException('The DOCX report is too large to preview.');
                }

                $escapedText = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $html[] = '<p>'.nl2br($escapedText, false).'</p>';
            }

            $options = new Options;
            $options->setIsRemoteEnabled(false);
            $options->setIsPhpEnabled(false);
            $options->setIsJavascriptEnabled(false);
            $pdf = new Dompdf($options);
            $pdf->loadHtml('<!doctype html><html lang="fr"><meta charset="utf-8"><style>body{font-family:DejaVu Sans,sans-serif;font-size:11pt;line-height:1.55;color:#222}p{margin:0 0 12px;white-space:pre-wrap}</style><body>'.implode("\n", $html).'</body></html>', 'UTF-8');
            $pdf->setPaper('A4');
            $pdf->render();

            if (file_put_contents($pdfPath, $pdf->output(), LOCK_EX) === false) {
                throw new RuntimeException('The DOCX report could not be converted to PDF.');
            }
        } finally {
            $archive->close();
        }
    }

    private function cropSinglePagePreview(string $imagePath): void
    {
        $image = imagecreatefrompng($imagePath);
        if ($image === false) {
            throw new RuntimeException('The single-page report preview could not be read.');
        }

        try {
            $height = max(1, intdiv(imagesy($image), 2));
            $preview = imagecrop($image, [
                'x' => 0,
                'y' => 0,
                'width' => imagesx($image),
                'height' => $height,
            ]);
            if ($preview === false) {
                throw new RuntimeException('The single-page report preview could not be cropped.');
            }

            try {
                $croppedPath = $imagePath.'.cropped';
                if (! imagepng($preview, $croppedPath) || ! rename($croppedPath, $imagePath)) {
                    throw new RuntimeException('The single-page report preview could not be saved.');
                }
            } finally {
                imagedestroy($preview);
            }
        } finally {
            imagedestroy($image);
        }
    }

    private function run(array $command): string
    {
        $process = new Process($command);
        $process->setTimeout(self::PROCESS_TIMEOUT);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException('The report could not be rendered.');
        }

        return $process->getOutput();
    }

    private function removeTemporaryDirectory(string $directory): void
    {
        if (is_dir($directory) && ! File::deleteDirectory($directory)) {
            Log::warning('A temporary report-rendering directory could not be removed.');
        }
    }
}
