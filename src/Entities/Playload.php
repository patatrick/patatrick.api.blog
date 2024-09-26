<?php
namespace App\Entities;
class Playload
{
	public int $id = 0;
	public string $type = "";
	public int $exp = time() + $_ENV["TOKEN_EXP"];
}
