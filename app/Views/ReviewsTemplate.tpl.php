<?php
$con = mysqli_connect('localhost', 'root', '');
mysqli_select_db($con, 'mydb2');

global $tplData;
global $resultg1;
global $resultg2;
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

// pokud je uzivatel prihlasen, tak ziskam jeho data
if($myDB->isUserLogged()){
    // ziskam data prihlasenoho uzivatele
    $userData = $myDB->getLoggedUserData();
    $userId = $myDB->getUserId();
}
?>
<div class="Content">
    <?php
    // pokud je uzivatel prihlasen, tak ziskam jeho data
    if($myDB->isUserLogged()){
        // ziskam data prihlasenoho uzivatele
        $userData = $myDB->getLoggedUserData();
        $userId = $myDB->getUserId();
    }

    // zpracovani odeslanych formularu
    if(isset($_POST['review'])){
        // smazu daneho uzivatele z databaze
        $res = $myDB->changeReview("$_POST[originalita]", "$_POST[tema]", "$_POST[tech]", "$_POST[jaz]", "$_POST[doporuceni]", "$userId", "$_POST[review]");
        // vysledek mazani
        if($res){
            echo "OK: Příspěvek byl ohodnocen.";
        } else {
            echo "ERROR:Ohodnocení příspěvku se nezdařilo.";
        }
    }



    if(isset($_GET["id"])) {
        $prispevekId = $_GET["id"]; // nastavim pozadovane
        $sql = "SELECT * FROM recenze WHERE id_uzivatel=" . $userId . " AND id_prispevek =" . $prispevekId;
        $result = mysqli_query($con, $sql);
        $sql2 = "SELECT * FROM prispevek WHERE id_prispevek=" . $prispevekId;
        $result2 = mysqli_query($con, $sql2);
        $titulek = '';
        while($row2 = mysqli_fetch_array($result2)){
            $titulek = $row2['titulek'];
        }
        while ($row = mysqli_fetch_array($result)) {
            echo '<h1>' . $titulek . '</h1>' . '<br>';
            echo "<h2>Editace posudku</h2>
        <a href=" . DIRECTORY_DATA . "/" . $prispevekId . ">Stáhnout příspěvek</a>
<form action='' method='POST'>
    <table>
        <tr><td>Originalita:</td><td><select name='originalita'>
                    <option value='5'>Neoriginální</option>
                    <option value='4'>Spíše neoriginální</option>
                    <option value='3'>Nerozhodné</option>
                    <option value='2'>Spíše originální</option>
                    <option value='1'>Originální</option>
                </select></td></tr>
        <tr><td>Téma:</td><td><select name='tema'>
                    <option value='5'>Nezajímavé</option>
                    <option value='4'>Spíše nezajímavé</option>
                    <option value='3'>Nerozhodné</option>
                    <option value='2'>Spíše zajímavé</option>
                    <option value='1'>Zajímavé</option>
                </select></td></tr>
        <tr><td>Technická kvalita:</td><td><select name='tech'>
                    <option value='5'>Nekvalitní</option>
                    <option value='4'>Spíše nekvalitní</option>
                    <option value='3'>Nerozhodné</option>
                    <option value='2'>Spíše kvalitní</option>
                    <option value='1'>Kvalitní</option>
                </select></td></tr>
        <tr><td>Jazyková kvalita:</td><td><select name='jaz'>
                    <option value='5'>Nekvalitní</option>
                    <option value='4'>Spíše nekvalitní</option>
                    <option value='3'>Nerozhodné</option>
                    <option value='2'>Spíše kvalitní</option>
                    <option value='1'>Kvalitní</option>
                </select></td></tr>
        <tr><td>Doporučení:</td>
            <td>
                <select name='doporuceni'>
                     <option value='5'>Nepřijmout</option>
                    <option value='4'>Spíše nepřijmout</option>
                    <option value='3'>Nerozhodné</option>
                    <option value='2'>Spíše prijmout</option>
                    <option value='1'>Přijmout</option>
                </select>
            </td>
        </tr>
    </table>
    
    <input type='hidden' name='review' value='$row[id_recenze]'>
    <input type='submit' name='potvrzeni' value='Potvrdit'>
</form>";
            //       echo '<a href="index.php?page=prispevky&id=' . $row['id_prispevek'] . '"' . '>Odkaz</a><br>';
            echo '<a href="index.php?page=reviews">' . 'Zpět na seznam hodnoceni' . '</a>';
        }
    } else {








        $sql = "SELECT * FROM recenze WHERE id_uzivatel=" . $userId;
        $result = mysqli_query($con, $sql);
        while ($row = mysqli_fetch_array($result)) {
            $sql2 = "SELECT * FROM prispevek WHERE id_prispevek=" . $row['id_prispevek'];
            $result2 = mysqli_query($con, $sql2);
            while ($row2 = mysqli_fetch_array($result2)) {

                echo '<h1>' . $row2['titulek'] . ', ';
                echo $row2['datum'] . '</h1>';
                echo '<table><th>originalita</th><th>tema</th><th>technika</th><th>jazyk</th><th>doporuceni</th><th>celkem</th><th>publikovano</th>';
                echo '<tr><td>' . $row['originalita'] . '</td><td>' . $row['tema'] . '</td><td>'  . $row['tech'] . '</td><td>'  . $row['jazyk'] . '</td><td>'  . $row['doporuceni'] . '</td><td>'  .  $row['celkem'] . '</td><td>' . $row2['publikovano'] . '</td></tr></table>';
                if($row2['publikovano'] == 'NE') {
                    echo '<a href="index.php?page=reviews&id=' . $row2['id_prispevek'] . '">' . 'Otevřít hodnocení' . '</a>';
                }
            }
        }
    }











    ?>
</div>
<?php
$tplHeaders->createFooter();
?>
