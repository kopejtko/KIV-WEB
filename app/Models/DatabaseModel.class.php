<?php
//////////////////////////////////////////////////////////////
////////////// Vlastni trida pro praci s databazi ////////////////
//////////////////////////////////////////////////////////////

/**
 * Vlastni trida spravujici databazi.
 */
class DatabaseModel {

    /** @var PDO $pdo  PDO objekt pro praci s databazi. */
    public $pdo;

    /** @var MySession $mySession  Vlastni objekt pro spravu session. */
    private $mySession;
    /** @var string $userSessionKey  Klicem pro data uzivatele, ktera jsou ulozena v session. */
    private $userSessionKey = "current_user_id";


    /**
     * MyDatabase constructor.
     * Inicializace pripojeni k databazi a pokud ma byt spravovano prihlaseni uzivatele,
     * tak i vlastni objekt pro spravu session.
     * Pozn.: v samostatne praci by sprava prihlaseni uzivatele mela byt v samostatne tride.
     * Pozn.2: take je mozne do samostatne tridy vytahnout konkretni funkce pro praci s databazi.
     */

    public function __construct(){
        // inicialilzuju pripojeni k databazi - informace beru ze settings
        $this->pdo = new PDO("mysql:host=".DB_SERVER.";dbname=".DB_NAME, DB_USER, DB_PASS);
        $this->pdo->exec("set names utf8");

    }

    ///////////////////  Obecne funkce  ////////////////////////////////////////////

    /**
     *  Provede dotaz a bud vrati ziskana data, nebo pri chybe ji vypise a vrati null.
     *
     *  @param string $dotaz        SQL dotaz.
     *  @return PDOStatement|null    Vysledek dotazu.
     */
    public function executeQuery(string $dotaz){
        // vykonam dotaz
        $res = $this->pdo->query($dotaz);
        // pokud neni false, tak vratim vysledek, jinak null
        if ($res) {
            // neni false
            return $res;
        } else {
            // je false - vypisu prislusnou chybu a vratim null
            $error = $this->pdo->errorInfo();
            echo $error[2];
            return null;
        }
    }

    /**
     * Jednoduche cteni z prislusne DB tabulky.
     *
     * @param string $tableName         Nazev tabulky.
     * @param string $whereStatement    Pripadne omezeni na ziskani radek tabulky. Default "".
     * @param string $orderByStatement  Pripadne razeni ziskanych radek tabulky. Default "".
     * @return array                    Vraci pole ziskanych radek tabulky.
     */
    public function selectFromTable(string $tableName, string $whereStatement = "", string $orderByStatement = ""):array {
        // slozim dotaz
        $q = "SELECT * FROM ".$tableName
            .(($whereStatement == "") ? "" : " WHERE $whereStatement")
            .(($orderByStatement == "") ? "" : " ORDER BY $orderByStatement");
        // provedu ho a vratim vysledek
        $obj = $this->executeQuery($q);
        // pokud je null, tak vratim prazdne pole
        if($obj == null){
            return [];
        }
        // projdu jednotlive ziskane radky tabulky
        /*while($row = $vystup->fetch(PDO::FETCH_ASSOC)){
            $pole[] = $row['login'].'<br>';
        }*/
        // prevedu vsechny ziskane radky tabulky na pole
        return $obj->fetchAll();
    }

    /**
     * Jednoduchy zapis do prislusne tabulky.
     *
     * @param string $tableName         Nazev tabulky.
     * @param string $insertStatement   Text s nazvy sloupcu pro insert.
     * @param string $insertValues      Text s hodnotami pro prislusne sloupce.
     * @return bool                     Vlozeno v poradku?
     */
    public function insertIntoTable(string $tableName, string $insertStatement, string $insertValues):bool {
        // slozim dotaz
        $q = "INSERT INTO $tableName($insertStatement) VALUES ($insertValues)";
        // provedu ho a vratim uspesnost vlozeni
        $obj = $this->executeQuery($q);
        if($obj == null){
            return false;
        } else {
            return true;
        }
    }

    /**
     * Jednoducha uprava radku databazove tabulky.
     *
     * @param string $tableName                     Nazev tabulky.
     * @param string $updateStatementWithValues     Cela cast updatu s hodnotami.
     * @param string $whereStatement                Cela cast pro WHERE.
     * @return bool                                 Upraveno v poradku?
     */
    public function updateInTable(string $tableName, string $updateStatementWithValues, string $whereStatement):bool {
        // slozim dotaz
        $q = "UPDATE $tableName SET $updateStatementWithValues WHERE $whereStatement";
        // provedu ho a vratim vysledek
        $obj = $this->executeQuery($q);
        if($obj == null){
            return false;
        } else {
            return true;
        }
    }

    /**
     * Dle zadane podminky maze radky v prislusne tabulce.
     *
     * @param string $tableName         Nazev tabulky.
     * @param string $whereStatement    Podminka mazani.
     */
    public function deleteFromTable(string $tableName, string $whereStatement){
        // slozim dotaz
        $q = "DELETE FROM $tableName WHERE $whereStatement";
        // provedu ho a vratim vysledek
        $obj = $this->executeQuery($q);
        if($obj == null){
            return false;
        } else {
            return true;
        }
    }

