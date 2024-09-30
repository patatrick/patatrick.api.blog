<?php 
namespace App\Services;
use App\DTO\EntriedDTO;
use App\Models\User;
use App\Services\MySqlService;

use \PDO;
class EntriedService extends MySqlService
{
	/**
	 * Retorna un array de las entradas del blog.
	 * @return EntriedDTO[]
	 */
	public function GetAll()
	{
		$db = $this->Connect();
		try {
			$stmt = $db->prepare("
				SELECT
					e.id,
					e.title,
					e.description,
					e.cover_image,
					e.slug,
					e.joined,
					e.id_user,
					u.name,
					u.avatar,
					u.occupation
				FROM
					entried e
					INNER JOIN users u ON e.id_user = u.id
			");
			$stmt->execute();
			$data = $stmt->fetchAll(PDO::FETCH_CLASS, EntriedDTO::class);
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
	/**
	 * Retorna la entrada asociada al Slug.
	 */
	public function GetOne(string $slug): EntriedDTO
	{
		$db = $this->Connect();
		try {
			$stmt = $db->prepare("
				SELECT
					e.*,
					u.name,
					u.descrption,
					u.avatar,
					u.occupation
				FROM
					entried e
					INNER JOIN users u ON e.id_user = u.id
				WHERE
					e.slug = :slug
			");
			$stmt->bindParam(":slug", $slug, PDO::PARAM_STR);
			$stmt->execute();
			$data = $stmt->fetchObject(EntriedDTO::class);
			return $data != false ? $data : new EntriedDTO();
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
