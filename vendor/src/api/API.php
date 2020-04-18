<?php


class API
{
    public const RESPONSE_TYPE_RETURN_URL           = "return";
    public const RESPONSE_TYPE_RETURN_JSON          = "json";
    
    public const POSSIBLE_RESPONSE_TYPES = [
        API::RESPONSE_TYPE_RETURN_URL,
        API::RESPONSE_TYPE_RETURN_JSON
    ];

    /**
     * Endpoints array
     * @var APIEndpoint[]
     */
    private static $endpoints = array();

    public static function register(APIEndpoint $a)
    {
        API::$endpoints[$a->getName()] = $a;
    }

    public static function invoke(array $post, array $get)
    {
        $responseType = API::getResponseType($get);
        
        $standardReturnURL = API::getReturnURL($get);
        $returnURLFailed = API::getFromSuperArray($get, APIEndpoint::GET_RETURN_URL_FAILED);
        $returnURLSuccess = API::getFromSuperArray($get, APIEndpoint::GET_RETURN_URL_SUCCEED);

        if($returnURLFailed === NULL)
        {
            $returnURLFailed = $standardReturnURL;
        }

        if($returnURLSuccess === NULL)
        {
            $returnURLSuccess = $standardReturnURL;
        }

        if(!isset($get[APIEndpoint::GET_ENDPOINT_NAME]))
        {
            API::finishInvoke($post, $responseType, $returnURLFailed, NULL, new RuntimeException("UnspecifiedEndpoint"));
        }

        if(!isset($get[APIEndpoint::GET_METHOD_NAME]))
        {
            API::finishInvoke($post, $responseType, $returnURLFailed, NULL, new RuntimeException("UnspecifiedMethod"));
        }

        $endpointName = $get[APIEndpoint::GET_ENDPOINT_NAME];
        $methodName = $get[APIEndpoint::GET_METHOD_NAME];

        if(!array_key_exists($endpointName, API::$endpoints))
        {
            API::finishInvoke($post, $responseType, $returnURLFailed, NULL, new RuntimeException("UnknownEndpoint"));
        }

        $endpoint = API::$endpoints[$endpointName];
        $method = $endpoint->getMethod($methodName);

        if($method === NULL)
        {
            API::finishInvoke($post, $responseType, $returnURLFailed, NULL, new RuntimeException("UnknownMethod"));
        }

        try
        {
            $db = DatabaseManager::create();
            $endpoint->setDatabase($db);
            $returnData = $endpoint->invoke($method, $post);
            
            API::finishInvoke($post, $responseType, $returnURLSuccess, $returnData, NULL);
        }
        catch(Exception $e)
        {
            API::finishInvoke($post, $responseType, $returnURLFailed, NULL, $e);
        }
    }

    /**
     * Finished invoke
     * @param array $args Args to return
     * @param string $type RESPONSE_TYPE constant
     * @param string $returnURL When RESPONSE TYPE is RETURN_URL this is the url to return to
     * @param mixed|null $returnData Data to send back
     * @param Exception|null $e Exception that has been thrown
     */
    public static function finishInvoke(array $args, string $type, string $returnURL, $returnData, $e)
    {
        if($type === API::RESPONSE_TYPE_RETURN_URL)
        {
            if(is_array($returnData))
            {
                $returnData = json_encode($returnData);
            }
            else if(is_object($returnData))
            {
                ErrorHandler::reportError("Return URL response type does not support non-primitive returnData");
            }
        }

        if($e !== NULL)
        {
            $err = $e->getMessage();

            if($type === API::RESPONSE_TYPE_RETURN_JSON)
            {
                Lang::init();

                Render::printJSONAndExit(Utils::toJsonString([
                    "error" => $err,
                    "errorTranslated" => Lang::getInstance()->get($err)
                ]));
            }
            
            $err = urlencode($err);
            $imploder = strpos($returnURL, "?") === FALSE ? "?" : "&";

            //$encodedArgs = urlencode(json_encode($args));
            //Utils::redirect("$returnURL${imploder}error=$err&args=$encodedArgs");
            Utils::redirect("$returnURL${imploder}error=$err");
        }

        if($type === API::RESPONSE_TYPE_RETURN_JSON)
        {
            $js = [ "data" => $returnData ];

            if($returnData instanceof APICustomResponse)
            {
                $js = $returnData->getReturnData();
            }
            
            Render::printJSONAndExit(Utils::toJsonString($js));
        }

        $pushToEndUrl = "";

        if($returnData !== NULL)
        {
            $imploder = strpos($returnURL, "?") === FALSE ? "?" : "&";
            $pushToEndUrl = "${imploder}data=" . urlencode($returnData);
        }

        Utils::redirect("$returnURL$pushToEndUrl");
    }

    public static function getResponseType(?array $src, string $def = API::RESPONSE_TYPE_RETURN_JSON)
    {
        if($src === NULL)
        {
            $src = $_GET;
        }

        $responseType = $def;
        
        if(isset($src[APIEndpoint::GET_RESPONSE_TYPE]))
        {
            $responseType = $src[APIEndpoint::GET_RESPONSE_TYPE];
        }

        if(!Utils::isOneOf($responseType, API::POSSIBLE_RESPONSE_TYPES))
        {
            $responseType = $def;
        }

        return $responseType;
    }

    public static function getReturnURL(?array $src, string $def = "index")
    {
        if($src === NULL)
        {
            $src = $_GET;
        }

        $returnURL = $def;

        if(isset($_SERVER["HTTP_REFERER"]))
        {
            $returnURL = $_SERVER["HTTP_REFERER"];
        }

        if(isset($src[APIEndpoint::GET_RETURN_URL]))
        {
            $returnURL = $src[APIEndpoint::GET_RETURN_URL];
        }

        return $returnURL;
    }

    private static function getFromSuperArray(?array $src, string $key)
    {
        if($src === NULL)
        {
            $src = $_GET;
        }

        if(isset($src[$key]))
        {
            return $src[$key];
        }

        return null;
    }

}


?>