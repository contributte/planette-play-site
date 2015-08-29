<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\Model;

use Nette\Object;

class SiteLayout extends Object
{
	public $develMode = FALSE;

	public $versionName = 'public';


	/**
	 * SiteLayout constructor.
	 *
	 * @param array $settings
	 */
	public function __construct($settings = NULL)
	{
		if ($settings) {
			if (isset($settings['develMode'])) {
				$this->develMode = boolval($settings['develMode']);
			}

			if (isset($settings['versionName'])) {
				$this->versionName = (string)$settings['versionName'];
			}

			if ($this->develMode) {
				$this->versionName = time();
			}

		}
	}
}