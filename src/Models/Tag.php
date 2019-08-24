<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 20.08.2019, 18:30
 */

namespace Models;

use JsonSerializable;
use Doctrine\ORM\Mapping as ORM;
use Core\Utility\ParsedownExtended;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(name="tag")
 */
class Tag implements JsonSerializable
{
    /**
     * @var int $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     */
    private $id;

    /**
     * @var string $name
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @var string $description
     * @ORM\Column(type="string", nullable=false)
     */
    private $description;

    /**
     * @var ArrayCollection $articles
     * @ORM\ManyToMany(targetEntity="Article", mappedBy="tags")
     */
    private $articles;

    /**
     * Tag constructor.
     */
    public function __construct()
    {
        $this->articles = new ArrayCollection();
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Tag
     */
    public function setName(string $name): Tag
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Tag
     */
    public function setDescription(string $description): Tag
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getArticles(): ArrayCollection
    {
        return $this->articles;
    }

    /**
     * @param Article $article
     *
     * @return Tag
     */
    public function addArticle(Article $article): Tag
    {
        $this->articles[] = $article;
        return $this;
    }

    /**
     * @param Article $article
     *
     * @return Tag
     */
    public function removeArticle(Article $article): Tag
    {
        $this->articles->removeElement($article);
        return $this;
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

        $out = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $pd->parse($this->description),
            'articles' => $this->articles
        ];
        return (object) $out;
    }

}
