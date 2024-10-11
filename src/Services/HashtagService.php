<?php 
namespace App\Services;
use App\Models\Hashtag;
use App\Services\MySqlService;

use \PDO;
final class HashtagService extends MySqlService
{
	/**
	 * Retorna un array de los hasgtags
	 * @return Hashtag[]
	 */
	public function GetAll()
	{
		$db = $this->Connect();
		try {
			$stmt = $db->prepare("SELECT * FROM hashtag");
			$stmt->execute();
			$data = $stmt->fetchAll(PDO::FETCH_CLASS, Hashtag::class);
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
