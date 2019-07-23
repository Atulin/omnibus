<?php

namespace Core;

use Core\Security\Token;
use Doctrine\ORM\EntityManager;
use Models\User;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Environment;

abstract class Controller
{
    /** @var Environment */
    private $twig;
    /** @var null|User */
    private $user;
    /** @var string */
    private $token;
    /** @var array */
    private $base_data = [];
    /** @var Session */
    protected $session;
    /** @var EntityManager */
    protected $em;

    public function __construct(Session $session, ?User $user, EntityManager $em)
    {
        $this->session = $session;
        $this->user = $user;
        $this->em = $em;
        $this->twig = TwigHandler::Get();
        $this->token = Token::Get();
    }


    /**
     * Renders the given data
     * @param string $template The template name
     * @param array $data The data to render as an associative array
     */
    protected function render($template, $data): void
    {
        // Append twig template extension
        $template .= '.twig';

        try {
            // Render the actual Twig template
            echo $this->twig->render($template, array_merge($this->base_data, $data));

        } catch (LoaderError $e) {
            die($e->getMessage());
        } catch (RuntimeError $e) {
            die($e->getMessage());
        } catch (SyntaxError $e) {
            die($e->getMessage());
        }

        $this->session->set('token', $this->token);
    }

    /**
     * Sets base data to be displayed by most views
     */
    public function setBaseData(): void
    {
        $this->base_data  = [
            'theme' => $_COOKIE['theme'] ?? 'light',
            'token' => $this->token,
            'user'  => $this->user,
        ];
    }

    /**
     * Returns the current user
     * @return null|string|User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Returns generated token
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
}
