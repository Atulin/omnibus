<?php

namespace Core;

use ParsedownExtra;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig_Extensions_Extension_Date;

class TwigHandler
{
    public static function Get(): Environment
    {
        $loader = new FilesystemLoader([
            dirname(__DIR__, 1) . '/Views',
            dirname(__DIR__, 2) . '/public/assets'
        ]);
        $twig = new Environment($loader);

        ///
        /// Markdown parse filter
        ///
        $twig->addFilter(new TwigFilter('md', static function (?string $string): ?string {
            $Extra = new ParsedownExtra();
            return $string ? $Extra->parse($string) : null;
        }));

        ///
        /// Asset loading function
        ///
        $twig->addFunction(new TwigFunction('asset', static function ($asset) {
            return sprintf('/public/%s', ltrim($asset, '/'));
        }));

        ///
        /// Versioned asset loading function
        ///
        $twig->addFunction(new TwigFunction('vasset', static function ($asset, $extension) {
            $filename = ASSETS."/$asset.$extension";
            if (file_exists($filename)) {
                $timestamp = filemtime($filename);
                return ASSETS."/$asset.$timestamp.$extension";
            }
            return ASSETS."/$asset.$extension";
        }));

        ///
        /// Get a Gravatar from email
        ///
        $twig->addFunction(new TwigFunction('gravatar', static function($email) {
            $hash = md5( strtolower( trim( $email ) ) );
            return "https://www.gravatar.com/avatar/$hash";
        }));

        // Date extension
        $twig->addExtension(new Twig_Extensions_Extension_Date());

        return $twig;
    }
}
