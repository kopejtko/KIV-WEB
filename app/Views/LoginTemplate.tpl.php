<?php
global $tplData;
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
if(isset($tplData['msg'])) {
    echo $tplData['msg'];
}
///////////// PRO NEPRIHLASENE UZIVATELE ///////////////
// pokud uzivatel neni prihlasen nebo nebyla ziskana jeho data, tak vypisu prihlasovaci formular
if ($tplData['logged'] == 0 || $tplData['logged'] == null){

    ?>

        <h2>Přihlášení uživatele</h2>

        <form action="" method="POST">
            <table>
                <tr><td>Login:</td><td><input type="text" name="login"></td></tr>
                <tr><td>Heslo:</td><td><input type="password" name="heslo"></td></tr>
            </table>
            <input type="hidden" name="action" value="login">
            <input type="submit" name="potvrzeni" value="Přihlásit">
        </form>
<?php
    ///////////// KONEC: PRO NEPRIHLASENE UZIVATELE ///////////////

    } else if ($tplData['logged'] == 1){

?>
        <h2>Přihlášený uživatel</h2>

        Login: <?php echo $tplData['name'] ; ?><br>
        Jméno: <?php echo $tplData['jmeno'] ; ?><br>
        E-mail: <?php echo $tplData['email'] ; ?><br>
        Právo: <?php echo $tplData['pravo'] ; ?><br>
        <br>

        Odhlášení uživatele:
        <form method="POST">
            <input type="hidden" name="action" value="logout">
            <input type="submit" name="potvrzeni" value="Odhlásit">
        </form>

<?php

}
?>
</div>
<?php
$tplHeaders->createFooter();
?>
