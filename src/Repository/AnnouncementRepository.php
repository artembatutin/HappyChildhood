<?php

namespace App\Repository;

use App\Entity\Announcement;
use App\Entity\AnnouncementViewers;
use App\Entity\Group;
use App\Entity\User;
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
	 * Grabs all announcement in date order format.
	 * @return array
	 */
	public function allOrdered(): array
	{
		$qb = $this->createQueryBuilder('a');
		$qb = $qb
			->select('a')
			->orderBy('a.creation_date', 'ASC')
			->getQuery();
		return $qb->execute();
	}
	
	/**
	 * @param User $user
	 * @return array
	 */
	public function findForUser(User $user): array
	{
		$groups = $this->getEntityManager()->getRepository(Group::class)->getUserGroups($user);
		
		$qb = $this->createQueryBuilder('a');
		$qb ->join(AnnouncementViewers::class, 'av');
		$qb ->where('a.public = 1');
		
		if(sizeof($groups) > 0) {
			foreach($groups as $index=>$group) {
				$qb ->orWhere('av.child_group = ?'.$index)
					->setParameter($index, $group->getId());
			}
		}
		$qb ->andWhere('a.hidden = 0')
			->orderBy('a.creation_date', 'DESC');
		
		return $qb->getQuery()->getResult();
	}
	
	/**
	 * @param User $user
	 * @param $block_id block id.
	 * @return array
	 */
	public function findUserBlock(User $user, $block_id): array
	{
		$groups = $this->getEntityManager()->getRepository(Group::class)->getUserGroups($user);
		
		$qb = $this->createQueryBuilder('a');
		$qb ->join(AnnouncementViewers::class, 'av');
		$qb ->where('a.public = 1');
		
		if(sizeof($groups) > 0) {
			foreach($groups as $index=>$group) {
				$qb ->orWhere('av.child_group = ?'.$index)
				    ->setParameter($index, $group->getId());
			}
		}
		$qb ->andWhere('a.hidden = 0')
			->andWhere('a.id = ' . $block_id);
		
		return $qb->getQuery()->getResult();
	}
}
