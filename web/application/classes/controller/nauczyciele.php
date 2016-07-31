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
 * Odpowiada za obsluge nauczycieli
 * 
 * @package nauczyciele
 */
class Controller_Nauczyciele extends Controller {

    /**
     * Tworzy obiekt sesji i sprawdza czy zalogowany
     */
    public function __construct() {

	App_Auth::isLogged();

	if (App_Globals::getRegistryKey('edycja_danych') != 1) {
	    Kohana_Request::factory()->redirect('');
	    exit;
	}
    }

    /**
     * Wyswietla strone z nauczycielami
     *
     * @param string $err kod bledu w szablonie
     */
    public function action_index($err=null) {
	$view = View::factory('_root_template');
	$view2 = View::factory('nauczyciele_index');

	$view2->set('_err', $err);

	$view->set('bodystr', 'onLoad=\'document.forms.form1.inpName.focus()\'');
	$view->set('content', $view2->render());
	echo $view->render();
    }

    public function action_commit() {
	if (!isset($_POST)) {
	    Kohana_Request::factory()->redirect('');
	}
	if (isset($_POST['btnAddTeacher'])) {
	    $this->action_dodaj($_POST['inpTeacherName']);
	}
	if (isset($_POST['btnEdit'])) {
	    if (!isset($_POST['rdTeacher'])) {
		Kohana_Request::factory()->redirect('nauczyciele/index/nonesel');
	    }
	    $this->action_zarzadzanie($_POST['rdTeacher']);
	}
	if (isset($_POST['btnDel'])) {
	    if (!isset($_POST['tDel'])) {
		Kohana_Request::factory()->redirect('nauczyciele/index/nonesel');
	    }
	    foreach ($_POST['tDel'] as $id => $sym) {
		$this->action_usun($sym);
	    }
	    Kohana_Request::factory()->redirect('nauczyciele/index/completed');
	}
	if (isset($_POST['btnAddClass'])) {
	    $this->action_dodklasa($_POST['skrot'], $_POST['selKlasy']);
	    $this->action_zarzadzanie($_POST['skrot']);
	}
	if (isset($_POST['btnDelClass'])) {
	    if (!isset($_POST['rdClass'])) {
		$this->action_zarzadzanie($_POST['skrot']);
	    }
	    $this->action_delklasa($_POST['skrot'], $_POST['rdClass']);
	    $this->action_zarzadzanie($_POST['skrot']);
	}
	if (isset($_POST['btnAddSubject'])) {
	    $this->action_dodprzed($_POST['skrot'], $_POST['selPrzedm']);
	    $this->action_zarzadzanie($_POST['skrot']);
	}
	if (isset($_POST['btnDelSubject'])) {
	    if (!isset($_POST['rdSubject'])) {
		$this->action_zarzadzanie($_POST['skrot']);
	    }
	    $this->action_delprzed($_POST['skrot'], $_POST['rdSubject']);
	    $this->action_zarzadzanie($_POST['skrot']);
	}
    }

    /**
     * Strona zarzadzania nauczycielem
     *
     * @param string $skrot kod nauczyciela
     */
    private function action_zarzadzanie($skrot) {

	$nauczyciel = App_Globals::getTeacherName($skrot);

	$view = View::factory('_root_template');
	$view2 = View::factory('nauczyciele_zarzadzanie');
	$view2->set('nauczyciel', $nauczyciel);
	$view2->set('skrot', $skrot);

	$view->set('content', $view2->render());
	echo $view->render();
    }

    /**
     * Dodaje nauczyciela
     * 
     * @param string $teacher Imie i nazwisko
     */
    private function action_dodaj($teacher) {

	$isf = Isf2::Connect();

	$nl_exist = $isf->Select('nauczyciele')
			->Where(array('imie_naz' => $teacher))
			->Execute()->fetchAll();

	if (count($nl_exist) != 0) {
	    Kohana_Request::factory()->redirect('nauczyciele/index/e1');
	    exit;
	}

	$m = preg_match('/([.!@#$%;^&*()_+|])/i', $teacher);

	if ($m == true) {
	    Kohana_Request::factory()->redirect('nauczyciele/index/e2');
	    exit;
	}

	if ($teacher == '' || $teacher == null || empty($teacher)) {
	    Kohana_Request::factory()->redirect('nauczyciele/index/e3');
	    exit;
	}

	$lit = substr($teacher, 0, 1);

	$rsl = $isf->Select('nauczyciele')->Where(array('imie_naz::like' => $lit . '%'))
			->OrderBy(array('skrot' => 'desc'))
			->Execute()->fetchAll();

	if (count($rsl) == 0)
	    $nr = 1;
	else
	    $nr = (int) (substr($rsl[0]['skrot'], 1)) + 1;

	$skrot = strtoupper($lit . $nr);

	$isf->Insert('nauczyciele', array(
	    'imie_naz' => $teacher,
	    'skrot' => $skrot,
	))->Execute();

	Kohana_Request::factory()->redirect('nauczyciele/index/pass');
    }

    /**
     * Usuwa nauczyciela
     *
     * @param string $teacher_sym kod nauczyciela
     */
    private function action_usun($teacher_sym) {
	$isf = Isf2::Connect();
	$isf->Delete('nauczyciele')
		->Where(array('skrot' => $teacher_sym))->Execute();
	$nauczyciel = App_Globals::getTeacherName($teacher_sym);
	$isf->Delete('nl_klasy')
		->Where(array('nauczyciel' => $nauczyciel))->Execute();
	$isf->Delete('nl_przedm')
		->Where(array('nauczyciel' => $nauczyciel))->Execute();
    }

    /**
     * Przypisuje nauczycielowi klase
     */
    private function action_dodklasa($skrot, $klasa) {
	$isf = Isf2::Connect();
	$nauczyciel = App_Globals::getTeacherName($skrot);
	$isf->Insert('nl_klasy', array(
	    'nauczyciel' => $nauczyciel,
	    'klasa' => $klasa
	))->Execute();
    }

    /**
     * Wypisuje klase nauczycielowi
     *
     * @param string $nauczyciel kod nauczyciela
     * @param string $klasa klasa klasa
     */
    public function action_delklasa($skrot, $klasa) {
	$isf = Isf2::Connect();
	$nl = App_Globals::getTeacherName($skrot);
	$isf->Delete('nl_klasy')
		->Where(array(
		    'nauczyciel' => $nl,
		    'klasa' => $klasa))
		->Execute();
    }

    /**
     * Przypisuje przedmiot nauczycielowi
     */
    private function action_dodprzed($skrot, $przedmiot) {
	$isf = Isf2::Connect();
	$nauczyciel = App_Globals::getTeacherName($skrot);
	$isf->Insert('nl_przedm', array(
	    'nauczyciel' => $nauczyciel,
	    'przedmiot' => $przedmiot
	))->Execute();
    }

    /**
     * Wypisuje przedmiot nauczycielowi
     *
     * @param string $nauczyciel kod nauczyciela
     * @param string $przedmiot przedmiot do wypisania
     */
    private function action_delprzed($skrot, $przedmiot) {
	$isf = Isf2::Connect();
	$nl = App_Globals::getTeacherName($skrot);
	$isf->Delete('nl_przedm')
		->Where(array(
		    'nauczyciel' => $nl,
		    'przedmiot' => $przedmiot
		))->Execute();
    }

}
