<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

class TexyFactory
{
	/**
	 * @return Texy
	 */
	public static function createTexy()
	{
		$texy = new Texy();
		$texy->encoding = 'utf-8';
		$texy->setOutputMode(\Texy::HTML5);

		// from https://github.com/nette/web-content/blob/convertor/src/Wiki/Convertor.php
		$texy->linkModule->root = '';
		$texy->alignClasses['left'] = 'left';
		$texy->alignClasses['right'] = 'right';
		$texy->emoticonModule->class = 'smiley';
		$texy->headingModule->top = 1;
		$texy->headingModule->generateID = TRUE;
		$texy->tabWidth = 4;
		$texy->typographyModule->locale = 'cs'; //en ?
		$texy->tableModule->evenClass = 'alt';
		$texy->dtd['body'][1]['style'] = TRUE;
		$texy->allowed['longwords'] = FALSE;
		$texy->allowed['block/html'] = FALSE;

		$texy->phraseModule->tags['phrase/strong'] = 'b';
		$texy->phraseModule->tags['phrase/em'] = 'i';
		$texy->phraseModule->tags['phrase/em-alt'] = 'i';
		$texy->phraseModule->tags['phrase/acronym'] = 'abbr';
		$texy->phraseModule->tags['phrase/acronym-alt'] = 'abbr';

		$texy->addHandler('block', array('TexyFactory', 'blockHandler'));

		return $texy;
	}

	/********************* Texy handlers ****************d*g**/


	/**
	 * User handler for code block.
	 *
	 * @param  TexyHandlerInvocation  handler invocation
	 * @param  string  block type
	 * @param  string  text to highlight
	 * @param  string  language
	 * @param  TexyModifier modifier
	 * @return TexyHtml
	 */
	public static function blockHandler($invocation, $blocktype, $content, $lang, $modifier)
	{
		if (preg_match('#^block/(php|neon|javascript|js|css|html|htmlcb|latte)$#', $blocktype)) {
			list(, $lang) = explode('/', $blocktype);

		} elseif ($blocktype !== 'block/code') {
			return $invocation->proceed();
		}

		$lang = strtolower($lang);
		if ($lang === 'htmlcb' || $lang === 'latte') $lang = 'html';
		elseif ($lang === 'javascript') $lang = 'js';

		if ($lang === 'html') $langClass = 'FSHL\Lexer\LatteHtml';
		elseif ($lang === 'js') $langClass = 'FSHL\Lexer\LatteJavascript';
		else $langClass = 'FSHL\Lexer\\' . ucfirst($lang);

		$texy = $invocation->getTexy();
		$content = Texy::outdent($content);

		if (class_exists($langClass)) {
			$fshl = new FSHL\Highlighter(new FSHL\Output\Html, FSHL\Highlighter::OPTION_TAB_INDENT);
			$content = $fshl->highlight($content, new $langClass);
		} else {
			$content = htmlSpecialChars($content);
		}
		$content = $texy->protect($content, Texy::CONTENT_BLOCK);

		$elPre = TexyHtml::el('pre');
		if ($modifier) {
			$modifier->decorate($texy, $elPre);
		}
		$elPre->attrs['class'] = 'src-' . strtolower($lang);

		$elCode = $elPre->create('code', $content);

		return $elPre;
	}
}