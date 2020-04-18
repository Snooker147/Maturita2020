<?php

error_reporting(0);

require_once("vendor/autoload.php");

require_once("src/ErrorHandler.php");
require_once("src/Utils.php");
require_once("src/Config.php");
require_once("src/Render.php");
require_once("src/Session.php");
require_once("src/Log.php");
require_once("src/MetaData.php");
require_once("src/FrontendUtils.php");

require_once("src/resource/IResource.php");
require_once("src/resource/ResourceManager.php");

require_once("src/lang/Lang.php");
require_once("src/lang/PageHandler.php");

require_once("src/database/SimpleDatabaseManager.php");
require_once("src/database/Database.php");
require_once("src/database/DatabaseDocument.php");
require_once("src/database/DatabaseField.php");
require_once("src/database/DatabaseManager.php");
require_once("src/database/DatabaseQueryResult.php");
require_once("src/database/DatabaseTable.php");

require_once("src/api/utils/UploadedFile.php");
require_once("src/api/utils/UploadedFileResource.php");
require_once("src/api/API.php");
require_once("src/api/APIArguments.php");
require_once("src/api/APICustomResponse.php");
require_once("src/api/APIException.php");
require_once("src/api/APIEndpoint.php");
require_once("src/api/ArticleEndpoint.php");
require_once("src/api/UserEndpoint.php");
require_once("src/api/PageEndpoint.php");
require_once("src/api/MediaEndpoint.php");
require_once("src/api/FeedbackEndpoint.php");
require_once("src/api/EventEndpoint.php");

require_once("src/data/Article.php");
require_once("src/data/User.php");
require_once("src/data/Page.php");
require_once("src/data/Event.php");
require_once("src/data/Feedback.php");

require_once("src/Init.php");

?>