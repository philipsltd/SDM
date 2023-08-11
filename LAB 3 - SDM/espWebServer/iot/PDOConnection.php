<?php
if (!defined("APP_MODE")) {

    define("MODE_DEVELOPMENT", "MODE_DEVELOPMENT");
    define("MODE_PRODUCTION", "MODE_PRODUCTION");
    // UNCOMENT ONE OF THESE TWO LINES FOR SETTING THE APP_MODE 
    //define("APP_MODE", MODE_DEVELOPMENT);
    define("APP_MODE", MODE_PRODUCTION);
    if (APP_MODE == MODE_DEVELOPMENT) {
        // use these settings for the development localhost
        define("DATABASE_HOST",     "localhost");
        define("DATABASE_PORT",     3306);
        define("DATABASE_USER",     "root");
        define("DATABASE_PASSWORD", "");
        define("DATABASE_NAME",     "4308562_iot");
    } else if (APP_MODE == MODE_PRODUCTION) {
        // use these settings for the PRODUCTION server
        define("DATABASE_HOST",     "fdb1028.awardspace.net");
        define("DATABASE_PORT",     3306);
        define("DATABASE_USER",     "4308562_iot");
        define("DATABASE_PASSWORD", "MoraisSalgueiro22.23");
        define("DATABASE_NAME",     "4308562_iot");
    } else {
        echo '<h2>App mode not set  to existing modes ...</h2>';
        die;
    }
    // the recommended approach is to use INI files.
    // see e.g. https://coderwall.com/p/91nk1a/php-database-connection-with-file-ini
} else {
    echo '<h2>App mode not set...</h2>';
    die;
}
if(!defined("PDOCONNECTION_CLASS") ) {
    define( "PDOCONNECTION_CLASS", " PDOConnection");
    class PDOConnection extends PDO
    {
        // these settings are usually stored in an settings.ini file
        public function __construct($host = DATABASE_HOST, $port = DATABASE_PORT, $dbName = DATABASE_NAME)
        {
            $dns = "mysql:host=$host;port=$port;dbname=$dbName;charset=UTF8;";
            parent::__construct($dns, DATABASE_USER, DATABASE_PASSWORD);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }
}
