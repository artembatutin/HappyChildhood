<?php

namespace App\Repository;

use App\Entity\Child;
use App\Entity\Family;
use App\Entity\Group;
use App\Entity\ParentFamilyLink;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository {
	public function __construct(RegistryInterface $registry) {
		parent::__construct($registry, Group::class);
	}
	
	public function getUserGroups(User $user) {
		$qb = $this ->createQueryBuilder('g');
		return $qb  ->select('g')
					->innerJoin(Child::class, 'c')
					->innerJoin(Family::class, 'f')
					->innerJoin(ParentFamilyLink::class, 'pfl')
					->where('pfl.parent_id = ?1')
					->groupBy('g')
					->setParameter(1, $user->getId())
					->getQuery()
					->getResult();
	}
	
	// /**
	//  * @return Group[] Returns an array of Group objects
	//  */
	/*
	public function findByExampleField($value)
	{
		return $this->createQueryBuilder('g')
			->andWhere('g.exampleField = :val')
			->setParameter('val', $value)
			->orderBy('g.id', 'ASC')
			->setMaxResults(10)
			->getQuery()
			->getResult()
		;
	}
	*/
	
	/*
	public function findOneBySomeField($value): ?Group
	{
		return $this->createQueryBuilder('g')
			->andWhere('g.exampleField = :val')
			->setParameter('val', $value)
			->getQuery()
			->getOneOrNullResult()
		;
	}
	*/
}
