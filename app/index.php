<?php
include_once("Handlers.php");
include_once("Router.php");

$app = new Router;
$app->route("POST", "/api/generate/", "generate_value");///api/retrieve/
$app->route("GET", "/api/retrieve/", "get_value");
$app->run();