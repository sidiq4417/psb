<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Class Branding
 *
 * Product name and short institution name shown in page titles, the sidebar
 * header and the sender name of outgoing emails. The full institution name,
 * tagline, logo and contact details stay in Pengaturan Aplikasi
 * (application_settings).
 *
 * Both values can be overridden from .env, e.g. `branding.shortName = 'PPTQ Al-Fath'`.
 */
class Branding extends BaseConfig
{
    /**
     * Product name (Penerimaan Santri Baru).
     */
    public string $productName = 'PSB Online';

    /**
     * Short institution name for narrow places. Leave empty to use the
     * institution name from Pengaturan Aplikasi instead.
     */
    public string $shortName = 'PPTQ Al-Fath Solo Baru';
}
