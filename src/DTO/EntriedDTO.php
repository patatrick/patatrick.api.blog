<?php
use App\Models\Entried;
use App\Models\User;
class EntriedDTO extends Entried
{
	public User $user;
}
