<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\Model;

use Nette\Utils\Strings;

class ArticleRepository extends BaseRepository
{
	/** @var string */
	public $table = 'article';

	/** @var string */
	public $tableArticleTag = 'article_tag';

	/** @var string */
	public $tableArticleImage = 'article_image';

	/** @var array */
	public $articleTypes = [];


	/**
	 * @param array $types
	 */
	public function setArticleTypes($types = [])
	{
		$this->articleTypes = $types;
	}


	/**
	 * @return \Nette\Database\Table\Selection
	 */
	protected function tableArticleTag()
	{
		return $this->database->table($this->tableArticleTag);
	}


	/**
	 * @return \Nette\Database\Table\Selection
	 */
	protected function tableArticleImage()
	{
		return $this->database->table($this->tableArticleImage);
	}


	/**
	 * @param int $id articleId
	 * @param array $tags of tags ids
	 */
	public function addTags($id, $tags)
	{
		if (!empty($tags) && is_array($tags)) {
			foreach ($tags as $tag) {
				$this->tableArticleTag()->insert([
					'article_id' => $id,
					'tag_id' => $tag,
				]);
			}
		}
	}


	/**
	 * @param int $id article id
	 */
	public function removeAllTags($id)
	{
		$this->tableArticleTag()->where('article_id', $id)->delete();
	}


	/**
	 * @param string $query
	 *
	 * @return \Nette\Database\Table\Selection
	 */
	public function findFulltext($query)
	{
		if (strpos($query, ' ') !== FALSE) {
			$query = explode(' ', $query);
		} else {
			$query = array($query);
		}

		$result = $this->table()->where('document_state', 'public');

		// maybe, there will be some tags in the query, let's see
		$tags = [];

		foreach ($query as $keyword) {
			if (strpos($keyword, '#') === 0 and Strings::length($keyword) > 2) {
				// yes, this is a tag
				$tags[] = str_replace('#', '', $keyword);
			} elseif (strpos($keyword, '#') === FALSE) {
				// this is definitely not a tag
				$result->where('title LIKE ? OR content LIKE ? OR :article_tag.tag.name LIKE ?',
					array(
						"%" . $keyword . "%",
						"%" . $keyword . "%",
						"%" . $keyword . "%",
					)
				);
			}

		}

		// if there are any tags, add them to the search conditions

		if (count($tags) > 0) {
			$result->where(':article_tag.tag.name IN ?', $tags);
			$result->group("article.id")->having("COUNT(DISTINCT :article_tag.tag.name) = ?", count($tags));
		}

		return $result;
	}


