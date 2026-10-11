<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= esc($error_code ?? '') ?></title>
    <link rel="icon" href="<?= esc(base_url('favicon.ico'), 'attr') ?>">
    <style>
        body {
            margin: 0;
            height: 100vh;
            background: #1b400d;
            color: #eaeaea;
            display: flex;
            font-family: Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
            justify-content: center;
            align-items: center;
        }

        .container {
            text-align: center;
        }
        .error-code, .error-message {
            letter-spacing: 1.5px;
        }
        .error-code {
            font-size: 1.5rem;
            margin: 0;
            font-weight: lighter;
            opacity: 0.8;
        }

        .error-message {
            font-size: 1rem;
            opacity: 0.8;
        }
    </style>
</head>
<body>
<div class="container">
    <p class="error-code"><?= esc($error_code ?? '') ?></p>
    <p class="error-message"><?= esc($error_message ?? '') ?></p>
    <p><a href="<?= esc(base_url(), 'attr') ?>" style="color: #eaeaea;">Kembali ke halaman utama</a></p>
</div>
</body>
</html>
