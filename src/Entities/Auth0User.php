<?php
namespace App\Entities;
class Auth0User
{
	public string $nickname = "";
	public string $name = "";
	public string $picture = "";
	public string $updated_at = "";
	public string $email = "";
	public bool $email_verified = false;
	public string $iss = "";
	public string $aud = "";
	public int $iat = 0;
	public int $exp = 0;
	public string $sub = "";
	public string $sid = "";
	public string $nonce = "";
}
