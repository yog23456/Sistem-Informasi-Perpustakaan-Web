<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PerpusPanel | Mazer Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" crossorigin href="<?= base_url('assets/'.$THEME.'/assets/compiled/css/app.css') ?>">
    <link rel="stylesheet" crossorigin href="<?= base_url('assets/'.$THEME.'/assets/compiled/css/app-dark.css') ?>">
    <link rel="stylesheet" crossorigin href="<?= base_url('assets/'.$THEME.'/assets/compiled/css/iconly.css') ?>">
    <style>
        @media screen and (min-width: 1200px) {
            #main { margin-left: 300px; padding: 0 !important; }
            #sidebar { width: 300px; position: fixed; }
        }
        .container-fluid { max-width: 100% !important; padding: 0 25px !important; }
        
        /* Merapikan Sidebar Header */
        .sidebar-header { padding: 2rem 2rem 1rem 2rem !important; }
        .sidebar-header .logo i { font-size: 1.8rem; }
        .sidebar-header .logo span { font-size: 1.4rem; font-weight: 800; letter-spacing: -0.5px; }

        /* Memperbaiki Warna Tombol Tambah Data agar lebih kontras */
        .btn-tambah-data { 
            background-color: #435ebe !important;
            color: #fff !important;
            border-radius: 8px;
            font-weight: 600;
            padding: 8px 18px;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-tambah-data:hover {
            background-color: #394fa3 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(67, 94, 190, 0.3);
        }

        [data-bs-theme="dark"] body { background-color: #1e1e2d !important; }
        [data-bs-theme="dark"] #sidebar { background-color: #1b1b28 !important; }
    </style>
</head>
<body>
    <script src="<?= base_url('assets/'.$THEME.'/assets/static/js/initTheme.js') ?>"></script>
    <div id="app">