<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Omnibus\Models;

use DateTime;
use JsonSerializable;
use Doctrine\ORM\Mapping as ORM;
use Omnibus\Core\Utility\Gravatar;
use Omnibus\Core\Utility\ParsedownExtended;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @package Omnibus\Models
 * @ORM\Entity
 * @ORM\Table(name="comments")
 */
class Comment implements JsonSerializable
{
    /**
     * @var int $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var User $author
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @var CommentThread $thread
     * @ORM\ManyToOne(targetEntity="CommentThread", inversedBy="comments")
     * @ORM\JoinColumn(name="thread_id", referencedColumnName="id")
     */
    private $thread;

    /**
     * @var DateTime $date
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $date;

    /**
     * @var string $body
     * @ORM\Column(type="string", length=2500, nullable=false)
     */
    private $body;


    /**
     * @var Collection $reports
     * @ORM\OneToMany(targetEntity="Report", mappedBy="comment", orphanRemoval=true)
     */
    private $reports;

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $this->date = new DateTime('now');
        $this->reports = new ArrayCollection();
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return CommentThread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * @param CommentThread $thread
     */
    public function setThread(CommentThread $thread): void
    {
        $this->thread = $thread;
    }

    /**
     * @return Collection
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    /**
     * @param Report $report
     */
    public function addReport(Report $report): void
    {
        $this->reports[] = $report;
    }

    /**
     * Parse raw comment with Markdown
     */
    public function parse(): void
    {
        $pd = new ParsedownExtended();
        $pd->setSafeMode(true);
        $this->body = $pd->parse($this->body);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $pd = new ParsedownExtended();
        $pd->setSafeMode(true);

        $a = $this->author;
        $data = [
            'id' => $this->id,
            'thread' => $this->thread->getId(),
            'date' => $this->date->format('d.m.Y H:i'),
            'body' => $pd->parse($this->body),
            'author' => [
                'id' => $a->getId(),
                'name' => $a->getName(),
                'avatar' => $a->getAvatar() ? '//'.CONFIG['cdn domain'].'/file/Omnibus/' . $a->getAvatar() : (new Gravatar($a->getEmail(), 50))->getGravatar(),
                'role' => null
            ],
            'reports' => $this->reports->map(static function(Report $e) { return $e->jsonSerialize(); })->toArray()
        ];

        $role = $this->author->getRole();
        if ($role) {
            $data['author']['role'] = [
                'name' => $role->getName(),
            ];
        }

        file_put_contents('log.log',json_encode($this->getReports(), JSON_PRETTY_PRINT));

        return $data;
    }
}
