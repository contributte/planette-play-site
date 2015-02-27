<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\Model;

use Aprila\Model\BaseRepository;


class TagRepository extends BaseRepository
{
	public $table = 'tag';


	public function findAll()
	{
		return $this->table()->where('type', 'normal');
	}


	public function findAllForAssign()
	{
		return $this->table()->where("type = ? OR type = ?", 'normal', 'category');
	}


	public function getByName($tag)
	{
		return $this->table()->where('name', $tag)->fetch();
	}

	public function findCategories()
	{
		return $this->table()->where('type', 'category');
	}

}