<?php

session_start();
include("baglanti.php");https://www.eventbrite.com/

if (!isset($_SESSION["login"])) {

    echo "Bu sayfayı görüntüleme yetkiniz yoktur.";
    exit;

}


$sorgu = "SELECT * FROM uyelik";
$sonuc = mysqli_query($baglanti, $sorgu);

if (isset($_GET['uye_id']) && is_numeric($_GET['uye_id'])) {
    $id = intval($_GET['uye_id']);

    $sor = $baglanti->query("DELETE FROM uyelik WHERE id = $id");

    if ($sor) {
        if (mysqli_affected_rows($baglanti) > 0) {
            header("Location: admin.php");
            exit;
        } else {
            echo "<p style='color:red;'>Kullanıcı bulunamadı veya silinemedi.</p>";
        }
    } else {
        echo "<p style='color:red;'>Sorgu çalıştırılamadı: " . mysqli_error($baglanti) . "</p>";
    }
}

if (isset($_GET['onayla'])) {
    $id = intval($_GET['onayla']);
    mysqli_query($baglanti, "UPDATE uyelik SET onay_durumu = 1 WHERE id = $id");
    header("Location: admin.php");
    exit;
}

if (isset($_POST["enyakin"])) {
    $baslik = mysqli_real_escape_string($baglanti, $_POST['baslik']);
    $sehir = mysqli_real_escape_string($baglanti, $_POST['sehir']);
    $mekan = mysqli_real_escape_string($baglanti, $_POST['mekan']);
    $tarih = $_POST['tarih'];
    $saat = $_POST['saat'];
    $fiyat = $_POST['fiyat'];
    $kontenjan_baslangic = intval($_POST['kontenjan_baslangic']);
    $kontenjan = $kontenjan_baslangic;


    $afis_ad = null;
    if (isset($_FILES['afis']) && $_FILES['afis']['error'] == 0) {
        $izinli_uzantilart = ['jpg', 'jpeg', 'png', 'gif'];
        $dosya_adit = $_FILES['afis']['name'];
        $dosya_gecicit = $_FILES['afis']['tmp_name'];
        $dosya_uzantit = strtolower(pathinfo($dosya_adit, PATHINFO_EXTENSION));

        if (in_array($dosya_uzantit, $izinli_uzantilart)) {
            $yeni_dosya_adit = uniqid() . '.' . $dosya_uzantit;
            $hedef_klasort = 'uploads/';
            if (!is_dir($hedef_klasort)) {
                mkdir($hedef_klasort, 0777, true);
            }
            $hedef_yolt = $hedef_klasort . $yeni_dosya_adit;
            if (move_uploaded_file($dosya_gecicit, $hedef_yolt)) {
                $afis_ad = $yeni_dosya_adit;
            } else {
                echo "Afiş yüklenirken bir hata oluştu.";
                exit;
            }
        } else {
            echo "Sadece JPG, JPEG, PNG ve GIF dosyaları yüklenebilir.";
            exit;
        }
    }

    $sayfa = mysqli_real_escape_string($baglanti, $_POST['sayfa']);


    $kayit = "INSERT INTO yakinlar (baslik, sehir, mekan, tarih, saat, afis, sayfa, fiyat, kontenjan, kontenjan_baslangic) VALUES ('$baslik', '$sehir', '$mekan', '$tarih', '$saat', '$afis_ad', '$sayfa', '$fiyat', '$kontenjan', '$kontenjan_baslangic')";



    if (mysqli_query($baglanti, $kayit)) {
        echo "Etkinlik başarıyla eklendi.";
    } else {
        echo "Hata: " . mysqli_error($baglanti);
    }}


$kayit = "SELECT * FROM yakinlar ORDER BY tarih DESC";
$resultante = mysqli_query($baglanti, $kayit);


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $etkinlik_id = intval($_GET['id']);
    $sorun = mysqli_query($baglanti, "DELETE FROM yakinlar WHERE id = $etkinlik_id");

    if ($sorun) {
        if (mysqli_affected_rows($baglanti) > 0) {
            header("Location: admin.php");
            exit;
        } else {
            echo "<p style='color:red;'>Etkinlik bulunamadı veya silinemedi.</p>";
        }
    } else {
        echo "<p style='color:red;'>Sorgu çalıştırılamadı: " . mysqli_error($baglanti) . "</p>";
    }
}




if (isset($_POST["duyuru"])) {
    $baslik = $_POST['baslik'];
    $icerik = $_POST['icerik'];

    $sorgu = $baglanti->prepare("INSERT INTO duyurular (baslik, icerik) VALUES (?, ?)");
    $sorgu->execute([$baslik, $icerik]);

    echo "<p>Duyuru başarıyla eklendi!</p>";
}

$result = $baglanti->query("SELECT * FROM duyurular");

