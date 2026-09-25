<?php
$polaczenie = mysqli_connect(
    "sql309.infinityfree.com",
    "if0_42821616",
    "JaeqiJqzBEW",
    "if0_42821616_projects",
    3306
);

$data = $_POST["data"];
$osoby = $_POST["osoby"];
$telefon = $_POST["telefon"];

$sql = "INSERT INTO rezerwacje (data_rez, liczba_osob, telefon)
        VALUES ('$data', '$osoby', '$telefon')";

mysqli_query($polaczenie, $sql);

echo "Dodano rezerwację do bazy";

mysqli_close($polaczenie);
?>