    ///////////////////  KONEC: Obecne funkce  ////////////////////////////////////////////

    ///////////////////  Konkretni funkce  ////////////////////////////////////////////

    /**
     * Ziskani zaznamu vsech uzivatelu aplikace.
     *
     * @return array    Pole se vsemi uzivateli.
     */
    public function getAllUsers(){
        // ziskam vsechny uzivatele z DB razene dle ID a vratim je
        $users = $this->selectFromTable(TABLE_UZIVATEL, "", "id_uzivatel");
        return $users;
    }

    /**
     * Ziskani zaznamu vsech prav uzivatelu.
     *
     * @return array    Pole se vsemi pravy.
     */
    public function getAllRights(){
        // ziskam vsechna prava z DB razena dle ID a vratim je
        $rights = $this->selectFromTable(TABLE_PRAVO, "", "vaha ASC, nazev ASC");
        return $rights;
    }

    /**
     * Ziskani konkretniho prava uzivatele dle ID prava.
     *
     * @param int $id       ID prava.
     * @return array        Data nalezeneho prava.
     */
    public function getRightById(int $id){
        // ziskam pravo dle ID
        $rights = $this->selectFromTable(TABLE_PRAVO, "id_pravo=$id");
        if(empty($rights)){
            return null;
        } else {
            // vracim prvni nalezene pravo
            return $rights[0];
        }
    }

    /**
     * Vytvoreni noveho uzivatele v databazi.
     *
     * @param string $login     Login.
     * @param string $jmeno     Jmeno.
     * @param string $email     E-mail.
     * @param int $idPravo      Je cizim klicem do tabulky s pravy.
     * @return bool             Vlozen v poradku?
     */
    public function addNewUser(string $login, string $heslo, string $jmeno, string $email, int $idPravo = 4){
        // hlavicka pro vlozeni do tabulky uzivatelu
        $insertStatement = "login, heslo, jmeno, email, id_pravo";
        // hodnoty pro vlozeni do tabulky uzivatelu
        $insertValues = "'$login', '$heslo', '$jmeno', '$email', $idPravo";
        // provedu dotaz a vratim jeho vysledek
        return $this->insertIntoTable(TABLE_UZIVATEL, $insertStatement, $insertValues);
    }

    /**
     * Uprava konkretniho uzivatele v databazi.
     *
     * @param int $idUzivatel   ID upravovaneho uzivatele.
     * @param string $login     Login.
     * @param string $heslo     Heslo.
     * @param string $jmeno     Jmeno.
     * @param string $email     E-mail.
     * @param int $idPravo      ID prava.
     * @return bool             Bylo upraveno?
     */
    public function updateUser(int $idUzivatel, string $login, string $heslo, string $jmeno, string $email, int $idPravo){
        // slozim cast s hodnotami
        $updateStatementWithValues = "login='$login', heslo='$heslo', jmeno='$jmeno', email='$email', id_pravo='$idPravo'";
        // podminka
        $whereStatement = "id_uzivatel=$idUzivatel";
        // provedu update
        return $this->updateInTable(TABLE_UZIVATEL, $updateStatementWithValues, $whereStatement);
    }

    ///////////////////  KONEC: Konkretni funkce  ////////////////////////////////////////////

    ///////////////////  Sprava prihlaseni uzivatele  ////////////////////////////////////////

    /**
     * Overi, zda muse byt uzivatel prihlasen a pripadne ho prihlasi.
     *
     * @param string $login     Login uzivatele.
     * @param string $heslo     Heslo uzivatele.
     * @return bool             Byl prihlasen?
     */
    public function userLogin(string $login, string $heslo){
        // ziskam uzivatele z DB - primo overuju login i heslo
        $where = "login='$login' AND heslo='$heslo'";
        $user = $this->selectFromTable(TABLE_UZIVATEL, $where);
        // ziskal jsem uzivatele
        if(count($user)){
            if($user[0]['zablokovan'] == 'NE') {
                // ziskal - ulozim ho do session
                $_SESSION[$this->userSessionKey] = $user[0]['id_uzivatel']; // beru prvniho nalezeneho a ukladam jen jeho ID
                return true;
            } return false;
        } else {
            // neziskal jsem uzivatele
            return false;
        }
    }

    public function getUserId():String{

        return $_SESSION[$this->userSessionKey];
    }

    /**
     * Odhlasi soucasneho uzivatele.
     */
    public function userLogout(){
        unset($_SESSION[$this->userSessionKey]);
    }

    /**
     * Test, zda je nyni uzivatel prihlasen.
     *
     * @return bool     Je prihlasen?
     */
    public function isUserLogged(){
        return isset($_SESSION[$this->userSessionKey]);
    }