	/**
	 * vraci pocet lika na clanek
	 *
	 * @param int $id article id
	 *
	 * @return int
	 */
	public function countLikesForArticle($id)
	{
		$count = $this->database->query("
			SELECT COUNT(user_id) as likes
			FROM article_like
			WHERE article_id = ?", $id)->fetch();

		return (int)$count->likes;
	}


	/**
	 * pridava prave jeden like od uzivatele ke clanku
	 *
	 * @param int $articleId
	 * @param int $userId
	 *
	 * @return bool
	 */
	public function addLikeToArticle($articleId, $userId)
	{
		$data = [
			'user_id' => $userId,
			'article_id' => $articleId,
			'created_date' => new \DateTime(),
		];
		try {
			$this->database->table('article_like')->insert($data);

			return TRUE;
		} catch (\PDOException $e) {
			if ($e->getCode() == 23000) {
				return FALSE;
			} else {
				throw $e;
			}
		}
	}


	/**
	 * @param int $articleId
	 * @param int $userId
	 *
	 * @return bool
	 */
	public function removeLikeFromArticle($articleId, $userId)
	{
		$this->database->table('article_like')
			->where('article_id', $articleId)
			->where('user_id', $userId)
			->delete();

		return TRUE;
	}


	/**
	 * @param int $articleId
	 * @param int $userId
	 *
	 * @return bool
	 */
	public function likesUserArticle($articleId, $userId)
	{
		$like = $this->database->table('article_like')
			->where('article_id', $articleId)
			->where('user_id', $userId)
			->fetch();

		if ($like && isset($like->created_date)) {
			return TRUE;

		} else {
			return FALSE;
		}
	}


	/**
	 * @return array
	 */
	public function getTypes()
	{
		return $this->articleTypes;
	}


	/**
	 * @param $articleId
	 * @param $tagId
	 */
	public function addArticleType($articleId, $tagId)
	{
		$this->addTags($articleId, [$tagId]);
	}


	/**
	 * vraci typ clanku podle tagu
	 *
	 * @param int $articleId
	 *
	 * @return bool|\Nette\Database\Table\IRow
	 */
	public function getArticleTypeTag($articleId)
	{
		$tagId = $this->database->table('article_tag')->where('article_id', $articleId)->where('tag.type', 'type')->fetch();

		if (isset($tagId->tag->id)) {
			return $this->database->table('tag')->wherePrimary($tagId->tag->id)->fetch();
		} else {
			return FALSE;
		}
	}


	public function getCountForUser($userId)
	{
		return $this->table()->where('user_id', $userId)->count();
	}


	public function findAllMutations($articleId)
	{
		$article = $this->get($articleId);
		if ($article->translation_id) {
			return $this->findAll()->where('translation_id', $article->translation_id)->fetchAll();
		} else {
			return FALSE;
		}
	}


	/**
	 * @param int $articleId
	 * @param int $userId
	 *
	 * @return \Nette\Database\Table\IRow
	 */
	public function createTranslationForArticle($articleId = 0, $userId = 0)
	{
		$article = $this->get($articleId);
		// TODO : nemit to tu tak natvrdo ...
		$newLanguage = 'cs';
		if ($article->language == 'cs') {
			$newLanguage = 'en';
		}

		$max = $this->database->query("SELECT max(translation_id) as translation FROM article")->fetch();

		$translationId = $max->translation + 1;

		$this->update($article->id, ['translation_id' => $translationId]);

		$data = [
			'translation_id' => $translationId,
			'title' => $article->title . ' (' . $newLanguage . ')',
			'content' => $article->content,
			'language' => $newLanguage,
			'created_date' => new \DateTime(),
			'updated_date' => new \DateTime(),
			'user_id' => $userId,
		];

		$translatedArticle = $this->insert($data);

		// add same tags as article
		$tags = [];
		foreach ($article->related('article_tag') as $tag) {
			$tags[] = $tag->tag->id;
		}
		$this->addTags($translatedArticle->id, $tags);

		return $translatedArticle;
	}


	/**
	 * @param int $articleId
	 * @param string $imageFile
	 * @param string $imageNamespace
	 * @param string $note
	 *
	 * @return bool
	 */
	public function addImage($articleId = 0, $imageFile = '', $imageNamespace = '', $note = '')
	{
		$image = [
			'article_id' => $articleId,
			'namespace' => $imageNamespace,
			'filename' => $imageFile,
			'note' => $note,
		];
		$this->tableArticleImage()->insert($image);

		return TRUE;
	}


	/**
	 * Vraci seznam clanku co maji tag typu kategorie shodny s $category
	 *
	 * @param string $category
	 * @param string $language
	 *
	 * @return \Nette\Database\Table\Selection
	 */
	public function findAllInCategory($category = '', $language = '')
	{
		$result = $this->table()->where('document_state', 'public');
		$result->where(':article_tag.tag.name = ? AND  :article_tag.tag.type = ?', $category, 'category');

		if ($language !== '') {
			$result->where('language', $language);
		}

		return $result;
	}


	public function findAllByTag($tag = '', $language = '')
	{
		$result = $this->table()->where('document_state', 'public');
		$result->where(':article_tag.tag.name = ?', $tag);

		if ($language !== '') {
			$result->where('language', $language);
		}

		return $result;
	}

}