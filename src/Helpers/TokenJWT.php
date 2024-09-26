<?php
namespace App\Helpers;
use App\Entities\JsonResponse;
use App\Entities\Playload;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
Class TokenJWT
{
	public JsonResponse $errObj = new JsonResponse();
	public bool $noExpire = false;
	public function Generate(Playload $playLoad) : string
	{
		try
		{
			if ($this->noExpire === true) {
				$playLoad->exp = time() + 365 * 24 * 60 * 60 * 10;
			}
			return JWT::encode((array) $playLoad, $_ENV["TOKEN_KEY"], "HS256");
		}
		catch (\Throwable $e) {
			echo json_encode([ "status"=> 500, "message"=> $e->getMessage()." ".$e->getFile()." on line ".$e->getLine() ]);
			http_response_code(500);
			die();
		}
	}
	public function UpdateJWT(string $headerLine) : string
	{
		$token = trim(str_replace("Bearer ", "", $headerLine));
		$playLoad = $this->DecodeJWT($token);
		if ($this->errObj->status == 401) {
			return "";
		}
		return $this->Generate($playLoad);
	}
	public function getPlayLoad(string $headerLine) : Playload
	{
		$token = trim(str_replace("Bearer ", "", $headerLine));
		$playLoad = $this->DecodeJWT($token);
		return $playLoad;
	}
	private function DecodeJWT(string $token): Playload
	{
		try
		{
			print_r(JWT::decode($token, new Key($_ENV["TOKEN_KEY"], 'HS256')));
			return new Playload();
		}
		catch (\Firebase\JWT\ExpiredException $e) {
			$this->errObj->status = 401;
			$this->errObj->message = "Token expirado";
			return new Playload();
		}
		catch (\Throwable $e) {
			echo json_encode([ "status"=> 500, "message"=> $e->getMessage()." ".$e->getFile()." on line ".$e->getLine() ]);
			http_response_code(500);
			die();
		}
	}
}