    /**
     * Pokud je uzivatel prihlasen, tak vrati jeho data,
     * ale pokud nebyla v session nalezena, tak vypisu chybu.
     *
     * @return mixed|null   Data uzivatele nebo null.
     */
    public function getLoggedUserData(){
        if($this->isUserLogged()){
            // ziskam data uzivatele ze session
            $userId = $_SESSION[$this->userSessionKey];
            // pokud nemam data uzivatele, tak vypisu chybu a vynutim odhlaseni uzivatele
            if($userId == null) {
                // nemam data uzivatele ze session - vypisu jen chybu, uzivatele odhlasim a vratim null
                echo "SEVER ERROR: Data přihlášeného uživatele nebyla nalezena, a proto byl uživatel odhlášen.";
                $this->userLogout();
                // vracim null
                return null;
            } else {
                // nactu data uzivatele z databaze
                $userData = $this->selectFromTable(TABLE_UZIVATEL, "id_uzivatel=$userId");
                // mam data uzivatele?
                if(empty($userData)){
                    // nemam - vypisu jen chybu, uzivatele odhlasim a vratim null
                    echo "ERROR: Data přihlášeného uživatele se nenachází v databázi (mohl být smazán), a proto byl uživatel odhlášen.";
                    $this->userLogout();
                    return null;
                } else {
                    // protoze DB vraci pole uzivatelu, tak vyjmu jeho prvni polozku a vratim ziskana data uzivatele
                    return $userData[0];
                }
            }
        } else {
            // uzivatel neni prihlasen - vracim null
            return null;
        }
    }

    /*
     *
     */
    public function addNewTopic(string $titulek, string $obsah, string $datum, string $uzivatel){
        // hlavicka pro vlozeni do tabulky uzivatelu
        $insertStatement = "titulek, obsah, datum, id_uzivatel";
        // hodnoty pro vlozeni do tabulky uzivatelu
        $insertValues = "'$titulek', '$obsah', '$datum', '$uzivatel'";
        // provedu dotaz a vratim jeho vysledek
        return $this->insertIntoTable('prispevek', $insertStatement, $insertValues, $uzivatel);
    }

    ///////////////////  KONEC: Sprava prihlaseni uzivatele  ////////////////////////////////////////
    ///


    public function publishTopic($topicId){
        $q = "UPDATE prispevek SET publikovano='ANO' WHERE id_prispevek=$topicId";

        $obj = $this->executeQuery($q);
        if($obj == null){
            return false;
        } else {
            return true;
        }
    }

    public function deleteTopic($topicId){
        $q1 = "DELETE FROM recenze WHERE id_prispevek=$topicId";
        $q2 = "DELETE FROM prispevek WHERE id_prispevek=$topicId";
        $obj = $this->executeQuery($q1);
        $obj = $this->executeQuery($q2);
        if($obj == null){
            return false;
        } else {
            return true;
        }
    }

    public function deleteReview($topicId){
        $q1 = "DELETE FROM recenze WHERE id_recenze=$topicId";
        $obj = $this->executeQuery($q1);
        if($obj == null){
            return false;
        } else {
            return true;
        }
    }

    public function assignReview($topicId, $userId){
        //  $q1 = "INSERT INTO recenze() VALUES id_recenze=$topicId";
        $insertStatement = "id_prispevek, id_uzivatel";
        $insertValues = "'$topicId', '$userId'";
        return $this->insertIntoTable('recenze', $insertStatement, $insertValues);
    }

    public function changeReview($originalita, $tema, $tech, $jazyk, $doporuceni, $userId, $reviewId){

        $celkem = ($tema + $tech + $jazyk + $doporuceni + $originalita) / 5;
        //  $q1 = "INSERT INTO recenze() VALUES id_recenze=$topicId";
        $updateStatementWithValues = "celkem='$celkem', originalita='$originalita', tema='$tema', tech='$tech', jazyk='$jazyk', doporuceni='$doporuceni'";
        // podminka
        $whereStatement = "id_recenze=$reviewId";
        return $this->updateInTable('recenze', $updateStatementWithValues, $whereStatement);
    }
    public function blockUser($userId){
        $updateStatementWithValues = "zablokovan='ANO'";
        // podminka
        $whereStatement = "id_uzivatel=$userId";
        return $this->updateInTable('uzivatel', $updateStatementWithValues, $whereStatement);
    }

    public function unblockUser($userId){
        $updateStatementWithValues = "zablokovan='NE'";
        // podminka
        $whereStatement = "id_uzivatel=$userId";
        return $this->updateInTable('uzivatel', $updateStatementWithValues, $whereStatement);
    }
    public function changeUserRole($userId, $rightId){
        $updateStatementWithValues = "id_pravo=$rightId";
        // podminka
        $whereStatement = "id_uzivatel=$userId";
        return $this->updateInTable('uzivatel', $updateStatementWithValues, $whereStatement);
    }
    public function searchForDuplicates($topicTitle){
        // podminka
        $whereStatement = "titulek='$topicTitle'";
        return $this->selectFromTable('prispevek', $whereStatement);
    }

}
?>
