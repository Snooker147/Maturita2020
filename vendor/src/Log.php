<?php

class Log
{

    private static $path = NULL;

    public static function message(string $msg)
    {
        if(Log::$path === NULL)
        {
            Log::init();
        }

        $dt = Utils::getReadableTime();
        Utils::appendFile(Log::$path, "$dt : $msg\n");
    }   

    private static function init()
    {
        if(Config::isReleaseMode())
        {
            return;
        }

        Log::$path = Log::getFileName();
        Utils::writeFile(Log::$path, "Source Log for " . Utils::getReadableTime() . "\n");
    }

    private static function getFileName()
    {
        return "errors/last_development_log.txt";
    }

}