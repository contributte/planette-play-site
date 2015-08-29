<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\FrontModule\Presenters;
use Nette\Application\BadRequestException;


/**
 * Homepage presenter.
 */
class TypePresenter extends BasePresenter
{

	/**
	 * @inject
	 * @var \App\Model\ArticleManager
	 */
	public $articleManager;


	public function renderDefault($type = '')
	{
		// TODO: check
		$selectedType = $type;

		if (!$selectedType){
			throw new BadRequestException;
		}

		$this->template->selectedType = $selectedType;
		$this->template->articles = $this->articleManager->findAllByType($type, $this->locale);
	}

}
