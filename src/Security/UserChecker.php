<?php
/**
 * Created by PhpStorm.
 * User: dorin
 * Date: 13-Feb-2019
 * Time: 11:10 PM
 */

namespace App\Security;


use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface {
	
	/**
	 * Checks the user account before authentication.
	 *
	 * @throws AccountStatusException
	 */
	public function checkPreAuth(UserInterface $user) {
		if(!$user instanceof User) {
			return;
		}
		
		if($user->isDisabled()) {
			throw new AccountExpiredException('Account was disabled');
		}
	}
	
	/**
	 * Checks the user account after authentication.
	 *
	 * @throws AccountStatusException
	 */
	public function checkPostAuth(UserInterface $user) {
		if(!$user instanceof User) {
			return;
		}
	}
}