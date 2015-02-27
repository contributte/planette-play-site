<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\FrontModule\Presenters;

use Nette,
	App\Model;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	/** @var \KnowledgeBase @inject */
	public $knowledgebase;

	/** @var string @persistent */
	public $language;


	protected function startup()
	{
		parent::startup();
		// TODO : nemit to tu tak natvrdo
		if ($this->language != 'cs' && $this->language != 'en'){
			$this->language = 'en';
//			throw new Nette\Application\BadRequestException;
		}
	}


	public function beforeRender()
	{
		parent::beforeRender();

		$this->knowledgebase->setLanguage($this->language);

		$this->template->knowledgebase = $this->knowledgebase;
		$this->template->language = $this->language;

		$this->template->production = !$this->context->parameters['site']['develMode'];
		$this->template->version = $this->context->parameters['site']['version'];
	}
}
