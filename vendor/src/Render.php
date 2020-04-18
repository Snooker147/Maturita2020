<?php

class Render
{
    /**
     * TWIG loader
     * @var \Twig\Loader\FilesystemLoader
     */
    private static $LOADER = null;

    /**
     * TWIG instance
     * @var \Twig\Environment
     */
    private static $TWIG = null;

    public static function get(string $templateName, array $vars = NULL)
    {
        if($vars === NULL)
        {
            $vars = array();
        }

        return Render::$TWIG->load($templateName)->render($vars);
    }

    public static function print(string $templateName, array $vars = NULL)
    {
        echo Render::get($templateName, $vars);
    }

    public static function printFullPage(string $templateName, array $vars = NULL)
    {
        echo "<html>";
        Render::print($templateName, $vars);
        echo "</html>";
    }

    public static function printJSONAndExit(string $json)
    {
        Render::printJSON($json);
        exit;
    }

    public static function printJSON(string $json)
    {
        header("Content-Type: application/json");
        echo $json;
    }

    public static function init()
    {
        if(Render::$TWIG !== NULL)
        {
            return;
        }

        $config = array();
        
        if(Config::isReleaseMode())
        {
            $config["cache"] = Utils::getPathRelativeToBase("cache");
        }

        $paths = [
            "shared",
            "admin"
        ];

        $pathsRelative = [ Utils::getPathRelativeToBase("src/frontend") ];

        foreach ($paths as $value)
        {
            $pathsRelative[] = Utils::getPathRelativeToBase("src/frontend/$value");    
        }

        Render::$LOADER = new \Twig\Loader\FilesystemLoader($pathsRelative);
        Render::$TWIG = new \Twig\Environment(Render::$LOADER, $config);
        
        Render::$TWIG->addGlobal("lang", Lang::getInstance());
        Render::$TWIG->addGlobal("utils", new FrontendUtils());
        Render::$TWIG->addGlobal("isDevelopment", Config::isDevelopmentMode());
        Render::$TWIG->addGlobal("isRelease", Config::isReleaseMode());
        Render::$TWIG->addGlobal("currentYear", strftime("%Y"));
        Render::$TWIG->addGlobal("serverMetaData", MetaData::getInstance());

        Render::$TWIG->addGlobal("argsGet", new APIArguments("RenderGET", $_GET));
        Render::$TWIG->addGlobal("argsPost", new APIArguments("RenderPOST", $_POST));
    }

}

?>