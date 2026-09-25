<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>ZDOBYWCY GÓR</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Klub zdobywców gór polskich</h1>
    </header>
    <nav>
        <a href="kw1.png">kwerenda1</a>
        <a href="kw2.png">kwerenda2</a>
        <a href="kw3.png">kwerenda3</a>
        <a href="kw4.png">kwerenda4</a>
    </nav>
    <section id="lewy">
        <img src="logo.png" alt="logo zdobywcy">
        <h3>razem z nami:</h3>
        <ul>
            <li>wyjazdy</li>
            <li>szkolenia</li>
            <li>rekreacja</li>
            <li>wypoczynek</li>
            <li>wyzwania</li>
        </ul>
    </section>
    <section id="prawy">
        <h2>Dołącz do naszego zespołu!</h2>
        <p>Wpisz swoje dane do formularza:</p>
        <form action="zdobywcy.php" method="post">
            <label for="nazwisko">Nazwisko: </label>
            <input type="text" name="nazwisko" id="nazwisko">
            <label for="imie">Imię: </label>
            <input type="text" name="imie" id="imie">
            <label for="funkcja">Funkcja: </label>
            <select name="funkcja" id="funkcja">
                <option>uczestnik</option>
                <option>przewodnik</option>
                <option>zaopatrzeniowiec</option>
                <option>organizator</option>
                <option>ratownik</option>
            </select>
            <label for="email">Email: </label>
            <input type="email" name="email" id="email">
            <button type="submit">Dodaj</button>
        </form>
        <?php
        $polaczenie = mysqli_connect('sql309.infinityfree.com', 'if0_42821616', 'JaeqiJqzBEW', 'if0_42821616_inf_03_2025_01_09_SG');

        // Skrypt 2 (wykonywany przed skryptem 1, aby nowa osoba od razu pojawiła się w tabeli)
        if (!empty($_POST['nazwisko']) && !empty($_POST['imie']) && !empty($_POST['funkcja']) && !empty($_POST['email'])) {
            $nazwisko = $_POST['nazwisko'];
            $imie = $_POST['imie'];
            $funkcja = $_POST['funkcja'];
            $email = $_POST['email'];
            $zapytanie = "INSERT INTO osoby (nazwisko, imie, funkcja, email) VALUES ('$nazwisko', '$imie', '$funkcja', '$email');";
            mysqli_query($polaczenie, $zapytanie);
        }
        ?>
        <table>
            <tr>
                <th>Nazwisko</th>
                <th>Imię</th>
                <th>Funkcja</th>
                <th>Email</th>
            </tr>
            <?php
            // Skrypt 1
            $zapytanie = "SELECT nazwisko, imie, funkcja, email FROM osoby;";
            $wynik = mysqli_query($polaczenie, $zapytanie);
            while ($wiersz = mysqli_fetch_row($wynik)) {
                echo "<tr><td>$wiersz[0]</td><td>$wiersz[1]</td><td>$wiersz[2]</td><td>$wiersz[3]</td></tr>";
            }
            mysqli_close($polaczenie);
            ?>
        </table>
    </section>
    <footer>
        <p>Stronę wykonał: 00000000000</p>
    </footer>
</body>
</html>
