<?php 
namespace App\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Entities\Playload;
use App\Helpers\TokenJWT;
use App\Entities\Auth0User;
use App\Models\User;
use App\Services\LoginService;
final class LoginController
{
	private readonly string $route_url_index;
	private readonly string $route_url_login;
	private readonly string $route_url_callback;
	private readonly string $route_url_logout;
	private readonly LoginService $CallLoginService;
	public function __construct()
	{
		$this->CallLoginService = new LoginService();
		$this->route_url_index = rtrim($_ENV['AUTH0_BASE_URL'], '/');
		$this->route_url_login = $this->route_url_index . '/login';
		$this->route_url_callback = $_ENV['AUTH0_CALLBACK'];
		$this->route_url_logout = $this->route_url_login;
	}
	public function Index(Request $request, Response $response, array $getData) : Response
	{
		try
		{
			$auth0 = $this->getAuth0();
			if (isset($_GET["logout"])) {
				header("Location: ".$_ENV['AUTH0_CALLBACK_CLIENT']);
			}
			else {
				header("Location: ".$auth0->login($this->route_url_callback));
			}
			exit;
		}
		catch (\Throwable $e) {
			http_response_code(500);
			die(json_encode([ "status"=> 500, "message"=> $e->getMessage() ]));
		}
	}
	public function Callback(Request $request, Response $response, array $getData) : Response
	{
		try
		{
			$auth0 = $this->getAuth0();
			$auth0->exchange($this->route_url_callback);
			$auth0User = $this->getAuth0User($auth0->getUser());
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
			$this->CallLoginService->Loguear($auth0User);
			header("Location: ".$_ENV['AUTH0_CALLBACK_CLIENT']."?code=".$_GET['code']."&state=".$_GET['state']);
			exit;
		}
		catch (\Throwable $e) {
			http_response_code(500);
			die(json_encode([ "status"=> 500, "message"=> $e->getMessage() ]));
		}
	}
	public function GetUser(Request $request, Response $response, array $getData) : Response
	{
		try
		{
			$code = trim($getData["code"]);
			$state = trim($getData["state"]);
			$userLogin = $this->CallLoginService->GetUser($code, $state);
			if (!$userLogin) {
				header("Location: ".$this->route_url_login);
				$response->getBody()->write(json_encode([
					"status"=> 200,
					"data"=> null,
					"token"=> null,
				]));
				return $response;
			}
			$playLoad = $this->CreatePlayload($userLogin);
			$response->getBody()->write(json_encode([
				"status"=> 200,
				"data"=> $userLogin,
				"token"=> TokenJWT::GenerateJWT($playLoad, true),
			]));
			return $response;
		}
		catch (\Throwable $e) {
			http_response_code(500);
			die(json_encode([ "status"=> 500, "message"=> $e->getMessage() ]));
		}
	}
	public function Logout(Request $request, Response $response, array $getData) : Response
	{
		try
		{
			$auth0 = $this->getAuth0();
			$auth0->clear();
			header("Location: " . $auth0->logout($this->route_url_logout."?logout=true"));
			exit;
		}
		catch (\Throwable $e) {
			http_response_code(500);
			die(json_encode([ "status"=> 500, "message"=> $e->getMessage() ]));
		}
	}
	private function getAuth0()
	{
		$auth0 = new \Auth0\SDK\Auth0(configuration: [
			'domain' => $_ENV['AUTH0_DOMAIN'],
			'clientId' => $_ENV['AUTH0_CLIENT_ID'],
			'clientSecret' => $_ENV['AUTH0_CLIENT_SECRET'],
			'cookieSecret' => $_ENV['AUTH0_COOKIE_SECRET'],
		]);
		return $auth0;
	}
	private function getAuth0User(array $auth0Arr) : Auth0User
	{
		$auth0User = new Auth0User();
		$auth0User->name = $auth0Arr["name"];
		$auth0User->picture = $auth0Arr["picture"];
		$auth0User->email = $auth0Arr["email"];
		$auth0User->email_verified = (bool) $auth0Arr["email_verified"];
		$auth0User->sub = $auth0Arr["sub"];
		return $auth0User;
	}
	private function CreatePlayload(User $userLogin) : Playload
	{
		$playload = new Playload();
		$playload->id_user = $userLogin->id;
		$playload->type = $userLogin->type;
		$this->noExpire = true;
		return $playload;
	}
}