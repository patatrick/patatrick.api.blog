<?php
namespace App\Entities;

class Playload
{
	public int $id_user;
	public string $type;
	public int $exp;

	public function __construct()
	{
		$this->id = 0;
		$this->type = "";
		$this->exp = time() + $_ENV["TOKEN_EXP"];
	}
}