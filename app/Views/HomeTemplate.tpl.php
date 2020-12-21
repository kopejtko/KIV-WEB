<?php



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

?>
<div class="Content">
    <?php
    if($tplData['idk'] == 0) {
        while ($row = mysqli_fetch_array($resultg)) {
            echo '<h1>' . $row['titulek'] . '</h1>' . '<br>';
            echo $row['obsah'] . '<br><br>';
            echo '<a href="' . DIRECTORY_DATA . '/' . $row['titulek'] . '.pdf' . '">' . 'Stáhnout příspěvek' . '</a><br><br><hr>';
            //       echo '<a href="index.php?page=prispevky&id=' . $row['id_prispevek'] . '"' . '>Odkaz</a><br>';
            echo '<a href="index.php?page=prispevky">' . 'Zpět na seznam příspěvků' . '</a><br><br><hr>';
        }
    } else if($tplData['idk'] == 1) {

        while ($row = mysqli_fetch_array($resultg)) {
            echo '<h1>' . $row['titulek'] . ', ';
            echo $row['datum'] . '</h1>';
            echo '<a href="index.php?page=prispevky&id=' . $row['id_prispevek'] . '&stranka=' . $page . '"' . '>Odkaz</a><br><br><hr>';
        }
    }
    if($tplData['idk'] == 1) {
    ?>

    <Div class="PaginationFooter">
        <br>
        <?php
        for($page=1;$page<=$tplData['pages'];$page++) {
            echo '<a href="index.php?page=prispevky&stranka=' . $page . '">' . $page . '</a>';
        }

        ?>
        <br><hr>
    </Div>
    <?php
    }
    ?>
</div>
<?php
$tplHeaders->createFooter();
?>
