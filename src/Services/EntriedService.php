<?php 
namespace App\Services;
use App\Models\Entried;
use App\Models\User;
use App\Services\MySqlService;

use EntriedDTO;
use \PDO;
class EntriedService extends MySqlService
{
	/**
	 * Retorna un array de las entradas del blog.
	 * @return Entried[]
	 */
	public function GetAll()
	{
		$db = $this->Connect();
		try {
			$prepare =  "SELECT id, title, description, cover_image, slug, joined FROM entried";
			$stmt = $db->prepare($prepare);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_CLASS, Entried::class);
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
	 * Retorna la entrada asociada al id.
	 */
	public function GetOne(int $id): EntriedDTO
	{
		$db = $this->Connect();
		$db->beginTransaction();
		try {
			$stmt = $db->prepare("SELECT * FROM entried WHERE id = :id");
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();
			$entrada = $stmt->fetchObject(Entried::class);
			
			$stmt = $db->prepare("SELECT * FROM users WHERE id = :id_user");
			$stmt->bindParam(":id_user", $entrada->id_user, PDO::PARAM_INT);
			$stmt->execute();
			$usuario = $stmt->fetchObject(User::class);
			$db->commit();

			
			return $data != false ? $data : new Entried();
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
