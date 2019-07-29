<?php

namespace Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class User
 * @package Models
 * @ORM\Entity
 * @ORM\Table(
 *     name="users",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="user_name_idx", columns={"name"}),
 *          @ORM\UniqueConstraint(name="user_email_idx", columns={"email"}),
 *      }
 * )
 */
class User
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
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $name;

    /**
     * @var string|null $avatar
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $avatar;

    /**
     * @var string $email
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private $email;

    /**
     * @var string $password
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $password;


    /**
     * @var string|null $bioŁ
     * @ORM\Column(type="string", nullable=true)
     */
    private $bio;

    /**
     * @var string|null $title
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $title;

    /**
     * @var string|null $remember_me
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $remember_me;

    /**
     * @var DateTime $creation_date
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $creation_date;

    /**
     * @var DateTime $last_seen
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $last_seen;

    /**
     * @var Role $role
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="users")
     */
    private $role;

    /**
     * @var string $mfa
     * @ORM\Column(type="string", nullable=true)
     */
    private $mfa;


    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->creation_date = new DateTime('now');
        $this->last_seen = new DateTime('now');
    }

    /**
     * @return string
     */
    public function getBio(): string
    {
        return $this->bio;
    }

    /**
     * @param string $bio
     */
    public function setBio(string $bio): void
    {
        $this->bio = $bio;
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
     * @return string
     */
    public function getAvatar(): string
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar(string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getRememberMe(): string
    {
        return $this->remember_me;
    }

    /**
     * @param string $remember_me
     */
    public function setRememberMe(string $remember_me): void
    {
        $this->remember_me = $remember_me;
    }

    /**
     * @return DateTime
     */
    public function getCreationDate(): DateTime
    {
        return $this->creation_date;
    }

    /**
     * @param DateTime $creation_date
     */
    public function setCreationDate(DateTime $creation_date): void
    {
        $this->creation_date = $creation_date;
    }

    /**
     * @return DateTime
     */
    public function getLastSeen(): DateTime
    {
        return $this->last_seen;
    }

    /**
     * @param DateTime $last_seen
     */
    public function setLastSeen(DateTime $last_seen): void
    {
        $this->last_seen = $last_seen;
    }

    /**
     * @return Role|null
     */
    public function getRole(): ?Role
    {
        return $this->role;
    }

    /**
     * @param Role $role
     */
    public function setRole(Role $role): void
    {
        $role->addUser($this);
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getMfa(): string
    {
        return $this->mfa;
    }

    /**
     * @param string $mfa
     */
    public function setMfa(string $mfa): void
    {
        $this->mfa = $mfa;
    }



}
