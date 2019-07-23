<?php
namespace Core;

use AltoRouter;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Exception;
use Models\Database;
use Models\Role;
use Models\User;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Application
{
    /** @var AltoRouter */
    protected $router;
    /** @var Run */
    protected $whoops;
    /** @var null|User */
    protected $user;
    /** @var null|Role */
    protected $role;
    /** @var Session */
    public $session;
    /** @var EntityManager|string */
    private $em;

    /**
     * Application constructor.
     */
    public function __construct()
    {
        $this->router = new AltoRouter();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function run(): void
    {
        $this->bootstrap();
        $this->addRoutes();
        $this->handleRequest();
    }

    private function handleRequest(): void
    {
        /* *********************************************** *\
        |*                  ROUTE HANDLING                 *|
        \* *********************************************** */
        $match = $this->router->match();

        // Handle routing
        if ($match) {
            $target = ((array)$match)['target'];
            if (is_callable($target)) {
                call_user_func_array(((array)$match)['target'], ((array)$match)['params']);
            } else {
                $parts = explode('#', $target);
                if (count($parts) === 2) {
                    // instantiate a new controller
                    $controller = new $parts[0]($this->session, $this->user, $this->em);
                    // run the controller's method
                    $controller->{$parts[1]}(((array)$match)['params']);
                }
            }
        } else {
            // no route was matched
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            die('404');
        }
    }

    protected function addRoutes(): void
    {

        /* *********************************************** *\
        |*                  ROUTE MAPPINGS                 *|
        \* *********************************************** */

        /***************************************************************\
        |*                    PUBLIC ACCESS ROUTES                     *|
        |*             Parts of the site anyone can access             *|
        \***************************************************************/
        try {
            $this->router->addRoutes([
                //Home
                ['GET', '/', 'Controllers\\HomeController#index', 'home'],
            ]);
        } catch (Exception $e) {
            echo $e;
        }

        // Routes only for users that aren't logged in
        if ($this->user === null) {
            try {
                $this->router->addRoutes([
                    //Login
                    ['GET', '/login', 'Controllers\\User\\LoginController#index', 'login'],
                    ['POST', '/login', 'Controllers\\User\\LoginController#login'],
                    //Register
                    ['GET', '/register', 'Controllers\\User\\RegisterController#index', 'register'],
                    ['POST', '/register', 'Controllers\\User\\RegisterController#register'],
                    ['GET', '/register/validate', 'Controllers\\User\\RegisterController#validate']
                ]);
            } catch (Exception $e) {
                echo $e;
            }
        // Routes only fur users that are logged in
        } else {
            try {
                $this->router->addRoutes([
                    //Logout
                    ['GET', '/logout', 'Controllers\\User\\LogoutController#index', 'logout'],
                ]);
            } catch (Exception $e) {
                echo $e;
            }
        }

    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    protected function bootstrap(): void
    {
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);

        $sessionStorage = new NativeSessionStorage([
            'cookie_httponly' => true,
            'cookie_samesite' => 'Strict',
            'cookie_secure'   => true,
        ], new NativeFileSessionHandler());
        $this->session = new Session($sessionStorage);
        $this->session->start();

        // Set up router
        $this->router->setBasePath('');
        $this->router->addMatchTypes(['f' => '[a-zA-Z0-9\-]++']);

        // Set up Whoops
        $this->whoops = new Run;
        $this->whoops->appendHandler(new PrettyPageHandler);
        $this->whoops->register();

        // Load .env file
        (new Dotenv())->load(dirname(__DIR__, 2).'/.env');

        // Set up database connection
        $this->em = (new Database())->Get();

        // Check RememberMe
        $remember_cookie = $_COOKIE['__Secure-rememberme'] ?? '';
        if ($remember_cookie) {
            [$user_id, $token, $mac] = explode(':', $remember_cookie);
            if (!password_verify($user_id . ':' . $token, $mac)) {
                die('Invalid!');
            }

            $user = $this->em->find(User::class, $user_id);

            $user_token = $user === null ? '' : $user->getRememberme();
            if (hash_equals($user_token, $token)) {
                $this->session->set('userid', $user_id);
                echo "I 'member you, $user_id $user_token";
            } else {
                echo "$user_token  :::  $token";
            }
        }

        // Get user
        $this->user = $this->session->has('userid') ? $this->em->find(User::class, $this->session->get('userid')) : null;

        // Get user role
        $this->role = ($this->session->has('userid') && $this->user !== null) ? $this->user->GetRole() : null;
    }
}
