<?php


session_start();
include("baglanti.php");

if (!isset($_SESSION['name'])) {
    echo "<script>alert('Lütfen giriş yapınız.'); window.location.href='page.php';</script>";
    exit;
}

$kullaniciAdi = $_SESSION['name'];


$kontrolSorgu = mysqli_query($baglanti, "SELECT onay_durumu FROM uyelik WHERE uye_adi = '$kullaniciAdi'");
$veri = mysqli_fetch_assoc($kontrolSorgu);

if ($veri['onay_durumu'] != 1) {
    echo "<script>alert('Hesabınız henüz yönetici tarafından onaylanmamış. Satın alma yapamazsınız.'); window.location.href='page.php';</script>";
    exit;
}





// Sepet dizisini oluştur
if (!isset($_SESSION['sepet'])) {
    $_SESSION['sepet'] = [];
}

// Sepete ekleme
if (isset($_GET['ekle'])) {
    $etkinlik_id = intval($_GET['ekle']);

    // Sepette varsa adeti arttır
    if (isset($_SESSION['sepet'][$etkinlik_id])) {
        $_SESSION['sepet'][$etkinlik_id]++;
    } else {
        $_SESSION['sepet'][$etkinlik_id] = 1;
    }

    header("Location: sepet.php");
    exit;
}

// Sepetten silme
if (isset($_GET['sil'])) {
    $etkinlik_id = intval($_GET['sil']);
    unset($_SESSION['sepet'][$etkinlik_id]);
    header("Location: sepet.php");
    exit;
}


// Satın alma işlemi
// Satın alma işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['sepet']) && !empty($_SESSION['sepet'])) {

    // Ödeme yöntemi kontrolü
    if (!isset($_POST['odeme'])) {
        echo "<script>alert('Lütfen bir ödeme yöntemi seçiniz.'); window.history.back();</script>";
        exit;
    }

    $odemeYontemi = $_POST['odeme']; // "Kredi Kartı" veya "Kapıda Ödeme"

    foreach ($_SESSION['sepet'] as $id => $adet) {
        $etkinlikSorgu = mysqli_query($baglanti, "SELECT kontenjan FROM yakinlar WHERE id = $id");
        $etkinlik = mysqli_fetch_assoc($etkinlikSorgu);

        if ($etkinlik && $etkinlik['kontenjan'] >= $adet) {
            $yeniKontenjan = $etkinlik['kontenjan'] - $adet;
            mysqli_query($baglanti, "UPDATE yakinlar SET kontenjan = $yeniKontenjan WHERE id = $id");
        } else {
            echo "<div style='background: #ffcdd2; padding: 10px; border: 1px solid rgb(0, 157, 255); color: rgb(0, 115, 255);'>
                    Yetersiz kontenjan!
                </div>";
            exit;
        }
    }

    $satinalma_basarili = true;

    $_SESSION['sepet'] = [];
}




?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deneme</title>
    <link rel="stylesheet" href="style-sepet.css">
</head>
<body style="background-color: #030B16;">

    <nav>
        <h2>BiletArena</h2>
        <a href="page.php">Ana Sayfa</a>
        <a href="konser.php">Konser</a>
        <a href="festival.php">Festival</a>
        <a href="tiyatro.php">Tiyatro</a>
        <a href="film.php">Film</a>
        <a href="kayit.php">Üyelik</a>
        <a href="sepet.php">Sepet</a>
        <span></span>
    </nav>



    <h2>Sepetiniz</h2>

    <?php if (empty($_SESSION['sepet'])): ?>
        <p>Sepetiniz boş</p>
    <?php else: ?>
        <form action="" method="post">
            <table>
                <tr>
                    <th>Etkinlik</th>
                    <th>Adet</th>
                    <th>Fiyat</th>
                    <th>Toplam</th>
                </tr>
                <?php
                $toplam = 0;
                foreach ($_SESSION['sepet'] as $id => $adet):
                    $sorgu = mysqli_query($baglanti, "SELECT * FROM yakinlar WHERE id = $id");
                    $etkinlik = mysqli_fetch_assoc($sorgu);
                    $araToplam = $etkinlik['fiyat'] * $adet;
                    $toplam += $araToplam;
                ?>
                <tr>
                    <td><?= htmlspecialchars($etkinlik['baslik']) ?></td>
                    <td><?= $adet ?></td>
                    <td><?= number_format($etkinlik['fiyat'], 2) ?> ₺</td>
                    <td><?= number_format($araToplam, 2) ?> ₺</td>
                    <td><a href="?sil=<?= $id ?>" class="btn">Sil</a></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td><strong>Genel Toplam</strong></td>
                    <td><strong><?= number_format($toplam, 2) ?> ₺</strong></td>
                </tr>
            </table>
            <div>
                <label>
                    <input type="radio" name="odeme" value="Kapıda Ödeme"> Kapıda Ödeme
                </label>
                <label>
                    <input type="radio" name="odeme" value="Kredi Kartı"> Kredi Kartı
                </label>
            </div>
            <div>
                <input type="submit" name="satinal" value="Satın Al">
            </div>
        </form>
    <?php endif; ?>


    <?php if (isset($satinalma_basarili)): ?>
        <div style="background:rgb(255, 255, 255); padding: 10px; border: 1px solid rgb(10, 148, 175); color:rgb(0, 195, 255);">
            Satın alma işlemi başarıyla gerçekleşti!
        </div>
    <?php endif; ?>



    <div class="latest">
        <h1>Bizi Tercih Ettiğiniz için Teşekkürler</h1>
    </div>





</body>
</html>