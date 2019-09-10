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
     * @var bool $canManageArticles
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $canManageArticles;

    /**
     * @var bool $canAddCategories
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $canAddCategories;
    /**
     * @var bool $canManageCategories
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $canManageCategories;

    /**
     * @var bool $canAddTags
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $canAddTags;
    /**
     * @var bool $canManageTags
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $canManageTags;



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
        return $this->isAdmin ?? false;
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
        return $this->isStaff ?? false;
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
        return $this->canModerateComments ?? false;
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
        return $this->canAddArticles ?? false;
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
    public function canManageArticles(): bool
    {
        return $this->canManageArticles ?? false;
    }

    /**
     * @param bool $canManageArticles
     *
     * @return Role
     */
    public function setCanManageArticles(bool $canManageArticles): Role
    {
        $this->canManageArticles = $canManageArticles;
        return $this;
    }

    /**
     * @return bool
     */
    public function canAddCategories(): bool
    {
        return $this->canAddCategories ?? false;
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
    public function canManageCategories(): bool
    {
        return $this->canManageCategories ?? false;
    }

    /**
     * @param bool $canManageCategories
     *
     * @return Role
     */
    public function setCanManageCategories(bool $canManageCategories): Role
    {
        $this->canManageCategories = $canManageCategories;
        return $this;
    }

    /**
     * @return bool
     */
    public function canAddTags(): bool
    {
        return $this->canAddTags ?? false;
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
    public function canManageTags(): bool
    {
        return $this->canManageTags ?? false;
    }

    /**
     * @param bool $canManageTags
     *
     * @return Role
     */
    public function setCanManageTags(bool $canManageTags): Role
    {
        $this->canManageTags = $canManageTags;
        return $this;
    }


}
