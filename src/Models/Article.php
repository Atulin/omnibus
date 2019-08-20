<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 20.08.2019, 18:30
 */

namespace Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 */
class Article
{
    /**
     * @var int $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     */
    private $id;

    /**
     * @var string $title
     * @ORM\Column(type="string", nullable=false)
     */
    private $title;

    /**
     * @var DateTime $date
     * @ORM\Column(type="string", nullable=false)
     */
    private $date;

    /**
     * @var string $body
     * @ORM\Column(type="string", nullable=false)
     */
    private $body;

    /**
     * @var string $excerpt
     * @ORM\Column(type="string", nullable=false)
     */
    private $excerpt;

    /**
     * @var string|null $image
     * @ORM\Column(type="string")
     */
    private $image;

    /**
     * @var User $author
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\Column(nullable=false)
     */
    private $author;

    /**
     * @var Category $category
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\Column(nullable=false)
     */
    private $category;

    /**
     * @var ArrayCollection $tags
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="articles")
     */
    private $tags;

    /**
     * @var CommentThread $comments
     * @ORM\OneToOne(targetEntity="CommentThread", cascade={"persist", "remove"})
     * @ORM\Column(nullable=false)
     */
    private $comments;

    /**
     * Article constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
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
     * @return string
     */
    public function getExcerpt(): string
    {
        return $this->excerpt;
    }

    /**
     * @param string $excerpt
     */
    public function setExcerpt(string $excerpt): void
    {
        $this->excerpt = $excerpt;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
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
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    /**
     * @return ArrayCollection
     */
    public function getTags(): ArrayCollection
    {
        return $this->tags;
    }

    /**
     * @param Tag $tag
     */
    public function addTag(Tag $tag): void
    {
        $this->tags[] = $tag;
        $tag->addArticle($this);
    }

    /**
     * @param Tag $tag
     */
    public function removeTag(Tag $tag): void
    {
        $this->tags->removeElement($tag);
        $tag->removeArticle($this);
    }

    /**
     * @return CommentThread
     */
    public function getComments(): CommentThread
    {
        return $this->comments;
    }

    /**
     * @param CommentThread $comments
     */
    public function setComments(CommentThread $comments): void
    {
        $this->comments = $comments;
    }



}
