<?php

session_start();

include("baglanti.php");


if (!isset($_SESSION['name'])) {
    echo "<script>alert('Lütfen kayıt yapınız.'); window.location.href='kayit.php';</script>";
    exit;
}


$soru = "SELECT * FROM yakinlar WHERE sayfa = 'festival' ORDER BY tarih DESC";
$resultante = $baglanti->query($soru);

$city = "Istanbul";
$apiKey = "314dd15f37ff532aff4116b86d844e2f"; // Buraya kendi API key'ini yaz
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
    <link rel="stylesheet" href="style-festival.css">
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



    <div class="enalinan"><h1><i class="fa-brands fa-bluesky"></i> Festivaller</h1></div>

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
                            <h4><i class='bx bx-time'></i><?php echo htmlspecialchars($yakinlar['saat']); ?></h4>
                            <h4>Kontenjan : <?php echo htmlspecialchars($yakinlar['kontenjan']); ?></h4>
                            <button><?php echo htmlspecialchars($yakinlar['fiyat']); ?>₺</button>
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