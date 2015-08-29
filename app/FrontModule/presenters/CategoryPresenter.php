<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\FrontModule\Presenters;
use Nette\Application\BadRequestException;


/**
 * Homepage presenter.
 */
class CategoryPresenter extends BasePresenter
{

	/**
	 * @inject
	 * @var \App\Model\ArticleManager
	 */
	public $articleManager;


	public function renderDefault($category = '')
	{
		$selectedCategory = $this->articleManager->getCategory($category);

		if (!$selectedCategory){
			throw new BadRequestException;
		}

		$this->template->selectedCategory = $selectedCategory;
		$this->template->categories = $this->articleManager->findCategories();
		$this->template->articles = $this->articleManager->findAllInCategory($category, $this->locale);
	}


	public function createComponentSearch()
	{
		return new \SearchControl($this->articleManager, $this->translator, $this->locale);
	}

}
