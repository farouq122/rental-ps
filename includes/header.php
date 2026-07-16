<?php
require_once __DIR__ . '/helper.php';
require_once __DIR__ . '/../config/db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental PlayStation</title>
    <meta name="description" content="Aplikasi manajemen penyewaan PlayStation - PS3, PS4, PS5">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --primary:        #4f46e5;
            --primary-dark:   #4338ca;
            --secondary:      #7c3aed;
            --success:        #10b981;
            --primary-grad:   linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --bg:             #f1f5f9;
            --radius:         0.875rem;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: #1e293b;
            min-height: 100vh;
        }

        /* ── NAVBAR ── */
        .navbar {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: 0 1px 8px rgba(0,0,0,.05);
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.2rem;
            background: var(--primary-grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -.3px;
        }
        .nav-link {
            font-weight: 500;
            color: #475569 !important;
            padding: .5rem .9rem !important;
            border-radius: .5rem;
            transition: background .2s, color .2s;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--primary) !important;
            background: rgba(79, 70, 229, .08);
        }

        /* ── CARD ── */
        .card {
            border: none;
            border-radius: var(--radius);
            box-shadow: 0 2px 12px rgba(0,0,0,.05);
            transition: transform .2s, box-shadow .2s;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,.09);
        }

        /* ── BUTTONS ── */
        .btn-primary {
            background: var(--primary-grad);
            border: none;
            font-weight: 500;
            letter-spacing: .01rem;
        }
        .btn-primary:hover { opacity: .88; background: var(--primary-grad); }

        /* ── TABLE WRAPPER ── */
        .table-responsive {
            background: #fff;
            border-radius: var(--radius);
            padding: 1.25rem;
            box-shadow: 0 2px 12px rgba(0,0,0,.05);
        }
        .table th {
            font-weight: 600;
            color: #64748b;
            font-size: .8125rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            border-bottom: 2px solid #f1f5f9;
        }
        .table td { vertical-align: middle; }
        .table tbody tr:hover { background: #fafafa; }

        /* ── ALERTS ── */
        .alert { border-radius: .75rem; }

        /* ── BADGE ── */
        .badge { font-size: .75rem; padding: .35em .65em; }

        /* ── FORM ── */
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 .2rem rgba(79,70,229,.15);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light mb-4">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>/index.php">
            <i class="bi bi-controller"></i> PS-RENTAL
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto gap-1">
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/index.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/playstation/list.php"><i class="bi bi-device-ssd me-1"></i>PlayStation</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/pelanggan/list.php"><i class="bi bi-people me-1"></i>Pelanggan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/sewa/list.php"><i class="bi bi-hourglass-split me-1"></i>Transaksi Sewa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/riwayat/list.php"><i class="bi bi-journal-text me-1"></i>Riwayat</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container pb-5">
