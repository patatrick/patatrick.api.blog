<?php
namespace App\Models;
class UserReplies
{
	public int $id = 0; # INT(11) NOT NULL,
	public int $id_user = 0; # INT(11) NOT NULL,
	public int $id_replies = 0; # INT(11) NOT NULL,
	public string $comment = ""; # VARCHAR(512) NOT NULL,
	public ?string $joined = null; # DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
}