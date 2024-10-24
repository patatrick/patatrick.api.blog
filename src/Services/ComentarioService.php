<?php 
namespace App\Services;
use App\DTO\ComentarioDTO;
use App\Services\MySqlService;

use \PDO;
final class ComentarioService extends MySqlService
{
	/**
	 * Retorna todos los comentarios de una entrada
	 * @param int $id_entried
	 * @return ComentarioDTO[]
	 */
	public function GetAllByIdEntried(int $id_entried): Array
	{
		$db = $this->Connect();
		try {
			$prepare =  "SELECT
							R.id,
							R.id_entried,
							R.id_user,
							UC.name,
							R.comment,
							UC.avatar,
							R.joined,
							UCC.id AS id_user_c,
							UCC.name AS name_c,
							UR.comment comment_c,
							UCC.avatar AS avatar_c,
							UR.joined AS joined_c
						FROM replies R
						INNER JOIN users_replies UR ON R.id = UR.id_replies
						INNER JOIN users UC ON R.id_user = UC.id
						LEFT JOIN users UCC ON UR.id_user = UCC.id
						WHERE R.id_entried = :id_entried";
			$stmt = $db->prepare($prepare);
			$stmt->bindParam(":id_entried", $id_entried, PDO::PARAM_INT);
			$stmt->execute();
			$data = $stmt->fetchAll(PDO::FETCH_CLASS, ComentarioDTO::class);
			return $data;
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