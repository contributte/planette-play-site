<?php

namespace App\Model;

use Nette,
	Nette\Database\Context;


class UserManager extends Nette\Object
{

	/** @var Context */
	private $db;


	public function __construct(Context $db)
	{
		$this->db = $db;
	}


	/**
	 * @param  array
	 * @return Nette\Database\Table\IRow
	 */
	public function create(array $data)
	{
		return $this->db->table('users')->insert($data);
	}


	/**
	 * @param  int
	 * @param  array
	 * @return Nette\Database\Table\IRow|FALSE
	 */
	public function signInUpdate($id, array $data)
	{
		$this->db->table('users')->where('id', $id)->update($data);
		return $this->db->table('users')->where('id', $id)->fetch();
	}

}
