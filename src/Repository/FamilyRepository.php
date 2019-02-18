<?php

namespace App\Repository;

use App\Entity\Family;
use App\Entity\ParentFamilyLink;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Family|null find($id, $lockMode = null, $lockVersion = null)
 * @method Family|null findOneBy(array $criteria, array $orderBy = null)
 * @method Family[]    findAll()
 * @method Family[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FamilyRepository extends ServiceEntityRepository {
	public function __construct(RegistryInterface $registry) {
		parent::__construct($registry, Family::class);
	}
	
	public function getCaretakersOf(Family $family) {
		$qb = $this->getEntityManager()->createQueryBuilder();
		return $qb->select('u')
			->from(User::class, 'u')
			->innerJoin(ParentFamilyLink::class, 'pfl', Join::WITH, 'u.id = pfl.parent_id')
			->innerJoin(Family::class, 'f', Join::WITH, 'pfl.family_id = f.id')
			->andWhere('f.id = ?1')
			->setParameter(1, $family->getId())
			->getQuery()
			->getResult();
	}
	
	// /**
	//  * @return Family[] Returns an array of Family objects
	//  */
	/*
	public function findByExampleField($value)
	{
		return $this->createQueryBuilder('f')
			->andWhere('f.exampleField = :val')
			->setParameter('val', $value)
			->orderBy('f.id', 'ASC')
			->setMaxResults(10)
			->getQuery()
			->getResult()
		;
	}
	*/
	
	/*
	public function findOneBySomeField($value): ?Family
	{
		return $this->createQueryBuilder('f')
			->andWhere('f.exampleField = :val')
			->setParameter('val', $value)
			->getQuery()
			->getOneOrNullResult()
		;
	}
	*/
}
