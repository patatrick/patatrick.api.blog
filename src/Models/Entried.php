<?php
namespace App\Models;
class Entried
{
	public int $id = 0; #INT(11) NOT NULL AUTO_INCREMENT,
	public int $id_menu = 0; #TINYINT NOT NULL,
	public string $title = ""; #VARCHAR(100) NOT NULL,
	public string $description = ""; #VARCHAR(512) NOT NULL,
	public ?string $cover_image = null; #VARCHAR(256) NULL,
	public string $slug = ""; #VARCHAR(50) NOT NULL,
	public string $content = ""; #TEXT NOT NULL,
	public int $id_user = 0; #INT(11) NOT NULL,
	public ?string $joined = null; #DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
}
