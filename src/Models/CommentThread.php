<?php
namespace Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class CommentThread
 * @package Models
 * @ORM\Entity
 * @ORM\Table(name="threads")
 */
class CommentThread
{
    /**
     * @var int $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var ArrayCollection $comments
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="thread")
     */
    private $comments;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return ArrayCollection
     */
    public function getComments(): ArrayCollection
    {
        return $this->comments;
    }

    /**
     * @param ArrayCollection $comments
     */
    public function setComments(ArrayCollection $comments): void
    {
        $this->comments = $comments;
    }

    /**
     * @param Comment $comment
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;
    }
}
