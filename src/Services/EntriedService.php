<?php 
namespace App\Services;
use App\DTO\EntriedDTO;
use App\Models\Entried;
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
	/**
	 * Retorna el id de la entrada creada.
	 */
	public function Insert(Entried $entried): int
	{
		$db = $this->Connect();
		try {
			$stmt = $db->prepare("
				INSERT INTO entried (title, description, cover_image, slug, content, id_user)
				VALUES(:title, :description, :cover_image, :slug, :content, :id_user)
			");
			$stmt->bindParam(":title", $entried->title, PDO::PARAM_STR);
			$stmt->bindParam(":description", $entried->description, PDO::PARAM_STR);
			$stmt->bindParam(":cover_image", $entried->cover_image, PDO::PARAM_STR);
			$stmt->bindParam(":slug", $entried->slug, PDO::PARAM_STR);
			$stmt->bindParam(":content", $entried->content, PDO::PARAM_STR);
			$stmt->bindParam(":id_user", $entried->id_user, PDO::PARAM_INT);
			$stmt->execute();
			return $db->lastInsertId();
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
	 * Retorna True o False en la actualización
	 */
	public function Update(Entried $entried): bool
	{
		$db = $this->Connect();
		try {
			$stmt = $db->prepare("
				UPDATE entried SET
				title = :title
				,description = :description
				,cover_image = :cover_image
				,slug = :slug
				,content = :content
				WHERE id = :id
			");
			$stmt->bindParam(":title", $entried->title, PDO::PARAM_STR);
			$stmt->bindParam(":description", $entried->description, PDO::PARAM_STR);
			$stmt->bindParam(":cover_image", $entried->cover_image, PDO::PARAM_STR);
			$stmt->bindParam(":slug", $entried->slug, PDO::PARAM_STR);
			$stmt->bindParam(":content", $entried->content, PDO::PARAM_STR);
			$stmt->bindParam(":id", $entried->id, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->rowCount() ? true : false;
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
	 * Retorna True o False en la eliminación
	 */
	public function Delete(int $id_entried, int $id_user): bool
	{
		$db = $this->Connect();
		try {
			$stmt = $db->prepare("DELETE FROM entried WHERE id = :id AND id_user = :id_user");
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->bindParam(":id_user", $id_user, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->rowCount() ? true : false;
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
