<?php
namespace App\Controllers;
use App\Helpers\TokenJWT;
use App\Services\ComentarioService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ComentarioController
{
	private readonly ComentarioService $callComentarioService;
	public function __construct() {
		$this->callComentarioService = new ComentarioService();
	}
	public function GetAllByIdEntried(Request $request, Response $response, array $getData) : Response
	{
		try
		{
			$id_entried = (int) $getData["id"];
			$arrComentario = $this->callComentarioService->GetAllByIdEntried($id_entried);
			$response->getBody()->write(json_encode([
				"status" => 200,
				"data" => $arrComentario
			]));
			return $response;
		}
		catch (\Throwable $ex) {
			http_response_code(500);
			die(json_encode([ "status"=> 500, "message"=> $ex->getMessage() ]));
		}
	}
}