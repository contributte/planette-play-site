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
	 * @var \Kdyby\Translation\Translator
	 */
	public $translation;

	private $locale;



	public function __construct(\App\Model\ArticleManager $articleManager, \Kdyby\Translation\Translator $translator, $locale)
	{
		$this->articleManager = $articleManager;
		$this->translation = $translator;
		$this->locale = $locale;
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
		$this->template->locale = $this->locale;
		$this->prepareFulltext($this->q);

		$template->render();
	}
}