<?php

namespace App\Repository;

use App\Entity\PreliminaryRegistration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PreliminaryRegistration>
 */
class PreliminaryRegistrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PreliminaryRegistration::class);
    }
}

