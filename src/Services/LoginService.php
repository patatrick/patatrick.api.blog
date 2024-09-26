<?php 
namespace App\Services;
use \PDO, \PDOException;
use App\Services\MySqlService;
use App\Models\Auth0User;

class LoginService extends MySqlService
{
	public string $error = "";
	public function Loguear(Auth0User $auth0User) : bool
	{
		try {
			$type = "US";
			$db = $this->Connect();
			$prepare = "
				INSERT INTO users (name, avatar, email, auth0_sub, type)
				SELECT :name, :avatar, :email, :auth0_sub, :type
				WHERE NOT EXISTS (SELECT 1 FROM users WHERE auth0_sub = :auth0_sub);
			";
			$stmt = $db->prepare($prepare);
			$stmt->bindParam(":name", $auth0User->name, PDO::PARAM_STR);
			$stmt->bindParam(":avatar", $auth0User->picture, PDO::PARAM_STR);
			$stmt->bindParam(":email", $auth0User->email, PDO::PARAM_STR);
			$stmt->bindParam(":auth0_sub", $auth0User->sub, PDO::PARAM_STR);
			$stmt->bindParam(":type", $type, PDO::PARAM_STR);
			$stmt->execute();
			return true;
		}
		catch (PDOException $e)
		{
			$this->error = $e->getMessage(). " on line " .$e->getLine();
			return false;
		}
		finally {
			$db = null;
		}
	}
}
