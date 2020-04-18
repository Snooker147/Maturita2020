<?php

class FrontendUtils
{
    public function ucFirst(string $a)
    {
        return ucfirst($a);
    }

    public function getUCFirst(string $any)
    {
        return ucfirst($any);
    }

    public function getCurrentDay()
    {
        return intval(strftime("%d"));
    }

    public function getCurrentMonth()
    {
        return intval(strftime("%m"));
    }

    public function getCurrentYear()
    {
        return intval(strftime("%Y"));
    }

    public function getFileReadableDate()
    {
        return strftime("%d-%m-%Y---%H-%M-%S");
    }

    public function toReadableDate(string $dt)
    {
        $unix = strtotime($dt);
        return strftime("%d.%m.%Y %H:%M", $unix);
    }

    public function toReadableShortDate(string $dt)
    {
        $unix = strtotime($dt);
        return strftime("%d.%m.%Y", $unix);
    }

    public function toReadableTime(int $dt)
    {
        return strftime("%d.%m.%Y %H:%M", $dt);
    }

    public function toReadableShortTime(int $dt)
    {
        return strftime("%d.%m.%Y", $dt);
    }

    public function getDateDay(int $dt)
    {
        return strftime("%d", $dt);
    }

    public function getDateMonth(int $dt)
    {
        return strftime("%m", $dt);
    }

    public function getDateYear(int $dt)
    {
        return strftime("%Y", $dt);
    }

    public function getDateHour(int $dt)
    {
        return strftime("%H", $dt);
    }

    public function getDateMinute(int $dt)
    {
        return strftime("%M", $dt);
    }

    public function debugVariable($any)
    {
        var_dump($any);
    }
    
    public function clamp($val, $min, $max)
    {
        if($val < $min)
        {
            return $min;
        }
        else if($val > $max)
        {
            return $max;
        }

        return $val;
    }

    public function redirectIf(bool $condition, string $redirectTo)
    {
        if($condition)
        {
            Utils::redirect($redirectTo);
        }
    }

    public function conClass(bool $condition, string $base, string $addition)
    {
        if(!$condition)
        {
            return $base;
        }
        
        return "$base $addition";
    }

    public function getHumanReadableFileName(string $flname)
    {
        $pos = strrpos($flname, ".");

        if($pos === FALSE)
        {
            return $flname;
        }

        return substr($flname, 0, $pos);
    }
    
}

?>