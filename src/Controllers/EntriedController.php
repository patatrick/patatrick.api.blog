<?php
namespace App\Controllers;
use App\DTO\EntriedDTO;
use App\Helpers\TokenJWT;
use App\Models\Entried;
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
	public function Insert(Request $request, Response $response, array $getData) : Response
	{
		try
		{
			$params = $request->getParsedBody();
			$entried = new Entried();
			$entried->title = trim($params["title"]);
			$entried->description = trim($params["description"]);
			$entried->cover_image = trim($params["cover_image"]);
			$entried->slug = trim($params["slug"]);
			$entried->content = trim($params["content"]);
			if(
				!$entried->title ||
				!$entried->description ||
				!$entried->slug ||
				!$entried->content
			) {
				throw new HttpBadRequestException($request, "Campos en blanco.");
			}
			$idNew = $this->CallEntriedService->Insert($entried);
			$response->getBody()->write(json_encode([
				"status" => 200,
				"data" => $idNew,
                "token" => TokenJWT::UpdateJWT($request->getHeaderLine("Authorization"))
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
	public function Update(Request $request, Response $response, array $getData) : Response
	{
		try
		{
			$params = $request->getParsedBody();
			$entried = new Entried();
			$entried->title = trim($params["title"]);
			$entried->description = trim($params["description"]);
			$entried->cover_image = trim($params["cover_image"]);
			$entried->slug = trim($params["slug"]);
			$entried->content = trim($params["content"]);
			$entried->id = (int) $params["id"];
			if(
				!$entried->title ||
				!$entried->description ||
				!$entried->slug ||
				!$entried->content ||
                !$entried->id
			) {
				throw new HttpBadRequestException($request, "Campos en blanco.");
			}
			$exito = $this->CallEntriedService->Update($entried);
			$response->getBody()->write(json_encode([
				"status" => 200,
				"data" => $exito,
                "token" => TokenJWT::UpdateJWT($request->getHeaderLine("Authorization"))
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
	public function Delete(Request $request, Response $response, array $getData) : Response
	{
		try
		{
			$id_entried = (int) $getData["id"];
            $id_user = TokenJWT::getPlayLoad($request->getHeaderLine("Authorization"))->id_user;
			if( !$id_entried ) {
				throw new HttpBadRequestException($request, "Campos en blanco.");
			}
			$exito = $this->CallEntriedService->Delete($id_entried, $id_user);
			$response->getBody()->write(json_encode([
				"status" => 200,
				"data" => $exito,
                "token" => TokenJWT::UpdateJWT($request->getHeaderLine("Authorization"))
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