<?php
namespace App\Models;
class Menu
{
	public int $id = 0; # TINYINT NOT NULL,
	public string $name = ""; # VARCHAR(50) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	public string $slug = ""; # VARCHAR(100) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	public string $icon = ""; # VARCHAR(50) NOT NULL COLLATE 'utf8mb4_unicode_ci',
}