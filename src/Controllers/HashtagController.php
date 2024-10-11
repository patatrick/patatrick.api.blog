<?php
namespace App\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\HashtagService;

final class HashtagController
{
	private readonly HashtagService $CallHashtagService;
	public function __construct() {
		$this->CallHashtagService = new HashtagService();
	}
	public function GetAll(Request $request, Response $response, array $getData) : Response
	{
		try
		{
			$entradas = $this->CallHashtagService->GetAll();
			$response->getBody()->write(json_encode([
				"status" => 200,
				"data" => $entradas
			]));
			return $response;
		}
		catch (\Throwable $ex) {
			http_response_code(500);
			die(json_encode([ "status"=> 500, "message"=> $ex->getMessage() ]));
		}
	}
}