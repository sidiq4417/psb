<?php

declare(strict_types=1);

if (!function_exists('gender')) {
    /**
     * Get a gender dictionary or a specific translation by key.
     *
     * @param string $key Optional dictionary key.
     * @return array|string Array of all items or specific string value.
     */
    function gender(string $key = ''): array|string
    {
        $data = [
            'M' => 'Laki-laki',
            'F' => 'Perempuan'
        ];
        return $key === '' ? $data : $data[$key] ?? '';
    }
}

if (!function_exists('academic_degree')) {
    /**
     * Get academic_degree dictionary or a specific translation by key.
     *
     * @param string $key Optional dictionary key.
     * @return array|string Array of all items or specific string value.
     */
    function academic_degree(string $key = ''): array|string
    {
        $data = [
            '20' => 'D1',
            '21' => 'D2',
            '22' => 'D3',
            '23' => 'D4',
            '30' => 'S1',
            '31' => 'Profesi',
            '32' => 'Sp-1',
            '35' => 'S2',
            '36' => 'S2 Terapan',
            '37' => 'Sp-2',
            '40' => 'S3',
            '41' => 'S3 Terapan',
        ];
        return $key === '' ? $data : $data[$key] ?? '';
    }
}

if (!function_exists('religions')) {
    /**
     * Get a religion dictionary or a specific translation by key.
     *
     * @param string $key Optional dictionary key.
     * @return array|string Array of all items or specific string value.
     */
    function religions(string $key = ''): array|string
    {
        $data = [
            '1' => 'Islam',
            '2' => 'Kristen',
            '3' => 'Katolik',
            '4' => 'Hindu',
            '5' => 'Budha',
            '6' => 'Khonghucu',
            '7' => 'Penghayat Kepercayaan Terhadap Tuhan YME',
            '99' => 'Lainnya'
        ];
        return $key === '' ? $data : $data[$key] ?? '';
    }
}

if (!function_exists('incomes')) {
    /**
     * Get an income dictionary or a specific translation by key.
     *
     * @param string $key Optional dictionary key.
     * @return array|string Array of all items or specific string value.
     */
    function incomes(string $key = ''): array|string
    {
        $data = [
            '0' => '0',
            '11' => 'Kurang dari Rp. 500,000',
            '12' => 'Rp. 500,000 - Rp. 999,999',
            '13' => 'Rp. 1,000,000 - Rp. 1,999,999',
            '14' => 'Rp. 2,000,000 - Rp. 4,999,999',
            '15' => 'Rp. 5,000,000 - Rp. 20,000,000',
            '16' => 'Lebih dari Rp. 20,000,000'
        ];
        return $key === '' ? $data : $data[$key] ?? '';
    }
}

if (!function_exists('occupations')) {
    /**
     * Get an occupation dictionary or a specific translation by key.
     *
     * @param string $key Optional dictionary key.
     * @return array|string Array of all items or specific string value.
     */
    function occupations(string $key = ''): array|string
    {
        $data = [
            '1' => 'Tidak bekerja',
            '2' => 'Nelayan',
            '3' => 'Petani',
            '4' => 'Peternak',
            '5' => 'PNS/TNI/Polri',
            '6' => 'Karyawan Swasta',
            '7' => 'Pedagang Kecil',
            '8' => 'Pedagang Besar',
            '9' => 'Wiraswasta',
            '10' => 'Wirausaha',
            '11' => 'Buruh',
            '12' => 'Pensiunan',
            '13' => 'Peneliti',
            '14' => 'Tim Ahli/Konsultan',
            '15' => 'Magang',
            '16' => 'Tenaga Pengajar/Instruktur/Fasiltator',
            '17' => 'Pimpinan/Manajerial',
            '98' => 'Sudah Meninggal',
            '99' => 'Lainnya'
        ];
        return $key === '' ? $data : $data[$key] ?? '';
    }
}

if (!function_exists('educations')) {
    /**
     * Get an education dictionary or a specific translation by key.
     *
     * @param string $key Optional dictionary key.
     * @return array|string Array of all items or specific string value.
     */
    function educations(string $key = ''): array|string
    {
        $data = [
            '0' => 'Tidak sekolah',
            '1' => 'PAUD',
            '2' => 'TK/sederajat',
            '3' => 'Putus SD',
            '4' => 'SD/sederajat',
            '5' => 'SMP/sederajat',
            '6' => 'SMA/sederajat',
            '7' => 'Paket A',
            '8' => 'Paket B',
            '9' => 'Paket C',
            '20' => 'D1',
            '21' => 'D2',
            '22' => 'D3',
            '23' => 'D4',
            '30' => 'S1',
            '31' => 'Profesi',
            '32' => 'Sp-1',
            '35' => 'S2',
            '36' => 'S2 Terapan',
            '37' => 'Sp-2',
            '40' => 'S3',
            '41' => 'S3 Terapan',
            '90' => 'Non formal',
            '91' => 'Informal',
            '99' => 'Lainnya'
        ];
        return $key === '' ? $data : $data[$key] ?? '';
    }
}

