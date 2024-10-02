<?php
namespace App\Helpers;
use App\Entities\Playload;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class TokenJWT
{
	public static function GenerateJWT(Playload $playLoad, $noExpire = false) : string
	{
		try
		{
			if ($noExpire === true) {
				$playLoad->exp = time() + 365 * 24 * 60 * 60 * 10;
			}
			return JWT::encode((array) $playLoad, $_ENV["TOKEN_KEY"], "HS256");
		}
		catch (\Throwable $e) {
			throw new \Exception(basename($e->getFile()). ": ". $e->getMessage(). " on line ". $e->getLine());
		}
	}
    /**
     * Actualiza el token actual
     * @param string $headerLine $request->getHeaderLine("Authorization")
     * @return string
     */
	public static function UpdateJWT(string $headerLine) : string
	{
		$token = trim(str_replace("Bearer ", "", $headerLine));
		$playLoad = self::DecodeJWT($token);
		return self::GenerateJWT($playLoad);
	}
        /**
     * Obtiene los parámetros del token (Playload)
     * @param string $headerLine $request->getHeaderLine("Authorization")
     * @return string
     */
	public static function getPlayLoad(string $headerLine) : Playload
	{
		$token = trim(str_replace("Bearer ", "", $headerLine));
		$playLoad = self::DecodeJWT($token);
		return $playLoad;
	}
	private static function DecodeJWT(string $token): Playload
	{
		try
		{
			if (!$token) throw new ExpiredException("Token expirado", 401);
			print_r(JWT::decode($token, new Key($_ENV["TOKEN_KEY"], 'HS256')));
			return new Playload();
		}
		catch (ExpiredException $e) {
			throw new \Exception("Token expirado", 401);
		}
		catch (\Throwable $e) {
			throw new \Exception(basename($e->getFile()). ": ". $e->getMessage(). " on line ". $e->getLine());
		}
	}
}