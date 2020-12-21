<?php

global $tplData;

// pripojim objekt pro vypis hlavicky a paticky HTML
require(DIRECTORY_VIEWS ."/BasicHTML.class.php");
$tplHeaders = new BasicHTML();


// nacteni souboru s funkcemi
require_once(DIRECTORY_MODELS ."/DatabaseModel.class.php");
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
    if(isset($tplData['msg'])) {
        echo $tplData['msg'];
    }
    ?>


<h2>Přidat nový příspěvek</h2>
<form action="" method="POST" id="forminput" enctype="multipart/form-data">
    <table>
        <tr><td>Titulek:</td><td><input type="text" name="titulek" required></td></tr>
        <tr><td>Obsah:</td><td><textarea name="obsah" form="forminput" cols="60" wrap="soft"></textarea></td></tr>
        <tr><td>Datum:</td><td><input type="date" name="datum" required</td></tr>
        <tr><td>Soubor:</td><td><input type="file" name="soubory[]" id="fileToUpload" multiple></td></tr>
    </table>
    <input type="submit" name="potvrzeni" value="Přidat příspěvek">
</form>
</div>
<?php
$tplHeaders->createFooter();
?>
