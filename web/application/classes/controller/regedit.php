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
 * Rejestr systemowy
 * 
 * @package regedit
 */
class Controller_Regedit extends Controller {

    /**
     * Tworzy obiekt sesji i sprawdza czy zalogowany
     */
    public function __construct() {
	App_Auth::isLogged();
    }

    /**
     * Strona glowna rejestru
     */
    public function action_index() {
	$view = View::factory('_root_template');
	$view2 = view::factory('regedit_index');

	$view->set('content', $view2->render());
	echo $view->render();
    }

}

?>