<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Omnibus\Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;


/**
 * @package Omnibus\Models
 * @ORM\Entity
 * @ORM\Table(name="reports")
 */
class Report
{

    /** @var Comment $comment
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="reports")
     */
    private $comment;

    /** @var User $user
     * @ORM\Id
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
        $comment->addReport($this);
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
