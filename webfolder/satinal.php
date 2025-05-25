<?php
session_start();
include("baglanti.php");

if (!isset($_SESSION['sepet']) || empty($_SESSION['sepet'])) {
    echo "Sepet boş. <a href='page.php'>Etkinliklere Dön</a>";
    exit;
}



// Sepeti temizle
unset($_SESSION['sepet']);

echo "<h2>Satın alma işlemi başarılı!</h2>";
echo "<p><a href='page.php'>Etkinliklere Dön</a></p>";
?>