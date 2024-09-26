<?php
class Replies
{
	public int $id = 0; # INT(11) NOT NULL AUTO_INCREMENT,
	public int $id_entried = 0; # INT(11) NOT NULL,
	public int $id_user = 0; # INT(11) NOT NULL,
	public string $comment = ""; # VARCHAR(512) NOT NULL,
	public string $joined = null; # DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
}