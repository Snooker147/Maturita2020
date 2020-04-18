<?php

use Symfony\Component\Yaml\Yaml;

class Utils
{

    private static $idCounter = 0;

    public static function getUniqueID()
    {
        return Utils::$idCounter++;
    }

    public static function translateUTCWeekdayToEuropeWeekday(int $weekday)
    {
        $n = $weekday - 1;

        if($n < 0)
        {
            $n = 6;
        }

        return $n;
    }   


    /**
     * Maps a string array into another string arr
     * @param string[] $arr String array to map
     * @return string[]
     */
    public static function map(array $arr, callable $fn)
    {
        $ret = [];

        foreach ($arr as $value) 
        {
            $ret[] = $fn($value);
        }

        return $ret;
    }

    public static function toJsonString($any)
    {
        return json_encode($any, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public static function isOneOf($value, array $oneOf)
    {
        return Utils::indexOf($value, $oneOf) != -1;
    }

    /**
     * Returns an index of the oneOf array value coresponds to or -1
     */
    public static function indexOf($value, array $oneOf)
    {
        for($i = 0, $len = count($oneOf); $i < $len; $i++)
        {
            if($oneOf[$i] === $value)
            {
                return $i;
            }
        }     

        return -1;
    }

    public static function find(array $arr, $any)
    {
        $index = Utils::findIndex($arr, $any);

        if($index === -1)
        {
            return null;
        }

        return $arr[$index];
    }

    public static function findIndex(array $arr, $any)
    {
        for($i = 0, $len = count($arr); $i < $len; $i++)
        {
            if($arr[$i] === $any)
            {
                return $i;
            }
        }

        return -1;
    }

    public static function findIndexOrDefault(array $arr, $needle, $def = 0)
    {
        for($i = 0, $len = count($arr); $i < $len; $i++)
        {
            if($arr[$i] === $needle)
            {
                return $i;
            }
        }

        return $def;
    }

    public static function findByKeyOrDefault(array $arr, $key, $defValueToReturn = NULL)
    {
        if(array_key_exists($key, $arr))
        {
            return $arr[$key];
        }
        
        return $defValueToReturn;
    }

    public static function forEach(array $arr, callable $fn): int
    {
        $len = count($arr);

        for($i = 0; $i < $len; $i++)
        {
            $fn($arr[$i], $i);
        }

        return $len;
    }

    public static function safeURL(string $url)
    {
        return urlencode($url);
    }

    public static function safeEcho(string $any)
    {
        echo htmlspecialchars($any);
    }

    public static function redirect(string $url)
    {
        if(strpos($url, "http://") === FALSE && strpos($url, "https://") === FALSE)
        {
            $url = "/$url";
        }
        
        Header("Location: $url");
        exit();
    }

    public static function getReadableTime(): string
    {
        return strftime("%d.%m.%Y %H:%M:%S");
    }

    public static function getSpecificReadableTime(int $timestemp): string
    {
        return strftime("%d.%m.%Y %H:%M:%S", $timestemp);
    }

    /**
     * Reads file into string
     */
    public static function readFile(string $pathRelativeToBase, ?string $def = ""): string
    {
        $path = Utils::getPathRelativeToBase($pathRelativeToBase);

        if(!file_exists($path))
        {
            return $def;
        }

        $handle = fopen($path, "r");
        $size = filesize($path);
        $content = fread($handle, $size);
        fclose($handle);

        if($content === FALSE)
        {
            return $def;
        }
        
        return $content;
    }

    /**
     * Writes string into file and returns whether it was successful or not 
     */
    public static function writeFile(string $pathRelativeToBase, string $content): bool
    {
        $path = Utils::getPathRelativeToBase($pathRelativeToBase);

        $handle = fopen($path, "w");
        $res = fwrite($handle, $content);
        fclose($handle);
        
        return $res === FALSE ? FALSE : TRUE;
    }

    /**
     * Writes string into file and returns whether it was successful or not 
     */
    public static function appendFile(string $pathRelativeToBase, string $content): bool
    {
        $path = Utils::getPathRelativeToBase($pathRelativeToBase);

        $handle = fopen($path, "a");
        $res = fwrite($handle, $content);
        fclose($handle);
        
        return $res === FALSE ? FALSE : TRUE;
    }

    public static function copyDir(string $srcRelativeToBase, string $dstRelativeToBase)
    {
        $src = Utils::getPathRelativeToBase($srcRelativeToBase);
        $dst = Utils::getPathRelativeToBase($dstRelativeToBase);

        if(!is_dir($src))
        {
            throw new InvalidArgumentException("$src must be directory");
        }

        if(!is_dir($dst))
        {
            throw new InvalidArgumentException("$dst must be directory");
        }
        
        $srcContents = array_diff(scandir($src), array("..", "."));
        
        foreach($srcContents as $file)
        {
            copy("$src/$file", "$dst/$file");
        }
    }

    /**
     * Relative to htdocs/vendor
     */
    public static function getPathRelativeToBase(string $rel): string
    {
        if(DIRECTORY_SEPARATOR === "\\")
        {
            $rel = str_replace("/", DIRECTORY_SEPARATOR, $rel);
        }
        
        return realpath(__DIR__ . "/../") . DIRECTORY_SEPARATOR . $rel;
    }

    public static function mergeArray(array &$dst, array $other)
    {
        foreach ($other as $key => $value) 
        {
            $dst[$key] = $value;
        }
    }

    public static function hashPassword(string $pwd)
    {
        return password_hash($pwd, PASSWORD_BCRYPT);
    }

    public static function verifyPassword(string $pwd, string $hashed)
    {
        return password_verify($pwd, $hashed);
    }

    public static function getYaml(string $file, bool $checkUserOverrides = true)
    {
        $contents = Utils::readFile($file);
        $arr = Yaml::parse($contents);

        if(!$checkUserOverrides)
        {
            return $arr;
        }
        
        $dotPos = strrpos($file, ".");
        if($dotPos !== FALSE)
        {
            $userFileName = substr($file, 0, $dotPos) . "--user.yml";
            $anotherContents = Utils::readFile($userFileName, "");

            if(!empty($anotherContents))
            {
                Utils::mergeArray($arr, Yaml::parse($anotherContents));
            }
        }
        
        return $arr;
    }

    public static function getRequestURL()
    {
        $url = $_SERVER["REQUEST_URI"];
        
        if($url[0] === "/")
        {
            $url = mb_substr($url, 1);
        }

        $queryPos = mb_strrpos($url, "?");
        
        if($queryPos !== FALSE)
        {
            $url = substr($url, 0, $queryPos);
        }

        $dotPos = mb_strrpos($url, ".");

        if($dotPos !== FALSE)
        {
            $url = substr($url, 0, $dotPos);
        }

        return mb_strtolower($url);
    }

    public static function getDomainName()
    {   
        if(array_key_exists("HTTP_HOST", $_SERVER))
        {
            return $_SERVER["HTTP_HOST"];
        }

        return $_SERVER["SERVER_NAME"];
    }

}