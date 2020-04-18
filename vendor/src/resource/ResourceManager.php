<?php

class ResourceManager
{
    public const CACHE_FILE = "resources.json";

    private static $resources = array("__sample__" => FALSE);

    public static function register(IResource $r): bool
    {
        if(array_key_exists($r->getResourceName(), ResourceManager::$resources))
        {
            if(ResourceManager::$resources[$r->getResourceName()])
            {
                // Resource has already been built
                // no need to recreate them
                return FALSE;
            }
        }

        $raw = Utils::readFile(ResourceManager::CACHE_FILE, "");
        $cache = array();

        $createFile = empty($raw);

        // if the file has been already created
        // we check whether the resource has been previously built
        // if not, build it
        if(!$createFile)
        {
            $cache = json_decode($raw, TRUE);

            if(array_key_exists($r->getResourceName(), $cache) && $cache[$r->getResourceName()])
            {
                $r->finalizeBuild(FALSE);
                
                // resource has been built previously
                return FALSE;
            }
        }

        // ask the resource to build
        $r->build();
        $r->finalizeBuild(TRUE);

        // the resource should use ErrorHandler when handleing error
        // thus we expect it to be correct everytime we get to this line
        $cache[$r->getResourceName()] = TRUE;

        if(!Utils::writeFile(ResourceManager::CACHE_FILE, json_encode($cache, JSON_PRETTY_PRINT)))
        {
            ErrorHandler::reportError("Failed to save ResourceManager save file");
        }

        return TRUE;
    }

}


?>