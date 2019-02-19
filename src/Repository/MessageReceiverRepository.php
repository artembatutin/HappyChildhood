<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\MessageReceiver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MessageReceiver|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageReceiver|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageReceiver[]    findAll()
 * @method MessageReceiver[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageReceiverRepository extends ServiceEntityRepository {
	public function __construct(RegistryInterface $registry) {
		parent::__construct($registry, MessageReceiver::class);
	}
	
	public function getOrdered($receiver_inbox_id) {
		$qb = $this->createQueryBuilder('mr');
		return $qb->join('mr.message', 'm')
		          ->where('mr.receiver_inbox = ?1')
		          ->orderBy('m.date_sent', 'DESC')
		          ->setParameter(1, $receiver_inbox_id)
		          ->getQuery()
		          ->getResult();
	}
	
	/*
	public function getOrderedNested($receiver_inbox_id) {
		$qb = $this->getEntityManager()->createQueryBuilder();
		return $qb->select('mr')
			->from(MessageReceiver::class, 'mr')
			->innerJoin(Message::class, 'm', Join::WITH, 'mr.message = m.id')
			->innerJoin(Message::class, 'ch', Join::WITH, 'm.childMessage = ch.id')
			->where('mr.receiver_inbox = ?1')
			->andWhere('m.childMessage IS NULL')
			->orWhere('ch.childMessage IS NULL')
			->orderBy('m.date_sent', 'DESC')
			->setParameter(1, $receiver_inbox_id)
			->getQuery()
			->getResult();
	}
	*/
}
