<?php
/**
 * @author Tomáš Blatný
 */

namespace WebDev\Model;

use LeanMapper\Entity;
use Nette\ArrayHash;

abstract class BaseEntity extends Entity {
	/**
	 * @param ArrayHash $data
	 * @return static
	 */
	public static function from(ArrayHash $data)
	{
		$class = get_called_class();
		$entity = new $class;
		foreach($data as $key => $value) {
			$entity->$key = $value;
		}
		return $entity;
	}

	/**
	 * @param ArrayHash $data
	 */
	public function modify(ArrayHash $data)
	{
		foreach($data as $key => $value) {
			$this->$key = $value;
		}
	}
}
 