<?php
namespace App\Controllers;
use App\Helpers\TokenJWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\EntriedService;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;

final class EntriedController
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
			$slug = trim($getData["slug"]);
			if ($slug == 1) {
				throw new HttpBadRequestException($request, "Slug incorrecto");
			}
			$entrada = $this->CallEntriedService->GetOne($slug);
			$response->getBody()->write(json_encode([
				"status" => 200,
				"data" => $entrada
			]));
			return $response;
		}
		catch (HttpException $ex)
		{
			http_response_code($ex->getCode());
			die(json_encode([ "status"=> $ex->getCode(), "message"=> $ex->getMessage() ]));
		}
		catch (\Throwable $ex) {
			http_response_code(500);
			die(json_encode([ "status"=> 500, "message"=> $ex->getMessage() ]));
		}
	}
}