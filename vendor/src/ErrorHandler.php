<?php

class ErrorHandler
{
    private const DEF_API_REPORTER = "ERROR_HANDLER_USE_API_REPORTER"; 

    public static function reportException(Throwable $e) 
    {
        ErrorHandler::report($e->getMessage(), $e->getTrace());
    }

    public static function reportError(string $msg, string $detailedDevMessage = "")
    {
        ErrorHandler::report($msg, debug_backtrace(40), $detailedDevMessage);
    }

    private static function report(string $message, $trace, string $detailed = "")
    {
        // when entering the last report func, we should remove custom handler
        // otherwise we might go to loop
        set_exception_handler(NULL);
        set_error_handler(NULL);

        $reportObj = array(
            "message" => $message,
            "detailed-message" => $detailed,
            "date" => Utils::getReadableTime(),
            "trace" => $trace
        );

        $flname = Config::isDevelopmentMode() ? "last_development.txt" : ("report_" . strftime("%d_%m_%Y-%H_%M_%S") . ".txt");

        Utils::writeFile("errors/$flname", json_encode($reportObj, JSON_PRETTY_PRINT));
        
        if(!defined(ErrorHandler::DEF_API_REPORTER))
        {
            Utils::redirect("error?message=" . Utils::safeURL($message) . "&referer=" . Utils::safeURL($_SERVER["REQUEST_URI"]));
        }

        API::finishInvoke($_GET, API::getResponseType(NULL), API::getReturnURL(NULL), NULL, new RuntimeException($message));
    }

    public static function init()
    {
        function errorHandlerExceptionHandler(Throwable $e)
        {
            ErrorHandler::reportException($e);
        }

        function errorHandlerPHPWarning(int $errno, string $errstr, string $errfile)
        {
            ErrorHandler::reportError($errstr, $errfile);
        }
        
        set_exception_handler("errorHandlerExceptionHandler");
        set_error_handler("errorHandlerPHPWarning");
    }

}


?>