<?php
namespace App\Services;
use \PDO;
class MySqlService
{
	public static function Connect()
	{
        $dbConnection = new PDO($_ENV["DNS"], $_ENV["DATABASE_USER"], $_ENV["DATABASE_PSW"]);
		$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $dbConnection;
	}
}