<?php
namespace App\Models;
class Hashtag
{
	public int $id = 0; #INT(11) NOT NULL AUTO_INCREMENT,
	public string $name = ""; #VARCHAR(50) NOT NULL
    public string $create_by = ""; #DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
}
