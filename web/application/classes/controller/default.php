<?php

/**
 * Internetowy Plan Lekcji
 * 
 * @author Michal Bocian <mhl.bocian@gmail.com>
 * @license GNU GPL v3
 * @package ipl\logic
 */
defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * Glowny kontroler i domyslny podczas uruchomienia
 * 
 * @package default
 */
class Controller_Default extends Controller {

    /**
     *
     * @var nusoap_client instancja klasy nusoap
     */
    public $wsdl;

    /**
     * Tworzy obiekt sesji i sprawdza system RAND_TOKEN
     */
    public function __construct() {
	if (isset($_SESSION['token'])) {
	    App_Auth::isLogged(false);
	}
    }

    /**
     * Wyswietla strone glowna
     */
    public function action_index($param=false) {
	if ($param == 'nomobile') {
	    setcookie('_nomobile', true, time() + 3600 * 24, '/');
	}
	$view = View::factory('_root_template');

	$view->set('content', App_Globals::getRegistryKey('index_text'));
	echo $view->render();
    }

    /**
     * Zmienia temat strony
     */
    public function action_look() {
	if (!isset($_POST)) {
	    Kohana_Request::factory()->redirect();
	    exit;
	}
	$_SESSION['app_theme'] = $_POST['look'];
	Kohana_Request::factory()->redirect($_POST['site']);
    }

    /**
     * Informacje o systemie
     */
    public function action_about() {
	$view = View::factory('_root_template');
	$view2 = View::factory('default_about');

	$view->set('content', $view2->render());
	echo $view->render();
    }

    /**
     * Ukryta strona dla eksperymentow
     */
    public function action_experimental() {
        $view = View::factory('podglad_zestawienie_new');
        $out = str_replace('{{theme}}', $_SESSION['app_theme'], $view->render());
        echo $out;
    }

}