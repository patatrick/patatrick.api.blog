<?php
namespace App\Middlewares;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use App\Helpers\TokenJWT;
class AuthMiddleware extends TokenJWT
{
	public function __invoke(Request $request, RequestHandler $handler): Response
	{
		$response = new Response();
		try
		{
			$this->UpdateJWT($request->getHeaderLine('Authorization'));
			return $handler->handle($request);
		}
		catch (\Throwable $e) {
			$response->getBody()->write(json_encode(["status"=> 401, "message"=> $e->getMessage()]));
			return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
		}
	}
}