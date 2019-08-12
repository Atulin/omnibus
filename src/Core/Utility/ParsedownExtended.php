<?php

namespace Core\Utility;

use Doctrine\ORM\ORMException;
use Models\Database;
use Models\User;
use Parsedown;

/**
 * Class ParsedownExt
 * @package Core\Utility
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
    }


    /**
     * @param  $link
     */
    protected function addLinkAttributes(&$link): void
    {
        $link['target'] = '_blank';
        $link['rel'] = 'nofollow';
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
                            'class' => 'mention',
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
}
