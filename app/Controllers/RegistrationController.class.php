<?php
// nactu rozhrani kontroleru
require_once(DIRECTORY_CONTROLLERS."/IController.interface.php");

/**
 * Ovladac zajistujici vypsani uvodni stranky.
 */
class RegistrationController implements IController {

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

        $userData = $this->db->getLoggedUserData();
        $tplData['right'] = $userData['id_pravo'];

        // zpracovani odeslanych formularu
        if(isset($_POST['potvrzeni'])){
            // mam vsechny pozadovane hodnoty?
            if(isset($_POST['login']) && isset($_POST['heslo']) && isset($_POST['heslo2'])
                && isset($_POST['jmeno']) && isset($_POST['email']) && isset($_POST['pravo'])
                && $_POST['heslo'] == $_POST['heslo2']
                && $_POST['login'] != "" && $_POST['heslo'] != "" && $_POST['jmeno'] != "" && $_POST['email'] != ""
                && $_POST['pravo'] > 0
            ){
                // pozn.: heslo by melo byt sifrovano
                // napr. password_hash($password, PASSWORD_BCRYPT) pro sifrovani
                // a password_verify($password, $hash) pro kontrolu hesla.

                // mam vsechny atributy - ulozim uzivatele do DB
                $res = $this->db->addNewUser($_POST['login'], $_POST['heslo'], $_POST['jmeno'], $_POST['email'], $_POST['pravo']);
                // byl ulozen?
                if($res){
                    $tplData['msg'] =  "OK: Uživatel byl přidán do databáze.";
                } else {
                    $tplData['msg'] =  "ERROR: Uložení uživatele se nezdařilo.";
                }
            } else {
                // nemam vsechny atributy
                $tplData['msg'] =  "ERROR: Nebyly přijaty požadované atributy uživatele.";
            }
            $tplData['msg'] .=  "<br><br>";
        }

        $right = $this->db->getRightById(2);
        $tplData['id'] = $right['id_pravo'];
        $tplData['nazev'] = $right['nazev'];

        if($this->db->isUserLogged()) {
        $tplData['logged'] = 1;
        } else $tplData['logged'] = 0;

        //// vypsani prislusne sablony
        // zapnu output buffer pro odchyceni vypisu sablony
        ob_start();
        // pripojim sablonu, cimz ji i vykonam
        require(DIRECTORY_VIEWS ."/RegistrationTemplate.tpl.php");
        // ziskam obsah output bufferu, tj. vypsanou sablonu
        $obsah = ob_get_clean();

        // vratim sablonu naplnenou daty
        return $obsah;
    }

}

?>