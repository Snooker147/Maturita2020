<?php
    require_once("vendor/backend.php");
    
    $getArgs = new APIArguments("LoginForm", $_GET);
    $redirect = $getArgs->getString("redirectURL", NULL, NULL, "");

    if(Session::isLoggedIn())
    {
        Utils::redirect($redirect);
    }

    Render::printFullPage("Login.twig", [
        "hasLoginError" => $getArgs->has("error"),
        "loginError" => $getArgs->getString("error", NULL, NULL, ""),
        "redirectTo" => $redirect,
        "redirectToFailedEncoded" => urlencode("login?redirectURL=$redirect")
    ]);
?>