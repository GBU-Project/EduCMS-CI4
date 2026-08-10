<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Halaman Tidak Ditemukan | <?php echo function_exists('site_name') ? esc_html(site_name()) : 'EduCMS'; ?></title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #3f51b5 0%, #5c6bc0 100%);
            color: #fff;
            text-align: center;
            padding: 20px;
        }
        .wrap { max-width: 480px; }
        .code { font-size: 96px; font-weight: 800; line-height: 1; letter-spacing: -2px; margin: 0; text-shadow: 0 4px 24px rgba(0,0,0,0.2); }
        h1 { font-size: 22px; font-weight: 600; margin: 12px 0 8px; }
        p { opacity: .85; margin: 0 0 28px; font-size: 15px; }
        a.btn { display: inline-block; padding: 10px 24px; background: #fff; color: #3f51b5; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; transition: transform .15s ease; }
        a.btn:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="wrap">
        <p class="code">404</p>
        <h1>Halaman Tidak Ditemukan</h1>
        <p>Halaman yang Anda cari tidak tersedia atau sudah dipindahkan.</p>
        <a class="btn" href="<?php echo base_url(); ?>">Kembali ke Beranda</a>
    </div>
</body>
</html>
