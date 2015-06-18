<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\Model;

use Nette;
use Nette\Utils\Strings;


class ArticleManager extends Nette\Object
{

	/** @var ArticleRepository */
	protected $repository;

	/** @var TagRepository */
	protected $tagRepository;

	/** @var  string */
	protected $dir;

	/** @var  string */
	protected $uri;

	/** @var  bool */
	protected $useFiles = FALSE;


	/**
	 * @param ArticleRepository $article
	 * @param TagRepository $tag
	 */
	public function __construct(ArticleRepository $article, TagRepository $tag)
	{
		$this->repository = $article;
		$this->tagRepository = $tag;
	}


	/**
	 * @param $dir
	 * @param $uri
	 */
	public function setFilesFolder($dir, $uri)
	{
		$this->dir = $dir;
		$this->uri = $uri;
		if (is_dir($this->dir)) {
			$this->useFiles = TRUE;
		}
	}


	/********************* find* methods & get *********************/


	/**
	 * @param int $id
	 * @return Nette\Database\Table\IRow
	 */
	public function get($id)
	{
		return $this->repository->get($id);
	}


	/**
	 * @return int
	 */
	public function getCount($userId = 0)
	{
		if ($userId == 0){
			return $this->repository->getCount();
		} else {
			return $this->repository->getCountForUser($userId);
		}
	}


	/**
	 * @return Nette\Database\Table\Selection
	 */
	public function findAll()
	{
		return $this->repository->findAll();
	}


	/**
	 * @param $query
	 * @return \Nette\Database\Table\Selection
	 */
	public function findFulltext($query)
	{
		return $this->repository->findFulltext($query);
	}


	/********************* update* methods *********************/

	/**
	 * @param $title
	 * @param $content
	 * @param bool $draft
	 * @param string $language
	 * @param array $tags
	 * @param Nette\Database\Table\IRow $type
	 * @param int $author
	 * @return Nette\Database\Table\IRow
	 */
	public function addArticle($title, $content, $draft = TRUE,  $language = 'en', $tags = [], Nette\Database\Table\IRow $type, $author = 0)
	{
		if ($draft){
			$state = 'draft';
		} else {
			$state = 'public';
		}

		$data = array(
			'title' => $title,
			'content' => $content,
			'language' => $language,
			'user_id' => $author,
			'slug' => Strings::webalize($title),
			'created_date' => new \DateTime(),
			'updated_date' => new \DateTime(),
			'document_state' => $state,
		);

		$article = $this->repository->insert($data);
		$this->repository->addTags($article->id, $tags);
		if ($type != NULL && isset($type->id)){
			$this->repository->addArticleType($article->id, $type->id);
		}

		return $this->repository->get($article->id);
	}


	/**
	 * @param $id
	 * @param $title
	 * @param $content
	 * @param bool $draft
	 * @param string $language
	 * @param array $tags
	 * @param Nette\Database\Table\IRow $type
	 * @param int $author
	 * @return Nette\Database\Table\IRow
	 */
	public function editArticle($id, $title, $content, $draft = TRUE, $language = 'en', $tags = [], Nette\Database\Table\IRow $type, $author = 0)
	{
		if ($draft){
			$state = 'draft';
		} else {
			$state = 'public';
		}

		$data = array(
			'title' => $title,
			'content' => $content,
			'language' => $language,
			'user_id' => $author,
			'slug' => Strings::webalize($title),
			'updated_date' => new \DateTime(),
			'document_state' => $state,
		);

		$this->repository->removeAllTags($id);
		$this->repository->addTags($id, $tags);

		if ($type != NULL && isset($type->id)){
			$this->repository->addArticleType($id, $type->id);
		}

		$this->repository->update($id, $data);

		return $this->repository->get($id);
	}


	/**
	 * delete article
	 *
	 * @param $id int
	 * @return mixed
	 */
	public function deleteArticle($id)
	{
		return $this->repository->delete($id);
	}


	/**
	 * edit object
	 *
	 * @param $id
	 * @param $data
	 * @return bool
	 */
	public function edit($id, $data)
	{
		return $this->repository->update($id, $data);
	}


	/**
	 * @return array
	 */
	public function pairsAllTags()
	{
		$pairs = [];
		$tags = $this->tagRepository->findAll();
		if (!empty($tags)) {
			foreach ($tags as $tag) {
				$pairs[$tag->id] = $tag->name;
			}
		}

		return $pairs;
	}


