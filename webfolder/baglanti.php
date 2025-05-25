<?php

$host="localhost";
$kullanici="root";
$parola="";
$vt="uyeler";

$user= "admin@gmail.com";
$pass="123456";

$baglanti = mysqli_connect($host, $kullanici, $parola, $vt);
mysqli_set_charset($baglanti, "UTF8");

?>