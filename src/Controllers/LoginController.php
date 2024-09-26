<?php 
namespace App\Controllers;
use App\Entities\Playload;
use App\Helpers\TokenJWT;
use App\Models\Auth0User;
use App\Services\LoginService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
final class LoginController extends TokenJWT
{
	private string $route_url_index;
	private string $route_url_login;
	private string $route_url_callback;
	private string $route_url_logout;
	public readonly LoginService $_loginService = new LoginService();
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
			$response->getBody()->write(json_encode([
				"status"=> 200, "redirect"=> $auth0->login($this->route_url_callback)
			]));
			return $response;
		}
		catch (\Throwable $th) {
			$response->getBody()->write(json_encode([
				"status"=> 500, "message"=> $th->getMessage(). " on line " .$th->getLine()
			]));
			$response->withStatus(500);
			return $response;
		}
	}
	public function Callback(Request $request, Response $response, array $getData) : Response
	{
		try
		{
			$auth0 = $this->getAuth0();
			$auth0->exchange($this->route_url_callback);
			$user = $auth0->getUser();
			$auth0User = new Auth0User();
			$auth0User->name = $user["name"];
			$auth0User->picture = $user["picture"];
			$auth0User->email = $user["email"];
			$auth0User->email_verified = (bool) $user["email_verified"];
			$auth0User->sub = $user["sub"];
			if ($auth0User->email_verified === false)
			{
				$auth0->clear();
				$response->withStatus(400)->getBody()->write(json_encode([
					"status"=> 400,
					"message"=> "Tu correo no ha sido verificado.",
					"redirect"=> $auth0->logout($this->route_url_logout),
				]));
				return $response;
			}
			if (!$this->_loginService->Loguear($auth0User)) {
				$response->withStatus(500)->getBody()->write(json_encode([
					"status"=> 500,
					"message"=> $this->_loginService->error,
				]));
				return $response;
			}
			$playload = new Playload();
			$playload->id = ;
			$playload->type = 
			$response->getBody()->write(json_encode([
				"status"=> 200,
				"data"=> "",
				"token"=> $this->GenerateJWT(),
			]));
			// $response->getBody()->write(json_encode($auth0User));
			return $response;
		}
		catch (\Throwable $th) {
			$response->getBody()->write(json_encode([
				"status"=> 500, "message"=> $th->getMessage(). " on line " .$th->getLine()
			]));
			$response->withStatus(500);
			return $response;
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
			$response->getBody()->write(json_encode([
				"status"=> 500, "message"=> $th->getMessage(). " on line " .$th->getLine()
			]));
			$response->withStatus(500);
			return $response;
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
}