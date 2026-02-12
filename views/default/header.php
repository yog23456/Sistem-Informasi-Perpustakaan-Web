<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Modern & Elegant</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/'.$THEME.'/css/style.css') ?>">
    <!-- Custom CSS for Theme Colors -->
    <style>
        :root {
            /* Light Mode */
            --bg-main: #F3F4F6;
            --bg-card: #FFFFFF;
            --bg-sidebar: #FFFFFF;
            --text-main: #2c2c2c;
            --text-secondary: #9CA3AF;
            --border-color: #9CA3AF;
            --btn-primary-bg: #2c2c2c;
            --btn-primary-hover: #1a1a1a;
            --btn-outline-color: #9CA3AF;
            --btn-outline-hover: #9CA3AF;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        [data-theme="dark"] {
            /* Dark Mode */
            --bg-main: #1f2937;
            --bg-card: #374151;
            --bg-sidebar: #374151;
            --text-main: #F3F4F6;
            --text-secondary: #9CA3AF;
            --border-color: #4B5563;
            --btn-primary-bg: #4B5563;
            --btn-primary-hover: #374151;
            --btn-outline-color: #9CA3AF;
            --btn-outline-hover: #F3F4F6;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }

        .bg-gray-light {
            background-color: var(--bg-main);
        }

        .border-gray {
            border-color: var(--border-color) !important;
        }

        .text-dark-alt {
            color: var(--text-main);
        }

        .text-gray {
            color: var(--text-secondary);
        }

        .card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            box-shadow: var(--card-shadow);
        }

        .sidebar, .topnav {
            background-color: var(--bg-sidebar);
            border-color: var(--border-color);
            color: var(--text-main);
        }

        .btn-primary {
            background-color: var(--btn-primary-bg);
            border-color: var(--btn-primary-bg);
            color: var(--text-main);
        }

        .btn-add-book {
            background-color: var(--btn-primary-bg);
            border: 1px solid var(--btn-primary-bg);
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-add-book:hover {
            background-color: var(--btn-primary-hover);
            border-color: var(--btn-primary-hover);
            color: white;
            transform: translateY(-2px);
        }

        .btn-primary:hover {
            background-color: var(--btn-primary-hover);
            border-color: var(--btn-primary-hover);
        }

        .btn-outline-secondary {
            color: var(--btn-outline-color);
            border-color: var(--btn-outline-color);
        }

        .btn-outline-secondary:hover {
            background-color: var(--btn-outline-hover);
            color: var(--bg-card);
        }

        .btn-add-book {
            background-color: var(--btn-primary-bg);
            border: 1px solid var(--btn-primary-bg);
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-add-peminjaman {
            background-color: var(--btn-primary-bg);
            border: 1px solid var(--btn-primary-bg);
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-save-peminjaman {
            background-color: var(--btn-primary-bg);
            border: 1px solid var(--btn-primary-bg);
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-add-pengembalian {
            background-color: var(--btn-primary-bg);
            border: 1px solid var(--btn-primary-bg);
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-save-petugas {
            background-color: var(--btn-primary-bg);
            border: 1px solid var(--btn-primary-bg);
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-save-petugas:hover {
            background-color: var(--btn-primary-hover);
            border-color: var(--btn-primary-hover);
            color: white;
            transform: translateY(-2px);
        }

        .btn-add-petugas {
            background-color: var(--btn-primary-bg);
            border: 1px solid var(--btn-primary-bg);
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-add-petugas:hover {
            background-color: var(--btn-primary-hover);
            border-color: var(--btn-primary-hover);
            color: white;
            transform: translateY(-2px);
        }

        .btn-add-pengembalian:hover {
            background-color: var(--btn-primary-hover);
            border-color: var(--btn-primary-hover);
            color: white;
            transform: translateY(-2px);
        }

        .btn-save-peminjaman:hover {
            background-color: var(--btn-primary-hover);
            border-color: var(--btn-primary-hover);
            color: white;
            transform: translateY(-2px);
        }

        .btn-add-peminjaman:hover {
            background-color: var(--btn-primary-hover);
            border-color: var(--btn-primary-hover);
            color: white;
            transform: translateY(-2px);
        }

        .btn-add-book:hover {
            background-color: var(--btn-primary-hover);
            border-color: var(--btn-primary-hover);
            color: white;
            transform: translateY(-2px);
        }

        .btn-add-peminjaman {
            background-color: var(--btn-primary-bg);
            border: 1px solid var(--btn-primary-bg);
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-add-peminjaman:hover {
            background-color: var(--btn-primary-hover);
            border-color: var(--btn-primary-hover);
            color: white;
            transform: translateY(-2px);
        }

        .btn-save-peminjaman {
            background-color: var(--btn-primary-bg);
            border: 1px solid var(--btn-primary-bg);
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-save-peminjaman:hover {
            background-color: var(--btn-primary-hover);
            border-color: var(--btn-primary-hover);
            color: white;
            transform: translateY(-2px);
        }

        .btn-add-pengembalian {
            background-color: var(--btn-primary-bg);
            border: 1px solid var(--btn-primary-bg);
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-add-pengembalian:hover {
            background-color: var(--btn-primary-hover);
            border-color: var(--btn-primary-hover);
            color: white;
            transform: translateY(-2px);
        }

        .btn-save-petugas {
            background-color: var(--btn-primary-bg);
            border: 1px solid var(--btn-primary-bg);
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-save-petugas:hover {
            background-color: var(--btn-primary-hover);
            border-color: var(--btn-primary-hover);
            color: white;
            transform: translateY(-2px);
        }

        .btn-add-petugas {
            background-color: var(--btn-primary-bg);
            border: 1px solid var(--btn-primary-bg);
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-add-petugas:hover {
            background-color: var(--btn-primary-hover);
            border-color: var(--btn-primary-hover);
            color: white;
            transform: translateY(-2px);
        }
    </style>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Toggle Dark/Light Mode
        function toggleTheme() {
            const body = document.body;
            const currentTheme = localStorage.getItem('theme') || 'light';

            if (currentTheme === 'light') {
                body.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
            } else {
                body.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
            }
        }

        // Apply saved theme on page load
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'light';
            if (savedTheme === 'dark') {
                document.body.setAttribute('data-theme', 'dark');
            }
        });
    </script>
</head>
<body>