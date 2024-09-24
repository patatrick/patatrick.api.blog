<?php
namespace App\Traits;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Config;
trait TokenTrait
{
	public function GenerateJWT($playLoad, $noExpire = false) : string
	{
		try
		{
			$playLoad["exp"] = ($noExpire === true) ? (time() + (365 * 24 * 60 * 60 * 10)) : (time() + $this->Config()->token["exp"]);
			return JWT::encode($playLoad, $this->Config()->token["key"], "HS256");
		}
		catch (\Throwable $th) {
			echo "TokenTrait " . $th->getMessage()." in line ".$th->getLine();
			http_response_code(500);
			die();
		}
	}
	public function DecodeJWT($token) : \stdClass|bool
	{
		try
		{
			if (!$token) {
				return false;
			}
			return JWT::decode($token, new Key($this->Config()->token["key"], 'HS256'));
		}
		catch (\Firebase\JWT\ExpiredException $e) {
			// Manejar la excepción de token expirado
			return false;
		}
		catch (\Exception $e) {
			return false;
			// echo "Error: " . $e->getMessage();
		}
	}
	public function UpdateJWT(Request $request) : string
	{
		$token = trim(str_replace("Bearer ", "", $request->getHeaderLine('Authorization')));
		return $this->GenerateJWT((array) $this->DecodeJWT($token));
	}
	public function getUserId(Request $request) : int
	{
		$token = trim(str_replace("Bearer ", "", $request->getHeaderLine('Authorization')));
		$playload = $this->DecodeJWT($token);
		return (int) $playload->id_usuario;
	}
}
