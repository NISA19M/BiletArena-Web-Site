<?php
include("baglanti.php");
session_start();




function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$emailErr = $parolaErr = "";
$email = $parola = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $hasError = false;

    if (empty($_POST["email"])) {
        $emailErr = "Email alanı boş bırakılamaz.";
        $hasError = true;
    } 
    else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Geçersiz email formatı.";
            $hasError = true;
        }
    }

    if (empty($_POST["parola"])) {
        $parolaErr = "Parola alanı boş bırakılamaz.";
        $hasError = true;
    }
    else{
        $parola = test_input($_POST["parola"]);
    }



    if (!$hasError) {

        if ($email == $user && $parola == $pass) {
            $_SESSION["login"] = true;
            header("Location: admin.php");
            }
        else {

            $secim="SELECT * FROM uyelik WHERE uye_email = '$email'";
            $calistir=mysqli_query($baglanti,$secim);

            if(mysqli_num_rows($calistir) > 0){

                $ilgilikayit = mysqli_fetch_assoc($calistir);
                $hashlisifre=$ilgilikayit["uye_parola"];

                if(password_verify($parola,$hashlisifre)){
                    $_SESSION["name"]=$ilgilikayit["uye_adi"];
                    $_SESSION["email"]=$ilgilikayit["uye_email"];
                    header('Location: page.php');
                    exit;
                }
                else{
                    $parolaErr = "Geçersiz parola.";

                }
            }
            else{
                $emailErr = "Geçersiz email.";
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
    <title>Giriş Ekranı</title>
    <link rel="stylesheet" href="style-giris.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>


<div class="loginparttwo">
    <form action="login.php" method="POST">
        <h2>Giriş Ekranı</h2>
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
        <button type="submit" name="giris">Giriş</button>
        <h3><a href="/webfolder/kayit.php"> Eğer hesabınız yoksa kayıt olmak için tıklayınız </a></h3>
    </form>
</div>


<video autoplay loop muted>
    <source src="video1.mp4" type="video/mp4">
</video>


</body>
<script url ='app.js'></script>


</html>