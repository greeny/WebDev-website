<?php
/**
 * @author Tomáš Blatný
 */

namespace WebDev\Model;

class PostRepository extends BaseRepository {
	public function getLast()
	{
		return $this->createEntities($this->connection->select('*')
			->from($this->getTable())
			->orderBy('time DESC')
			->limit(3)
			->fetchAll()
		);
	}
}
 