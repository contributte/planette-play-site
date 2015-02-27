<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */


/**
 * Class KnowledgeBase
 */
class KnowledgeBase
{

	public $title = 'Knowledge Base';
	public $question = 'Start type here';
	public $noresult = 'Nothing we found...';
	public $shortquestion = 'Short query, type another letter...';

	private $params = [];


	/**
	 * @param array $params
	 */
	function __construct($params = [])
	{
		$this->params = $params;
	}


	function setLanguage($language = '')
	{
		if (isset($this->params[$language]['title'])) {
			$this->title = $this->params[$language]['title'];
		}
		if (isset($this->params[$language]['question'])) {
			$this->question = $this->params[$language]['question'];
		}
		if (isset($this->params[$language]['noresult'])) {
			$this->noresult = $this->params[$language]['noresult'];
		}
		if (isset($this->params[$language]['shortquestion'])) {
			$this->shortquestion = $this->params[$language]['shortquestion'];
		}

	}

}