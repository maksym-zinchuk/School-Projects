<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>PIEKARNIA</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <img src="wypieki.png" alt="Produkty naszej piekarni">
    <nav>
        <a href="kw1.png">KWERENDA1</a>
        <a href="kw2.png">KWERENDA2</a>
        <a href="kw3.png">KWERENDA3</a>
        <a href="kw4.png">KWERENDA4</a>
    </nav>
    <header>
        <h1>WITAMY</h1>
        <h4>NA STRONIE PIEKARNI</h4>
        <p>Od 31 lat oferujemy najwyższej jakości pieczywo. Naturalnie świeże, naturalnie smaczne. Pieczemy wyłącznie wypieki na naturalnym zakwasie bez polepszaczy i zagęstników. Korzystamy wyłącznie z najlepszych ziaren pochodzących z ekologicznych upraw położonych w rejonach zgierskim i ozorkowskim.</p>
    </header>
    <main>
        <h4>Wybierz rodzaj wypieków:</h4>
        <?php
        $polaczenie = mysqli_connect('sql309.infinityfree.com', 'if0_42821616', 'JaeqiJqzBEW', 'if0_42821616_inf_03_2025_01_12_SG');
        ?>
        <form action="piekarnia.php" method="post">
            <select name="rodzaj">
                <?php
                // Skrypt 1
                $zapytanie = "SELECT DISTINCT Rodzaj FROM wyroby ORDER BY Rodzaj DESC;";
                $wynik = mysqli_query($polaczenie, $zapytanie);
                while ($wiersz = mysqli_fetch_row($wynik)) {
                    echo "<option>$wiersz[0]</option>";
                }
                ?>
            </select>
            <button type="submit">Wybierz</button>
        </form>
        <table>
            <tr>
                <th>Rodzaj</th>
                <th>Nazwa</th>
                <th>Gramatura</th>
                <th>Cena</th>
            </tr>
            <?php
            // Skrypt 2
            if (isset($_POST['rodzaj'])) {
                $rodzaj = $_POST['rodzaj'];
                $zapytanie = "SELECT Rodzaj, Nazwa, Gramatura, Cena FROM wyroby WHERE Rodzaj = '$rodzaj';";
                $wynik = mysqli_query($polaczenie, $zapytanie);
                while ($wiersz = mysqli_fetch_row($wynik)) {
                    echo "<tr><td>$wiersz[0]</td><td>$wiersz[1]</td><td>$wiersz[2]</td><td>$wiersz[3]</td></tr>";
                }
            }
            mysqli_close($polaczenie);
            ?>
        </table>
    </main>
    <footer>
        <p>AUTOR: 00000000000</p>
        <p>Data: 22.09.2026</p>
    </footer>
</body>
</html>
