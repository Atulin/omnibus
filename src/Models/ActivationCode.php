<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Omnibus\Models;

use Omnibus\Core\Model;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\OptimisticLockException;


/**
 * Class ActivationCode
 * @package Omnibus\Models
 * @ORM\Entity
 * @ORM\Table(name="activation_codes")
 */
class ActivationCode extends Model
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
//     * @ORM\Column(type="integer")
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
     *
     * @return ActivationCode
     */
    public function setUserId(int $user_id): ActivationCode
    {
        $this->user_id = $user_id;
        return $this;
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
     *
     * @return ActivationCode
     */
    public function setCode(string $code): ActivationCode
    {
        $this->code = $code;
        return $this;
    }
}
