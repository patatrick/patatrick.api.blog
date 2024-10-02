<?php 
namespace App\Services;
use App\DTO\EntriedDTO;
use App\Models\EntriedHashtag;
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
			$arrEntriedDTO = $stmt->fetchAll(PDO::FETCH_CLASS, EntriedDTO::class);
			if (count($arrEntriedDTO)) {
				$stmt2 = $db->prepare("SELECT * FROM entried_hashtag");
				$stmt2->execute();
				$arrHashtag = $stmt2->fetchAll(PDO::FETCH_CLASS, EntriedHashtag::class);
				$hashtagsByEntried = [];
				foreach ($arrHashtag as $hashtag) {
					$hashtagsByEntried[$hashtag->id_entried][] = $hashtag->id_hashtag;
				}
				foreach ($arrEntriedDTO as $entry) {
					$entry->hashtag = $hashtagsByEntried[$entry->id] ?? [];
				}
			}
			return $arrEntriedDTO;
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
					u.description AS user_description,
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
			$entried = $stmt->fetchObject(EntriedDTO::class);
			if ($entried) {
				$stmt2 = $db->prepare("SELECT id_hashtag FROM entried_hashtag WHERE id_entried = :id_entried");
				$stmt2->bindParam(":id_entried", $entried->id, PDO::PARAM_INT);
				$stmt2->execute();
				$entried->hashtag = array_column($stmt2->fetchAll(PDO::FETCH_NUM), 0);
			}
			return $entried != false ? $entried : new EntriedDTO();
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
	public function Insert(EntriedDTO $entried): int
	{
		$db = $this->Connect();
		$db->beginTransaction();
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
			$idNewEntried = $db->lastInsertId();
			foreach ($entried->hashtag as $hashtagString) {
				$stmt2 = $db->prepare("SELECT id FROM hashtag WHERE name = :name");
				$stmt2->bindParam(":name", $hashtagString, PDO::PARAM_STR);
				$stmt2->execute();
				$existingHashtag = $stmt2->fetch(PDO::FETCH_ASSOC);
				if ($existingHashtag) {
					$idNewHashtag = $existingHashtag['id'];
				}
				else {
					$stmt2 = $db->prepare("
						INSERT INTO hashtag (name, create_by) VALUES(:name, :create_by)
					");
					$stmt2->bindParam(":name", $hashtagString, PDO::PARAM_STR);
					$stmt2->bindParam(":create_by", $entried->id_user, PDO::PARAM_INT);
					$stmt2->execute();
					$idNewHashtag = $db->lastInsertId();
				}
				$stmt3 = $db->prepare("
					INSERT INTO entried_hashtag (id_entried, id_hashtag) VALUES(:id_entried, :id_hashtag)
				");
				$stmt3->bindParam(":id_entried", $idNewEntried, PDO::PARAM_INT);
				$stmt3->bindParam(":id_hashtag", $idNewHashtag, PDO::PARAM_INT);
				$stmt3->execute();
			}
			$db->commit();
			return $idNewEntried;
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
	
	/**
	 * Retorna True o False en la actualización
	 */
	public function Update(EntriedDTO $entried): bool
	{
		$db = $this->Connect();
		$db->beginTransaction();
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

			$stmt2 = $db->prepare("DELETE FROM entried_hashtag WHERE id_entried = :id_entried");
			$stmt2->bindParam(":id_entried", $entried->id, PDO::PARAM_INT);
			$stmt2->execute();

			foreach ($entried->hashtag as $hashtagString) {
				$stmt3 = $db->prepare("SELECT id FROM hashtag WHERE name = :name");
				$stmt3->bindParam(":name", $hashtagString, PDO::PARAM_STR);
				$stmt3->execute();
				$existingHashtag = $stmt3->fetch(PDO::FETCH_ASSOC);
				if ($existingHashtag) {
					$idNewHashtag = $existingHashtag['id'];
				}
				else {
					$stmt3 = $db->prepare("
						INSERT INTO hashtag (name, create_by) VALUES(:name, :create_by)
					");
					$stmt3->bindParam(":name", $hashtagString, PDO::PARAM_STR);
					$stmt3->bindParam(":create_by", $entried->id_user, PDO::PARAM_INT);
					$stmt3->execute();
					$idNewHashtag = $db->lastInsertId();
				}
				$stmt4 = $db->prepare("
					INSERT INTO entried_hashtag (id_entried, id_hashtag) VALUES(:id_entried, :id_hashtag)
				");
				$stmt4->bindParam(":id_entried", $entried->id, PDO::PARAM_INT);
				$stmt4->bindParam(":id_hashtag", $idNewHashtag, PDO::PARAM_INT);
				$stmt4->execute();
			}
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
	/**
	 * Retorna True o False en la eliminación
	 */
	public function Delete(int $id_entried, int $id_user): bool
	{
		$db = $this->Connect();
		$db->beginTransaction();
		try {
			$stmt = $db->prepare("DELETE FROM entried_hashtag WHERE id_entried = :id_entried");
			$stmt->bindParam(":id_entried", $id_entried, PDO::PARAM_INT);
			$stmt->execute();
			$stmt2 = $db->prepare("DELETE FROM entried WHERE id = :id AND id_user = :id_user");
			$stmt2->bindParam(":id", $id_entried, PDO::PARAM_INT);
			$stmt2->bindParam(":id_user", $id_user, PDO::PARAM_INT);
			$stmt2->execute();
			if ($stmt2->rowCount() == 0) {
				$db->rollBack();
				return false;
			}
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
}
