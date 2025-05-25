<?php
include("baglanti.php");

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$nameErr = $emailErr = $parolaErr = "";
$name = $email = $parola ="";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["kaydet"])) {

    $hasError = false;

    if (empty($_POST["name"])) {
        $nameErr = "Kullanıcı adı boş bırakılamaz.";
        $hasError = true;
    } else {
        $name = test_input($_POST["name"]);
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email alanı boş bırakılamaz.";
        $hasError = true;
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Geçersiz email formatı.";
            $hasError = true;
        }
    }

    if (empty($_POST["parola"])) {
        $parolaErr = "parola alanı boş bırakılamaz.";
        $hasError = true;
    } else {
        $parola = test_input($_POST["parola"]);
    }

    if (!$hasError) {
        // Email zaten kayıtlı mı kontrol et
        $kontrolSorgu = "SELECT * FROM uyelik WHERE uye_email = '$email'";
        $sonuc = mysqli_query($baglanti, $kontrolSorgu);

        if (mysqli_num_rows($sonuc) > 0) {
            $emailErr = "Bu email zaten kayıtlı.";
            $hasError = true;
        } 
        else {


          $kontrol = "SELECT * FROM uyelik WHERE uye_adi = '$name'";
          $son = mysqli_query($baglanti, $kontrol);
          if (mysqli_num_rows($son) > 0) {
            $nameErr = "Bu isimde bir kullanıcı zaten var.";
            $hasError = true;
          } else {
              // Kayıt işlemi
              $password = password_hash($_POST["parola"], PASSWORD_DEFAULT);
              $ekle = "INSERT INTO uyelik(uye_adi, uye_email, uye_parola) VALUES ('$name','$email','$password')";
              $calistirekle = mysqli_query($baglanti, $ekle);

              mysqli_close($baglanti);
              $kayit_basarili = true; 
              //header("Location:/webfolder/login.php");
          }

        }
    }
}
?>




<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ekranı</title>
    <link rel="stylesheet" href="style-kayit.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  </head>
  <body>
    


    <div class="loginpart">
      <form action="kayit.php" method="POST">
        <h2>Kayıt Ekranı</h2>
        <div class="input-box">          
          <input type="text" name="name" required>          
          <div class="label">Kullanıcı Adı</div>         
          <i class='bx bx-user-plus'></i>
          <span class="error">* <?php echo $nameErr;?></span>
        </div>
        <div class="input-box">
          <input type="email" name="email" required>
          <div class="label">Email</div>
          <i class='bx bxl-gmail'></i>
          <span class="error">* <?php echo $emailErr;?></span>
        </div> 
        <div class="input-box">
          <input type="password" name="parola" required>
          <div class="label">Şifre</div>
          <i class='bx bx-lock-alt'></i>
          <span class="error">* <?php echo $parolaErr;?></span>
        </div>
        <button type="submit" name="kaydet" >Kayıt Ol </button>
        <h3><a href="/webfolder/login.php"> Eğer hesabınız varsa giriş yapmak için tıklayınız </a></h3>
      </form>
    </div>

    <video autoplay loop muted>
      <source src="video1.mp4" type="video/mp4">
    </video>


    <?php if (isset($kayit_basarili) && $kayit_basarili): ?>
    <script>
        alert("Kayıt başarılı! Lütfen şifrenizi daha sonra değiştirin.");
        window.location.href = "/webfolder/login.php";
    </script>
    <?php endif; ?>


  </body>


</html>