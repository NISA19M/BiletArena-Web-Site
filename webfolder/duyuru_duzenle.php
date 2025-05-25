<?php

include("baglanti.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $result = $baglanti->query("SELECT * FROM duyurular WHERE id = $id");
    $duyuru = $result->fetch_assoc();

    if (!$duyuru) {
        die("Duyuru bulunamadı.");
    }
} else {
    die("ID belirtilmedi.");
}


if (isset($_POST['id'], $_POST['baslik'], $_POST['icerik'])) {
    $id = intval($_POST['id']);
    $baslik = $_POST['baslik'];
    $icerik = $_POST['icerik'];

    $stmt = $baglanti->prepare("UPDATE duyurular SET baslik = ?, icerik = ? WHERE id = ?");
    $stmt->bind_param("ssi", $baslik, $icerik, $id);

    if ($stmt->execute()) {
        header("Location: admin.php");
        exit;
    } else {
        echo "Güncelleme başarısız.";
    }
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
    <h2>Duyuru Düzenle</h2>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?= $duyuru['id'] ?>">

        <label>Başlık:</label><br>
        <input type="text" name="baslik" value="<?= htmlspecialchars($duyuru['baslik']) ?>"><br><br>

        <label>İçerik:</label><br>
        <textarea name="icerik" rows="5" cols="40"><?= htmlspecialchars($duyuru['icerik']) ?></textarea><br><br>

        <input type="submit" value="Güncelle">
    </form>
</body>
</html>