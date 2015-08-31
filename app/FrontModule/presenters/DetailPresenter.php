<?php

namespace App\FrontModule\Presenters;

use Nette\Application\ForbiddenRequestException;
use Texy;
use Nette\Application\BadRequestException;

class DetailPresenter extends BasePresenter
{
	use \Brabijan\Images\TImagePipe;

	/** @var \Texy @inject */
	public $texy;

	/** @var \Nette\Database\Row */
	public $articleDetail;


	public function beforeRender()
	{
		parent::beforeRender();
		$this->registerTexyMacros($this->texy);
	}


	protected function createTemplate($class = NULL)
	{
		$template = parent::createTemplate($class);
		$template->addFilter('texy', callback($this->texy, 'process'));
		$template->addFilter('timeAgo', 'Helpers::timeAgoInWords');

		return $template;
	}


	public function actionDefault($id, $slug = '')
	{
		$this->articleDetail = $this->articleManager->get($id);
		if (!$this->articleDetail) {
			throw new BadRequestException;
		}

		if ($this->articleDetail->document_state == 'draft') {
			if (!$this->user->isLoggedIn()){
				throw new ForbiddenRequestException;
			}
			if ($this->user->getId() != $this->articleDetail->user_id){
				throw new ForbiddenRequestException;
			}
		}

		if ($slug != $this->articleDetail->slug || $this->locale != $this->articleDetail->language) {
			$this->redirect(303, 'Detail:default', [$id, $this->articleDetail->slug, 'locale' => $this->articleDetail->language]);
		}

	}


	/**
	 * @param int $id
	 */
	public function renderDefault($id, $slug = '')
	{
		$this->template->articleDetail = $this->articleDetail;

		if ($this->user->isAllowed('Article', 'like')) {
			$this->template->likesUserArticle = $this->articleManager->likesUserArticle($id, $this->user->id);
		}

		$this->template->articleLikes = $this->articleManager->getCountArticleLikes($id);

		$this->template->mutations = $this->articleManager->findArticleMutatations($id);
	}


	public function handleLikeArticle($id)
	{
		if ($this->user->isAllowed('Article', 'like') && $this->articleDetail->user_id != $this->user->id) {
			if ($this->articleManager->userLikeArticle($id, $this->user->id)) {
				// todo: if ajax, redraw component
				$this->redirect('this');
			} else {
				$this->flashMessage('Only one like for article is allowed', 'danger');
				$this->redirect('this');
			}
		}
	}


	public function handleDislikeArticle($id)
	{
		if ($this->user->isAllowed('Article', 'like')) {
			if ($this->articleManager->userDislikeArticle($id, $this->user->id)) {
				// todo: if ajax, redraw component
				$this->redirect('this');
			} else {
				$this->redirect('this');
			}
		}
	}

}