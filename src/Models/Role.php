<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Omnibus\Models;

use Doctrine\ORM\Mapping as ORM;


/**
 * Class Role
 * @package Omnibus\Models
 * @ORM\Entity
 * @ORM\Table(
 *      name="roles",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="role_name_idx", columns={"name"})
 *      }
 * )
 */
class Role
{
    /**
     * @var int $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string $name
     * @ORM\Column(type="string", length=64)
     */
    private $name;

    /**
     * Special access rights
     * @var bool $isAdmin
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $isAdmin;

    /**
     * Is the user even a staff member?
     * @var bool $isStaff
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $isStaff;

    /**
     * Comment moderation rights
     * @var bool $canModerateComments
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $canModerateComments;

    /**
     * @var bool $canAddArticles
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $canAddArticles;
    /**
     * @var bool $canEditArticles
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $canEditArticles;
    /**
     * @var bool $canDeleteArticles
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $canDeleteArticles;

    /**
     * @var bool $canAddCategories
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $canAddCategories;
    /**
     * @var bool $canEditCategories
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $canEditCategories;
    /**
     * @var bool $canDeleteCategories
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $canDeleteCategories;

    /**
     * @var bool $canAddTags
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $canAddTags;
    /**
     * @var bool $canEditTags
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $canEditTags;
    /**
     * @var bool $canDeleteTags
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $canDeleteTags;



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
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    /**
     * @param bool $isAdmin
     *
     * @return Role
     */
    public function setIsAdmin(bool $isAdmin): Role
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    /**
     * @return bool
     */
    public function isStaff(): bool
    {
        return $this->isStaff;
    }

    /**
     * @param bool $isStaff
     *
     * @return Role
     */
    public function setIsStaff(bool $isStaff): Role
    {
        $this->isStaff = $isStaff;
        return $this;
    }

    /**
     * @return bool
     */
    public function canModerateComments(): bool
    {
        return $this->canModerateComments;
    }

    /**
     * @param bool $canModerateComments
     *
     * @return Role
     */
    public function setCanModerateComments(bool $canModerateComments): Role
    {
        $this->canModerateComments = $canModerateComments;
        return $this;
    }

    /**
     * @return bool
     */
    public function canAddArticles(): bool
    {
        return $this->canAddArticles;
    }

    /**
     * @param bool $canAddArticles
     *
     * @return Role
     */
    public function setCanAddArticles(bool $canAddArticles): Role
    {
        $this->canAddArticles = $canAddArticles;
        return $this;
    }

    /**
     * @return bool
     */
    public function canEditArticles(): bool
    {
        return $this->canEditArticles;
    }

    /**
     * @param bool $canEditArticles
     *
     * @return Role
     */
    public function setCanEditArticles(bool $canEditArticles): Role
    {
        $this->canEditArticles = $canEditArticles;
        return $this;
    }

    /**
     * @return bool
     */
    public function canDeleteArticles(): bool
    {
        return $this->canDeleteArticles;
    }

    /**
     * @param bool $canDeleteArticles
     *
     * @return Role
     */
    public function setCanDeleteArticles(bool $canDeleteArticles): Role
    {
        $this->canDeleteArticles = $canDeleteArticles;
        return $this;
    }

    /**
     * @return bool
     */
    public function canAddCategories(): bool
    {
        return $this->canAddCategories;
    }

    /**
     * @param bool $canAddCategories
     *
     * @return Role
     */
    public function setCanAddCategories(bool $canAddCategories): Role
    {
        $this->canAddCategories = $canAddCategories;
        return $this;
    }

    /**
     * @return bool
     */
    public function canEditCategories(): bool
    {
        return $this->canEditCategories;
    }

    /**
     * @param bool $canEditCategories
     *
     * @return Role
     */
    public function setCanEditCategories(bool $canEditCategories): Role
    {
        $this->canEditCategories = $canEditCategories;
        return $this;
    }

    /**
     * @return bool
     */
    public function canDeleteCategories(): bool
    {
        return $this->canDeleteCategories;
    }

    /**
     * @param bool $canDeleteCategories
     *
     * @return Role
     */
    public function setCanDeleteCategories(bool $canDeleteCategories): Role
    {
        $this->canDeleteCategories = $canDeleteCategories;
        return $this;
    }

    /**
     * @return bool
     */
    public function canAddTags(): bool
    {
        return $this->canAddTags;
    }

    /**
     * @param bool $canAddTags
     *
     * @return Role
     */
    public function setCanAddTags(bool $canAddTags): Role
    {
        $this->canAddTags = $canAddTags;
        return $this;
    }

    /**
     * @return bool
     */
    public function canEditTags(): bool
    {
        return $this->canEditTags;
    }

    /**
     * @param bool $canEditTags
     *
     * @return Role
     */
    public function setCanEditTags(bool $canEditTags): Role
    {
        $this->canEditTags = $canEditTags;
        return $this;
    }

    /**
     * @return bool
     */
    public function canDeleteTags(): bool
    {
        return $this->canDeleteTags;
    }

    /**
     * @param bool $canDeleteTags
     *
     * @return Role
     */
    public function setCanDeleteTags(bool $canDeleteTags): Role
    {
        $this->canDeleteTags = $canDeleteTags;
        return $this;
    }


}
