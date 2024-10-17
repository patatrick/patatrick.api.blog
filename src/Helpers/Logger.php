<?php
namespace App\Helpers;
class Logger
{
	static function Write($str): void
	{
		$namearchivo = __DIR__.'/../../logs/'. date('d-m-Y') .'.log';
		$file = fopen($namearchivo, 'a');
		fwrite($file, date("H:i:s").': '.$str);
		fwrite($file, "\r\n");
		fclose($file);
	}
}