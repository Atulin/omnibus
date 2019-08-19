<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Role
 * @package Models
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
     * @var User[] $users
     * @ORM\OneToMany(targetEntity="User", mappedBy="role")
     */
    private $users;

    /**
     * @var bool $is_admin
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $is_admin;


    /**
     * @var bool $is_moderator
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $is_moderator;

    /**
     * Role constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param User $user
     */
    public function addUser(User $user): void
    {
        $this->users[] = $user;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * @param bool $is_admin
     */
    public function setIsAdmin(bool $is_admin): void
    {
        $this->is_admin = $is_admin;
    }

    /**
     * @return bool
     */
    public function isModerator(): bool
    {
        return $this->is_moderator;
    }

    /**
     * @param bool $is_moderator
     */
    public function setIsModerator(bool $is_moderator): void
    {
        $this->is_moderator = $is_moderator;
    }

}
