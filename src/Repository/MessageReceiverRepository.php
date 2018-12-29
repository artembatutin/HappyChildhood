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
class MessageReceiverRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MessageReceiver::class);
    }

    // /**
    //  * @return MessageReceiver[] Returns an array of MessageReceiver objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MessageReceiver
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
