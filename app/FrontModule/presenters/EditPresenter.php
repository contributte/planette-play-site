<?php

namespace App\FrontModule\Presenters;


use Nette\Application\BadRequestException;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Form;
use Nette\Utils\FileSystem;

class EditPresenter extends BasePresenter
{
	use \Brabijan\Images\TImagePipe;

	/**
	 * @var \App\Model\ArticleManager
	 * @inject
	 */
	public $articleManager;

	/**
	 * @var \Nette\Http\IRequest
	 * @inject
	 */
	public $httpRequest;

	/**
	 * @inject
	 * @var \Brabijan\Images\ImageStorage
	 */
	public $imageStorage;

	protected $article;


	public function beforeRender()
	{
		parent::beforeRender();
		$this->template->defaultTags = $this->articleManager->getTagsAsString();
	}


	/**
	 * @param int $id
	 */
	public function renderNew()
	{
		if (!$this->user->isAllowed('Article', 'add')) {
			throw new ForbiddenRequestException();
		}
	}


	public function renderDrafts()
	{
		if (!$this->user->isAllowed('Article', 'add')) {
			throw new ForbiddenRequestException();
		}

		$this->template->draftsList = $this->articleManager->findDrafts($this->user->id);
	}


	/**
	 * @param int $articleId
	 */
	public function actionTranslate($articleId = 0)
	{
		if (!$this->user->isAllowed('Article', 'add')) {
			throw new ForbiddenRequestException();
		}

		$article = $this->articleManager->prepareMutationForTranslate($articleId, $this->user->id);
		$this->redirect('edit', $article->id);
	}


	/**
	 * @param int $id
	 */
	public function actionEdit($id)
	{
		if (!$this->user->isAllowed('Article', 'edit')) {
			throw new ForbiddenRequestException();
		}

		$this->article = $this->articleManager->get($id);
		$this->template->articleDetail = $this->article;

		if (isset($this->article->id) && $this->article->id > 0) {
			if ($this->user->id == $this->article->user->id) {
				$this->template->article = $this->article;

			} else {
				throw new ForbiddenRequestException;
			}

		} else {
			throw new BadRequestException;
		}

	}


	/**
	 * Prototype of save image
	 */
	public function handleSaveImage()
	{
		// todo : refactor
		$this->imageStorage->setNamespace("knowledgebase");
		$onlinePath = 'knowledgebase/';

		$response = array();

		/** @var \Nette\Http\FileUpload $file */
		if ($file = $this->httpRequest->getFile('file')) {
			$filename = uniqid() . '.' . (pathinfo($file->name, PATHINFO_EXTENSION) ?: 'png');
			$image = $this->imageStorage->save($file->getContents(), $filename);
			$returnPath = pathinfo($image->file, PATHINFO_BASENAME);
			if ($this->article) {
				$this->articleManager->assignImageToArticle($this->article->id, $returnPath, "knowledgebase");
			}

			$response['filename'] = $onlinePath . pathinfo($image->file, PATHINFO_BASENAME);
		} else {
			$response['error'] = 'Error while uploading file';
		}

		$this->sendJson($response);
	}


	/**
	 * Article form factory.
	 *
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentArticleForm()
	{
		$form = new Form();

		$form->setTranslator($this->translator->domain('ui.forms.createArticle'));

		$form->addText('title', 'title')
			->setRequired('title-r');

		$form->addTextArea('content', 'content')
			->setRequired('content-r');

		$form->addCheckbox('draft', 'draft');

		$languages = array(
			'cs' => 'czech',
			'en' => 'english',
		);

		$form->addSelect('language', 'language', $languages);

		$types = $this->articleManager->getArticleTypes();
		if (!empty($types)) {
			$form->addSelect('type', 'type', $types);
		} else {
			$form->addHidden('type', 'default');
		}

		$form->addText('tags', 'tags');

		if (!empty($this->article)) {
			$tags = $this->articleManager->getTagsAsStringForArticle($this->article->id);

			if ($this->article->document_state == 'draft') {
				$draft = TRUE;
			} else {
				$draft = FALSE;
			}

			$defaults = [
				'title' => $this->article->title,
				'content' => $this->article->content,
				'tags' => $tags,
				'language' => $this->article->language,
				'draft' => $draft,
			];
			$form->setDefaults($defaults);


			if (!empty($types)) {
				$type = $this->articleManager->getArticleType($this->article->id);
				if ($type) {
					$form->setDefaults(['type' => $type->name]);
				}
			}

			$image = $form->addUpload('file', 'file');
			$image->addCondition(Form::FILLED)
				->addRule(Form::IMAGE, 'file-r');

			$form->addText('fileNote', 'filenote');

			$form->addSubmit('send', 'save');
			$form->addSubmit('sendAndView', 'saveAndViewArticle');

		} else {
			$defaults = [
				'draft' => TRUE,
			];
			$form->setDefaults($defaults);
			$form->addSubmit('send', 'saveArticle');
		}


		$form->onSuccess[] = $this->articleFormSucceeded;

		return $form;
	}


	/**
	 * @param Form $form
	 * @throws ForbiddenRequestException
	 */
	public function articleFormSucceeded(Form $form)
	{
		$v = $form->getValues();
		if (!$this->user->isAllowed('Article', 'add') ||
			!$this->user->isAllowed('Article', 'edit')
		) {
			throw new ForbiddenRequestException();
		}


		$tags = [];

		if ($v->tags !== '') {
			$articleTags = explode(',', $v->tags);
			$tags = $this->articleManager->createTagsAndReturnIds($articleTags);
		}

		// type
		if ($v->type != 'default') {
			$type = $this->articleManager->addTag($v->type, 'type');
		} else {
			$type = NULL;
		}

		if ($v->draft) {
			$draft = TRUE;
		} else {
			$draft = FALSE;
		}

		if (empty($this->article)) {

			$article = $this->articleManager
				->addArticle(
					$v->title,
					$v->content,
					$draft,
					$v->language,
					$tags,
					$type,
					$this->getUser()->getId());

			$this->flashMessage($this->translator->translate('ui.flash.articleWasAdded'));
			$this->redirect('Detail:default', $article->id, $article->slug);

		} else {

			// file upload
			if ($v->file->isOk()) {
				/** @var \Nette\Http\FileUpload $file */
				$file = $v->file;

				// TODO : refactor
				$this->imageStorage->setNamespace("knowledgebase");
				$filename = uniqid() . '.' . (pathinfo($file->name, PATHINFO_EXTENSION) ?: 'png');
				$image = $this->imageStorage->save($file->getContents(), $filename);
				$returnPath = pathinfo($image->file, PATHINFO_BASENAME);

				$note = '';
				if ($v->fileNote) {
					$note = $v->fileNote;
				}
				$status = $this->articleManager->assignImageToArticle($this->article->id, $returnPath, "knowledgebase", $note);
				if ($status) {
					$this->flashMessage($this->translator->translate('ui.flash.imageWasUploaded'));
				}
			}

			$this->article = $this->articleManager->editArticle($this->article->id,
				$v->title,
				$v->content,
				$draft,
				$v->language,
				$tags,
				$type,
				$this->getUser()->getId());

			$this->flashMessage($this->translator->translate('ui.flash.articleWasUpdated'));
			if (isset($form['sendAndView']) && $form['sendAndView']->isSubmittedBy()) {
				$this->redirect('Detail:default', $this->article->id, $this->article->slug);
			} else {
				$this->redirect('Edit:edit', $this->article->id);
			}
		}

	}

}