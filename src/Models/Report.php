<?php
namespace Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @package Models
 * @ORM\Entity
 * @ORM\Table(name="reports")
 */
class Report
{
    /** @var int $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @var Comment $comment
     * @ORM\ManyToOne(targetEntity="Comment")
     */
    private $comment;

    /** @var User $user
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /** @var DateTime $date
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $date;

    /** @var string $reason
     * @ORM\Column(type="string", nullable=true)
     */
    private $reason;


    /**
     * Report constructor.
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
     * @return Comment
     */
    public function getComment(): Comment
    {
        return $this->comment;
    }

    /**
     * @param Comment $comment
     */
    public function setComment(Comment $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
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
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     */
    public function setReason(string $reason): void
    {
        $this->reason = $reason;
    }


}
