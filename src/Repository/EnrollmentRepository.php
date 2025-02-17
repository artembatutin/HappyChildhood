<?php

namespace App\Repository;

use App\Entity\Enrollment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Enrollment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Enrollment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Enrollment[]    findAll()
 * @method Enrollment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnrollmentRepository extends ServiceEntityRepository {
	public function __construct(RegistryInterface $registry) {
		parent::__construct($registry, Enrollment::class);
	}
	
	public function getAllOrderedByDateDesc() {
		$qb = $this->createQueryBuilder('enrl');
		return $qb  ->select()
					->orderBy('enrl.creation_date', 'DESC')
					->getQuery()
					->getResult();
	}
	
	public function getUsable($email) {
		$qb = $this->createQueryBuilder('enrl');
		return $qb  ->select()
					->where('enrl.email = ?1')
					->andWhere('enrl.expired = 0')
					->andWhere('enrl.canAddChild = 1')
					->setParameter(1, $email)
					->getQuery()
					->getResult();
	}
	
	// /**
	//  * @return Enrollment[] Returns an array of Enrollment objects
	//  */
	/*
	public function findByExampleField($value)
	{
		return $this->createQueryBuilder('e')
			->andWhere('e.exampleField = :val')
			->setParameter('val', $value)
			->orderBy('e.id', 'ASC')
			->setMaxResults(10)
			->getQuery()
			->getResult()
		;
	}
	*/
	
	/*
	public function findOneBySomeField($value): ?Enrollment
	{
		return $this->createQueryBuilder('e')
			->andWhere('e.exampleField = :val')
			->setParameter('val', $value)
			->getQuery()
			->getOneOrNullResult()
		;
	}
	*/
}
