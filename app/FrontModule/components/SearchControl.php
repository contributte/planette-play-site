<?php

use Nette\Application\UI\Control;
use App\Model;
use Nette\Utils\Strings;

class SearchControl extends Control
{
	/**
	 * @persistent
	 * @var string
	 */
	public $q = '';

	/**
	 *
	 */
	public $queryPrepared = FALSE;

	/**
	 * @var \App\Model\ArticleManager
	 */
	private $articleManager;

	/**
	 * @var \KnowledgeBase
	 */
	public $knowledgebase;

	private $language;



	public function __construct(\App\Model\ArticleManager $articleManager, KnowledgeBase $knowledgeBase, $language = 'en')
	{
		$this->articleManager = $articleManager;
		$this->knowledgebase = $knowledgeBase;
		$this->language = $language;
	}
	/**
	 * @param $q string
	 */
	public function handleSearch($q)
	{
		$this->prepareFulltext($q);
		$this->redrawControl("searchResults");
	}



	/**
	 * @param $queryString
	 */
	public function prepareFulltext($queryString)
	{
		if (!$this->queryPrepared) {
			if (Strings::length($queryString) > 2) {
				$this->template->searchResults = $this->articleManager
					->findFulltext($queryString);

			} elseif (Strings::length($queryString) > 0) {
				$this->template->searchStatus = 'too-short';
			}
			$this->queryPrepared = TRUE;
		}
	}


	public function render()
	{
		$template = $this->template;
		$template->setFile(dirname(__FILE__) . '/SearchControl.latte');

		$this->template->query = $this->q;
		$this->template->knowledgebase = $this->knowledgebase;
		$this->template->language = $this->language;
		$this->prepareFulltext($this->q);

		$template->render();
	}
}