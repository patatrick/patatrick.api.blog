<?php
namespace App\DTO;
use App\Models\Entried;
class EntriedDTO extends Entried
{
	public string $name;
	public ?string $user_description;
	public string $avatar;
	public ?string $occupation;
	public array $hashtag = []; # INT[] | STRING[]
}
