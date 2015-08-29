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
	/**
	 * @persistent
	 */
	public $locale;

	/**
	 * @inject
	 * @var \Kdyby\Translation\Translator
	 */
	public $translator;

	/**
	 * @inject
	 * @var \App\Model\SiteLayout
	 */
	public $siteLayout;


	public function beforeRender()
	{
		parent::beforeRender();

		$this->template->locale = $this->locale;
		$this->template->production = !$this->siteLayout->develMode;
		$this->template->version = $this->siteLayout->versionName;
	}
}
