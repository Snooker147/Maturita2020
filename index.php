<?php
    require_once("vendor/backend.php");

    $headerCfg = Utils::getYaml("usercfg/header.yml");
    $sponsorsCfg = Utils::getYaml("usercfg/sponsors.yml");
    $mediaCfg = Utils::getYaml("usercfg/socialmedia.yml");
    
    $db = DatabaseManager::create();
    $pageHandler = new PageHandler($db);

    $lang = Lang::getInstance();

    $articles = $db->paginateArticlesPreview($lang->getLanguage(), 0, 4);

    $contents = Render::get("Index.twig", [
        "headerCfg" => $headerCfg,
        "sponsorsCfg" => $sponsorsCfg,
        "mediaCfg" => $mediaCfg,
        "pageHandler" => $pageHandler,
        "pageName" => Utils::getRequestURL(),
        "mapURL" => Config::getGoogleMapsURL(),
        "articles" => $articles->items
    ]);

    $title = $lang->get("DomainTitle");
    $deliveredTitle = MetaData::getInstance()->get("title");

    if($deliveredTitle !== NULL)
    {
        $title = "$title - $deliveredTitle";
    }
    
    $header = Render::get("Header.twig", [ 
        "title" => $title,
        "script" => "bundle",
        "css" => "bundle"  
    ]);

    echo $header;
    echo $contents;
?>