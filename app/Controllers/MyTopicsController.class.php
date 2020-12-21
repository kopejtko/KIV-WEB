<?php
// nactu rozhrani kontroleru
require_once(DIRECTORY_CONTROLLERS."/IController.interface.php");

/**
 * Ovladac zajistujici vypsani uvodni stranky.
 */
class MyTopicsController implements IController {

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




    ///

    /**
     * Vrati obsah uvodni stranky.
     * @param string $pageTitle     Nazev stranky.
     * @return string               Vypis v sablone.
     */
    public function show(string $pageTitle):string {

        global $tplData;
        $tplData = [];
        global $resultg;
        global $page;

        $con = mysqli_connect('localhost', 'root', '');
        mysqli_select_db($con, 'mydb2');
        $results_per_page = 4;

        $sql = "SELECT * FROM prispevek WHERE publikovano='ANO'";
// $result = mysqli_query($myDB->pdo, $sql);
        $result = mysqli_query($con, $sql);
        $number_of_results = mysqli_num_rows($result);

//while ($row = mysqli_fetch_array($result)) {
//  echo $row['id_prispevek'] . ' ' . $row['obsah'] . '<br>';
//}

        $number_of_pages = ceil($number_of_results/$results_per_page);

        if(!isset($_GET['stranka'])) {
            $page = 1;
        } else {
            $page = $_GET['stranka'];
        }

        $this_page_first_result = ($page - 1)*$results_per_page;


        if (isset($_GET["id"])) {
            $prispevekId = $_GET["id"]; // nastavim pozadovane
            $sql = "SELECT * FROM prispevek WHERE id_prispevek=" . $prispevekId;
            $result = mysqli_query($con, $sql);
            $resultg = $result;
            $tplData['idk'] = 0;

        } else {
            $uzivatelId = $this->db->getUserId();
            $sql = "SELECT * FROM prispevek WHERE id_uzivatel=" . $uzivatelId . " ORDER BY id_prispevek DESC LIMIT " . $this_page_first_result . ',' . $results_per_page;;
            $result = mysqli_query($con, $sql);
            $resultg = $result;
            $tplData['idk'] = 1;
        }

        $userData = $this->db->getLoggedUserData();
        $tplData['right'] = $userData['id_pravo'];


        //// vsechna data sablony budou globalni

        // nazev
        $tplData['title'] = $pageTitle;
        // data pohadek
        //   $tplData['stories'] = $this->db->getAllIntroductions();

        $tplData['pages'] = $number_of_pages;
        $tplData['rpp'] = $results_per_page;
        $tplData['fpr'] = $this_page_first_result;

        //// vypsani prislusne sablony
        // zapnu output buffer pro odchyceni vypisu sablony
        ob_start();
        // pripojim sablonu, cimz ji i vykonam
        require(DIRECTORY_VIEWS ."/MyTopicsTemplate.tpl.php");
        // ziskam obsah output bufferu, tj. vypsanou sablonu
        $obsah = ob_get_clean();

        // vratim sablonu naplnenou daty
        return $obsah;
    }

}

?>