	/**
	 * create new or return exists tag
	 *
	 * @param $newTag
	 * @param string $type
	 * @return Nette\Database\Table\IRow
	 */
	public function addTag($newTag, $type = 'normal')
	{
		$tag = $this->tagRepository->getByName($newTag, $type);

		if (!isset($tag->id)){
			$tag = $this->tagRepository->insert([
				'name' => $newTag,
				'type' => $type
			]);
		}

		return $tag;
	}


	/**
	 * @param $articleTags array
	 * @return array
	 */
	public function createTagsAndReturnIds($articleTags)
	{
		$returnIds = [];
		foreach ($articleTags as $tag) {
			$findTag = $this->tagRepository->getByName($tag);
			if (isset($findTag->id) && $findTag->id > 0) {
				$returnIds[] = $findTag->id;
			} else {
				// create new tag
				$newTag = $this->tagRepository->insert(['name' => $tag]);
				$returnIds[] = $newTag->id;
			}
		}

		return $returnIds;
	}


	/**
	 * @param string $separator
	 * @return string
	 */
	public function getTagsAsString($separator = ';')
	{
		$tags = [];
		foreach ($this->tagRepository->findAllForAssign() as $tag) {
			$tags[] = $tag->name;
		}

		return implode($separator, $tags);
	}


	/**
	 * @param $id
	 * @param string $separator
	 * @return string
	 */
	public function getTagsAsStringForArticle($id, $separator = ',')
	{
		$tags = [];
		$articleTags = $this->repository->get($id)
			->related('article_tag')
			->where('tag.type = ? OR tag.type = ?', 'normal', 'category');

		foreach ($articleTags as $articleTag) {
			$tags[] = $articleTag->tag->name;
		}

		return implode($separator, $tags);
	}


	/**
	 * vraci pocet like
	 *
	 * @param int $id article id
	 * @return int
	 */
	public function getCountArticleLikes($id)
	{
		return $this->repository->countLikesForArticle($id);
	}


	/**
	 * @param int $articleId
	 * @param int $userId
	 * @return bool
	 */
	public function userLikeArticle($articleId, $userId)
	{
		return $this->repository->addLikeToArticle($articleId, $userId);
	}


	/**
	 * @param int $articleId
	 * @param int $userId
	 * @return bool
	 */
	public function userDislikeArticle($articleId, $userId)
	{
		return $this->repository->removeLikeFromArticle($articleId, $userId);
	}


	/**
	 * @param $articleId
	 * @param $userId
	 * @return bool
	 */
	public function likesUserArticle($articleId, $userId)
	{
		return $this->repository->likesUserArticle($articleId, $userId);
	}


	/**
	 *
	 * @return array
	 */
	public function getArticleTypes()
	{
		$types = $this->repository->getTypes();

		$returnArray = [];
		foreach ($types as $type){
			$returnArray[($type)] = $type;
		}

		return $returnArray;
	}


	/**
	 * @param $articleId
	 * @return bool|Nette\Database\Table\IRow
	 */
	public function getArticleType($articleId)
	{
		return $this->repository->getArticleTypeTag($articleId);
	}



	public function findArticleMutatations($articleId)
	{
		return $this->repository->findAllMutations($articleId);
	}


	/**
	 * @param int $articleId
	 * @param int $userId
	 * @return Nette\Database\Table\IRow
	 */
	public function prepareMutationForTranslate($articleId = 0, $userId = 0)
	{
		return $this->repository->createTranslationForArticle($articleId, $userId);
	}


	public function assignImageToArticle($articleId = 0, $imageFile = '', $imageNamespace = '', $note = '')
	{
		return $this->repository->addImage($articleId, $imageFile, $imageNamespace, $note);
	}


	public function findDrafts($userId = 0)
	{
		return $this->repository->table()->where('user_id', $userId)->where('document_state', 'draft');
	}


	public function findCategories()
	{
		return $this->tagRepository->findCategories();
	}


	public function findAllInCategory($category = '', $language = '')
	{
		return $this->repository->findAllInCategory($category, $language);
	}


	public function getCategory($category = '')
	{
		$category = $this->tagRepository->getByName($category);
		if ($category && $category->type == 'category'){
			return $category;
		}
		return FALSE;
	}

}