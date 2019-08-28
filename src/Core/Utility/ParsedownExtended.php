<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Omnibus\Core\Utility;

use Parsedown;
use Omnibus\Models\User;
use Omnibus\Models\Database;
use Doctrine\ORM\ORMException;


/**
 * Class ParsedownExt
 * @package Omnibus\Core\Utility
 */
class ParsedownExtended extends Parsedown
{
    /**
     * ParsedownExtended constructor.
     */
    public function __construct()
    {
        $this->InlineTypes['@'][] = 'Mention';
        $this->inlineMarkerList .= '@';

        $this->InlineTypes['^'][] = 'Superscript';
        $this->inlineMarkerList .= '^';

    }


    /**
     * @param  $link
     */
    protected function addLinkAttributes(&$link): void
    {
        $link['target'] = '_blank';
        $link['rel'] = 'nofollow';
        $link['class'] = 'link';
    }


    /**
     * Overwrites `inlineLink()` function
     * @param $excerpt
     * @return array|void
     */
    protected function inlineLink($excerpt)
    {
        $temp = parent::inlineLink($excerpt);
        if (is_array($temp) && isset($temp['element']['attributes']['href'])) {
            $this->addLinkAttributes($temp['element']['attributes']);

            if ($temp['element']['attributes']['href'][0] === '#') {
                unset($temp['element']['attributes']['target']);
            }
        }
        return $temp;
    }

    /**
     * Overwrites `inlineUrl()` function
     * @param $excerpt
     * @return array|void
     */
    protected function inlineUrl($excerpt)
    {
        $temp = parent::inlineUrl($excerpt);
        if (is_array($temp) && isset($temp['element']['attributes']['href'])) {
            $this->addLinkAttributes($temp['element']['attributes']);
        }
        return $temp;
    }

    /**
     * Creates an URL leading to mentioned user's profile
     * @param $excerpt
     * @return array
     * @throws ORMException
     */
    protected function inlineMention($excerpt): array
    {
        if (preg_match('/@[\d]+/', $excerpt['text'], $matches)) {
            $em = (new Database())->Get();
            $id = trim($matches[0], '@');

            /** @var User $u */
            $u = $em->find(User::class, $id);

            if ($u) {
                return [
                    'extent' => strlen($matches[0]),
                    'element' => [
                        'name' => 'a',
                        'text' => '@'.$u->getName(),
                        'attributes' => [
                            'href' => 'https://omnibus2.test/profile/'.$id,
                            'class' => 'mention link',
                            'target' => '_blank'
                        ]
                    ]
                ];
            } else {
                return [
                    'extent' => strlen($matches[0]),
                    'element' => [
                        'name' => 'span',
                        'text' => $matches[0]
                    ]
                ];
            }
        }
        return [];
    }

    protected function inlineSuperscript($excerpt) {
        if (preg_match('/\^[\S]+/', $excerpt['text'], $matches)) {
            $text = trim($matches[0], '^');

            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'sup',
                    'text' => $text,
                ]
            ];

        }
        return [];
    }

    protected function blockHeader($Line)
    {
        if (isset($Line['text'][1]))
        {
            $level = 1;

            while (isset($Line['text'][$level]) and $Line['text'][$level] === '#')
            {
                $level ++;
            }

            if ($level > 6)
            {
                return;
            }

            $text = trim($Line['text'], '# ');

            $Block = array(
                'element' => array(
                    'name' => 'h' . min(6, $level),
                    'text' => $text,
                    'handler' => 'line',
                ),
            );

            if ($level <= 3) {
                $Block['element']['attributes']['id'] = Utils::friendlify($text);
            }

            return $Block;
        }
    }

}
