<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\FrontModule\Presenters;



/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{

	/**
	 * @inject
	 * @var \App\Model\ArticleManager
	 */
	public $articleManager;


	public function renderDefault()
	{
		$this->template->categories = $this->articleManager->findCategories();
	}

}
