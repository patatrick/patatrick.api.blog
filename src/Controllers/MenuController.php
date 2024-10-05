<?php
namespace App\Controllers;
use App\Services\MenuService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class MenuController
{
	private readonly MenuService $CallMenuService;
	public function __construct() {
		$this->CallMenuService = new MenuService();
	}
	public function GetAll(Request $request, Response $response, array $getData) : Response
	{
		try
		{
			$data = $this->CallMenuService->GetAll();
			$response->getBody()->write(json_encode([
				"status" => 200,
				"data" => $data
			]));
			return $response;
		}
		catch (\Throwable $ex) {
			http_response_code(500);
			die(json_encode([ "status"=> 500, "message"=> $ex->getMessage() ]));
		}
	}
}