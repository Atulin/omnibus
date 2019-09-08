<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 07.09.2019, 23:53
 */

namespace Omnibus\Models\Repositories;
use Omnibus\Models\User;
use Omnibus\Core\Repository;
use Doctrine\ORM\NonUniqueResultException;


class UserRepository extends Repository
{
    protected const ENTITY = User::class;

    /**
     * @param string $name
     * @param string $email
     *
     * @return bool True if user was found
     */
    public function checkNameOrEmailTaken(string $name, string $email): bool
    {
        try {
            $c = $this->createCountQueryBuilder('u')
                ->where('u.name = :name')
                ->orWhere('u.email = :email')
                ->setParameter('name', $name)
                ->setParameter('email', $email)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return true;
        }
        return $c !== 0;
    }
}
