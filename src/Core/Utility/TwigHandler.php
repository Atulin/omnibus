<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Core\Utility;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig_Extensions_Extension_Date;

class TwigHandler
{
    public static function Get(): Environment
    {
        $loader = new FilesystemLoader([dirname(__DIR__, 2) . '/Views',
            dirname(__DIR__, 3) . '/public/assets'
        ]);
        $twig = new Environment($loader);

        ///
        /// Markdown parse filter
        ///
        $twig->addFilter(new TwigFilter('md', static function (?string $string): ?string {
            $Extra = new ParsedownExtended();
            return $string ? $Extra->parse($string) : null;
        }));

        ///
        /// Asset loading function
        ///
        $twig->addFunction(new TwigFunction('asset', static function (string $asset) {
            return sprintf('/public/%s', ltrim($asset, '/'));
        }));

        ///
        /// Versioned asset loading function
        ///
        $twig->addFunction(new TwigFunction('vasset', static function (string $asset, string $extension, bool $strip = false) {
            $filename = ltrim(ASSETS, '/')."/$asset.$extension";
            if (file_exists($filename)) {
                $timestamp = dechex(filemtime($filename));
                if ($strip) {
                    return "$asset.$timestamp";
                } else {
                    return ASSETS . "/$asset.$timestamp.$extension";
                }
            }
            return $filename;
        }));

        ///
        /// Get a Gravatar from email
        ///
        $twig->addFunction(new TwigFunction('gravatar', static function(string $email, int $size = null) {
            return (new Gravatar($email, $size))->getGravatar();
        }));

        ///
        /// Get a value from site config
        ///
        $twig->addFunction(new TwigFunction('cfg', static function(string $key) {
            return CONFIG[$key];
        }));

        ///
        /// Dump data
        ///
        $twig->addFunction(new TwigFunction('prevex', static function($data, $circular = false) {
            if ($circular) {
                $var = print_r($data, true);
                return '<pre>'.$var.'</pre>';
            } else {
                return '<pre>'.var_export($data, true).'</pre>';
            }
        }, ['is_safe' => ['html']]));

        ///
        /// Lorem ipsum generator
        ///
        $twig->addFunction(new TwigFunction('lipsum', static function(int $paragraphs = 1) {
            return file_get_contents("https://loripsum.net/api/$paragraphs");
        }, ['is_safe' => ['html']]));

        // Date extension
        $twig->addExtension(new Twig_Extensions_Extension_Date());

        return $twig;
    }
}
