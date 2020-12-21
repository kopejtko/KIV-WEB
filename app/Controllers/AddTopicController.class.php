<?php
// nactu rozhrani kontroleru
require_once(DIRECTORY_CONTROLLERS."/IController.interface.php");

/**
 * Ovladac zajistujici vypsani uvodni stranky.
 */
class AddTopicController implements IController {

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
        $tplData['msg'] = '';
        // data pohadek
     //   $tplData['stories'] = $this->db->getAllIntroductions();
        $tplData['nahrat'] = 1;

        $userData = $this->db->getLoggedUserData();
        $tplData['right'] = $userData['id_pravo'];


        function prijmySoubory(){

            //// mam adresar data?
            $adr = "DATA";
            // neni souborem
            if(is_file($adr)){
                echo "Nelze vytvořit adresář DATA.<br>";
            }
            // neni souborem a neexistuje?
            elseif(!file_exists($adr)) {
                mkdir($adr);
            }
            // nemam adresar data?
            if(!is_dir($adr)){
                echo "Adresář DATA nelze použít.<br>";
                return; // konec funkce
            }

            //// byly na server odeslany nejake soubory?
            if(isset($_FILES["soubory"]["name"])
                && count($_FILES["soubory"]["name"])    // zamyslete se, proc funguje bez ">0"
                && !empty($_FILES["soubory"]["name"][0])
            ){
                // byly - prijmu je a ulozim na server
                // viz https://www.w3schools.com/php/php_file_upload.asp
                // pocet souboru vezmu z poctu nazvu
                for($i = 0; $i < count($_FILES["soubory"]["name"]); $i++){
                    // ziskam nazev souboru, slozim mu celou cestu a ziskam z ni priponu souboru
                    $nazev = basename( $_FILES["soubory"]["name"][$i]);
                    $pripona = strtolower(pathinfo($nazev,PATHINFO_EXTENSION));
                    if($pripona != 'pdf'){
                        $tplData['msg'] = "Soubor je v nespravnem formatu.";
                        return 0;
                    }
                    $nazev = $_POST['titulek'] . "." . $pripona;
                    $celyNazev = $adr ."/". $nazev;
                   // $pripona = strtolower(pathinfo($celyNazev,PATHINFO_EXTENSION));
                    // ziskam velikost souboru - varianta ciste pro upload souboru
                    $velikost = $_FILES["soubory"]["size"][$i] . " B.";
                    // ziskam velikost souboru - varianta pro libovolny soubor
                    //$velikost = filesize($_FILES["soubory"]["tmp_name"][$i]) . " B.";
                    // vypis informace
                    $tplData['msg'] =  "Zpracovávám soubor [$pripona]: '$nazev', velikost: $velikost<br>";

                    //// prenesu soubor na server
                    // pozor, pokud je server provozovan pod Windows, tak defaultne pracuje s kodovanim windows-1250 namisto utf-8
                    // prevod nazvu z UTF-8 do cp-1250
                    $celyNazev = iconv("UTF-8", "WINDOWS-1250", $celyNazev);
                    // samotny prenos
                    if (move_uploaded_file($_FILES["soubory"]["tmp_name"][$i], $celyNazev)) {
                        $tplData['msg'] .=  "Soubor '$nazev' byl nahrán na server.<br>";
                        return 1;
                    } else {
                        $tplData['msg'] .= "Soubor '$nazev' se nepodařilo nahrát na server.<br>";
                        return 0;
                    }
                }
            }
        }


        // zpracovani odeslanych formularu
        if(isset($_POST['potvrzeni'])){
            echo $tplData['msg'];
            $tplData['nahrat'] = prijmySoubory();
            // mam vsechny pozadovane hodnoty?
            if(isset($_POST['titulek'])  && isset($_POST['obsah']) && isset($_POST['datum'])
            ){
                // pozn.: heslo by melo byt sifrovano
                // napr. password_hash($password, PASSWORD_BCRYPT) pro sifrovani
                // a password_verify($password, $hash) pro kontrolu hesla.

                // mam vsechny atributy - ulozim uzivatele do DB

                $result = $this->db->searchForDuplicates($_POST['titulek']);
                if(!empty($result)){
                    $tplData['nahrat'] = 0;
                }

                $res = 0;
                if($tplData['nahrat'] == 1) {
                    $res = $this->db->addNewTopic($_POST['titulek'], $_POST['obsah'], $_POST['datum'], $this->db->getUserId());
                }
                // byl ulozen?
                echo $tplData['msg'];
                if($res){
                    $tplData['msg'] = "OK: Příspěvek byl přidán do databáze.";
                } else {
                    $tplData['msg'] =  "ERROR: Uložení příspěvku se nezdařilo.";
                }
            } else {
                // nemam vsechny atributy
                $tplData['msg'] = "ERROR: Nebyly přijaty požadované atributy příspěvku.";
            }
            echo "<br><br>";
        }



        //// vypsani prislusne sablony
        // zapnu output buffer pro odchyceni vypisu sablony
        ob_start();
        // pripojim sablonu, cimz ji i vykonam
        require(DIRECTORY_VIEWS ."/AddTopicTemplate.tpl.php");
        // ziskam obsah output bufferu, tj. vypsanou sablonu
        $obsah = ob_get_clean();

        // vratim sablonu naplnenou daty
        return $obsah;
    }

}

?>