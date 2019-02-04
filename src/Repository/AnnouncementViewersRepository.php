<?php

namespace App\Repository;

use App\Entity\AnnouncementViewers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AnnouncementViewers|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnnouncementViewers|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnnouncementViewers[]    findAll()
 * @method AnnouncementViewers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnouncementViewersRepository extends ServiceEntityRepository {
	public function __construct(RegistryInterface $registry) {
		parent::__construct($registry, AnnouncementViewers::class);
	}
	
	// /**
	//  * @return AnnouncementViewers[] Returns an array of AnnouncementViewers objects
	//  */
	/*
	public function findByExampleField($value)
	{
		return $this->createQueryBuilder('a')
			->andWhere('a.exampleField = :val')
			->setParameter('val', $value)
			->orderBy('a.id', 'ASC')
			->setMaxResults(10)
			->getQuery()
			->getResult()
		;
	}
	*/
	
	/*
	public function findOneBySomeField($value): ?AnnouncementViewers
	{
		return $this->createQueryBuilder('a')
			->andWhere('a.exampleField = :val')
			->setParameter('val', $value)
			->getQuery()
			->getOneOrNullResult()
		;
	}
	*/
}
