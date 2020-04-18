<?php

use Symfony\Component\Yaml\Yaml;

class Lang
{
    private const COOKIE_NAME = "usrlang";


    /** @var Lang */
    private static $instance = NULL;

    /**
     * @var string[]
     */
    private $registry;

    /**
     * @var int[]
     */
    private $langs;

    /** @var int */
    private $currentLangIdCache = NULL;

    public function __construct()
    {
        $this->langs = [];

        $configuredLangs = explode(",", Config::getLanguages());

        for($i = 0, $len = count($configuredLangs); $i < $len; $i++)
        {
            $this->langs[$configuredLangs[$i]] = $i;
        }

        $base = $this->getLanguageRegistry(Config::getDefaultLanguageID());
        $current = $this->getLanguage();

        if($current !== Config::getDefaultLanguageID())
        {
            Utils::mergeArray($base, $this->getLanguageRegistry($current));    
        }

        $this->registry = $base;
    }

    /**
     * If NULL is provided as $key empty string is returned
     */
    public function get(?string $key, ...$vars)
    {
        if($key === NULL)
        {
            return "";
        }

        if(array_key_exists($key, $this->registry))
        {
            $val = $this->registry[$key];

            if(is_array($val))
            {
                $val = implode(strpos($key, "NoBreaks") === 0 ? " " : "<br />", $val);
            }
            
            if(count($vars) > 0)
            {
                return sprintf($val, ...$vars);
            }

            return $val;
        }


        return $key;
    }

    private function getLanguageRegistry(int $id)
    {
        $name = $this->languageIdToName($id);
        $flname = "usercfg/lang/$name.yml";
        $contents = Utils::readFile($flname);
        
        $ret = Yaml::parse($contents);

        if($ret === NULL)
        {
            return [];
        }

        return $ret;
    }

    /**
     * Translates a language name to it's id
     * @param string $name Name to translate
     * @param string $def Default value to return if invalid name (if set to NULL, default language is returned)
     * @return int Language ID
     */
    public function languageNameToId(string $name, int $def = NULL)
    {
        if(array_key_exists($name, $this->langs))
        {
            return $this->langs[$name];
        }

        if($def === NULL)
        {
            return Config::getDefaultLanguageID();
        }

        return $def;
    }

    /**
     * Translates a language id to name
     * @param int $id Id to translate
     * @param string $def Default value to return if invalid id (if set to NULL, default language is returned)
     * @return string Language name
     */
    public function languageIdToName(int $id, string $def = NULL)
    {
        foreach ($this->langs as $key => $value) 
        {
            if($value === $id)
            {
                return $key;
            }    
        }

        if($def === NULL)
        {
            return $this->languageIdToName(Config::getDefaultLanguageID(), "unknown");
        }

        return $def;
    }

    public function setLanguage(string $name)
    {
        setcookie(Lang::COOKIE_NAME, $name, time() + 60 * 60 * 24 * 365);
    }

    /**
     * Returns the current language id
     */
    public function getLanguage()
    {
        if($this->currentLangIdCache !== NULL)
        {
            return $this->currentLangIdCache;
        }

        $id = Config::getDefaultLanguageID();

        if(isset($_COOKIE[Lang::COOKIE_NAME]))
        {
            $val = strval($_COOKIE[Lang::COOKIE_NAME]);
            $id = $this->languageNameToId($val);
        }

        $this->currentLangIdCache = $id;

        return $id;
    }

    public function getDefaultLanguageID()
    {
        return Config::getDefaultLanguageID();
    }

    public function getRegisteredLanguages()
    {
        return $this->langs;
    }

    public function getRegisteredLanguagesAsSortifiedArray()
    {
        $arr = [];

        foreach ($this->langs as $key => $value) 
        {
            $arr[] = [
                "name" => "PageLanguage" . ucfirst($key),
                "value" => $value
            ];
        }

        return $arr;
    }

    public static function init()
    {
        if(Lang::$instance !== NULL)
        {
            return;
        }

        Lang::$instance = new Lang();
    }

    public static function getInstance(): Lang
    {
        return Lang::$instance;
    }

}


?>