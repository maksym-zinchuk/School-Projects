<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>KOŁO SZACHOWE</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php
    $polaczenie = mysqli_connect('sql309.infinityfree.com', 'if0_42821616', 'JaeqiJqzBEW', 'if0_42821616_inf_03_2025_01_10_SG');
    ?>
    <header>
        <h2>Koło szachowe <em>gambit piona</em></h2>
    </header>
    <section id="lewy">
        <h4>Polecane linki</h4>
        <ul>
            <li><a href="kw1.png">kwerenda1</a></li>
            <li><a href="kw2.png">kwerenda2</a></li>
            <li><a href="kw3.png">kwerenda3</a></li>
            <li><a href="kw4.png">kwerenda4</a></li>
        </ul>
        <img src="logo.png" alt="Logo koła">
    </section>
    <section id="prawy">
        <h3>Najlepsi gracze naszego koła</h3>
        <table>
            <tr>
                <th>Pozycja</th>
                <th>Pseudonim</th>
                <th>Tytuł</th>
                <th>Ranking</th>
                <th>Klasa</th>
            </tr>
            <?php
            // Skrypt 1
            $zapytanie = "SELECT pseudonim, tytul, ranking, klasa FROM zawodnicy WHERE ranking > 2787 ORDER BY ranking DESC;";
            $wynik = mysqli_query($polaczenie, $zapytanie);
            $pozycja = 1;
            while ($wiersz = mysqli_fetch_row($wynik)) {
                echo "<tr><td>$pozycja</td><td>$wiersz[0]</td><td>$wiersz[1]</td><td>$wiersz[2]</td><td>$wiersz[3]</td></tr>";
                $pozycja++;
            }
            ?>
        </table>
        <form action="szachy.php" method="post">
            <button type="submit" name="losuj">Losuj nową parę graczy</button>
        </form>
        <?php
        // Skrypt 2
        if (isset($_POST['losuj'])) {
            $zapytanie = "SELECT pseudonim, klasa FROM zawodnicy ORDER BY RAND() LIMIT 2;";
            $wynik = mysqli_query($polaczenie, $zapytanie);
            $gracze = [];
            while ($wiersz = mysqli_fetch_row($wynik)) {
                $gracze[] = "$wiersz[0] $wiersz[1]";
            }
            echo "<h4>" . implode(" ", $gracze) . "</h4>";
        }
        mysqli_close($polaczenie);
        ?>
        <p>Legenda: AM - Absolutny Mistrz, SM - Szkolny Mistrz, PM - Mistrz Poziomu, KM - Mistrz Klasowy</p>
    </section>
    <footer>
        <p>Stronę wykonał: 00000000000</p>
    </footer>
</body>
</html>
