<?php
$con = mysqli_connect('localhost', 'root', '');
mysqli_select_db($con, 'mydb2');


global $tplData;
global $resultg;
global $page;
// pripojim objekt pro vypis hlavicky a paticky HTML
require(DIRECTORY_VIEWS ."/BasicHTML.class.php");
$tplHeaders = new BasicHTML();
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

require_once(DIRECTORY_MODELS."/DatabaseModel.class.php");
$myDB = new DatabaseModel();


?>
<div class="Content">
    <?php
    if($tplData['idk'] == 0) {

    // pokud je uzivatel prihlasen, tak ziskam jeho data
    if($myDB->isUserLogged()){
        // ziskam data prihlasenoho uzivatele
        $userData = $myDB->getLoggedUserData();
        $userId = $myDB->getUserId();
    }

         $prispevekId = $_GET["id"]; // nastavim pozadovane
        $sql2 = "SELECT * FROM prispevek WHERE id_prispevek=" . $prispevekId;
        $result2 = mysqli_query($con, $sql2);
        $titulek = '';
        while($row2 = mysqli_fetch_array($result2)){
            $titulek = $row2['titulek'];
            echo '<h1>' . $titulek . '</h1>' . '<br>' . '

            <h2>Upravit existující příspěvek</h2>
<form action="" method="POST" id="forminput" enctype="multipart/form-data">
    <table>
        <tr><td>Titulek:</td><td><input type="text" name="titulek" value=' . $row2['titulek'] . ' required></td></tr>
        <tr><td>Obsah:</td><td><textarea name="obsah" form="forminput"  cols="60" wrap="soft">' . $row2['obsah'] . '</textarea></td></tr>
        <tr><td>Datum:</td><td><input type="date" name="datum" value=' . $row2['datum'] . ' required</td></tr>
        <tr><td>Soubor:</td><td><input type="file" name="soubory[]" id="fileToUpload" multiple></td></tr>
    </table>
    <input type="submit" name="potvrzeni" value="Upravit">
</form>';
            //       echo '<a href="index.php?page=prispevky&id=' . $row['id_prispevek'] . '"' . '>Odkaz</a><br>';
            echo '<a href="index.php?page=my">' . 'Zpět na seznam hodnoceni' . '</a>';
        }


    } else if($tplData['idk'] == 1) {



        while ($row = mysqli_fetch_array($resultg)) {
            $avgsql = "SELECT AVG(celkem) AS avgCol FROM recenze WHERE id_prispevek=". $row['id_prispevek'];
            $result4 = mysqli_query($con, $avgsql);
            $row10 = mysqli_fetch_assoc($result4);

            echo '<h1>' . $row['titulek'] . ', ';
            echo $row['datum'] . '</h1>';
            echo  '<table><th>celkem</th><th>publikovano</th><tr><td>' . $row10['avgCol'] .'</td><td>' . $row['publikovano'] . '</td></tr></table>';
            echo '<a href="' . DIRECTORY_DATA . '/' . $row['titulek'] . '.pdf' . '">' . 'Stáhnout příspěvek' . '</a><br><br><hr>';
            echo '<a href="index.php?page=my&id=' . $row['id_prispevek'] . '&stranka=' . $page . '"' . '>Upravit</a>';
        }
    }
    if($tplData['idk'] == 1) {
    ?>
    <Div class="PaginationFooter">

        <?php
        for($page=1;$page<=$tplData['pages'];$page++) {
            echo '<a href="index.php?page=my&stranka=' . $page . '">' . $page . '</a>';
        }
        ?>
    </Div>
    <?php
    }
    ?>
</div>
<?php
$tplHeaders->createFooter();
?>
