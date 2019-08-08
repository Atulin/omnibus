<?php
namespace Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Parsedown;

/**
 * @package Models
 * @ORM\Entity
 * @ORM\Table(name="comments")
 */
class Comment
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
     * @var
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
     * Comment constructor.
     */
    public function __construct()
    {
        $this->date = new DateTime('now');
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
     * @return mixed
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * @param mixed $thread
     */
    public function setThread($thread): void
    {
        $this->thread = $thread;
    }

    /**
     * Parse raw comment with Markdown
     */
    public function parse(): void
    {
        $pd = new Parsedown();
        $pd->setSafeMode(true);
        $this->body = $pd->parse($this->body);
    }

}
