<?php
namespace App\Helpers;
use App\Entities\Playload;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
Class TokenJWT
{
	public bool $noExpire = false;
	public function GenerateJWT(Playload $playLoad) : string
	{
		try
		{
			if ($this->noExpire === true) {
				$playLoad->exp = time() + 365 * 24 * 60 * 60 * 10;
			}
			return JWT::encode((array) $playLoad, $_ENV["TOKEN_KEY"], "HS256");
		}
		catch (\Throwable $e) {
            throw new \Exception(basename($e->getFile()). ": ". $e->getMessage(). " on line ". $e->getLine());
		}
	}
	public function UpdateJWT(string $headerLine) : string
	{
		$token = trim(str_replace("Bearer ", "", $headerLine));
		$playLoad = $this->DecodeJWT($token);
		return $this->GenerateJWT($playLoad);
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
			throw new \Exception("Token expirado");
		}
		catch (\Throwable $e) {
            throw new \Exception(basename($e->getFile()). ": ". $e->getMessage(). " on line ". $e->getLine());
		}
	}
}