<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 20.08.2019, 18:30
 */

namespace Omnibus\Models;

use JsonSerializable;
use Doctrine\ORM\Mapping as ORM;
use Omnibus\Core\Utility\ParsedownExtended;


/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 */
class Category implements JsonSerializable
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
     * @var string|null $image
     * @ORM\Column(type="string", nullable=true)
     */
    private $image;


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
     * @return Category
     */
    public function setName(string $name): Category
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
     * @return Category
     */
    public function setDescription(string $description): Category
    {
        $this->description = $description;
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
     * @return Category
     */
    public function setImage(?string $image): Category
    {
        $this->image = $image;
        return $this;
    }

    public function __toString()
    {
        return $this->name;
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
            'description' => $this->description,
            'parsed_description' => $pd->parse($this->description),
            'image' => $this->image ?: 'https://via.placeholder.com/300'
        ];
        return (object) $out;
    }
}