if (isset($_GET['id'])) {
    $id_duyuru = intval($_GET['id']);
    $sorunlu = mysqli_query($baglanti, "DELETE FROM duyurular WHERE id = $id_duyuru");

    if ($sorunlu) {
        if (mysqli_affected_rows($baglanti) > 0) {
            header("Location: admin.php");
            exit;
        } else {
            echo "<p style='color:red;'>Etkinlik bulunamadı veya silinemedi.</p>";
        }
    } else {
        echo "<p style='color:red;'>Sorgu çalıştırılamadı: " . mysqli_error($baglanti) . "</p>";
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
    <h2>Yönetici Paneli</h2>
    <table>
        <caption>Kullanıcı Listesi</caption>
        <tr>
            <th>ID</th>
            <th>Adı</th>
            <th>Email</th>
            <th>Onay Durumu</th>
            <th>İşlem</th>
        </tr>
        <?php while($satir = mysqli_fetch_assoc($sonuc)): ?>
        <tr>
            <td><?php echo $satir['id']; ?></td>
            <td><?php echo $satir['uye_adi']; ?></td>
            <td><?php echo $satir['uye_email']; ?></td>
            <td>
            <?php if ($satir['onay_durumu']): ?>
                <span style="color: green;">✔️ Onaylandı</span>
            <?php else: ?>
                <a href="?onayla=<?= $satir['id'] ?>" style="color: blue;">Onayla</a>
            <?php endif; ?>
            </td>

            <td>
                <a href="admin.php?uye_id=<?php echo $satir['id']; ?>" onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?');">Sil</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>





    <form action="admin.php" method="post" enctype="multipart/form-data">
        <h2>Etkinlik Ekle</h2>

        <label>Sayfa:</label><br>
        <select name="sayfa" required>
            <option value="tiyatro">Tiyatro</option>
            <option value="konser">Konser</option>
            <option value="festival">Festival</option>
            <option value="sinema">Sinema</option>
        </select><br><br>

        <label>Etkinlik Başlığı:</label><br>
        <input type="text" name="baslik" required><br><br>

        <label>Şehir:</label><br>
        <input type="text" name="sehir"><br><br>

        <label>Mekan:</label><br>
        <input type="text" name="mekan"><br><br>   

        <label>Tarih:</label><br>
        <input type="date" name="tarih" required><br><br>

        <label>Saat:</label><br>
        <input type="time" name="saat"><br><br>

        <label>Fiyat (₺):</label><br>
        <input type="number" name="fiyat" step="0.01" min="0"><br><br>

        <label>Kontenjan:</label><br>
        <input type="number" name="kontenjan_baslangic" min="1"><br><br>

        <label>Afiş:</label><br>
        <input type="file" name="afis" accept="image/*"><br><br>

        <input type="submit" value="Etkinlik Ekle" name="enyakin">
    </form>




    <table>
        <caption>Etkinlik Listesi</caption>
        <tr>
            <th>ID</th>
            <th>Başlık</th>
            <th>Şehir</th>
            <th>Mekan</th>
            <th>Tarih</th>
            <th>Saat</th>
            <th>Fiyat</th>
            <th>Kontenjan</th>
            <th>Kalan Kontenjan</th>
            <th>Resim</th>
            <th>Eklenen Yer</th>
        </tr>
            <?php while($rowtwo = mysqli_fetch_assoc($resultante)): ?>
        <tr>
            <td><?php echo $rowtwo['id']; ?></td>
            <td><?php echo $rowtwo['baslik']; ?></td>
            <td><?php echo $rowtwo['sehir']; ?></td>
            <td><?php echo $rowtwo['mekan']; ?></td>
            <td><?php echo $rowtwo['tarih']; ?></td>
            <td><?php echo $rowtwo['saat']; ?></td>
            <td><?php echo $rowtwo['fiyat']; ?></td>
            <td><?php echo $rowtwo['kontenjan_baslangic']; ?></td>
            <td><?php echo $rowtwo['kontenjan']; ?></td>
            <td><?php echo $rowtwo['afis']; ?></td>
            <td><?php echo $rowtwo['sayfa']; ?></td>
            <td>
                <a href="admin.php?id=<?php echo $rowtwo['id']; ?>" onclick="return confirm('Bu etkinliği silmek istediğinize emin misiniz?');">Sil</a>
                <a href="duzenle.php?id=<?php echo $rowtwo['id']; ?>">Düzenle</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>





    <form method="POST">
        <h2>Duyuru Ekle</h2>
        <input type="text" name="baslik" placeholder="Duyuru Başlığı" required><br><br>
        <textarea name="icerik" placeholder="Duyuru İçeriği" rows="5" required></textarea><br><br>
        <button type="submit" value="Duyuru Ekle" name="duyuru">Ekle</button><br><br>
    </form>

    <h2>Duyurular</h2>

    <div class="duyuru-wrapper">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="duyuru">
            <h3><?= htmlspecialchars($row['baslik']) ?></h3>
            <p><?= nl2br(htmlspecialchars($row['icerik'])) ?></p>
            <small>Yayın Tarihi: <?= $row['tarih'] ?></small>

            <div class="butonlar">
                <a href="duyuru_duzenle.php?id=<?= $row['id'] ?>" class="btn btn-duzenle">Düzenle</a>
                <a href="admin.php?id=<?= $row['id'] ?>" onclick="return confirm('Bu duyuruyu silmek istediğinize emin misiniz?')">Sil</a>
            </div>
        </div>
    <?php endwhile; ?>
    </div>




</body>
</html>
