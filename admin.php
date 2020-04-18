<?php
    require_once("vendor/backend.php");

    if(!Session::isLoggedIn())
    {
        Utils::redirect("login?redirectURL=admin");
    }

    $args = new APIArguments("AdminPage", $_GET);

    $pages = [ 
        "page", 
        "pageedit",
        
        "article",
        "articleedit",

        "calendar",
        "calendaredit",

        "uploads",

        "feedback",
        "feedbackdetail"
    ];
    
    $page = ucfirst(strtolower($args->getEnum("page", $pages, "page")));
    
    Render::printFullPage("Admin.twig", [ 
        "page" => "AdminSection$page.twig",
        "selfRedirect" => urlencode("admin?page=$page"),
        "error" => $args->getString("error", NULL, NULL, ""),
        "db" => new DatabaseManager(),
    ]);
?>