if (!function_exists('admission_pathways')) {
    /**
     * Get admission_pathways dictionary or a specific translation by key.
     *
     * @param string $key Optional dictionary key.
     * @return array|string Array of all items or specific string value.
     */
    function admission_pathways(string $key = ''): array|string
    {
        $db = db_connect();
        $res = $db->query('SELECT id, name FROM admission_pathways WHERE is_deleted = false ORDER BY id')->getResult();
        $data = [];
        foreach ($res as $row) {
            $data[$row->id] = $row->name;
        }
        return $key === '' ? $data : $data[$key] ?? '';
    }
}

if (!function_exists('applicant_types')) {
    /**
     * Get applicant_types dictionary or a specific translation by key.
     *
     * @param string $key Optional dictionary key.
     * @return array|string Array of all items or specific string value.
     */
    function applicant_types(string $key = ''): array|string
    {
        $db = db_connect();
        $res = $db->query('SELECT id, name FROM applicant_types WHERE is_deleted = false ORDER BY id')->getResult();
        $data = [];
        foreach ($res as $row) {
            $data[$row->id] = $row->name;
        }
        return $key === '' ? $data : $data[$key] ?? '';
    }
}

if (!function_exists('area_levels')) {
    /**
     * Get area_levels dictionary or a specific translation by key.
     *
     * @param string $key Optional dictionary key.
     * @return array|string Array of all items or specific string value.
     */
    function area_levels(string $key = ''): array|string
    {
        $data = [
            '0' => 'Negara',
            '1' => 'Provinsi',
            '2' => 'Kabupaten/Kota',
            '3' => 'Kecamatan'
        ];
        return $key === '' ? $data : $data[$key] ?? '';
    }
}

if (!function_exists('transportations')) {
    /**
     * Get a transportation dictionary or a specific translation by key.
     *
     * @param string $key Optional dictionary key.
     * @return array|string Array of all items or specific string value.
     */
    function transportations(string $key = ''): array|string
    {
        $data = [
            '1' => 'Jalan kaki',
            '3' => 'Angkutan umum/bus/pete-pete',
            '4' => 'Mobil/bus antar jemput',
            '5' => 'Kereta api',
            '6' => 'Ojek',
            '7' => 'Andong/bendi/sado/dokar/delman/becak',
            '8' => 'Perahu penyeberangan/rakit/getek',
            '11' => 'Kuda',
            '12' => 'Sepeda',
            '13' => 'Sepeda motor',
            '14' => 'Mobil pribadi',
            '99' => 'Lainnya'
        ];
        return $key === '' ? $data : $data[$key] ?? '';
    }
}

if (!function_exists('financial_sources')) {
    /**
     * Get financial_sources dictionary or a specific translation by key.
     *
     * @param string $key Optional dictionary key.
     * @return array|string Array of all items or specific string value.
     */
    function financial_sources(string $key = ''): array|string
    {
        $data = [
            '1' => 'Mandiri',
            '2' => 'Beasiswa Tidak Penuh',
            '3' => 'Beasiswa Penuh'
        ];
        return $key === '' ? $data : $data[$key] ?? '';
    }
}

if (!function_exists('residences')) {
    /**
     * Get a residence dictionary or a specific translation by key.
     *
     * @param string $key Optional dictionary key.
     * @return array|string Array of all items or specific string value.
     */
    function residences(string $key = ''): array|string
    {
        $data = [
            '1' => 'Bersama orang tua',
            '2' => 'Wali',
            '3' => 'Kost',
            '4' => 'Asrama',
            '5' => 'Panti asuhan',
            '10' => 'Rumah sendiri',
            '99' => 'Lainnya'
        ];
        return $key === '' ? $data : $data[$key] ?? '';
    }
}

