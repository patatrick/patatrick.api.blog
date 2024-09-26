<?php
class User
{
	public int $id = 0;  # INT(11) NOT NULL AUTO_INCREMENT,
	public string $name = "";  # VARCHAR(100) NOT NULL
	public string $occupation = "";  # VARCHAR(100) NOT NULL
	public string $avatar = "";  # TINYTEXT NOT NULL
	public string $psw = null;  # CHAR(32) NULL
	public string $email = "";  # TINYTEXT NOT NULL
	public string $email_verified = 0;  # BIT(1) NOT NULL,
	public string $auth0_sub = "";  # VARCHAR(100) NOT NULL
	public string $type = "";  # CHAR(2) NOT NULL
	public string $joined = null;  # DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
}
