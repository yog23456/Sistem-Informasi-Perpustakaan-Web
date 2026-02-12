<?php
session_start();
require_once '../lib/functions.php';
require_once '../config/database.php';

$id = (int)($_GET['id'] ?? 0);
$query = mysqli_query($connection, "SELECT * FROM anggota WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) die("Data anggota tidak ditemukan.");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kartu Anggota - <?= htmlspecialchars($data['nama']) ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; }
        
        /* Desain Kartu Utama */
        .id-card {
            width: 450px;
            height: 260px;
            margin: 50px auto;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            background: #fff;
            border: 1px solid #ddd;
        }

        /* Latar Belakang menggunakan hero-bg.jpg dari folder kamu */
        .card-bg {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: url('<?= base_url('assets/img/hero-bg.jpg') ?>') no-repeat center center;
            background-size: cover;
            filter: brightness(0.4);
            z-index: 1;
        }

        .card-content {
            position: relative;
            z-index: 2;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: 100%;
            box-sizing: border-box;
        }

        .card-header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid rgba(255,255,255,0.3);
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .card-header h2 { margin: 0; font-size: 18px; letter-spacing: 1px; }

        .card-body { display: flex; align-items: flex-start; }

        /* Foto Anggota menggunakan folder assets/img/anggota/ */
        .photo-box {
            width: 100px;
            height: 125px;
            border: 3px solid #fff;
            border-radius: 8px;
            overflow: hidden;
            background: #eee;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .photo-box img { width: 100%; height: 100%; object-fit: cover; }

        .info-box { margin-left: 20px; flex: 1; }
        .info-box div { margin-bottom: 8px; }
        .label { font-size: 10px; text-transform: uppercase; color: #ccc; display: block; }
        .value { font-size: 15px; font-weight: bold; color: #fff; }

        .card-footer {
            margin-top: auto;
            text-align: right;
            font-size: 10px;
            color: rgba(255,255,255,0.6);
            font-style: italic;
        }
        @media print {
            /* Memaksa warna dan gambar latar tetap muncul */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            /* Menghilangkan margin otomatis browser (header/footer url) */
            @page {
                margin: 0;
            }

            body {
                margin: 0;
                padding: 0;
                background: none;
            }

            /* Memastikan bayangan (shadow) tidak merusak tata letak saat cetak */
            .id-card {
                box-shadow: none !important;
                margin: 10mm auto; /* Memberi jarak aman dari tepi kertas */
            }
        }
        
    </style>
</head>
<body onload="window.print()">
    <div class="id-card">
        <div class="card-bg"></div>
        <div class="card-content">
            <div class="card-header">
                <i class="fa fa-university" style="margin-right: 10px; font-size: 24px;"></i>
                <h2>PERPUSTAKAAN DIGITAL</h2>
            </div>
            
            <div class="card-body">
                <div class="photo-box">
                    <?php 
                    $foto = (!empty($data['foto']) && file_exists('../assets/img/anggota/'.$data['foto'])) 
                            ? $data['foto'] : 'default.png'; 
                    ?>
                    <img src="<?= base_url('assets/img/anggota/'.$foto) ?>" alt="Foto">
                </div>
                
                <div class="info-box">
                    <div>
                        <span class="label">Nama Lengkap</span>
                        <span class="value"><?= strtoupper(htmlspecialchars($data['nama'])) ?></span>
                    </div>
                    <div>
                        <span class="label">Nomor Induk Mahasiswa</span>
                        <span class="value"><?= htmlspecialchars($data['nim']) ?></span>
                    </div>
                    <div>
                        <span class="label">Kelas / Jurusan</span>
                        <span class="value"><?= htmlspecialchars($data['kelas']) ?></span>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                Kartu ini berlaku sebagai bukti keanggotaan sah Perpustakaan Digital.
            </div>
        </div>
    </div>
</body>
</html>