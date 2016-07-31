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
 * Wersja mobilna IPL
 * 
 * @package mobile
 */
class Controller_Mobile extends Controller {
    /**
     * Konstruktor klasy
     */
    public function __construct() {
	unset($_COOKIE['_nomobile']);
    }
    /**
     * Strona glowna wersji mobilnej
     */
    public function action_index() {

	$view = View::factory('_mobile');
	$view->set('content', View::factory('_mobile_classlist')->render());
	echo $view->render();
    }
    /**
     * Zwraca plan lekcji dla klasy
     *
     * @param string $klasa Klasa
     */
    public function action_klasa($klasa) {
	$view = View::factory('_mobile');
	$view2 = View::factory('_mobile_klasa');

	$view2->set('klasa', $klasa);

	$view->set('title', $klasa);
	$view->set('content', $view2->render());
	echo $view->render();
    }

}
