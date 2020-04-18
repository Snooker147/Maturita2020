<?php

class Config
{
    public const CONFIG_FILE = "config.json";
    public const MODE_DEVELOPMENT = "development";
    public const MODE_RELEASE = "release";

    /**
     * Static instance
     * @var Config
     */
    private static $INSTANCE = null;
    
    /**
     * Build mode Either development or release
     */
    private $mode = "development";

    /**
     * Site name URL
     */
    private $sitename = "localhost";

    /**
     * Database IP address
     */
    private $databaseIP = "localhost";

    /**
     * Database port
     */
    private $databasePort = 3306;
    
    /**
     * Database user name
     */
    private $databaseUser = "root";

    /**
     * Database password
     */
    private $databasePassword = "password";

    /**
     * Database name
     */
    private $databaseName = "db";

    /**
     * Forces tables to be recreated after build even if they already exists
     */
    private $forceDatabaseTableRecreation = false;

    /**
     * Root username
     */
    private $rootUsername = "root";

    /**
     * Root password
     */
    private $rootPassword = "root";

    /**
     * Whether to attempt to CREATE DATABASE upon setup
     */
    private $attemptToCreateDatabase = FALSE;

    /**
     * Google Static Maps URL
     */
    private $mapsURL = "no";

    /**
     * Default language ID
     */
    private $defaultLanguage = 0;

    /**
     * Base language ID
     */
    private $baseLanguage = 0;

    /**
     * Languages file names
     */
    private $languages = "";

    public function __construct()
    {
        $str = Utils::readFile(Config::CONFIG_FILE, "");

        if(empty($str))
        {
            die("Config file " . Config::CONFIG_FILE . " does not exists!");
        }

        $json = json_decode($str, TRUE);

        if($json === NULL)
        {
            die(Config::CONFIG_FILE . " is not a valid JSON format: " . json_last_error_msg());
        }

        $this->mode = $json["mode"]; 
        if($this->mode !== Config::MODE_DEVELOPMENT && $this->mode !== Config::MODE_RELEASE)
        {
            die("Invalid config mode!");
        }

        foreach ($this as $key => $value) 
        {
            if($key === "mode" || !isset($json[$key]))
            {
                continue;
            }

            $val = $json[$key];

            if(is_array($val))
            {
                $val = $val[$this->mode];
            }

            $this->$key = $val;
        }
    }

    public function isDevelopment(): bool
    {
        return $this->mode === "development";
    }

    public function isRelease(): bool
    {
        return $this->mode === "release";
    }

    public static function init()
    {
        if(Config::$INSTANCE === NULL)
        {
            Config::$INSTANCE = new Config();
        }
    }

    public static function isDevelopmentMode(): bool
    {
        return Config::$INSTANCE->isDevelopment();
    }

    public static function isReleaseMode(): bool
    {
        return Config::$INSTANCE->isRelease();
    }

    public static function getSitename(): string
    {
        return Config::$INSTANCE->sitename;
    }

    public static function getDatabaseIP(): string
    {
        return Config::$INSTANCE->databaseIP;
    }

    public static function getDatabasePort(): int
    {
        return Config::$INSTANCE->databasePort;
    }

    public static function getDatabaseUser(): string
    {
        return Config::$INSTANCE->databaseUser;
    }

    public static function getDatabasePassword(): string
    {
        return Config::$INSTANCE->databasePassword;
    }
    
    public static function getDatabaseName(): string
    {
        return Config::$INSTANCE->databaseName;
    }
    
    public static function forceTableRecreation(): bool
    {
        return Config::$INSTANCE->forceDatabaseTableRecreation;
    }

    public static function shouldAttemptToCreateDatabase(): bool
    {
        return Config::$INSTANCE->attemptToCreateDatabase;
    }

    public static function getRootUsername()
    {
        return Config::$INSTANCE->rootUsername;   
    }
    
    public static function getRootPassword()
    {
        return Config::$INSTANCE->rootPassword;   
    }
    
    public static function getGoogleMapsURL()
    {
        return Config::$INSTANCE->mapsURL;   
    }
    
    public static function getDefaultLanguageID()
    {
        return Config::$INSTANCE->defaultLanguage;
    }

    public static function getLanguages()
    {
        return Config::$INSTANCE->languages;
    }
    
}

?>