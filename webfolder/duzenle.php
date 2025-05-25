<?php
include("baglanti.php");

if (!isset($_GET['id'])) {
    echo "Etkinlik ID belirtilmedi.";
    exit;
}

$id = intval($_GET['id']);
$query = "SELECT * FROM yakinlar WHERE id = $id";
$result = mysqli_query($baglanti, $query);
$yakinlar = mysqli_fetch_assoc($result);

if (!$yakinlar) {
    echo "Etkinlik bulunamadı.";
    exit;
}

if (isset($_POST['guncelle'])) {
    $id = intval($_POST['id']);
    $baslik = $_POST['baslik'];
    $sehir = $_POST['sehir'];
    $mekan = $_POST['mekan'];
    $tarih = $_POST['tarih'];
    $saat = $_POST['saat'];
    $fiyat = $_POST['fiyat'];
    $kontenjan = $_POST['kontenjan'];

    $resimAdi = $yakinlar['afis']; // eski resim adı
    if (!empty($_FILES['afis']['name'])) {
        $hedefKlasor = "uploads/";
        $yeniResimAdi = time() . "_" . basename($_FILES["afis"]["name"]);
        $hedefYol = $hedefKlasor . $yeniResimAdi;

        // Dosya yüklenirse
        if (move_uploaded_file($_FILES["afis"]["tmp_name"], $hedefYol)) {
            // Eski resmi sil (isteğe bağlı)
            if (!empty($resimAdi) && file_exists("uploads/$resimAdi")) {
                unlink("uploads/$resimAdi");
            }
            $resimAdi = $yeniResimAdi;
        }
    }
    $sql = "UPDATE yakinlar 
            SET baslik='$baslik', sehir='$sehir', mekan='$mekan', tarih='$tarih', 
                saat='$saat', fiyat='$fiyat', kontenjan='$kontenjan', afis='$resimAdi'
            WHERE id=$id";
    mysqli_query($baglanti, $sql);

    header("Location: admin.php");
}

?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style-admin.css">
    <title>Yönetici Ekranı</title>
</head>
<body>


    <form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $yakinlar['id']; ?>">

    <label>Başlık:</label><br>
    <input type="text" name="baslik" value="<?php echo $yakinlar['baslik']; ?>" required><br><br>

    <label>Şehir:</label><br>
    <input type="text" name="sehir" value="<?php echo $yakinlar['sehir']; ?>"><br><br>

    <label>Mekan:</label><br>
    <input type="text" name="mekan" value="<?php echo $yakinlar['mekan']; ?>"><br><br>

    <label>Tarih:</label><br>
    <input type="date" name="tarih" value="<?php echo $yakinlar['tarih']; ?>"><br><br>

    <label>Saat:</label><br>
    <input type="time" name="saat" value="<?php echo $yakinlar['saat']; ?>"><br><br>

    <label>Fiyat (₺):</label><br>
    <input type="number" step="0.01" name="fiyat" value="<?php echo $yakinlar['fiyat']; ?>"><br><br>

    <label>Kontenjan:</label><br>
    <input type="number" name="kontenjan" value="<?php echo $yakinlar['kontenjan']; ?>"><br><br>

    <label>Mevcut Resim:</label><br>
    <?php if (!empty($yakinlar['afis'])): ?>
        <img src="resimler/<?php echo $yakinlar['afis']; ?>" width="150"><br>
    <?php endif; ?>
    <input type="file" name="afis"><br><br>

    <input type="submit" name="guncelle" value="Güncelle">
    </form>


</body>
</html>