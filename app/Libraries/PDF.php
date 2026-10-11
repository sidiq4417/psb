<?php

namespace App\Libraries;

use TCPDF;

/**
 * Class PDF
 *
 * Custom wrapper around TCPDF for generating admission forms
 * and other university-related documents.
 *
 * This class sets up default fonts, margins, metadata, and
 * provides a custom footer with university branding.
 *
 * @package App\Libraries
 */
class PDF extends TCPDF
{
    /**
     * PDF Constructor.
     *
     * Initializes the PDF document with custom page size,
     * fonts, margins, and metadata based on the current session.
     */
    public function __construct()
    {
        parent::__construct('P', PDF_UNIT, [210, 210]);

        // Configure fonts and defaults
        $this->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
        $this->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Metadata
        $institution_name = mb_strtoupper(session()->get('institution_name') ?? '');
        $this->SetTitle('FORMULIR PENERIMAAN MURID BARU ' . $institution_name);
        $this->SetAuthor(base_url());
        $this->SetSubject($institution_name);
        $this->SetKeywords($institution_name);
        $this->SetCreator('https://sekolahku.web.id');

        // Layout
        $this->SetMargins(7, 7, 7, true);
        $this->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
    }

    /**
     * Override Header
     *
     * Renders the foundation name centered at the top of each page
     * with a horizontal rule separator.
     */
    public function Header(): void
    {
        // Header handled by HTML content
    }

    /**
     * Override Footer
     *
     * Renders page number is centered at the bottom of each page.
     */
    public function Footer(): void
    {
    }

    /**
     * Render the PDF document.
     *
     * Adds a new page, writes the provided HTML content,
     * embeds a QR code with the registration number,
     * and outputs the PDF file.
     *
     * @param string     $content             HTML content to render in the PDF.
     * @param int|string $registration_number Registration number to encode in QR code.
     * @param string     $filename            Output filename (default: "document.pdf").
     * @return string
     */
    public function render(string $content, int|string $registration_number, string $filename = 'document.pdf'): string
    {
        $this->AddPage();
        $this->writeHTML($content, true, false, true);

        $style = [
            'border' => false,
            'padding' => 0,
            'fgcolor' => [0, 0, 0],
            'bgcolor' => false
        ];

        $this->write2DBarcode($registration_number, 'QRCODE,Q', 180, 10, 20, 20, $style, 'N');
        return $this->Output($filename, 'S');
    }
}
