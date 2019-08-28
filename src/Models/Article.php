<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 20.08.2019, 18:30
 */

namespace Omnibus\Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\Column(type="datetime", nullable=false)
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $image;

    /**
     * @var User $author
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $author;

    /**
     * @var Category $category
     * @ORM\ManyToOne(targetEntity="Category")
     */
    private $category;

    /**
     * @var Collection $tags
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="articles")
     */
    private $tags;

    /**
     * @var CommentThread $comments
     * @ORM\OneToOne(targetEntity="CommentThread", cascade={"persist", "remove"})
     */
    private $comments;

    /**
     * Article constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->date = new DateTime();
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
     *
     * @return Article
     */
    public function setTitle(string $title): Article
    {
        $this->title = $title;
        return $this;
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
     *
     * @return Article
     */
    public function setDate(DateTime $date): Article
    {
        $this->date = $date;
        return $this;
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
     *
     * @return Article
     */
    public function setBody(string $body): Article
    {
        $this->body = $body;
        return $this;
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
     *
     * @return Article
     */
    public function setExcerpt(string $excerpt): Article
    {
        $this->excerpt = $excerpt;
        return $this;
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
     *
     * @return Article
     */
    public function setImage(?string $image): Article
    {
        $this->image = $image;
        return $this;
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
     *
     * @return Article
     */
    public function setAuthor(User $author): Article
    {
        $this->author = $author;
        return $this;
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
     *
     * @return Article
     */
    public function setCategory(Category $category): Article
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @param Tag  $tag
     *
     * @param bool $unique
     *
     * @return Article
     */
    public function addTag(Tag $tag, bool $unique = true): Article
    {
        if ($unique && $this->tags->contains($tag)) {
            return $this;
        }
        $this->tags[] = $tag;
        $tag->addArticle($this);
        return $this;
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
     *
     * @return Article
     */
    public function setComments(CommentThread $comments): Article
    {
        $this->comments = $comments;
        return $this;
    }



}
