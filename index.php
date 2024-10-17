<?php
use Slim\Factory\AppFactory;
use App\Middlewares\CustomCorsMiddleware;
use App\Routes;

const PRODUCTION = false;

# Cabeceras
	header('Content-Type: application/json');
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Max-Age: 86400');
	header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
	date_default_timezone_set('America/Santiago');

# autoLoad
	require __DIR__ . "/vendor/autoload.php";
	if (PRODUCTION === false) {
		ini_set('display_errors', 1);
		error_reporting(-1);
	}
	(Dotenv\Dotenv::createImmutable(__DIR__))->load();

# Inicio de SlimFramework v4
	$app = AppFactory::create();
	$app->addBodyParsingMiddleware();
	if (PRODUCTION === false) {
		$app->setBasePath(basePath: "/patatrick.api.blog");
		$app->addErrorMiddleware(true, true, true);
	}
	else {
		$app->add(new CustomCorsMiddleware());
		$app->addErrorMiddleware(false, false, false);
	}

// Carga de rutas
	Routes::Login($app);
	Routes::Entried($app);
	Routes::Menu($app);
	Routes::Hashtag($app);

$app->run();