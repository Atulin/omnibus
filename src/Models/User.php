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
 * Class User
 * @package Omnibus\Models
 * @ORM\Entity
 * @ORM\Table(
 *     name="users",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="user_name_idx", columns={"name"}),
 *          @ORM\UniqueConstraint(name="user_email_idx", columns={"email"}),
 *      }
 * )
 */
class User implements JsonSerializable
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
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $bio;

    /**
     * @var string|null $title
     * @ORM\Column(type="string", length=20, nullable=true)
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
     * @var
     * @ORM\OneToOne(targetEntity="CommentThread", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="thread_id", referencedColumnName="id")
     */
    private $comment_thread;

    /**
     * @var string|null $mfa
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
        $this->setCommentThread(new CommentThread());
    }

    /**
     * @return string|null
     */
    public function getBio(): ?string
    {
        return $this->bio;
    }

    /**
     * @param string|null $bio
     *
     * @return User
     */
    public function setBio(?string $bio): User
    {
        $this->bio = $bio;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     *
     * @return User
     */
    public function setTitle(?string $title): User
    {
        $this->title = $title;
        return $this;
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
     * @return User
     */
    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * @param string|null $avatar
     *
     * @return User
     */
    public function setAvatar(?string $avatar): User
    {
        $this->avatar = $avatar;
        return $this;
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
     *
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
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
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
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
     *
     * @return User
     */
    public function setRememberMe(string $remember_me): User
    {
        $this->remember_me = $remember_me;
        return $this;
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
     *
     * @return User
     */
    public function setCreationDate(DateTime $creation_date): User
    {
        $this->creation_date = $creation_date;
        return $this;
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
     *
     * @return User
     */
    public function setLastSeen(DateTime $last_seen): User
    {
        $this->last_seen = $last_seen;
        return $this;
    }

    /**
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role ?: new Role();
    }

    /**
     * @param Role $role
     *
     * @return User
     */
    public function setRole(Role $role): User
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCommentThread()
    {
        return $this->comment_thread;
    }

    /**
     * @param mixed $comment_thread
     *
     * @return User
     */
    public function setCommentThread($comment_thread): User
    {
        $this->comment_thread = $comment_thread;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMfa(): ?string
    {
        return $this->mfa;
    }

    /**
     * @param string|null $mfa
     *
     * @return User
     */
    public function setMfa(?string $mfa): User
    {
        $this->mfa = $mfa;
        return $this;
    }


    /**
     * Specify data which should be serialized to JSON.
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource.
     *
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return (object)[
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}
