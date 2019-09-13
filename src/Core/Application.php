<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:52
 */

namespace Omnibus\Core;

use Redis;
use Exception;
use AltoRouter;
use Whoops\Run;
use Monolog\Logger;
use Omnibus\Models\Role;
use Omnibus\Models\User;
use Omnibus\Models\Database;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\EntityManager;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Dotenv\Dotenv;
use Whoops\Handler\PrettyPageHandler;
use Omnibus\Controllers\HomeController;
use Omnibus\Controllers\EditorController;
use Doctrine\ORM\OptimisticLockException;
use Omnibus\Controllers\User\MFAController;
use Omnibus\Controllers\Admin\TagsController;
use Omnibus\Controllers\StaticDocsController;
use Omnibus\Controllers\User\LoginController;
use Omnibus\Controllers\User\LogoutController;
use Doctrine\ORM\TransactionRequiredException;
use Omnibus\Controllers\User\RecoverController;
use Omnibus\Controllers\User\RegisterController;
use Omnibus\Controllers\User\ActivateController;
use Omnibus\Controllers\Admin\ArticlesController;
use Omnibus\Controllers\Admin\CommentsController;
use Omnibus\Controllers\Admin\DashboardController;
use Omnibus\Controllers\API\CommentsApiController;
use Omnibus\Controllers\Admin\CategoriesController;
use Symfony\Component\HttpFoundation\Session\Session;
use Omnibus\Controllers\User\Profile\ProfileController;
use Omnibus\Controllers\User\ForgottenPasswordController;
use Omnibus\Controllers\User\Profile\AccountEditController;
use Omnibus\Controllers\User\Profile\ProfileEditController;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler;


class Application
{
    /** @var AltoRouter $router */
    protected $router;
    /** @var Run $whoops */
    protected $whoops;

    /** @var null|User $user */
    protected $user;
    /** @var Role $role */
    protected $role;

    /** @var Session $session */
    public $session;
    /** @var EntityManager $em */
    private $em;
    /** @var Redis $redis*/
    private $redis;
    private $logger;

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
        $bs = microtime(true);
        $this->bootstrap();
        $be = microtime(true);

        $rs = microtime(true);
        $this->addRoutes();
        $re = microtime(true);

        $hs = microtime(true);
        $this->handleRequest();
        $he = microtime(true);

        file_put_contents('perf.log', json_encode([
            'bootstrap' => $be - $bs,
            'router'    => $re - $rs,
            'handler'   => $he - $hs,
            'total'     => $he - $bs
        ], JSON_PRETTY_PRINT));
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
                    $controller = new $parts[0]($this->session, $this->user, $this->em, $this->logger);
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
                ['GET', '/', HomeController::class . '#index', 'home'],

                // User profiles
                ['GET',  '/profile/[i:id]/[f:furl]?', ProfileController::class . '#index'  ],

                // Documents
                ['GET',  '/tos', StaticDocsController::class . '#tos'],
                ['GET',  '/md',  StaticDocsController::class . '#md'],

