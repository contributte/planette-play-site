<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\Model;

use Nette,
	Nette\Database\Context;

abstract class BaseRepository extends Nette\Object
{
	/** @var \Nette\Database\Context */
	protected $database;

	/** @var string */
	public $table;

	/**
	 * @param \Nette\Database\Context $database
	 */
	public function __construct(Context $database)
	{
		$this->database = $database;
	}


	/**
	 * @return Nette\Database\Table\Selection
	 */
	public function table()
	{
		return $this->database->table($this->table);
	}


	/********************* find* methods & get *********************/


	/**
	 * @param int $id
	 * @return Nette\Database\Table\IRow
	 */
	public function get($id)
	{
		return $this->table()->get($id);
	}


	/**
	 * @return int
	 */
	public function getCount()
	{
		return $this->table()->count();
	}


	/**
	 * @param int $from
	 * @param int $limit
	 * @return Nette\Database\Table\Selection
	 */
	public function findAll()
	{
		return $this->table();
	}


	/**
	 * @param $query
	 * @return Nette\Database\Table\Selection
	 */
	public function findFulltext($query)
	{
		return $this->table()
			->where('title LIKE ? OR content LIKE ?',
				array(
					"%" . $query . "%",
					"%" . $query . "%")
			);
	}


	/********************* update* methods *********************/


	/**
	 * @param $data
	 * @throws \Aprila\DuplicateEntryException
	 * @return Nette\Database\Table\IRow
	 */
	public function insert($data)
	{
		try {
			$newRow = $this->table()->insert($data);

		} catch (\PDOException $e) {
			if ($e->getCode() == '23000') {
				throw new DuplicateEntryException;
			} else {
				throw $e;
			}
		}

		return $newRow;
	}


	/**
	 * @param $id
	 * @param $data
	 * @throws \Aprila\DuplicateEntryException
	 * @return Nette\Database\Table\IRow
	 */
	public function update($id, $data)
	{
		try {
			$this->get($id)->update($data);

		} catch (\PDOException $e) {
			if ($e->getCode() == '23000') {
				throw new DuplicateEntryException;
			} else {
				throw $e;
			}
		}

		return $this->get($id);
	}


	/**
	 * @param $id int
	 * @return bool
	 */
	public function hardDelete($id)
	{
		$this->get($id)->delete();

		return TRUE;
	}


	/**
	 * @param $id
	 * @return bool
	 */
	public function delete($id)
	{
		return $this->hardDelete($id);
	}
}