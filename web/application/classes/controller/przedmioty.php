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
 * Odpowiada za obluge przedmiotow
 * 
 * @package przedmioty
 */
class Controller_Przedmioty extends Controller {

    /**
     * Tworzy obiekt sesji i sprawdza czy zalogowany
     */
    public function __construct() {

	App_Auth::isLogged();

	if (App_Globals::getRegistryKey('edycja_danych') != 1) {
	    Kohana_Request::factory()->redirect('');
	}
    }

    /**
     * Wyswietla strone przedmiotow
     *
     * @param string $err kod bledu
     */
    public function action_index($err=null) {

	$view = View::factory('_root_template');
	$view2 = view::factory('przedmioty_index');

	$view2->set('_err', $err);
	$view2->set('res', Isf2::Connect()->Select('przedmioty')
			->OrderBy(array('przedmiot' => 'asc'))
			->Execute()->fetchAll());

	$view->set('content', $view2->render());
	echo $view->render();
    }

    /**
     * Operacje na przedmiotach
     */
    public function action_commit() {
	if (!isset($_POST)) {
	    Kohana_Request::factory()->redirect('');
	    exit;
	}
	if (isset($_POST['btnAddSubject'])) {
	    $isf = Isf2::Connect();

	    $subjExist = $isf->Select('przedmioty')
			    ->Where(array('przedmiot' => $_POST['inpPrzedmiot']))
			    ->Execute()->fetchAll();
	    if (count($subjExist) > 0) {
		Kohana_Request::factory()->redirect('przedmioty/index/e1');
		exit;
	    }

	    $m = preg_match('/([.!@;#$%^&*()_+|])/i', $_POST['inpPrzedmiot']);

	    if ($m == true) {
		Kohana_Request::factory()->redirect('przedmioty/index/e2');
		exit;
	    }

	    if ($_POST['inpPrzedmiot'] == '' || $_POST['inpPrzedmiot'] == null || empty($_POST['inpPrzedmiot'])) {
		Kohana_Request::factory()->redirect('przedmioty/index/e3');
		exit;
	    }
	    $isf->Insert('przedmioty', array('przedmiot' => $_POST['inpPrzedmiot']))
		    ->Execute();

	    Kohana_Request::factory()->redirect('przedmioty/index/pass');
	}
	if (isset($_POST['btnEditSubject'])) {
	    if (!isset($_POST['rdSubject'])) {
		Kohana_Request::factory()->redirect('przedmioty/index/nse');
		exit;
	    }
	    $this->action_zarzadzanie($_POST['rdSubject']);
	}
	if (isset($_POST['btnDelSubjects'])) {
	    if (!isset($_POST['sDel'])) {
		Kohana_Request::factory()->redirect('przedmioty/index/nsd');
		exit;
	    }
	    $isf = Isf2::Connect();
	    foreach ($_POST['sDel'] as $id => $przedmiot) {
		$this->action_usun($przedmiot);
	    }
	    Kohana_Request::factory()->redirect('przedmioty/index');
	}
	if (isset($_POST['btnPrzypiszNl'])) {
	    $this->action_przypisz_nl($_POST['przedmiot'], $_POST['selNaucz']);
	}
	if (isset($_POST['btnWypiszNl'])) {
	    if (!isset($_POST['rdNauczyciel'])) {
		$this->action_zarzadzanie($_POST['przedmiot']);
	    } else {
		$this->action_wypisz($_POST['przedmiot'], $_POST['rdNauczyciel']);
	    }
	}
	if (isset($_POST['btnAddClassroom'])) {
	    Isf2::Connect()->Insert('przedmiot_sale', array(
		'przedmiot' => $_POST['formPrzedmiot'],
		'sala' => $_POST['selSala']
	    ));
	    $this->action_zarzadzanie($_POST['formPrzedmiot']);
	}
    }

    /**
     * Usuwa przedmiot
     *
     * @param string $przedmiot przedmiot
     */
    private function action_usun($przedmiot) {
	$isf = Isf2::Connect();
	$isf->Delete('przedmioty')->Where(array('przedmiot' => $przedmiot))->Execute();
	$isf->Delete('przedmiot_sale')->Where(array('przedmiot' => $przedmiot))->Execute();
	$isf->Delete('nl_przedm')->Where(array('przedmiot' => $przedmiot))->Execute();
    }

    /**
     * Wypisuje nauczyciela z przedmiotu
     *
     * @param string $przedmiot przedmiot
     * @param string $nauczyciel nauczyciel
     */
    private function action_wypisz($przedmiot, $nauczyciel) {
	Isf2::Connect()->Delete('nl_przedm')
		->Where(array(
		    'nauczyciel' => $nauczyciel,
		    'przedmiot' => $przedmiot,
		))->Execute();
	$this->action_zarzadzanie($przedmiot);
    }

    /**
     * Przypisuje nauczyciela do przedmiotu
     */
    private function action_przypisz_nl($przedmiot, $nauczyciel) {
	Isf2::Connect()->Insert('nl_przedm', array(
	    'nauczyciel' => $nauczyciel,
	    'przedmiot' => $przedmiot
	))->Execute();
	$this->action_zarzadzanie($przedmiot);
    }

    /**
     * Strona zarzadzania przedmiotem
     *
     * @param string $przedmiot przedmiot
     */
    private function action_zarzadzanie($przedmiot) {
	$view = View::factory('_root_template');
	$view2 = view::factory('przedmioty_zarzadzanie');

	$view2->set('przedmiot', $przedmiot);

	$view->set('content', $view2->render());
	echo $view->render();
    }

}