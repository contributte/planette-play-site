<?php
/**
 * This file is part of project planette.
 *
 * @author Vaclav Kraus <krauva@gmail.com>
 * @since  22. 7. 2015
 */

namespace App\Model;

use Nette;
use Nette\Database\Context;

class VersionManager extends Nette\Object
{
	private $db;

	const
		TABLE_NAME = 'version',
		ARTICLE_TAG = 'article_version';

	/**
	 * @param Context $db
	 */
	public function __construct(Context $db)
	{
		$this->db = $db;
	}

	/**
	 * Return all versions in assoc array.
	 * @return mixed
	 */
	public function getVersions()
	{
		return $this->db->table(self::TABLE_NAME)->select('id, version')->fetchPairs('id', 'version');
	}

	/**
	 * @param $version_id
	 * @param $article_id
	 */
	public function addVersionToArticle($version_id, $article_id)
	{
		$this->db->table(self::ARTICLE_TAG)->insert(['article_id' => $article_id, 'version_id' => $version_id]);
	}

	public function updateVersion($version_id, $article_id)
	{
		$this->db->table(self::ARTICLE_TAG)->where('article_id = ?', $article_id)
			->update(['article_id' => $article_id, 'version_id' => $version_id]);
	}
}