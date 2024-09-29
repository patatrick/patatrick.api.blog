<?php
namespace App\Controllers;
use App\Helpers\TokenJWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Services\EntriedService;

class EntriedController extends TokenJWT
{
	private readonly EntriedService $CallEntriedService;
	public function __construct() {
		$this->CallEntriedService = new EntriedService();
	}
	public function GetAll(Request $request, Response $response, array $getData) : Response
	{
		try
		{
			$entradas = $this->CallEntriedService->GetAll();
			$response->getBody()->write(json_encode([
				"status" => 200,
				"data" => $entradas
			]));
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
	public function GetOne(Request $request, Response $response, array $getData) : Response
	{
		try
		{
			$id = (int) $getData["id"];
			$entrada = $this->CallEntriedService->GetOne($id);
			$response->getBody()->write(json_encode([
				"status" => 200,
				"data" => $entrada
			]));
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