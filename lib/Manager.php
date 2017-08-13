<?php

namespace OCA\FreeContent;

use OCP\DB\QueryBuilder\IQueryBuilder;

use OCP\IConfig;

use OCP\IDBConnection;

use OCP\IUserSession;

class Manager {

	/** @var IConfig */
	protected $config;

	/** @var IDBConnection */
	protected $connection;

	/** @var IUserSession */
	protected $userSession;

	/**
	 * @param IConfig $config
	 * @param IDBConnection $connection
	 * @param IUserSession $userSession
	 */
	public function __construct(IConfig $config,
								IDBConnection $connection,
								IUserSession $userSession) {
		$this->config = $config;
		$this->connection = $connection;
		$this->userSession = $userSession;
	}

	/**
	 * @param string $userId
	 * @param string $identifier
	 * @param string $title
	 * @param string $content
	 * @param boolean $public
	 */
	public function insert($userId, $identifier, $title, $content, $public) {
		$identifier = $this->sanitizeId(trim($identifier));
		$content = trim($content);
		
		if (isset($identifier[32])) {
			throw new \InvalidArgumentException('Invalid identifier', 1);
		}
		
		if ($identifier === '') {
			throw new \InvalidArgumentException('Invalid identifier', 2);
		}
		
		if (isset($title[128])) {
			throw new \InvalidArgumentException('Invalid title', 1);
		}
		
		if ($title === '') {
			throw new \InvalidArgumentException('Invalid title', 2);
		}
		
		if (isset($content[1024])) {
			throw new \InvalidArgumentException('Invalid content', 1);
		}
		
		if ($content === '') {
			throw new \InvalidArgumentException('Invalid content', 2);
		}
		
		$queryBuilder = $this->connection->getQueryBuilder();
		
		$queryBuilder->insert('freecontent')
			->values([
				'id'=> $queryBuilder->createNamedParameter($identifier),
				'user_id_created' => $queryBuilder->createNamedParameter($userId),
				'time_created' => $queryBuilder->createNamedParameter(time()),
				'user_id_modified' => $queryBuilder->createNamedParameter($userId),
				'time_modified' => $queryBuilder->createNamedParameter(time()),
				'title' => $queryBuilder->createNamedParameter($title),
				'content' => $queryBuilder->createNamedParameter($content),
				'public' => $queryBuilder->createNamedParameter($public),
 			]);
		$queryBuilder->execute();

		return $identifier;
	}

	/**
	 * @param string $userId
	 * @param string $identifier
	 * @param string $title
	 * @param string $content
	 * @param boolean $public
	 */
	public function update($userId, $identifier, $title, $content, $public) {
		$identifier = trim($identifier);
		$content = trim($content);
		
		if (isset($identifier[32])) {
			throw new \InvalidArgumentException('Invalid identifier', 1);
		}
		
		if ($identifier === '') {
			throw new \InvalidArgumentException('Invalid identifier', 2);
		}
		
		if (isset($title[128])) {
			throw new \InvalidArgumentException('Invalid title', 1);
		}
		
		if ($title === '') {
			throw new \InvalidArgumentException('Invalid title', 2);
		}
		
		if (isset($content[1024])) {
			throw new \InvalidArgumentException('Invalid content', 1);
		}
		
		if ($content === '') {
			throw new \InvalidArgumentException('Invalid content', 2);
		}
		
		$queryBuilder = $this->connection->getQueryBuilder();
		
		$queryBuilder->update('freecontent')
			->set('user_id_modified', $queryBuilder->createNamedParameter($userId))
			->set('time_modified', $queryBuilder->createNamedParameter(time()))
			->set('title', $queryBuilder->createNamedParameter($title))
			->set('content', $queryBuilder->createNamedParameter($content))
			->set('public', $queryBuilder->createNamedParameter($public))
			->where('id = :id')
			->setParameter(':id', $identifier);
		$queryBuilder->execute();
	}

	/**
	 * @param string $identifier
	 */
	public function remove($identifier) {
		
		$queryBuilder = $this->connection->getQueryBuilder();
		
		$queryBuilder->delete('freecontent')
			->where('id = :id')
			->setParameter(':id', $identifier);
		$queryBuilder->execute();
	}

	/**
	 * @param string $identifier
	 * @param boolean $publicOnly
	 */
	public function getEntry($identifier, $publicOnly = false) {
		$query = $this->connection->getQueryBuilder();
		
		$query->select('*')
			->from('freecontent')
			->where('id = :id')
			->setParameter(':id', $identifier)
			->setMaxResults(1);

		if ($publicOnly) {
			$query->where('public = :public')
				->setParameter(':public', true);
		}

		$result = $query->execute();
		$entry = $result->fetch();
		$result->closeCursor();
		if (!$entry) {
			throw new \InvalidArgumentException('Invalid ID');
		}

		$freecontent = [
			'id' => $entry['id'],
			'user_id_created' => $entry['user_id_created'],
			'time_created' => $entry['time_created'],
			'user_id_modified' => $entry['user_id_modified'],
			'time_modified' => $entry['time_modified'],
			'title' => $entry['title'],
			'content' => $entry['content'],
			'public' => $entry['public'],
		];

		return $freecontent;
	}

	/**
	 * @param string $identifier
	 * @param boolean $publicOnly
	 */
	public function getEntries($limit = 10, $offset = 0, $publicOnly = false) {
		$query = $this->connection->getQueryBuilder();
		
		$query->select('*')
			->from('freecontent')
			->orderBy('id', 'asc')
			->setFirstResult($offset)
			->setMaxResults($limit);

		if ($publicOnly) {
			$query->where('public = :public')
				->setParameter(':public', true);
		}

		$result = $query->execute();

		$freecontents = [];
		while ($entry = $result->fetch()) {
			$freecontents[] = [
				'id' => $entry['id'],
				'user_id_created' => $entry['user_id_created'],
				'time_created' => $entry['time_created'],
				'user_id_modified' => $entry['user_id_modified'],
				'time_modified' => $entry['time_modified'],
				'title' => $entry['title'],
				'content' => $entry['content'],
				'public' => $entry['public'],
			];
		}

		$result->closeCursor();

		return $freecontents;
	}

	protected function sanitizeId($id) {
		return strtolower(str_replace(['/', '\', "\r\n", "\n", "\r", ' '], '-', $id));
	}
}