<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:52
 */

namespace Core;

use AltoRouter;
use Core\Utility\Email;
use Controllers\EditorController;
use Controllers\Admin\TagsController;
use Controllers\Admin\ArticlesController;
use Controllers\Admin\DashboardController;
use Controllers\StaticDocsController;
use Controllers\API\CommentsApiController;
use Controllers\HomeController;
use Controllers\Admin\CategoriesController;
use Controllers\User\ForgottenPasswordController;
use Controllers\User\LogoutController;
use Controllers\User\MFAController;
use Controllers\User\Profile\AccountEditController;
use Controllers\User\Profile\ProfileController;
use Controllers\User\Profile\ProfileEditController;
use Controllers\User\RecoverController;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Exception;
use Models\ActivationCode;
use Models\Database;
use Models\Role;
use Models\User;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Controllers\User\LoginController;
use Controllers\User\RegisterController;
use Controllers\User\ActivateController;

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
    /** @var bool */
    private $active;

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
                    $controller = new $parts[0]($this->session, $this->user, $this->em, $this->active);
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
                ['GET',  '/md',  StaticDocsController::class . '#md']
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
                    ['GET',  '/logout',                    LogoutController::class . '#index', 'logout'    ],

                    // Set up MFA
                    ['GET',  '/mfa',                       MFAController::class . '#index', 'mfa'          ],
                    ['POST', '/setup-mfa',                 MFAController::class . '#setup'                 ],
                    ['POST', '/remove-mfa',                MFAController::class . '#remove'                ],

                    // User profile
                    ['GET',  '/profile/edit',              ProfileEditController::class . '#index'         ],
                    ['POST', '/profile/edit',              ProfileEditController::class . '#edit'          ],
                    ['GET',  '/profile',                   ProfileController::class . '#index', 'profile'  ],

                    // User account
                    ['GET',  '/account',                   AccountEditController::class . '#index'         ],
                    ['POST', '/account',                   AccountEditController::class . '#edit'          ],
                    ['GET',  '/account/validate',          AccountEditController::class . '#validate'      ],

                    // Comments API
                    ['GET',  '/api/comments',              CommentsApiController::class . '#get'           ],
                    ['POST', '/api/comments',              CommentsApiController::class . '#add'           ],
                    ['POST', '/api/comments/report',       CommentsApiController::class . '#report'        ],

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
                        ['GET',  '/admin/tags',        TagsController::class . '#index'     ],
                        ['POST', '/admin/tags/create', TagsController::class . '#create'    ],
                        ['POST', '/admin/tags/update', TagsController::class . '#update'    ],
                        ['POST', '/admin/tags/delete', TagsController::class . '#delete'    ],
                        ['GET',  '/admin/tags/fetch',  TagsController::class . '#fetch'     ],

                        // Articles
                        ['GET',  '/admin/articles',        ArticlesController::class . '#index'      ],
                        ['GET',  '/admin/editor',          EditorController::class   . '#admin'      ],
                        ['POST', '/admin/editor/create',   EditorController::class   . '#create'     ],
                        ['POST', '/admin/articles/delete', ArticlesController::class . '#delete'     ],
                        ['GET',  '/admin/articles/fetch',  ArticlesController::class . '#fetch'      ],
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

            if (password_verify($user_id . ':' . $token, $mac)) {

                $user = $this->em->find(User::class, $user_id);

                $user_token = $user === null ? '' : $user->getRememberme();

                if (hash_equals($user_token, $token)) {
                    $this->session->set('userid', $user_id);
                }
            }
        }

        // Get user
        $this->user = $this->session->has('userid')
            ? $this->em->find(User::class, $this->session->get('userid'))
            : null;

        // Get user role
        $this->role = ($this->session->has('userid') && $this->user !== null)
            ? $this->user->GetRole()
            : null;

        // Check if user is activated
        $this->active = $this->user
            ? ($this->em->getRepository(ActivationCode::class)->findOneBy(['user_id' => $this->user->getId()]) === null)
            : false;
    }
}
