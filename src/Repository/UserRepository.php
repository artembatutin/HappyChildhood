<?php
/**
 * Created by PhpStorm.
 * User: Artem Batutin
 * Date: 2018-12-13
 * Time: 1:31 AM
 */

namespace App\Repository;

use App\Entity\Child;
use App\Entity\Family;
use App\Entity\ParentFamilyLink;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository {
	
	public function __construct(RegistryInterface $registry) {
		parent::__construct($registry, User::class);
	}
	
	/**
	 * Finds only staff users.
	 * @return array
	 */
	public function findStaffOnly(): array
	{
		$qb = $this->createQueryBuilder('u');
		$qb = $qb
			->select('u')
			->where('u.roles in (:moderator)')
			->orWhere('u.roles in (:administrator)')
			->setParameter('moderator', 'ROLE_MOD')
			->setParameter('administrator', 'ROLE_ADMIN')
			->getQuery();
		return $qb->execute();
	}
	
	/**
	 * @param string $role
	 * @return array
	 */
	public function findByRole($role)
	{
		$qb = $this->_em->createQueryBuilder();
		$qb->select('u')
			->from($this->_entityName, 'u')
			->where('u.roles LIKE :roles')
			->setParameter('roles', '%"'.$role.'"%');
		
		return $qb->getQuery()->getResult();
	}
	
	public function getCaretakersOf(Child $child) {
		$qb = $this->getEntityManager()->createQueryBuilder();
		return $qb->select('p')
			->from(User::class, 'p')
			->innerJoin(ParentFamilyLink::class, 'pfl', Join::WITH, 'p.id = pfl.parent_id')
			->innerJoin(Family::class, 'f', Join::WITH, 'pfl.family_id = f.id')
			->innerJoin(Child::class, 'c', Join::WITH, 'f.id = c.family')
			->andWhere('c.id = ?1')
			->setParameter(1, $child->getId())
			->getQuery()
			->getResult();
	}
}