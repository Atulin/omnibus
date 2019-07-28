<?php
namespace Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class RecoveryCode
 * @package Models
 * @ORM\Entity
 * @ORM\Table(name="recovery_codes")
 */
class RecoveryCode
{

    /**
     * @var int $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int $user_id
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @ORM\Column(type="integer")
     */
    private $user_id;

    /**
     * @var string $code
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $code;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }
}
