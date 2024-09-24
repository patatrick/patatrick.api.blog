<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
date_default_timezone_set('America/Santiago');

require __DIR__ . "/vendor/autoload.php";
const PRODUCTION = false;
if (PRODUCTION === false) {
	ini_set('display_errors', 1);
	error_reporting(-1);
}
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

use App\Controllers\HelloController;
use App\Controllers\LoginController;

// use App\Middlewares\AuthMiddleware;

$app = AppFactory::create();
if (PRODUCTION === false) {
	$app->setBasePath("/patatrick.api.blog");
	$app->addErrorMiddleware(true, true, true);
}
else {
	$app->addErrorMiddleware(false, false, false);
}

$app->addBodyParsingMiddleware();
// $app->add(new CustomCorsMiddleware());


// Routes
(Dotenv\Dotenv::createImmutable(__DIR__))->load();
$app->get('/hello', [HelloController::class, "Index"]);
$app->group('/login', function (RouteCollectorProxy $group)
{
	$group->get('', [LoginController::class, "Index"]);
	$group->get('/callback', [LoginController::class, "Callback"]);
	$group->get('/logout', [LoginController::class, "Logout"]);
});


$app->run();