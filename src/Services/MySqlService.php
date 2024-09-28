<?php
namespace App\Services;
use \PDO;
class MySqlService
{
	protected function Connect()
	{
        $dbConnection = new PDO($_ENV["DNS"], $_ENV["DATABASE_USER"], $_ENV["DATABASE_PSW"]);
		$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$dbConnection->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
		return $dbConnection;
	}
}