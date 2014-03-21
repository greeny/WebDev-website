<?php
/**
 * @author Tomáš Blatný
 */

namespace WebDev\Security;

use Nette\Security\Permission;

class Authorizator extends Permission {


	public function __construct()
	{
		$this->addRole('member');
		$this->addRole('admin', 'member');
		$this->addRole('owner', 'admin');

		$this->addResource('post');
		$this->addResource('user');
		$this->addResource('comment');

		$this->allow('member', 'post', array('view', 'compose', 'edit', 'delete'));
		$this->allow('admin', 'post', array('f-edit', 'f-delete'));

		$this->allow('owner');
	}
}
 