<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>403 Akses Ditolak (Forbidden)</title>
    <style>
        body {
            height: 100%;
            background: #fafafa;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #777;
            font-weight: 300;
        }
        h1 {
            font-weight: lighter;
            letter-spacing: normal;
            font-size: 3rem;
            margin-top: 0;
            margin-bottom: 0;
            color: #d9534f;
        }
        .wrap {
            max-width: 600px;
            margin: 5rem auto;
            padding: 2rem;
            background: #fff;
            text-align: center;
            border: 1px solid #efefef;
            border-radius: 0.5rem;
            position: relative;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        p {
            margin-top: 1.5rem;
            font-size: 1.1rem;
            color: #555;
        }
        a {
            display: inline-block;
            margin-top: 1.5rem;
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="wrap">
    <h1>403</h1>
    <p><?= esc($message ?? 'Anda tidak memiliki hak akses untuk halaman ini.') ?></p>
    <a href="<?= base_url('admin') ?>">&larr; Kembali ke Dashboard Admin</a>
</div>
</body>
</html>
