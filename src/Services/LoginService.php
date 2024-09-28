<?php 
namespace App\Services;
use App\Entities\ErrorResponse;
use App\Services\MySqlService;
use App\Models\Auth0User;
use App\Models\User;

use \PDO;
class LoginService extends MySqlService
{
	public function Loguear(Auth0User $auth0User, string $type): User
	{
		$db = $this->Connect();
		$db->beginTransaction();
		try {
			$prepare =  "INSERT INTO users (name, avatar, email, auth0_sub, type)
						 SELECT :name, :avatar, :email, :auth0_sub, :type
						 WHERE NOT EXISTS (SELECT 1 FROM users WHERE auth0_sub = :auth0_sub);";
			$stmt = $db->prepare($prepare);
			$stmt->bindParam(":name", $auth0User->name, PDO::PARAM_STR);
			$stmt->bindParam(":avatar", $auth0User->picture, PDO::PARAM_STR);
			$stmt->bindParam(":email", $auth0User->email, PDO::PARAM_STR);
			$stmt->bindParam(":auth0_sub", $auth0User->sub, PDO::PARAM_STR);
			$stmt->bindParam(":type", $type, PDO::PARAM_STR);
			$stmt->execute();
			$prepare = "SELECT * FROM users WHERE auth0_sub = :auth0_sub";
			$stmt = $db->prepare($prepare);
			$stmt->bindParam(":auth0_sub", $auth0User->sub, PDO::PARAM_STR);
			$stmt->execute();
			$dataUser = $stmt->fetchObject(User::class);
			$db->commit();
			return $dataUser;
		}
		catch (\PDOException $e)
		{
			$db->rollBack();
			throw new \Exception("Error LoginService". $e->getMessage());
		}
		finally {
			$db = null;
		}
	}
}
