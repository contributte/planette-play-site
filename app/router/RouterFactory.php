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
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter()
	{
		$router = new RouteList();

		$router[] = $frontRouter = new RouteList('Front');
		$frontRouter[] = new Route('[<language=cs [a-z]{2}>/]category/<category>', 'Category:default');
		$frontRouter[] = new Route('[<language=cs [a-z]{2}>/]post/<id>[-<slug>]', 'Detail:default');
		$frontRouter[] = new Route('[<language=cs [a-z]{2}>/]<presenter>/<action>[/<id>]', 'Homepage:default');

		return $router;
	}

}