if (!function_exists('countries')) {
    /**
     * Get a country dictionary or a specific translation by key.
     *
     * @param string $key Optional dictionary key.
     * @return array|string Array of all items or specific string value.
     */
    function countries(string $key = ''): array|string
    {
        $data = [
            'AD' => 'Andorra',
            'AE' => 'United Arab Emirates',
            'AF' => 'Afghanistan',
            'AG' => 'Antigua And Barbuda',
            'AI' => 'Anguilla',
            'AL' => 'Albania',
            'AM' => 'Armenia',
            'AN' => 'Netherlands Antilles',
            'AO' => 'Angola',
            'AQ' => 'Antarctica',
            'AR' => 'Argentina',
            'AS' => 'American Samoa',
            'AT' => 'Austria',
            'AU' => 'Australia',
            'AW' => 'Aruba',
            'AX' => 'Aland Islands',
            'AZ' => 'Azerbaijan',
            'BA' => 'Bosnia And Herzegovina',
            'BB' => 'Barbados',
            'BD' => 'Bangladesh',
            'BE' => 'Belgium',
            'BF' => 'Burkina Faso',
            'BG' => 'Bulgaria',
            'BH' => 'Bahrain',
            'BI' => 'Burundi',
            'BJ' => 'Benin',
            'BL' => 'Saint Bartelemey',
            'BM' => 'Bermuda',
            'BN' => 'Brunei Darussalam',
            'BO' => 'Bolivia',
            'BQ' => 'Bonaire, Saint Eustatius and Saba',
            'BR' => 'Brazil',
            'BS' => 'Bahamas',
            'BT' => 'Bhutan',
            'BV' => 'Bouvet Island',
            'BW' => 'Botswana',
            'BY' => 'Belarus',
            'BZ' => 'Belize',
            'CA' => 'Canada',
            'CC' => 'Cocos (Keeling) Islands',
            'CD' => 'Congo, The Democratic Republic Of The',
            'CF' => 'Central African Republic',
            'CG' => 'Congo',
            'CH' => 'Switzerland',
            'CI' => "Cote D'Ivoire",
            'CK' => 'Cook Islands',
            'CL' => 'Chile',
            'CM' => 'Cameroon',
            'CN' => 'China',
            'CO' => 'Colombia',
            'CR' => 'Costa Rica',
            'CS' => 'Serbia And Montenegro',
            'CU' => 'Cuba',
            'CV' => 'Cape Verde',
            'CW' => 'Curacao',
            'CX' => 'Christmas Island',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DE' => 'Germany',
            'DJ' => 'Djibouti',
            'DK' => 'Denmark',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'DZ' => 'Algeria',
            'EC' => 'Ecuador',
            'EE' => 'Estonia',
            'EG' => 'Egypt',
            'EH' => 'Western Sahara',
            'ER' => 'Eritrea',
            'ES' => 'Spain',
            'ET' => 'Ethiopia',
            'FI' => 'Finland',
            'FJ' => 'Fiji',
            'FK' => 'Falkland Islands (Malvinas)',
            'FM' => 'Micronesia, Federated States Of',
            'FO' => 'Faroe Islands',
            'FR' => 'France',
            'GA' => 'Gabon',
            'GB' => 'United Kingdom',
            'GD' => 'Grenada',
            'GE' => 'Georgia',
            'GF' => 'French Guiana',
            'GG' => 'Guernsey',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GL' => 'Greenland',
            'GM' => 'Gambia',
            'GN' => 'Guinea',
            'GP' => 'Guadeloupe',
            'GQ' => 'Equatorial Guinea',
            'GR' => 'Greece',
            'GS' => 'South Georgia And The South Sandwich Islands',
            'GT' => 'Guatemala',
            'GU' => 'Guam',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HK' => 'Hong Kong',
            'HM' => 'Heard Island And Mcdonald Islands',
            'HN' => 'Honduras',
            'HR' => 'Croatia',
            'HT' => 'Haiti',
            'HU' => 'Hungary',
            'ID' => 'Indonesia',
            'IE' => 'Ireland',
            'IL' => 'Israel',
            'IM' => 'Isle Of Man',
            'IN' => 'India',
            'IO' => 'British Indian Ocean Territory',
            'IQ' => 'Iraq',
            'IR' => 'Iran, Islamic Republic Of',
            'IS' => 'Iceland',
            'IT' => 'Italy',
            'JE' => 'Jersey',
            'JM' => 'Jamaica',
            'JO' => 'Jordan',
            'JP' => 'Japan',
            'KE' => 'Kenya',
            'KG' => 'Kyrgyzstan',
            'KH' => 'Cambodia',
            'KI' => 'Kiribati',
            'KM' => 'Comoros',
            'KN' => 'Saint Kitts And Nevis',
            'KP' => "Korea, Democratic People'S Republic Of",
            'KR' => 'Korea, Republic Of',
            'KW' => 'Kuwait',
            'KY' => 'Cayman Islands',
            'KZ' => 'Kazakhstan',
            'LA' => "Lao People'S Democratic Republic",
            'LB' => 'Lebanon',
            'LC' => 'Saint Lucia',
            'LI' => 'Liechtenstein',
            'LK' => 'Sri Lanka',
            'LR' => 'Liberia',
            'LS' => 'Lesotho',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'LV' => 'Latvia',
            'LY' => 'Libyan Arab Jamahiriya',
            'MA' => 'Morocco',
            'MC' => 'Monaco',
            'MD' => 'Moldova, Republic Of',
            'ME' => 'Montenegro',
            'MF' => 'Saint Martin',
            'MG' => 'Madagascar',
            'MH' => 'Marshall Islands',
            'MK' => 'Macedonia, The Former Yugoslav Republic Of',
            'ML' => 'Mali',
            'MM' => 'Myanmar',
            'MN' => 'Mongolia',
            'MO' => 'Macao',
            'MP' => 'Northern Mariana Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MS' => 'Montserrat',
            'MT' => 'Malta',
            'MU' => 'Mauritius',
            'MV' => 'Maldives',
            'MW' => 'Malawi',
            'MX' => 'Mexico',
            'MY' => 'Malaysia',
            'MZ' => 'Mozambique',
            'NA' => 'Namibia',
            'NC' => 'New Caledonia',
            'NE' => 'Niger',
            'NF' => 'Norfolk Island',
            'NG' => 'Nigeria',
            'NI' => 'Nicaragua',
            'NL' => 'Netherlands',
            'NO' => 'Norway',
            'NP' => 'Nepal',
            'NR' => 'Nauru',
            'NU' => 'Niue',
            'NZ' => 'New Zealand',
            'OM' => 'Oman',
            'PA' => 'Panama',
            'PE' => 'Peru',
            'PF' => 'French Polynesia',
            'PG' => 'Papua New Guinea',
            'PH' => 'Philippines',
            'PK' => 'Pakistan',
            'PL' => 'Poland',
            'PM' => 'Saint Pierre And Miquelon',
            'PN' => 'Pitcairn',
            'PR' => 'Puerto Rico',
            'PS' => 'Palestinian Territory, Occupied',
            'PT' => 'Portugal',
            'PW' => 'Palau',
            'PY' => 'Paraguay',
            'QA' => 'Qatar',
            'RE' => 'Reunion',
            'RO' => 'Romania',
            'RS' => 'Serbia',
            'RU' => 'Russian Federation',
            'RW' => 'Rwanda',
            'SA' => 'Saudi Arabia',
            'SB' => 'Solomon Islands',
            'SC' => 'Seychelles',
            'SD' => 'Sudan',
            'SE' => 'Sweden',
            'SG' => 'Singapore',
            'SH' => 'Saint Helena',
            'SI' => 'Slovenia',
            'SJ' => 'Svalbard And Jan Mayen',
            'SK' => 'Slovakia',
            'SL' => 'Sierra Leone',
            'SM' => 'San Marino',
            'SN' => 'Senegal',
            'SO' => 'Somalia',
            'SR' => 'Suriname',
            'SS' => 'South Sudan',
            'ST' => 'Sao Tome And Principe',
            'SV' => 'El Salvador',
            'SX' => 'Sint Maarten',
            'SY' => 'Syrian Arab Republic',
            'SZ' => 'Swaziland',
            'TC' => 'Turks And Caicos Islands',
            'TD' => 'Chad',
            'TF' => 'French Southern Territories',
            'TG' => 'Togo',
            'TH' => 'Thailand',
            'TJ' => 'Tajikistan',
            'TK' => 'Tokelau',
            'TL' => 'Timor-Leste',
            'TM' => 'Turkmenistan',
            'TN' => 'Tunisia',
            'TO' => 'Tonga',
            'TR' => 'Turkey',
            'TT' => 'Trinidad And Tobago',
            'TV' => 'Tuvalu',
            'TW' => 'Taiwan, Province Of China',
            'TZ' => 'Tanzania, United Republic Of',
            'UA' => 'Ukraine',
            'UG' => 'Uganda',
            'UM' => 'United States Minor Outlying Islands',
            'US' => 'United States',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VA' => 'Holy See (Vatican City State)',
            'VC' => 'Saint Vincent And The Grenadines',
            'VE' => 'Venezuela',
            'VG' => 'Virgin Islands, British',
            'VI' => 'Virgin Islands, U.S.',
            'VN' => 'Viet Nam',
            'VU' => 'Vanuatu',
            'WF' => 'Wallis And Futuna',
            'WS' => 'Samoa',
            'YE' => 'Yemen',
            'YT' => 'Mayotte',
            'ZA' => 'South Africa',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe'
        ];
        return $key === '' ? $data : $data[$key] ?? '';
    }
}

if (!function_exists('registration_status')) {
    /**
     * Get registration_status dictionary or a specific translation by key.
     *
     * @param string $key Optional dictionary key.
     * @return array|string Array of all items or specific string value.
     */
    function registration_status(string $key = ''): array|string
    {
        $data = [
            '0' => 'Pendaftaran Belum Lengkap',
            '1' => 'Ditinjau',
            '2' => 'Terverifikasi',
            '3' => 'Proses Seleksi',
            '4' => 'Lulus',
            '5' => 'Cadangan',
            '6' => 'Tidak Lulus',
            '7' => 'Daftar Ulang'
        ];
        return ($key === '') ? $data : $data[$key] ?? '';
    }
}
