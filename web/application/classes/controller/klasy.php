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
 * Odpowiada za obsluge klas
 * 
 * @package klasy
 */
class Controller_Klasy extends Controller {

    /**
     * Tworzy obiekt sesji i sprawdza czy zalogowany
     */
    public function __construct() {

	App_Auth::isLogged(true);

	if (App_Globals::getRegistryKey('edycja_danych') != 1) {
	    Kohana_Request::factory()->redirect('');
	    exit;
	}
    }

    /**
     * Strona glowna
     */
    public function action_index($err=null) {
	$view = View::factory('_root_template');
	$view2 = View::factory('klasy_index');

	$view2->set('_err', $err);
	$view->set('content', $view2->render());
	echo $view->render();
    }

    /**
     * Usuwa klase
     *
     * @param string $klasa klasa
     */
    public function action_usun() {
	if (!isset($_POST)) {
	    Kohana_Request::factory()->redirect('klasy/index');
	}
	$isf = Isf2::Connect();
	$isf->Delete('klasy')->Where(array('klasa' => $_POST['btnClass']))->Execute();
	$isf->Delete('nl_klasy')->Where(array('klasa' => $_POST['btnClass']))->Execute();
	Kohana_Request::factory()->redirect('klasy/index/usun');
    }

    /**
     * Dodaje klase, waliduje dane
     */
    public function action_dodaj() {

	if (!isset($_POST)) {
	    Kohana_Request::factory()->redirect('klasy/index');
	    exit;
	} else {
	    $class_exist = Isf2::Connect()->Select('klasy')
			    ->Where(array('klasa' => $_POST['inpKlasa']))
			    ->Execute()->fetchAll();
	    if (count($class_exist) != 0) {
		Kohana_Request::factory()->redirect('klasy/index/e1');
		exit;
	    }
	    $m = preg_match('/([.!@#$;%^&*()_+|])/i', $_POST['inpKlasa']);
	    if ($m == true) {
		Kohana_Request::factory()->redirect('klasy/index/e2');
		exit;
	    }
	    if ($_POST['inpKlasa'] == '' || $_POST['inpKlasa'] == null || empty($_POST['inpKlasa'])) {
		Kohana_Request::factory()->redirect('klasy/index/e3');
		exit;
	    }

	    Isf2::Connect()->Insert('klasy', array('klasa' => $_POST['inpKlasa']))
		    ->Execute();

	    Kohana_Request::factory()->redirect('klasy/index/pass');
	}
    }

    /**
     * Strona grup klasowych
     */
    public function action_grupyklasowe() {
	if (isset($_POST)) {
	    $i = $_POST['grp'];
	    Isf2::Connect()->Update('rejestr', array('wartosc' => $i))
		    ->Where(array('opcja' => 'ilosc_grup'))
		    ->Execute();
	}
	Kohana_Request::factory()->redirect('klasy/index');
    }

}
