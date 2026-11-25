<?php

namespace Tests\Feature;

use App\Models\PublicProject;
use App\Models\PublicProjectReport;
use App\Support\ReportDocumentRenderer;
use Dompdf\Dompdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Process\Process;
use Tests\TestCase;
use ZipArchive;

class ReportPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_pdf_preview_only_renders_pages_in_the_first_half(): void
    {
        Storage::fake('local');
        $pdf = new Dompdf;
        $pdf->loadHtml('<div style="page-break-after: always">Page one</div><div>Page two</div>');
        $pdf->render();
        $report = $this->createReport('rapport.pdf', $pdf->output());
        $renderer = new ReportDocumentRenderer;

        $pageCount = $renderer->pageCount($report);
        $previewPageCount = $renderer->previewPageCount($report);

        $this->assertGreaterThanOrEqual(2, $pageCount);
        $this->assertSame(max(1, intdiv($pageCount, 2)), $previewPageCount);
        $this->assertStringStartsWith("\x89PNG\r\n\x1a\n", $renderer->previewImage($report, 1));

        $this->expectException(HttpException::class);
        $renderer->previewImage($report, $previewPageCount + 1);
    }

    public function test_docx_full_view_renders_visible_text_without_hidden_or_deleted_text(): void
    {
        Storage::fake('local');
        $archivePath = tempnam(sys_get_temp_dir(), 'exposis-docx-test-');
        $archive = new ZipArchive;
        $this->assertNotFalse($archivePath);
        $this->assertTrue($archive->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE));
        $archive->addFromString('word/document.xml', <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
              <w:body>
                <w:p><w:r><w:t>Visible report text</w:t></w:r></w:p>
                <w:p><w:r><w:rPr><w:vanish/></w:rPr><w:t>Hidden text</w:t></w:r></w:p>
                <w:p><w:del><w:r><w:delText>Deleted text</w:delText></w:r></w:del></w:p>
              </w:body>
            </w:document>
            XML);
        $archive->close();
        $docx = file_get_contents($archivePath);
        unlink($archivePath);
        $this->assertNotFalse($docx);

        $report = $this->createReport('rapport.docx', $docx);
        $response = (new ReportDocumentRenderer)->inlineResponse($report);
        ob_start();
        try {
            $response->sendContent();
            $pdf = ob_get_contents();
        } finally {
            ob_end_clean();
        }
        $this->assertIsString($pdf);
        $pdfPath = tempnam(sys_get_temp_dir(), 'exposis-pdf-test-');
        $this->assertNotFalse($pdfPath);
        try {
            $this->assertNotFalse(file_put_contents($pdfPath, $pdf));
            $text = new Process(['pdftotext', $pdfPath, '-']);
            $text->mustRun();

            $this->assertStringContainsString('Visible report text', $text->getOutput());
            $this->assertStringNotContainsString('Hidden text', $text->getOutput());
            $this->assertStringNotContainsString('Deleted text', $text->getOutput());
        } finally {
            unlink($pdfPath);
        }
    }

    private function createReport(string $name, string $contents): PublicProjectReport
    {
        $project = PublicProject::create([
            'title' => 'Projet avec rapport',
            'summary' => 'Une expérimentation documentée.',
            'body' => 'Description du projet.',
            'pole' => 'Formation',
            'status' => 'Terminé',
            'slug' => 'rapport-'.str_replace('.', '-', $name),
            'published_at' => now()->subMinute(),
        ]);
        $path = "community-projects/{$project->id}/reports/{$name}";
        Storage::disk('local')->put($path, $contents);

        return $project->report()->create([
            'path' => $path,
            'original_name' => $name,
            'approved_at' => now()->subMinute(),
        ]);
    }
}
