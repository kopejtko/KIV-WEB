<?php

global $tplData;

// pripojim objekt pro vypis hlavicky a paticky HTML
require(DIRECTORY_VIEWS ."/BasicHTML.class.php");
$tplHeaders = new BasicHTML();

require_once(DIRECTORY_MODELS."/DatabaseModel.class.php");
$myDB = new DatabaseModel();

$tplHeaders->createHeader();
if($tplData['right'] == 1){

    $tplHeaders->createMenuAdmin();

} else if($tplData['right'] == 2) {
    $tplHeaders->createMenuAuthor();
} else if($tplData['right'] == 3) {
    $tplHeaders->createMenuReviewer();
} else {
    $tplHeaders->createMenuBasic();
}
?>
<div class="Content">
<?php
// pokud je uzivatel prihlasen, tak ziskam jeho data
if($myDB->isUserLogged()){
    // ziskam data prihlasenoho uzivatele
    $userData = $myDB->getLoggedUserData();
}

///////////// PRO NEPRIHLASENE UZIVATELE ///////////////
if(!$myDB->isUserLogged()){
    ?>
    <div>
        <b>Tato strána je dostupná pouze přihlášeným uživatelům.</b>
    </div>
    <?php
    ///////////// KONEC: PRO NEPRIHLASENE UZIVATELE ///////////////
} else if($userData['id_pravo'] > 2) {
    ///////////// PRO PRIHLASENE UZIVATELE BEZ PRAVA ADMIN ///////////////
    ?>
    <div>
        <b>Správu uživatelů mohou provádět pouze uživatelé s právem Administrátor.</b>
    </div>
    <?php
    ///////////// KONEC: PRO PRIHLASENE UZIVATELE BEZ PRAVA ADMIN ///////////////
} else {
    ///////////// PRO PRIHLASENE UZIVATELE S PRAVEM ADMIN ///////////////

    // zpracovani odeslanych formularu
    if(isset($_POST['id_uzivatel'])){
        // smazu daneho uzivatele z databaze
        $res = $myDB->deleteFromTable(TABLE_UZIVATEL, "id_uzivatel='$_POST[id_uzivatel]'");
        // vysledek mazani
        if($res){
            echo "OK: Uživatel byl smazán z databáze.";
        } else {
            echo "ERROR: Smazání uživatele se nezdařilo.";
        }
    }

    // zpracovani odeslanych formularu
    if(isset($_POST['publikovat1'])){
        // smazu daneho uzivatele z databaze
        $res = $myDB->publishTopic("$_POST[publikovat1]");
        // vysledek mazani
        if($res){
            echo "OK: Příspěvek byl publikován.";
        } else {
            echo "ERROR: Publikování příspěvku se nezdařilo.";
        }
    }

    if(isset($_POST['smazat1'])){
        // smazu daneho uzivatele z databaze
        $res = $myDB->deleteTopic("$_POST[smazat1]");

        // vysledek mazani
        if($res){
            echo "OK: Příspěvek byl smazán.";
        } else {
            echo "ERROR: Smazání příspěvku se nezdařilo.";
        }
    }

    if(isset($_POST['smazat2'])){
        // smazu daneho uzivatele z databaze
        $res = $myDB->deleteReview("$_POST[smazat2]");

        // vysledek mazani
        if($res){
            echo "OK: Příspěvek byl smazán.";
        } else {
            echo "ERROR: Smazání příspěvku se nezdařilo.";
        }
    }
    $idprispevku = '';
    if(isset($_POST['pridelit2'])){
        // smazu daneho uzivatele z databaze
        $qty = $_POST['pridelit2'];
        $topic = $qty[0];
        $user = $qty[1];
        $res = $myDB->assignReview($topic, $user);

        // vysledek mazani
        if($res){
            echo "OK: Recenze byla přidána.";
        } else {
            echo "ERROR: Přidání recenze se nezdařilo.";
        }
    }
    if(isset($_POST['blokace'])){
        // smazu daneho uzivatele z databaze
        $qty = $_POST['blokace'];
        $res = $myDB->blockUser($_POST['blokace']);

        // vysledek mazani
        if($res){
            echo "Uživatel zablokovan.";
        } else {
            echo "ERROR: Zablokování uživatele se nezdařilo.";
        }
    }

    if(isset($_POST['odblokace'])){
        // smazu daneho uzivatele z databaze
        $qty = $_POST['odblokace'];
        $res = $myDB->unblockUser($_POST['odblokace']);

        // vysledek mazani
        if($res){
            echo "OK: Uživatel odblokovan.";
        } else {
            echo "ERROR: Odblokování uživatele se nezdařilo.";
        }
    }

if(isset($_POST['zmenitroli'])) {


$res = $myDB->changeUserRole($_POST['zmenitroli'], $_POST['id_pravo']);

    if($res){
        echo "OK: Role byla změněna.";
    } else {
        echo "ERROR: Změna role se nezdařila.";
    }


}

else {
    // ziskam data vsech uzivatelu
    $users = $myDB->getAllUsers();
    ?>
    <h2>Seznam uživatelů</h2>
    <table border="1">
        <tr><th>ID</th><th>Login</th><th>Jméno</th><th>E-mail</th><th>Právo</th><th>Zablokovan</th><th>Smazat</th><th>Akce</th><th>Akce</th></tr>
        <?php
        // projdu uzivatele a vypisu je
        foreach ($users as $u) {
            $nazevpravo = $u['id_pravo'] == 2 ? 'autor' : ($u['id_pravo'] == 3 ? 'recenzent' : 'admin');
            echo "<tr><td>$u[id_uzivatel]</td><td>$u[login]</td><td>$u[jmeno]</td><td>$u[email]</td><td>$nazevpravo</td><td>$u[zablokovan]</td><td>"
                ."<form action='' method='POST'>
                              <input type='hidden' name='id_uzivatel' value='$u[id_uzivatel]'>
                              <input type='submit' name='potvrzeni' value='Smazat'>";
            if($u['zablokovan'] == 'NE') {
                echo "</form></td><td>" . "<form action='' method='POST'>
                              <input type='hidden' name='blokace' value='$u[id_uzivatel]'>
                              <input type='submit' name='blokovat' value='Zablokovat'>
                          </form>";
            } else {
                echo "</form></td><td>" . "<form action='' method='POST'>
                              <input type='hidden' name='odblokace' value='$u[id_uzivatel]'>
                              <input type='submit' name='blokovat' value='Odblokovat'>
                          </form></td>";
            }

            echo "<td><form action='' method='POST'>
                              
                              
                          

 <select name='id_pravo'>
                    <option value='3'>Recenzent</option>
                    <option value='2'>Autor</option>
                    <option value='1'>Admin</option>
                </select>
                <input type='hidden' name='zmenitroli' value='$u[id_uzivatel]'>
                <input type='submit' name='zmenit' value='Zmenit roli'></form></td>";

                echo "</td></tr>";
        }
        ?>

    </table>
    <br>
    <?php
    /* // akce by mela obsahovat formular s tlacitkem:
        <form action='' method='POST'>
            <input type='hidden' name='id_uzivatel' value='....'>
            <input type='submit' name='potvrzeni' value='Smazat'>
        </form>
    */
    ///////////// KONEC: PRO PRIHLASENE UZIVATELE S PRAVEM ADMIN ///////////////
}

$con = mysqli_connect('localhost', 'root', '');
mysqli_select_db($con, 'mydb2');
mysqli_set_charset($con, "utf8");
$sql1 = "SELECT * FROM prispevek";

$result = mysqli_query($con, $sql1);


if(isset($_GET["id"])) {
// $sql2 = "SELECT * FROM recenze WHERE id_prispevek=" . $_GET["id"];
$sql2 = "SELECT * FROM uzivatel WHERE id_pravo=3";
$result2 = mysqli_query($con, $sql2);


?>

<h2>Seznam příspěvků k publikování</h2>
<table border="1">
    <tr><th>Recenzent</th>
        <th>Orig.</th>
        <th>Tema</th>
        <th>Tech</th>
        <th>Jaz</th>
        <th>Dop</th>
        <th>Celk</th>
        <th>Akce</th>
    </tr>

    <?php
    while ($row = mysqli_fetch_array($result2)) {
        // $sql2 = "SELECT jmeno FROM uzivatel WHERE id_uzivatel=" . $row['id_uzivatel'] . " LIMIT 1";
        $sql2 = "SELECT * FROM recenze WHERE id_prispevek=" . $_GET['id'] . " AND id_uzivatel=" . $row['id_uzivatel'] . " LIMIT 1";
        $idprispevku = $_GET['id'];
        $result4 = mysqli_query($con, $sql2);
        if($result4) {$radka = mysqli_fetch_assoc($result4);}
        //  $jmeno = $radka['jmeno'];
        $jmeno = $row['jmeno'];
        $iduzivatele = $row['id_uzivatel'];
        if($radka) {

            echo '<tr><td>' . $jmeno . '</td><td>' . $radka['originalita'] . '</td><td>' . $radka['tema'] . '</td><td>'  . $radka['tech'] . '</td><td>'  . $radka['jazyk'] . '</td><td>'  . $radka['doporuceni'] . '</td><td>'  .  $radka['celkem'] .'</td><td>' . "<form action='' method='POST'>
                              <input type='hidden' name='smazat2' value='$radka[id_recenze]'>
                              <input type='submit' name='smazat' value='Smazat'>
                          </form>" . '</td>' . '</tr>';

        } else echo '<tr><td>' . $jmeno . '</td><td>' . "N/A" . '</td><td>' . 'N/A' . '</td><td>'  . 'N/A' . '</td><td>'  . 'N/A' . '</td><td>'  . 'N/A' . '</td><td>'  .  'N/A' .'</td><td>' . "<form action='' method='POST'>
                              <input type='hidden' name='pridelit2[]' value='$idprispevku'>
                              <input type='hidden' name='pridelit2[]' value='$iduzivatele'>
                              <input type='submit' name='pridelit' value='Pridelit'>
                          </form>" . '</td>' . '</tr>';

    }
    }
    else {
    ?>
    <h2>Seznam příspěvků k publikování</h2><table border="1"> <tr><th>Titulek</th><th>Autor</th><th>Datum vytvoření</th><th>Hodnoceni prumer</th><th>Publikovano</th><th>Publikovat</th><th>Smazat</th><th>Spravovat recenze</th></tr>
        <?php
        // projdu uzivatele a vypisu je
        while ($row = mysqli_fetch_array($result)) {
            $sql = "SELECT jmeno FROM uzivatel WHERE id_uzivatel=" . $row['id_uzivatel'];
            $result2 = mysqli_query($con, $sql);
            $row2 = mysqli_fetch_array($result2, MYSQLI_NUM);
            $jmeno = $row2[0];

            $autor = $jmeno;
            $sql2 = "SELECT * FROM recenze WHERE id_prispevek=" . $row['id_prispevek'];
            $result3 = mysqli_query($con, $sql2);
            $row3 = mysqli_fetch_array($result3);

            $avgsql = "SELECT AVG(celkem) AS avgCol FROM recenze WHERE id_prispevek=". $row['id_prispevek'];
            $result4 = mysqli_query($con, $avgsql);
            $row10 = mysqli_fetch_assoc($result4);
            // $row4 = mysqli_fetch($result4);


            echo '<tr><td>' . $row['titulek'] . '</td><td>' . $autor . '</td><td>' . $row['datum'] . '</td><td>' . $row10['avgCol'] .'</td><td>' . $row['publikovano'] . '</td>' .
                '<td>' . "<form action='' method='POST' >
                              <input type='hidden' name='publikovat1' value='$row[id_prispevek]'>
                              <input type='submit' name='publikovat' value='Publikovat'>
                          </form>" . '</td><td>'
                . "<form action='' method='POST'>
                              <input type='hidden' name='smazat1' value='$row[id_prispevek]'>
                              <input type='submit' name='smazat' value='Smazat'>
                          </form>" . '</td><td>' . '<a href="index.php?page=management&id=' . $row['id_prispevek'] . '"' . '>Odkaz</a>' .'</td></tr>';
        }
        }
        ?>
    </table>
</div>






<?php

$tplHeaders->createFooter();
}
?>