                // Comments API
                ['GET',  '/api/comments',        CommentsApiController::class . '#get'           ],
            ]);
        } catch (Exception $e) {
            echo $e;
        }

        // Routes only for users that aren't logged in
        if ($this->user === null) {
            try {
                $this->router->addRoutes([
                    // Login
                    ['GET',  '/login',               LoginController::class    . '#index', 'login'   ],
                    ['POST', '/login',               LoginController::class    . '#login'            ],
                    ['GET',  '/login/validate',      LoginController::class    . '#validate'         ],

                    // Register
                    ['GET',  '/register',            RegisterController::class . '#index', 'register'],
                    ['POST', '/register',            RegisterController::class . '#register'         ],
                    ['GET',  '/register/validate',   RegisterController::class . '#validate'         ],

                    // Activate
                    ['GET',  '/activate/[a:code]',   ActivateController::class . '#activate'         ],
                    ['GET',  '/activate',            ActivateController::class . '#index'            ],
                    ['POST', '/activate',            ActivateController::class . '#activate'         ],

                    // Forgot password
                    ['GET',  '/forgot',              ForgottenPasswordController::class . '#index'   ],
                    ['POST', '/forgot',              ForgottenPasswordController::class . '#forgot'  ],

                    // Recover password
                    ['GET',  '/recover/[a:code]',    RecoverController::class  . '#index', 'recover' ],
                    ['POST', '/recover/[a:code]',    RecoverController::class  . '#recover'          ],
                ]);
            } catch (Exception $e) {
                echo $e;
            }

        // Routes only fur users that are logged in
        } else {
            try {
                $this->router->addRoutes([
                    //Logout
                    ['GET',  '/logout',              LogoutController::class . '#index', 'logout'    ],

                    // Set up MFA
                    ['GET',  '/mfa',                 MFAController::class . '#index', 'mfa'          ],
                    ['POST', '/setup-mfa',           MFAController::class . '#setup'                 ],
                    ['POST', '/remove-mfa',          MFAController::class . '#remove'                ],

                    // User profile
                    ['GET',  '/profile/edit',        ProfileEditController::class . '#index'         ],
                    ['POST', '/profile/edit',        ProfileEditController::class . '#edit'          ],
                    ['GET',  '/profile',             ProfileController::class . '#index', 'profile'  ],

                    // User account
                    ['GET',  '/account',             AccountEditController::class . '#index'         ],
                    ['POST', '/account',             AccountEditController::class . '#edit'          ],
                    ['GET',  '/account/validate',    AccountEditController::class . '#validate'      ],

                    // Comments API
                    ['POST', '/api/comments',        CommentsApiController::class . '#add'           ],
                    ['POST', '/api/comments/report', CommentsApiController::class . '#report'        ],

                    // Editor
                    ['GET',  '/editor/[i:id]?',          EditorController::class . '#index'         ],
                    ['POST', '/editor/[i:id]?',          EditorController::class . '#create'        ],

                ]);
            } catch (Exception $e) {
                echo $e;
            }

            // Staff-only routes
            if ($this->role ? $this->role->isStaff() : false) {

                try {
                    $this->router->addRoutes([
                        // Dashboard
                        ['GET',  '/dashboard', DashboardController::class . '#index'],

                        // Categories
                        ['GET',  '/admin/categories',        CategoriesController::class . '#index'     ],
                        ['POST', '/admin/categories/create', CategoriesController::class . '#create'    ],
                        ['POST', '/admin/categories/update', CategoriesController::class . '#update'    ],
                        ['POST', '/admin/categories/delete', CategoriesController::class . '#delete'    ],
                        ['GET',  '/admin/categories/fetch',  CategoriesController::class . '#fetch'     ],

                        // Tags
                        ['GET',  '/admin/tags',              TagsController::class . '#index'           ],
                        ['POST', '/admin/tags/create',       TagsController::class . '#create'          ],
                        ['POST', '/admin/tags/update',       TagsController::class . '#update'          ],
                        ['POST', '/admin/tags/delete',       TagsController::class . '#delete'          ],
                        ['GET',  '/admin/tags/fetch',        TagsController::class . '#fetch'           ],

                        // Articles
                        ['GET',  '/admin/articles',          ArticlesController::class . '#index'       ],
                        ['POST', '/admin/articles/delete',   ArticlesController::class . '#delete'      ],
                        ['GET',  '/admin/articles/fetch',    ArticlesController::class . '#fetch'       ],

                        // Comments
                        ['GET',  '/admin/comments',          CommentsController::class    . '#index'       ],
                        ['POST', '/admin/comments/delete',   CommentsApiController::class . '#delete'      ],
                        ['POST', '/admin/comments/accept',   CommentsApiController::class . '#accept'      ],
                        ['GET',  '/admin/comments/reports',  CommentsApiController::class . '#getReports'  ],
                    ]);
                } catch (Exception $e) {
                    echo $e;
                }

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

        // Load .env file
        (new Dotenv())->load(dirname(__DIR__, 2).'/.env');

        // Set up Redis
        $this->redis = new Redis();
        $this->redis->connect($_ENV['REDIS_HOST'], $_ENV['REDIS_PORT']);

        // Set up session
        $sessionStorage = new NativeSessionStorage(
            [
            'cookie_httponly' => true,
            'cookie_samesite' => 'Strict',
            'cookie_secure'   => true,
            ],
            new RedisSessionHandler($this->redis)
        );
        $this->session = new Session($sessionStorage);
        $this->session->start();

        // Set up router
        $this->router->setBasePath('');
        $this->router->addMatchTypes(['f' => '[a-zA-Z0-9\-]++']);

        // Set up Whoops
        $this->whoops = new Run;
        $this->whoops->appendHandler(new PrettyPageHandler);
        $this->whoops->register();

        // Set up Monolog
        $this->logger = new Logger('main');
        $this->logger->pushHandler(new StreamHandler('logs/main.log', Logger::DEBUG));

        // Set up database connection
        $this->em = (new Database())->Get();

        // Get user id
        $USERID = $this->session->get('userid');
        $this->session->save();

        // Check RememberMe
        if (!$USERID) {
            $remember_cookie = $_COOKIE['__Secure-rememberme'] ?? '';

            if ($remember_cookie) {

                [$user_id, $token, $mac] = explode(':', $remember_cookie);

                if (password_verify($user_id . ':' . $token, $mac)) {

                    $user = $this->em->find(User::class, $user_id);

                    $user_token = $user === null ? '' : $user->getRememberme();

                    if (hash_equals($user_token, $token)) {
                        $this->session->set('userid', $user_id);
                    }
                }
            }
        }

        // Get user
        $this->user = $USERID
            ? $this->em->getRepository(User::class)->find($USERID)
            : null;

        // Get user role
        $this->role = ($USERID && $this->user !== null)
            ? $this->user->GetRole()
            : new Role();

        $this->session->save();
    }
}
