<?php
namespace App\Middlewares;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Tuupola\Middleware\CorsMiddleware;

class CustomCorsMiddleware
{
	public function __invoke(Request $request, RequestHandler $handler)
	{
		try {
			$corsMiddleware = new CorsMiddleware([
				"origin" => explode(",", $_ENV["CORS"]),
				"methods" => ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
				"headers.allow" => ["Authorization", "Content-Type"],
				"headers.expose" => [],
				"credentials" => true,
				"origin.server" => null,
				"cache" => 0,
				"error" => function($request, $response, $error) {
					throw new HttpForbiddenException($request, "CORS not allowed");
				},
				"logger" => null,
			]);
			return $corsMiddleware->process($request, $handler);
		}
		catch (HttpException $ex) {
			http_response_code($ex->getCode());
			die(json_encode([ "status"=> $ex->getCode(), "message"=> $ex->getMessage() ]));
		}
		catch (\Throwable $ex) {
			http_response_code(500);
			die(json_encode([ "status"=> 500, "message"=> $ex->getMessage() ]));
		}
	}
}