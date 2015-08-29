<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route;

/**
 * Router factory.
 */
class RouterFactory
{

	/**
	 * @param bool $enableSsl
	 *
	 * @return Nette\Application\IRouter
	 */
	public function createRouter($enableSsl = TRUE)
	{
		if ($enableSsl === TRUE) {
			Route::$defaultFlags = Route::SECURED;
		}

		$router = new RouteList();

		$router[] = $frontRouter = new RouteList('Front');
		$frontRouter[] = new Route('[<locale=cs [a-z]{2}>/]category/<category>', 'Category:default');
		$frontRouter[] = new Route('[<locale=cs [a-z]{2}>/]post/<id>[-<slug>]', 'Detail:default');
		$frontRouter[] = new Route('[<locale=cs [a-z]{2}>/]<presenter>/<action>[/<id>]', 'Homepage:default');

		return $router;
	}

}
