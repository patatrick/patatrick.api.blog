<?php 
namespace App\Services;
use App\Services\MySqlService;
use App\Entities\Auth0User;
use App\Models\User;

use \PDO;
final class LoginService extends MySqlService
{
	public function Loguear(Auth0User $auth0User): bool
	{
		$db = $this->Connect();
		$db->beginTransaction();
		try {
			$prepare =  "INSERT INTO users (name, avatar, email, auth0_sub)
						 SELECT :name, :avatar, :email, :auth0_sub
						 WHERE NOT EXISTS (SELECT 1 FROM users WHERE auth0_sub = :auth0_sub);";
			$stmt = $db->prepare($prepare);
			$stmt->bindParam(":name", $auth0User->name, PDO::PARAM_STR);
			$stmt->bindParam(":avatar", $auth0User->picture, PDO::PARAM_STR);
			$stmt->bindParam(":email", $auth0User->email, PDO::PARAM_STR);
			$stmt->bindParam(":auth0_sub", $auth0User->sub, PDO::PARAM_STR);
			$stmt->execute();

            $prepare = "UPDATE users SET code = :code, state = :state WHERE auth0_sub = :auth0_sub";
			$stmt2 = $db->prepare($prepare);
			$stmt2->bindParam(":code", $_GET["code"], PDO::PARAM_STR);
			$stmt2->bindParam(":state", $_GET["state"], PDO::PARAM_STR);
			$stmt2->bindParam(":auth0_sub", $auth0User->sub, PDO::PARAM_STR);
			$stmt2->execute();
            $db->commit();
			return true;
        }
		catch (\PDOException $e)
		{
			$db->rollBack();
			throw new \Exception(basename($e->getFile()). ": ". $e->getMessage(). " on line ". $e->getLine());
		}
		finally {
			$db = null;
		}
	}
	public function GetUser(string $code, string $state): ?User
	{
		try {
            $db = $this->Connect();
			$prepare = "SELECT * FROM users WHERE code = :code AND state = :state";
			$stmt = $db->prepare($prepare);
			$stmt->bindParam(":code", $code, PDO::PARAM_STR);
			$stmt->bindParam(":state", $state, PDO::PARAM_STR);
			$stmt->execute();
			$dataUser = $stmt->fetchObject(User::class);
			return !$dataUser ? null : $dataUser;
		}
		catch (\PDOException $e)
		{
			throw new \Exception(basename($e->getFile()). ": ". $e->getMessage(). " on line ". $e->getLine());
		}
		finally {
			$db = null;
		}
	}
}
