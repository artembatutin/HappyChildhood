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
}
