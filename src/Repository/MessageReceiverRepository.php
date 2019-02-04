<?php

namespace App\Repository;

use App\Entity\MessageReceiver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
		          ->set('mr.message.message_file', $qb->expr()
		                                              ->substring('mr.message.message_file', 1, 150))
		          ->orderBy('m.date_sent', 'DESC')
		          ->setParameter(1, $receiver_inbox_id)
		          ->getQuery()
		          ->getResult();
	}
	
}
