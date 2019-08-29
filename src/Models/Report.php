<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Omnibus\Models;

use DateTime;
use JsonSerializable;
use Doctrine\ORM\Mapping as ORM;


/**
 * @package Omnibus\Models
 * @ORM\Entity
 * @ORM\Table(name="reports",
 *      uniqueConstraints={
 *         @ORM\UniqueConstraint(
 *              name="report_unique",
 *              columns={"comment_id", "user_id"}
 *          )
 *     }
 * )
 */
class Report implements JsonSerializable
{

    /**
     * @var int $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Comment $comment
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="reports")
     */
    private $comment;

    /**
     * @var User $user
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @var DateTime $date
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $date;

    /**
     * @var string $reason
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

    /**
     * Specify data which should be serialized to JSON
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'date' => $this->date->format('d.m.Y H:i'),
            'reason' => $this->reason,
            'comment' => $this->comment->getId(),
            'reporter' => $this->user
        ];
    }
}
