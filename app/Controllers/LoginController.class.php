<?php
// nactu rozhrani kontroleru
require_once(DIRECTORY_CONTROLLERS."/IController.interface.php");

/**
 * Ovladac zajistujici vypsani uvodni stranky.
 */
class LoginController implements IController {

    /** @var DatabaseModel $db  Sprava databaze. */
    private $db;

    /**
     * Inicializace pripojeni k databazi.
     */
    public function __construct() {
        // inicializace prace s DB
        require_once (DIRECTORY_MODELS ."/DatabaseModel.class.php");
        $this->db = new DatabaseModel();
    }

    /**
     * Vrati obsah uvodni stranky.
     * @param string $pageTitle     Nazev stranky.
     * @return string               Vypis v sablone.
     */
    public function show(string $pageTitle):string {
        //// vsechna data sablony budou globalni
        global $tplData;
        $tplData = [];
        // nazev
        $tplData['title'] = $pageTitle;




// zpracovani odeslanych formularu
        if (isset($_POST['action'])) {
            // prihlaseni
            if ($_POST['action'] == 'login' && isset($_POST['login']) && isset($_POST['heslo'])) {
                // pokusim se prihlasit uzivatele
                $res = $this->db->userLogin($_POST['login'], $_POST['heslo']);
                if ($res) {
                    $tplData['login'] = "OK: Uživatel byl přihlášen.";
                } else {
                    $tplData['login'] = "ERROR: Přihlášení uživatele se nezdařilo.";
                }
            } // odhlaseni
            else if ($_POST['action'] == 'logout') {
                // odhlasim uzivatele
                $this->db->userLogout();
                $tplData['logout'] = "OK: Uživatel byl odhlášen.";
            } // neznama akce
            else {
                $tplData['logout'] = "WARNING: Neznámá akce.";
            }
            //echo "<br>";
        }
$user = '';
// pokud je uzivatel prihlasen, tak ziskam jeho data
        if ($this->db->isUserLogged()) {
            // ziskam data prihlasenoho uzivatele

            $user= $this->db->getLoggedUserData();
            $tplData['name'] = $user['login'];
            $tplData['jmeno'] = $user['jmeno'];
            $tplData['email'] = $user['email'];

        }

    $tplData['logged'] = null;
        if($this->db->isUserLogged()){
            $tplData['logged'] = 1;
        } else {
            $tplData['logged'] = 0;
        }

        if($tplData['logged'] == 1) {
            ///////////// PRO PRIHLASENE UZIVATELE /////////////
            // ziskam nazev prava uzivatele, abych ho mohl vypsat
            $pravo = $this->db->getRightById($user["id_pravo"]);
            // ziskam nazev
            $tplData['pravo'] = ($pravo == null) ? "*Neznámé*" : $pravo['nazev'];
        }
        $userData = $this->db->getLoggedUserData();
        $tplData['right'] = $userData['id_pravo'];











        //// vypsani prislusne sablony
        // zapnu output buffer pro odchyceni vypisu sablony
        ob_start();
        // pripojim sablonu, cimz ji i vykonam
        require(DIRECTORY_VIEWS ."/LoginTemplate.tpl.php");
        // ziskam obsah output bufferu, tj. vypsanou sablonu
        $obsah = ob_get_clean();

        // vratim sablonu naplnenou daty
        return $obsah;
    }

}

?>