<?php
namespace App\DTO;
class ComentarioDTO
{
	public int $id = 0;
	public int $id_entried = 0;
	public int $id_user = 0;
	public string $name = "";
	public string $comment = "";
	public string $avatar = "";
	public string $joined = "";
	public ?int $id_user_c = null;
	public ?string $name_c = null;
	public ?string $comment_c = null;
	public ?string $avatar_c = null;
	public ?string $joined_c = null;

}
