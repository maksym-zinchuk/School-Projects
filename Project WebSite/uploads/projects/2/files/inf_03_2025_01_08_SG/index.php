<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Mieszalnia farb</title>
    <link rel="icon" href="fav.png" type="image/png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <img src="baner.png" alt="Mieszalnia farb">
    </header>
    <section id="formularz">
        <form action="index.php" method="post">
            <label for="od">Data odbioru od: </label>
            <input type="date" name="od" id="od">
            <label for="do">do: </label>
            <input type="date" name="do" id="do">
            <button type="submit">Wyszukaj</button>
        </form>
    </section>
    <main>
        <table>
            <tr>
                <th>Nr zamówienia</th>
                <th>Nazwisko</th>
                <th>Imię</th>
                <th>Kolor</th>
                <th>Pojemność [ml]</th>
                <th>Data odbioru</th>
            </tr>
            <?php
            $polaczenie = mysqli_connect('sql309.infinityfree.com', 'if0_42821616', 'JaeqiJqzBEW', 'if0_42821616_inf_03_2025_01_08_SG');
            if (!empty($_POST['od']) && !empty($_POST['do'])) {
                $dataOd = $_POST['od'];
                $dataDo = $_POST['do'];
                $zapytanie = "SELECT klienci.Nazwisko, klienci.Imie, zamowienia.id, zamowienia.kod_koloru, zamowienia.pojemnosc, zamowienia.data_odbioru FROM klienci JOIN zamowienia ON klienci.Id = zamowienia.id_klienta WHERE zamowienia.data_odbioru BETWEEN '$dataOd' AND '$dataDo' ORDER BY zamowienia.data_odbioru ASC;";
            } else {
                $zapytanie = "SELECT klienci.Nazwisko, klienci.Imie, zamowienia.id, zamowienia.kod_koloru, zamowienia.pojemnosc, zamowienia.data_odbioru FROM klienci JOIN zamowienia ON klienci.Id = zamowienia.id_klienta ORDER BY zamowienia.data_odbioru ASC;";
            }
            $wynik = mysqli_query($polaczenie, $zapytanie);
            while ($wiersz = mysqli_fetch_row($wynik)) {
                echo "<tr>";
                echo "<td>$wiersz[2]</td>";
                echo "<td>$wiersz[0]</td>";
                echo "<td>$wiersz[1]</td>";
                echo "<td style=\"background-color: #$wiersz[3];\">$wiersz[3]</td>";
                echo "<td>$wiersz[4]</td>";
                echo "<td>$wiersz[5]</td>";
                echo "</tr>";
            }
            mysqli_close($polaczenie);
            ?>
        </table>
    </main>
    <footer>
        <h3>Egzamin INF.03</h3>
        <p>Autor: 00000000000</p>
    </footer>
</body>
</html>
