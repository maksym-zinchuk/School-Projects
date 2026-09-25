<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>WOLONTARIAT SZKOLNY</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>KONKURS - WOLONTARIAT SZKOLNY</h1>
    </header>
    <section id="lewy">
        <h3>Konkursowe nagrody</h3>
        <button onclick="location.reload()">Losuj nowe nagrody</button>
        <table>
            <tr>
                <th>Nr</th>
                <th>Nazwa</th>
                <th>Opis</th>
                <th>Wartość</th>
            </tr>
            <?php
            $polaczenie = mysqli_connect('sql309.infinityfree.com', 'if0_42821616', 'JaeqiJqzBEW', 'if0_42821616_inf_03_2025_01_11_SG');
            $zapytanie = "SELECT nazwa, opis, cena FROM nagrody ORDER BY RAND() LIMIT 5;";
            $wynik = mysqli_query($polaczenie, $zapytanie);
            $numer = 1;
            while ($wiersz = mysqli_fetch_row($wynik)) {
                echo "<tr><td>$numer</td><td>$wiersz[0]</td><td>$wiersz[1]</td><td>$wiersz[2]</td></tr>";
                $numer++;
            }
            mysqli_close($polaczenie);
            ?>
        </table>
    </section>
    <section id="prawy">
        <img src="puchar.png" alt="Puchar dla wolontariusza">
        <h4>Polecane linki</h4>
        <ul>
            <li><a href="kw1.png">Kwerenda1</a></li>
            <li><a href="kw2.png">Kwerenda2</a></li>
            <li><a href="kw3.png">Kwerenda3</a></li>
            <li><a href="kw4.png">Kwerenda4</a></li>
        </ul>
    </section>
    <footer>
        <p>Numer zdającego: 00000000000</p>
    </footer>
</body>
</html>
