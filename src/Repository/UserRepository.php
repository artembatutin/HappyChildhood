<?php
/**
 * Created by PhpStorm.
 * User: Artem Batutin
 * Date: 2018-12-13
 * Time: 1:31 AM
 */

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository {
	
	public function __construct(RegistryInterface $registry) {
		parent::__construct($registry, User::class);
	}
	
}