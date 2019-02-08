<?php

namespace App\Repository;

use App\Entity\Announcement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Announcement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Announcement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Announcement[]    findAll()
 * @method Announcement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnouncementRepository extends ServiceEntityRepository {
	public function __construct(RegistryInterface $registry) {
		parent::__construct($registry, Announcement::class);
	}
	
	/**
	 * @param $group
	 * @return array
	 */
	public function findForGroups($group): array
	{
		$qb = $this->createQueryBuilder('a');
		$qb = $qb
			->join('a.announcementViewers', 'v')
			->where('a.hidden = false')
			->where('v.child_group = :group')
			->setParameter('group', $group)
			->getQuery();
		return $qb->execute();
	}
}
