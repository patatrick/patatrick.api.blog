<?php 
namespace App\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class HelloController
{
	public function Index(Request $request, Response $response, array $getData) : Response
	{
		try
		{
			$response->getBody()->write("Hello World!");
			return $response;
		}
		catch (\Throwable $th) {
			$response->getBody()->write(json_encode([
				"status"=> 500, "message"=> $th->getMessage()
			]));
			$response->withStatus(500);
			return $response;
		}
	}
}