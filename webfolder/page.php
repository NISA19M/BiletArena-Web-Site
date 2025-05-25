<?php

session_start();

include("baglanti.php");

if (!isset($_SESSION['name'])) {
    echo "<script>alert('Lütfen kayıt yapınız.'); window.location.href='kayit.php';</script>";
    exit;
}

$result = $baglanti->query("SELECT * FROM duyurular");

$soru = "SELECT * FROM yakinlar ORDER BY tarih ASC, saat ASC";
$resultante = $baglanti->query($soru);

$favorette = $baglanti->query("SELECT *, (kontenjan_baslangic - kontenjan) AS satilan FROM yakinlar ORDER BY satilan DESC LIMIT 4;");



$city = "Istanbul";
$apiKey = "314dd15f37ff532aff4116b86d844e2f";
$apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=Istanbul&appid=$apiKey&units=metric&lang=tr";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "cURL Hatası: " . curl_error($ch);
    curl_close($ch);
    exit;
}
curl_close($ch);
$data = json_decode($response, true);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deneme</title>
    <link rel="stylesheet" href="style-page.css">
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

    <div class="ana-baslik">
        <h1>Feel the Rhythm of Your heart's Music</h1>
    </div>

    <div class="resimler">
        <img src="resim8.jpg">
        <img src="resim2.jpg">
        <img src="resim3.jpg">
        <img src="resim4.jpg">
        <img src="resim5.jpg">
        <img src="resim6.jpg">
        <img src="resim7.jpg">
    </div>    


    <h2>Duyurular</h2>

    <div class="duyuru-wrapper">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="duyuru">
            <h3><?= htmlspecialchars($row['baslik']) ?></h3>
            <p><?= nl2br(htmlspecialchars($row['icerik'])) ?></p>
            <small>Yayın Tarihi: <?= $row['tarih'] ?></small>
        </div>
    <?php endwhile; ?>
    </div>

    <div class="fav"><h1><i class='bx bx-heart'></i> En Sevilenler</h1></div>

    <div class="enler">
    <?php while($etkinlik = $favorette->fetch_assoc()): ?>
        <div class="ensevilen">
            <div class="wrapper">
                <div class="card front-face">
                    <img src="uploads/<?php echo htmlspecialchars($etkinlik['afis']); ?>">
                </div>
                <div class="card back-face">
                    <img src="uploads/<?php echo htmlspecialchars($etkinlik['afis']); ?>">
                    <div class="info">
                        <div class="title"><?php echo htmlspecialchars($etkinlik['baslik']); ?></div>
                        <p>Kalan Kontenjan : <?php echo nl2br(htmlspecialchars($etkinlik['kontenjan'])); ?></p>
                    </div>
                    <ul>
                        <a href="#"><i class='bx bxl-youtube'></i></a>
                        <a href="#"><i class='bx bxl-twitter'></i></a>
                        <a href="#"><i class='bx bxl-facebook'></i></a>
                        <a href="#"><i class='bx bxl-instagram'></i></a>
                    </ul>
                </div>
            </div>
            <div class="bilgi">
                <div class="tarih">
                    <h2><?php echo date('M', strtotime($etkinlik['tarih'])); ?></h2>
                    <h1><?php echo date('d', strtotime($etkinlik['tarih'])); ?></h1>
                </div>
                <h3><?php echo htmlspecialchars($etkinlik['baslik']); ?></h3>
                <h4><i class='bx bxs-location-plus'></i><?php echo htmlspecialchars($etkinlik['mekan']); ?></h4> 
                <h4><i class='bx bx-time'></i><?php echo htmlspecialchars($etkinlik['saat']); ?></h4>
                <a href="sepet.php?ekle=<?php echo $etkinlik['id']; ?>">
                    <button>Satın Al</button>
                </a>
            </div>
        </div>
    <?php endwhile; ?>
    </div>


    <div class="enalinan"><h1>Tüm Etkinlikler</h1></div>

    <div class="yakinlar">
        <?php while($yakinlar = $resultante->fetch_assoc()): ?>
                <div class="enyakin">
                    <div class="card front-face">
                        <img src="uploads/<?php echo htmlspecialchars($yakinlar['afis']); ?>">
                        <div class="card-info">
                            <h1><?php echo htmlspecialchars($yakinlar['baslik']); ?><br>
                                <?php echo date('d F l H:i', strtotime($yakinlar['tarih'] . ' ' . $yakinlar['saat'])); ?>
                            </h1>
                        </div>
                    </div>

                    <div class="card back-face">
                        <img src="uploads/<?php echo htmlspecialchars($yakinlar['afis']); ?>">
                        <div class="info">
                            <h4><i class='bx bxs-location-plus'></i><?php echo htmlspecialchars($yakinlar['mekan']); ?></h4> 
                            <h4><i class='bx bx-time'></i><?php echo date('d F l H:i', strtotime($yakinlar['tarih'] . ' ' . $yakinlar['saat'])); ?></h4>
                            <h4>Kontenjan :  <?php echo htmlspecialchars($yakinlar['kontenjan']); ?></h4>
                            <a href="sepet.php?ekle=<?php echo $yakinlar['id']; ?>">
                                <button><?php echo htmlspecialchars($yakinlar['fiyat']); ?>₺</button>
                            </a>
                        </div>
                    </div>
                </div>
        <?php endwhile; ?>
    </div>


    <div class="latest">
        <h1>Bizi Tercih Ettiğiniz için Teşekkürler</h1>
    </div>


    <div class="hava-durumu">
        <?php if (isset($data['main']) && isset($data['weather'][0])): ?>
            <h2><?= $data['name'] ?> Hava Durumu</h2>
            <p><strong>Sıcaklık:</strong> <?= $data['main']['temp'] ?> °C</p>
            <p><strong>Hissedilen:</strong> <?= $data['main']['feels_like'] ?> °C</p>
            <p><strong>Durum:</strong> <?= $data['weather'][0]['description'] ?></p>
        <?php else: ?>
            <p>Hava durumu verisi alınamadı. Hata: <?= $data['message'] ?? 'Bilinmeyen hata' ?></p>
        <?php endif; ?>
    </div>




</body>
</html>