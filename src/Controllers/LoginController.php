<?php
namespace App\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
class LoginController
{
	private string $route_url_index;
	private string $route_url_login;
	private string $route_url_callback;
	private string $route_url_logout;
	function __construct()
	{
		$this->route_url_index = rtrim($_ENV['AUTH0_BASE_URL'], '/');
		$this->route_url_login = $this->route_url_index . '/login';
		$this->route_url_callback = $this->route_url_index . '/login/callback';
		$this->route_url_logout = $this->route_url_login;
	}
	public function Index(Request $request, Response $response, array $getData) : Response
	{
		try
		{
			$auth0 = $this->getAuth0();
			$auth0->clear();
			header("Location: " . $auth0->login($this->route_url_callback));
			exit;
		}
		catch (\Throwable $th) {
			$response->getBody()->write($th->getMessage()." on line ".$th->getLine());
			return $response->withStatus(500);
		}
	}
	public function Callback(Request $request, Response $response, array $getData) : Response
	{
		try
		{
			$auth0 = $this->getAuth0();
			$auth0->exchange($this->route_url_callback);
			print_r($session = $auth0->getCredentials());
			die();
		}
		catch (\Throwable $th) {
			$response->getBody()->write($th->getMessage()." on line ".$th->getLine());
			return $response->withStatus(500);
		}
	}
	public function Logout(Request $request, Response $response, array $getData) : Response
	{
		try
		{
			$auth0 = $this->getAuth0();
			header("Location: " . $auth0->logout($this->route_url_logout));
			exit;
		}
		catch (\Throwable $th) {
			$response->getBody()->write($th->getMessage()." on line ".$th->getLine());
			return $response->withStatus(500);
		}
	}
	private function getAuth0()
	{
		$auth0 = new \Auth0\SDK\Auth0(configuration: [
			'domain' => $_ENV['AUTH0_DOMAIN'],
			'clientId' => $_ENV['AUTH0_CLIENT_ID'],
			'clientSecret' => $_ENV['AUTH0_CLIENT_SECRET'],
			'cookieSecret' => $_ENV['AUTH0_COOKIE_SECRET']
		]);
		return $auth0;
	}
	public function CheckSession()
	{
		$auth0 = $this->getAuth0();
		$session = $auth0->getCredentials();
		// $session === null ? $this->Logout() : true;
	}
}