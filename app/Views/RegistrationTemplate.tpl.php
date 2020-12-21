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
    if($tplData['logged'] == 0){
?>
        <h2>Registrační formulář</h2>
        <form action="" method="POST" oninput="x.value=(pas1.value==pas2.value)?'OK':'Nestejná hesla'">
            <table>
                <tr><td>Login:</td><td><input type="text" name="login" required></td></tr>
                <tr><td>Heslo 1:</td><td><input type="password" name="heslo" id="pas1" required></td></tr>
                <tr><td>Heslo 2:</td><td><input type="password" name="heslo2" id="pas2" required></td></tr>
                <tr><td>Ověření hesla:</td><td><output name="x" for="pas1 pas2"></output></td></tr>
                <tr><td>Jméno:</td><td><input type="text" name="jmeno" required></td></tr>
                <tr><td>E-mail:</td><td><input type="email" name="email" required></td></tr>
                <tr><td>Právo:</td>
                    <td>
                        <select name="pravo">
                            <?php
                                    echo "<option value=" . $tplData['id'] . ">" . $tplData['nazev'] . "</option>";

                            ?>
                        </select>
                    </td>
                </tr>
            </table>

            <input type="submit" name="potvrzeni" value="Registrovat">
        </form>
<?php
    ///////////// KONEC: PRO NEPRIHLASENE UZIVATELE ///////////////
    } else {
    ///////////// PRO PRIHLASENE UZIVATELE ///////////////
?>
        <div>
            <b>Přihlášený uživatel se nemůže znovu registrovat.</b>
        </div>

<?php
}
?>


    </div>
<?php
$tplHeaders->createFooter();
?>
