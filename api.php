<?php

define("ERROR_HANDLER_USE_API_REPORTER", true);

// We dont need render configuration for API endpoints
define("PREVENT_RENDER", true);

// We dont need Language configuration for API endpoints
define("PREVENT_LANG", true);

require_once("vendor/backend.php");

API::register(new ArticleEndpoint());
API::register(new UserEndpoint());
API::register(new PageEndpoint());
API::register(new MediaEndpoint());
API::register(new FeedbackEndpoint());
API::register(new EventEndpoint());
API::invoke($_POST, $_GET);

?>