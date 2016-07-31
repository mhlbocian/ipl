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
 * Odpowiada za obsluge planow zajec
 * 
 * @package plan
 */
class Controller_Plan extends Controller {

    /**
     * Sprawdza zalogowanie uzytkownika
     */
    public function __construct() {
	App_Auth::isLogged(FALSE);

	if (App_Globals::getRegistryKey('edycja_danych') == 1) {
	    Kohana_Request::factory()->redirect('');
	    exit;
	}
    }

    /**
     * Wyswietla edycje planu dla klasy AJAX
     *
     * @param string $klasa klasa
     */
    public function action_klasa($klasa) {
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$isf->JQUi();
	$isf->JQUi_AjaxdivDoAjax('progress', URL::site('plan/klasaajax/' . $klasa), true);

	$view = view::factory('plan_klasa');
	$view->set('klasa', $klasa);
	echo View::factory('_root_template')
		->set('content', $view)
		->set('script', $isf->JQUi_MakeScript());
    }

    /**
     * Wyswietla plan grupowy dla klasy AJAX
     *
     * @param string $klasa klasa
     */
    public function action_grupy($klasa) {
        $isf = new Kohana_Isf();
        $isf->JQUi();
	$isf->JQUi_AjaxdivDoAjax('progress', URL::site('plan/grupaajax/' . $klasa), true);
	$view = view::factory('plan_grupy');
	$view->set('klasa', $klasa);
	echo View::factory('_root_template')
		->set('content', $view)
		->set('script', $isf->JQUi_MakeScript());
    }

    /**
     * Wyswietla tresc strony dla wywolania AJAX
     * 
     * W przypadku przegladarki Internet Explorer wyswietlany jest ten
     * surowy szablon
     *
     * @param string $klasa klasa
     * @param boolean $alternative wyswietlanie klasycznej strony
     */
    public function action_klasaajax($klasa, $alternative=false) {
	$view = view::factory('plan_klasaajax');
	$view->set('alternative', $alternative);
	$view->set('klasa', $klasa);
	echo $view->render();
    }

    /**
     * Wyswietla tresc strony dla wywolania AJAX
     * 
     * W przypadku przegladarki Internet Explorer wyswietlany jest ten
     * surowy szablon
     * 
     * @param string $klasa klasa
     * @param boolean $alternative wyswietlanie klasycznej strony
     */
    public function action_grupaajax($klasa, $alternative=false) {
	$view = view::factory('plan_grupaajax');
	$view->set('alternative', $alternative);
	$view->set('klasa', $klasa);
	echo $view->render();
    }

    /**
     * Wprowadza zmiany do planu klasy
     */
    public function action_zatwierdz() {
	$isf = Isf2::Connect();

	$klasa = $_POST['klasa'];

	$isf->Delete('plan_grupy')->Where(array('klasa' => $klasa))->Execute();

	$dni = array('Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek');

	foreach ($dni as $dzien) {
	    foreach ($_POST[$dzien] as $lek => $przedm) {
		$isf->Delete('planlek')
			->Where(array(
			    'dzien' => $dzien,
			    'lekcja' => $lek,
			    'klasa' => $klasa
			))->Execute();
		if ($przedm != '---') {
		    $przedm = explode(':', $przedm);
		    if (count($przedm) == 1) {
			$colval = array(
			    'dzien' => $dzien,
			    'klasa' => $klasa,
			    'lekcja' => $lek,
			    'przedmiot' => $przedm[0],
			);
		    } else {
			$nl_s = App_Globals::getTeacherSym($przedm[2]);
			$colval = array(
			    'dzien' => $dzien,
			    'klasa' => $klasa,
			    'lekcja' => $lek,
			    'przedmiot' => $przedm[0],
			    'sala' => $przedm[1],
			    'nauczyciel' => $przedm[2],
			    'skrot' => $nl_s,
			);
		    }
		    try {
			$isf->Insert('planlek', $colval)->Execute();
		    } catch (Exception $e) {
			Core_Tools::ShowError($e->getMessage(), $e->getCode());
		    }
		}
	    }
	}

	Kohana_Request::factory()->redirect();
    }

    /**
     * Wprowadza zmiany do planu grupowego
     */
    public function action_grupazatw() {
	$isf = Isf2::Connect();

	$klasa = $_POST['klasa'];

	$isf->Delete('plan_grupy')->Where(array('klasa' => $klasa))->Execute();
	$dni = array('Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek');
	$err = '';
	foreach ($dni as $dzien) {
	    foreach ($_POST[$dzien] as $lek => $przedlek) {
		foreach ($przedlek as $grupa => $przedm) {
		    if ($przedm != '---') {
			$przedm = explode(':', $przedm);
			if (count($przedm) == 1) {
			    $colval = array(
				'dzien' => $dzien,
				'klasa' => $klasa,
				'lekcja' => $lek,
				'grupa' => $grupa,
				'przedmiot' => $przedm[0],
			    );
			} else {
			    $nl_s = App_Globals::getTeacherSym($przedm[2]);
			    $valid = $isf->Select('plan_grupy')->Where(array(
					'dzien' => $dzien,
					'lekcja' => $lek,
					'nauczyciel' => $przedm[2],
					'sala::!=' => $przedm[1]
				    ))->Execute()->fetchAll();
			    if (count($valid) > 0) {
				$err .= '<p>Nauczyciel ' . $przedm[2] . ' prowadzi juz zajecia z <b>' . $valid[0]['przedmiot'] . '</b> w
				    <b>' . $dzien . '</b> na lekcji ' . $lek . '
					w sali ' . $valid[0]['sala'] . ' (wybrana sala: <b>' . $przedm[1] . '</b>, przedmiot:
					    <b>' . $przedm[0] . '</b>)<br/>
					Pominieto: <b>' . $dzien . '</b> lek:<b>' . $lek . '</b>
					    klasa:<b>' . $klasa . '</b> gr:<b>' . $grupa . ' ' . $przedm[0] . '</b></p>---';
			    } else {

				$colval = array(
				    'dzien' => $dzien,
				    'klasa' => $klasa,
				    'lekcja' => $lek,
				    'grupa' => $grupa,
				    'przedmiot' => $przedm[0],
				    'sala' => $przedm[1],
				    'nauczyciel' => $przedm[2],
				    'skrot' => $nl_s,
				);
			    }
			}
			try {
			    $isf->Insert('plan_grupy', $colval)->Execute();
			} catch (Exception $e) {
			    Core_Tools::ShowError($e->getMessage(), $e->getCode());
			}
		    }
		}
	    }
	}
	if ($err == '') {
	    Kohana_Request::factory()->redirect();
	} else {
	    $view = View::factory('_root_template');
	    $view2 = view::factory('plan_error');
	    $view2->set('content', $err);
	    $view->set('content', $view2->render());
	    echo $view;
	}
    }

    /**
     * Wyswietla strone eksportu
     */
    public function action_export() {
	$view = View::factory('_root_template');
	$view2 = view::factory('plan_export');

	$view->set('content', $view2->render());
	echo $view->render();
    }

}