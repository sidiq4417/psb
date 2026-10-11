<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Tcpdf extends BaseConfig
{
    public $creator = 'Your Application';
    public $author = 'Your Name';
    public $title = 'PDF Document';
    public $subject = 'Generated PDF';
    public $keywords = 'PDF, CodeIgniter, TCPDF';

    // You can add more configuration options here
}
