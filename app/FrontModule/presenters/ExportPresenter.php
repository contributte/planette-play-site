<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\FrontModule\Presenters;

class ExportPresenter extends BasePresenter
{
	private $oldUrls = [
		'en/multiple-use-of-single-form' => 72,
		'en/record-editing-and-passing-id-to-form' => 81,
		'en/redirect-to-same-page-after-form-submit' => 79,
		'en/setting-up-defaults-to-edit-form' => 210,
		'en/tinymce-installation' => 136,
		'en/different-layout-in-administration' => 82,
		'en/helper-loader' => 211,
		'en/macro-loader' => 212,
		'en/how-open-files-in-ide-from-debugger' => 77,
		'en/loading-models-with-notorm-and-dependency-injection' => 213,
		'en/dependent-form-select-with-ajax' => 214,
		'en/simple-ajax-example' => 83,
		'en/grunt-with-usemin' => 75,
		'cs/bacamp-vsetin-2010-vitek-jezek-whitek-jan-tvrdik' => 126,
		'cs/best-practise-formulare-jako-komponenty' => 127,
		'cs/cms-a-frameworky-nad-nette' => 128,
		'cs/co-je-dependency-injection' => 62,
		'cs/create-components-with-autowiring' => 129,
		'cs/dedicnost-vs-kompozice' => 63,
		'cs/dynamicke-snippety' => 64,
		'cs/dynamicky-formular-jako-modul' => 73,
		'cs/faq' => 95,
		'cs/forms-toggle' => 65,
		'cs/grunt-a-usemin' => 74,
		'cs/historie-snippetu' => 134,
		'cs/inject-autowire' => 69,
		'cs/jak-otevrit-soubor-z-debuggeru-v-editoru' => 76,
		'cs/jak-po-odeslani-formulare-zobrazit-stejnou-stranku' => 78,
		'cs/jak-predavat-id-pri-editaci-zaznamu' => 80,
		'cs/model-entity-repository-mapper' => 139,
		'cs/multiplier' => 66,
		'cs/nacitani-modelu-s-notorm-a-dependency-injection' => 142,
		'cs/navod-vytvarime-blog' => 84,
		'cs/navod-vytvarime-staticky-web' => 85,
		'cs/navstevni-kniha-a-testovani' => 152,
		'cs/navstevni-kniha-vyuzivajici-ajax' => 86,
		'cs/nette-a-doctrine' => 88,
		'cs/nette-database-vs-dibi' => 154,
		'cs/php-frameworky-2007-david-grudl-nette-poprve' => 155,
		'cs/php-frameworky-jaro-2008-david-grudl-o-nette' => 140,
		'cs/posobota-10-tomas-jukin-inza-basnicka' => 153,
		'cs/posobota-10-tomas-jukin-inza-enterprise-architect' => 156,
		'cs/posobota-10-viliam-kopecky-enoice-adobe-fireworks' => 160,
		'cs/posobota-13-jan-marek-blog-za-5-minut' => 163,
		'cs/posobota-13-patrik-votocek-vrtak-cz-nella-cms' => 165,
		'cs/posobota-14-daniel-steigerwald-ajax' => 170,
		'cs/posobota-14-jakub-vrana-o-finoq-predchudci-notorm' => 173,
		'cs/posobota-18-jan-matousek-loose-coupling-dependency-injection-a-testing' => 177,
		'cs/posobota-18-sponzor-michal-spacek-skype-cr' => 178,
		'cs/posobota-20-david-grudl-nette-framework-1-0' => 179,
		'cs/posobota-22-predstavovani-se' => 209,
		'cs/posobota-22-radek-pavlicek-pristupnost-nette-aplikaci' => 208,
		'cs/posobota-22-roman-kabelka-pristupnost-nette-aplikaci-v-praxi' => 207,
		'cs/posobota-25-jiri-koutny-rozjizdime-php-v-amazon-cloudu' => 206,
		'cs/posobota-25-josef-kriz-venne-cms' => 204,
		'cs/posobota-25-predstavovani-se' => 182,
		'cs/posobota-29-david-grudl-predstavuje-nette-2-0-beta' => 185,
		'cs/posobota-29-jakub-kohout-a-patrik-votocek-nella-cms' => 190,
		'cs/posobota-29-mikulas-dite-haml-filter' => 193,
		'cs/posobota-29-pavel-ptacek-lightbulb' => 203,
		'cs/posobota-30-petr-prochazka-phpunit-html-interface' => 205,
		'cs/posobota-30-predstavovani-se' => 199,
		'cs/posobota-30-vitek-jezek-selenium' => 200,
		'cs/posobota-36-blahoprani-jakuba-vrany' => 201,
		'cs/posobota-36-david-grudl-latte' => 180,
		'cs/posobota-36-david-grudl-microframework' => 181,
		'cs/posobota-36-david-grudl-predstavuje-nette-2-0' => 183,
		'cs/posobota-36-jan-marek-autorizace-pres-heslo-fb-i-twitter' => 184,
		'cs/posobota-36-jan-smitka-routovani' => 186,
		'cs/posobota-36-jan-tvrdik-komponenty' => 187,
		'cs/posobota-36-martin-major-cache' => 188,
		'cs/posobota-36-sponzor-klik-pojisteni' => 189,
		'cs/posobota-36-uvod-vitka-jezka' => 191,
		'cs/posobota-36-vojtech-dobes-ajax-a-snippety' => 192,
		'cs/posobota-38-daniel-milde-testovani-nejen-v-phpunitu' => 194,
		'cs/posobota-38-david-grudl-dependency-injection' => 195,
		'cs/posobota-38-filip-prochazka-composer' => 196,
		'cs/posobota-38-jan-smitka-doplnky-v-nette' => 197,
		'cs/posobota-38-ondrej-brablc-mlada-fronta' => 198,
		'cs/posobota-39-filip-prochazka-kdyby-modules' => 176,
		'cs/posobota-39-jan-dolecek-nebojte-se-hackovat' => 175,
		'cs/posobota-39-jan-skrasek-nette-database' => 174,
		'cs/posobota-39-openspace-budoucnost-posoboty' => 172,
		'cs/posobota-39-openspace-nette-v-zahranici' => 171,
		'cs/posobota-48-martin-major-verzovani-databaze' => 169,
		'cs/posobota-48-martin-stekl-orm' => 168,
		'cs/posobota-50-jan-tvrdik-nette-api' => 167,
		'cs/posobota-51-karel-cizek-reactphp' => 166,
		'cs/posobota-51-ondrej-mirtes-websockety' => 164,
		'cs/posobota-55-filip-prochazka-usnadnete-si-souziti-s-doctrine' => 162,
		'cs/posobota-55-jan-tvrdik-bezpecnost-nette' => 161,
		'cs/posobota-55-karel-cizek-generatory-v-php-55' => 159,
		'cs/posobota-55-vojtech-kohout-lean-mapper' => 158,
		'cs/posobota-56-david-grudl-formulare-v-nette-21' => 157,
		'cs/posobota-56-jaroslav-kubicek-gruntjs-bower' => 141,
		'cs/posobota-56-martin-stekl-uzivatelske-hlasky' => 143,
		'cs/posobota-56-viliam-kopecky-ristretto' => 144,
		'cs/posobota-58-david-grudl-formulare-v-nette-21' => 145,
		'cs/posobota-58-filip-prochazka-di-v-nette-21' => 146,
		'cs/posobota-58-vojtech-kohout-curious' => 147,
		'cs/posobota-6-david-grudl-nove-sablony' => 148,
		'cs/posobota-6-jan-marek-texyla' => 149,
		'cs/posobota-patrik-votocek-filip-prochazka-co-je-dependency-injection' => 150,
		'cs/posobta-36-uvod-davida-grudla' => 151,
		'cs/prednaska-david-grudl-muni-2010' => 133,
		'cs/prednaska-david-grudl-vut-2011' => 130,
		'cs/pripojeni-komponenty-k-rodici' => 67,
		'cs/routovani-parametr-s-lomitky' => 91,
		'cs/routovani-v-cli' => 202,
		'cs/routovani-vice-parametru-ve-filtru' => 92,
		'cs/sandbox-composer' => 70,
		'cs/staticke-acl' => 89,
		'/post/131-testovani-jednoducheho-controlu' => 131,
		'cs/validace-prvku-v-zavislosti-na-hodnote-jinych-prvku' => 135,
		'cs/vicenasobne-pouziti-samostatneho-formulare' => 71,
		'cs/vlastni-validacni-pravidla' => 87,
		'cs/vychozi-data-pro-editacni-formular' => 137,
		'cs/vytvarime-kontaktny-formular' => 90,
		'cs/webexpo-2009-david-grudl-ria' => 138,
		'cs/webexpo-2010-david-grudl-nette-2-0' => 132,
		'cs/zmena-title-pri-ajaxu' => 68,
		'cs/zprovozneni-tinymce' => 93,
	];

	public function actionDefault()
	{
		$articles = $this->articleManager->findAll()->order('translation_id DESC, id ASC');
		echo '<pre>';

		foreach ($articles as $article) {
			$translated = '-';
			if ($article->translation_id){
				$translated = 'translated ('.$article->translation_id.')';
			}

			echo $article->id .',' .
				$article->language .',' .
				$translated .',' .
				$article->slug .',' .
				htmlentities($article->title) .',' .
				"\n";
		}

		$this->terminate();
	}

	public function actionLinks()
	{
		$articles = $this->articleManager->findAll()->order('translation_id DESC, id ASC');

		echo '<pre>';

		foreach ($articles as $article) {
			$originalUrl = array_search($article->id, $this->oldUrls);

			if ($originalUrl){
				echo
					'http://pla.nette.org/' . $originalUrl .',' .
					$article->id .',' .
					$article->language .',' .
					$article->slug .',' .
					"\n";
			}
		}

		$this->terminate();
	}

}
