<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= lang('Errors.pageNotFound') ?></title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            background: #1a1a2e;
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
    <p class="error-code">404 Page Not Found</p>
    <p class="error-message">
        <?php if (ENVIRONMENT !== 'production') : ?>
                <?= nl2br(esc($message)) ?>
            <?php else : ?>
                <?= lang('Errors.sorryCannotFind') ?>
            <?php endif; ?>
    </p>
</div>
</body>
</html>
