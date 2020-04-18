<?php

function initBackendLibraries(string $clsName)
{
    if(defined("PREVENT_" . strtoupper($clsName)))
    {
        return;
    }

    call_user_func(array($clsName, "init"));
}

initBackendLibraries("Config");
initBackendLibraries("ErrorHandler");
initBackendLibraries("MetaData");
initBackendLibraries("Lang");
initBackendLibraries("Render");
initBackendLibraries("Session");
initBackendLibraries("UploadedFileResource");    

?>