<?php 
namespace App\Services;
use App\Models\Menu;
use App\Services\MySqlService;
use \PDO;
final class MenuService extends MySqlService
{
	/**
	 * Retorna las opciones del menú
	 * @return Menu[]
	 */
	public function GetAll()
	{
		$db = $this->Connect();
		try {
			$stmt = $db->prepare("SELECT * FROM menus");
			$stmt->execute();
			$data = $stmt->fetchAll(PDO::FETCH_CLASS, Menu::class);
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
