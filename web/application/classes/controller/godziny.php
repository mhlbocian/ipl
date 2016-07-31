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
 * Przetwarzanie godzin lekcyjnych
 * 
 * @package godziny
 */
class Controller_Godziny extends Controller {

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
     * Strona godzin lekcyjnych
     */
    public function action_index() {
	$view = View::factory('_root_template');
	$view2 = view::factory('godziny_index');

	$view->set('content', $view2->render());
	echo $view->render();
    }

    /**
     * Ustawia ilosc godzin lekcyjnych
     */
    public function action_ustaw() {
	$ilosc = $_POST['iloscgodzin'];

	Isf2::Connect()->Update('rejestr', array('wartosc' => $ilosc))
		->Where(array('opcja' => 'ilosc_godzin_lek'))
		->Execute();

	Isf2::Connect()->Update('rejestr', array('wartosc' => $_POST['dlugosclekcji']))
		->Where(array('opcja' => 'dlugosc_lekcji'))
		->Execute();

	Isf2::Connect()->Update('lek_godziny', array('godzina' => 'wymagane jest ponowne ustawienie'))
		->Where(array('lekcja::like' => '%'))
		->Execute();

	Kohana_Request::factory()->redirect('godziny/index');
    }

    /**
     * Ustawia czas godzin lekcyjnych
     */
    public function action_lekcje() {
	$isf = Isf2::Connect();
	$czaslek = App_Globals::getRegistryKey('dlugosc_lekcji');

	$isf->Delete('lek_godziny')->Execute();
	$isf->Update('rejestr', array('wartosc' => $_POST['czasRZH'] . ':' . $_POST['czasRZM']))
		->Where(array('opcja' => 'godz_rozp_zaj'))->Execute();
	$g1;
	$g2;
	foreach ($_POST['lekcja'] as $nrlek => $dlprz) {
	    if ($nrlek == 1) {
		$g1 = $_POST['czasRZH'] . ':' . $_POST['czasRZM'];
	    } else {
		$g1 = explode(':', $g2);
		$nl = $nrlek - 1;
		$cp = explode(':', $_POST['lekcja'][$nl]);
		$g1 = date('H:i', mktime($g1[0], $g1[1] + $cp[1]));
	    }
	    $g2 = explode(':', $g1);
	    $g2 = date('H:i', mktime($g2[0], $g2[1] + $czaslek));
	    $res = $g1 . ' - ' . $g2;
	    $isf->Insert('lek_godziny', array(
		'lekcja' => $nrlek,
		'godzina' => $res,
		'dl_prz' => $dlprz,
	    ))->Execute();
	}
	Kohana_Request::factory()->redirect('godziny/index');
    }

}