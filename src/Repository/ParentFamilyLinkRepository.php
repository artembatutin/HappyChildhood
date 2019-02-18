<?php

namespace App\Repository;

use App\Entity\Family;
use App\Entity\ParentFamilyLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ParentFamilyLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParentFamilyLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParentFamilyLink[]    findAll()
 * @method ParentFamilyLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParentFamilyLinkRepository extends ServiceEntityRepository {
	public function __construct(RegistryInterface $registry) {
		parent::__construct($registry, ParentFamilyLink::class);
	}
}
