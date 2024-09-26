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
		try
		{
			$response = new Response();
			if ($this->UpdateJWT($request->getHeaderLine('Authorization')) == "") {
				$response->getBody()->write(json_encode($this->errObj));
				return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
			}
			return $handler->handle($request);
		}
		catch (\Throwable $e) {
			echo json_encode([ "status"=> 500, "message"=> $e->getMessage()." ".$e->getFile()." on line ".$e->getLine() ]);
			http_response_code(500);
			die();
		}
	}
}