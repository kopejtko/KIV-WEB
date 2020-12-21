<?php
///////////////////////////////////////////////////////
////////////// Zakladni nastaveni webu ////////////////
///////////////////////////////////////////////////////

////// nastaveni pristupu k databazi ///////

// prihlasovaci udaje k databazi
define("DB_SERVER","localhost"); // https://students.kiv.zcu.cz nebo ci 147.228.63.10
define("DB_NAME","mydb2");
define("DB_USER","root"); //kopejtko
define("DB_PASS",""); //A20B0147P

// definice konkretnich nazvu tabulek
define("TABLE_UZIVATEL","uzivatel");
define("TABLE_PRAVO","pravo");


///// vsechny stranky webu ////////

// pripona souboru
$phpExtension = ".inc.php";

/** Adresar kontroleru. */
const DIRECTORY_CONTROLLERS = "app\Controllers";
/** Adresar modelu. */
const DIRECTORY_MODELS = "app\Models";
/** Adresar sablon */
const DIRECTORY_VIEWS = "app\Views";
/** Adresar souboru */
const DIRECTORY_DATA = "DATA";

/** Klic defaultni webove stranky. */
const DEFAULT_WEB_PAGE_KEY = "home";

/** Dostupne webove stranky. */
const WEB_PAGES = array(
    //// Uvodni stranka ////
    "add" => array(
        "title" => "Přidat příspěvek",

        //// kontroler
        "file_name" => "AddTopicController.class.php",
        "class_name" => "AddTopicController",
    ),
    //// KONEC: Uvodni stranka ////

    //// Sprava uzivatelu ////
    "home" => array(
        "title" => "Úvodní stránka",

        //// kontroler
        "file_name" => "HomeController.class.php",
        "class_name" => "HomeController",
    ),
    //// KONEC: Sprava uzivatelu ////
    // Sprava uzivatelu ////
        "login" => array(
            "title" => "Přihlášení",

            //// kontroler
            "file_name" => "LoginController.class.php",
            "class_name" => "LoginController",
        ),
    "management" => array(
        "title" => "Správa",

        //// kontroler
        "file_name" => "ManagementController.class.php",
        "class_name" => "ManagementController",
    ),
    "registration" => array(
        "title" => "Registrace",

        //// kontroler
        "file_name" => "RegistrationController.class.php",
        "class_name" => "RegistrationController",
    ),
    "reviews" => array(
        "title" => "Recenze",

        //// kontroler
        "file_name" => "ReviewsController.class.php",
        "class_name" => "ReviewsController",
    ),
    "my" => array(
        "title" => "Moje Prispevky",

        //// kontroler
        "file_name" => "MyTopicsController.class.php",
        "class_name" => "MyTopicsController",
    )
);

?>
