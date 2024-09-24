<?php
const PRODUCTION = false;

if (PRODUCTION === false) {
	ini_set('display_errors', 1);
	error_reporting(-1);
}

date_default_timezone_set('America/Santiago');
require __DIR__ . "/vendor/autoload.php";
// Load our environment variables from the .env file:
(Dotenv\Dotenv::createImmutable(__DIR__))->load();

// Now instantiate the Auth0 class with our configuration:
$auth0 = new \Auth0\SDK\Auth0(configuration: [
    'domain' => $_ENV['AUTH0_DOMAIN'],
    'clientId' => $_ENV['AUTH0_CLIENT_ID'],
    'clientSecret' => $_ENV['AUTH0_CLIENT_SECRET'],
    'cookieSecret' => $_ENV['AUTH0_COOKIE_SECRET']
]);

print_r($auth0);
die();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

use App\Controllers\HelloController;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

// use App\Middlewares\AuthMiddleware;

$app = AppFactory::create();
if (PRODUCTION === false) {
	$app->setBasePath("/tienda.biotecnochile.api");
	$app->addErrorMiddleware(true, true, true);
}
else {
	$app->addErrorMiddleware(false, false, false);
}

$app->addBodyParsingMiddleware();
// $app->add(new CustomCorsMiddleware());


// Routes
$app->get('/hello', [HelloController::class, "Index"]);