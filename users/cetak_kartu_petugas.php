<?php
session_start();
require_once '../lib/functions.php';
require_once '../config/database.php';

$id = (int)($_GET['id'] ?? 0);
$query = mysqli_query($connection, "SELECT * FROM users WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) die("Data petugas tidak ditemukan.");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kartu Petugas - <?= htmlspecialchars($data['nama_lengkap']) ?></title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; }
        
        /* CSS Utama */
        .card {
            width: 400px; height: 250px;
            background: #fff; border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: relative; overflow: hidden;
            border: 1px solid #ddd; margin: 50px auto;
            
            /* PENTING: Agar warna muncul saat diprint */
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .accent { 
            height: 70px; 
            background: #fe0808 !important; /* Pakai !important agar warna hitam tetap muncul */
            -webkit-print-color-adjust: exact;
        }

        .content { display: flex; padding: 15px; margin-top: -35px; }
        
        .photo {
            width: 100px; height: 120px;
            background: #fff; border: 3px solid #fff;
            border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            object-fit: cover;
        }

        .info { margin-left: 20px; color: #333; margin-top: 40px; }
        .info h2 { margin: 0; font-size: 18px; color: #2c3e50; }
        .info p { margin: 5px 0; font-size: 12px; color: #666; }
        
        .role-tag {
            background: #e74c3c !important; 
            color: #fff !important;
            padding: 2px 10px; border-radius: 20px;
            font-size: 10px; text-transform: uppercase;
            display: inline-block; margin-top: 5px;
            -webkit-print-color-adjust: exact;
        }

        /* Khusus pengaturan cetak */
        @media print {
            body { background: none; }
            .card { margin: 0; box-shadow: none; border: 1px solid #ddd; }
            
            /* Menghilangkan header/footer otomatis browser (url, date, dll) */
            @page { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="card">
        <div class="accent"></div>
        <div class="content">
            <img class="photo" src="https://ui-avatars.com/api/?name=<?= urlencode($data['nama_lengkap']) ?>&background=random&color=fff">
            
            <div class="info">
                <h2><?= strtoupper(htmlspecialchars($data['nama_lengkap'])) ?></h2>
                <p>ID Petugas: #<?= $data['id'] ?></p>
                <p>Username: <?= htmlspecialchars($data['username']) ?></p>
                <span class="role-tag">Staff Perpustakaan</span>
            </div>
        </div>
        <div style="position:absolute; bottom:10px; width:100%; text-align:center; font-size:9px; color:#aaa;">
            Perpustakaan Digital - Kartu Akses Petugas
        </div>
    </div>
</body>
</html>