<!doctype html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?= esc($school_name ?? '') ?></title>
    <style media="all" type="text/css">
        @media all {
            .btn-primary table td:hover {
                background-color: #2e6b12 !important;
            }

            .btn-primary a:hover {
                background-color: #2e6b12 !important;
                border-color: #2e6b12 !important;
            }
        }

        @media only screen and (max-width: 640px) {
            .main p,
            .main td,
            .main span {
                font-size: 16px !important;
            }

            .wrapper {
                padding: 8px !important;
            }

            .content {
                padding: 0 !important;
            }

            .container {
                padding: 0 !important;
                padding-top: 8px !important;
                width: 100% !important;
            }

            .main {
                border-left-width: 0 !important;
                border-radius: 0 !important;
                border-right-width: 0 !important;
                padding: 24px 0;
            }

            .btn table {
                max-width: 100% !important;
                width: 100% !important;
            }

            .btn a {
                font-size: 16px !important;
                max-width: 100% !important;
                width: 100% !important;
            }
        }

        @media all {
            .apple-link a {
                color: inherit !important;
                font-family: inherit !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                text-decoration: none !important;
            }

            #MessageViewBody a {
                color: inherit;
                text-decoration: none;
                font-size: inherit;
                font-family: inherit;
                font-weight: inherit;
                line-height: inherit;
            }
        }
    </style>
</head>
<body style="font-family: Helvetica, sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.3; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: #f4f5f6; margin: 0; padding: 0;">
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body"
       style="border-collapse: separate; mso-table-lspace: 0; mso-table-rspace: 0; background-color: #f4f5f6; width: 100%;"
       width="100%" bgcolor="#f4f5f6">
    <tr>
        <td style="font-family: Helvetica, sans-serif; font-size: 14px; vertical-align: top;" >&nbsp;</td>
        <td class="container"
            style="font-family: Helvetica, sans-serif; font-size: 14px; vertical-align: top; max-width: 600px; padding: 24px 0 24px;width: 600px; margin: 0 auto;"
            width="600" >
            <div class="content"
                 style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 600px; padding: 0;">
                <span class="preheader"
                      style="color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;">Aktifkan akun pendaftaran Anda untuk melanjutkan proses pendaftaran.</span>
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="main"
                       style="border-collapse: separate; mso-table-lspace: 0; mso-table-rspace: 0; background: #ffffff; border: 1px solid #eaebed; border-radius: 16px; width: 100%; padding: 24px 0"
                       width="100%">
                    <tr>
                        <td class="wrapper"
                            style="font-family: Helvetica, sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 24px;"
                            >
                            <h4 style="font-family: Helvetica, sans-serif; font-size: 24px; font-weight: bold; margin: 0 0 16px;">
                                Aktivasi Akun</h4>
                            <p style="font-family: Helvetica, sans-serif; font-size: 14px; font-weight: normal; margin: 0 0 16px;">
                                Yth. <?= esc($full_name ?? '') ?>,</p>
                            <p style="font-family: Helvetica, sans-serif; font-size: 14px; font-weight: normal; margin: 0 0 16px;">
                                Selamat! Anda telah berhasil membuat akun pada Sistem Penerimaan Santri Baru (PSB) Online <?= esc($school_name ?? '') ?>. Untuk melanjutkan ke tahap berikutnya, silakan aktifkan akun Anda dengan menekan tombol “Verifikasi Akun” di bawah ini:
                            </p>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0"
                                   class="btn btn-primary"
                                   style="border-collapse: separate; mso-table-lspace: 0; mso-table-rspace: 0; box-sizing: border-box; width: 100%; min-width: 100%;"
                                   width="100%">
                                <tbody>
                                <tr>
                                    <td align="left"
                                        style="font-family: Helvetica, sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 16px;"
                                        >
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0"
                                               style="border-collapse: separate; mso-table-lspace: 0; mso-table-rspace: 0; width: auto;">
                                            <tbody>
                                            <tr>
                                                <td style="font-family: Helvetica, sans-serif; font-size: 14px; vertical-align: top; border-radius: 4px; text-align: center; background-color: #3f7d1c;"
                                                     align="center" bgcolor="#3f7d1c"><a
                                                            href="<?= esc($verification_link ?? '', 'attr') ?>" target="_blank"
                                                            style="border-radius: 4px; box-sizing: border-box; cursor: pointer; display: inline-block; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 24px; text-decoration: none; text-transform: capitalize; background-color: #3f7d1c; color: #ffffff;">Verifikasi Akun</a></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <p style="font-family: Helvetica, sans-serif; font-size: 14px; font-weight: normal; margin: 16px 0;">Tautan verifikasi berlaku selama 3 hari. Jika tautan sudah kedaluwarsa, masuk dengan email dan kata sandi Anda untuk menerima tautan yang baru.</p>
                            <p style="font-family: Helvetica, sans-serif; font-size: 14px; font-weight: normal; margin: 16px 0;">Setelah akun diaktifkan, Anda dapat melanjutkan pengisian data dan proses pendaftaran.</p>
                            <p style="font-family: Helvetica, sans-serif; font-size: 12px; font-weight: normal; margin: 16px 0; color: #6b7280; word-break: break-all;">Jika tombol di atas tidak berfungsi, salin tautan berikut ke browser Anda:<br><?= esc($verification_link ?? '') ?></p>
                            <p style="font-family: Helvetica, sans-serif; font-size: 14px; font-weight: normal; margin-bottom: 32px 0;">Terima kasih atas perhatian dan kerjasama Anda.</p>
                            <p style="font-family: Helvetica, sans-serif; font-size: 14px; font-weight: bold;">
                                Hormat kami,<br>
                                Panitia PSB <?= esc($school_name ?? '') ?></p>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
        <td style="font-family: Helvetica, sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
    </tr>
</table>
</body>
</html>
