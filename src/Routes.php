<?php
namespace App;
use App\Controllers\ComentarioController;
use App\Controllers\HashtagController;
use App\Controllers\MenuController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

use App\Middlewares\AuthMiddleware;
use App\Controllers\EntriedController;
use App\Controllers\HelloController;
use App\Controllers\LoginController;
final class Routes
{
	public static function hello(App $app): void
	{
		$app->get('/hello', [HelloController::class, "Index"]);
	}
	public static function Login(App $app): void
	{
		$app->group('/login', function (RouteCollectorProxy $group)
		{
			$group->get('', [LoginController::class, "Index"]);
			$group->get('/callback', [LoginController::class, "Callback"]);
			$group->get('/logout', [LoginController::class, "Logout"]);
			$group->get('/code/{code}/state/{state}', [LoginController::class, "GetUser"]);
		});
	}
	public static function Entried(App $app): void
	{
		$app->group('/entried', function (RouteCollectorProxy $group)
		{
			$group->get('', [EntriedController::class, "GetAll"]);
			$group->get('/menu/{menu}', [EntriedController::class, "GetAllMismoTipo"]);
			$group->get('/slug/{slug}', [EntriedController::class, "GetOne"]);
			$group->post('', [EntriedController::class, "Insert"])->add(new AuthMiddleware);
			$group->put('', [EntriedController::class, "Update"])->add(new AuthMiddleware);
			$group->delete('', [EntriedController::class, "Delete"])->add(new AuthMiddleware);
		});
	}
	public static function Menu(App $app): void
	{
		$app->get('/menu', [MenuController::class, "GetAll"]);
	}
	public static function Comentarios(App $app): void
	{
		$app->get('/comentarios/entrada/{id}', [ComentarioController::class, "GetAllByIdEntried"]);
	}
	public static function Hashtag(App $app): void
	{
		$app->get('/hashtag', [HashtagController::class, "GetAll"]);
	}
}
