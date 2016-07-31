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
 * Odpowiada za dostep do trybu administratora
 * 
 * @package admin
 */
class Controller_Admin extends Controller {

    /**
     * Uruchamia glowna strone
     */
    public function action_index() {
	App_Auth::isLogged(false);
    }

    /**
     * logowanie do systemu
     *
     * @param boolean $pass poprawnosc logowania
     */
    public function action_login($pass='') {

	if (App_Auth::isLogged(false, true) != false) {
	    Kohana_Request::factory()->redirect('');
	    exit;
	}

	$view = View::factory('_root_template');
	$view2 = view::factory('admin_login');

	if (isset($pass)) {
	    $view2->set('pass', $pass);
	} else {
	    $view2->set('pass', '');
	}

	$view->set('content', $view2->render());
	echo $view->render();
    }

    /**
     * odpowiada za walidacje danych do logowania
     */
    public function action_dologin() {
	if (!isset($_POST)) {
	    Kohana_Request::factory()->redirect('');
	} else {
	    if (!isset($_POST['inpLogin']))
		$_POST['inpLogin'] = '';
	    if (!isset($_POST['inpHaslo']))
		$_POST['inpHaslo'] = '';
	    if (!isset($_POST['inpToken']))
		$_POST['inpToken'] = '';
	    App_Auth::doLogin($_POST['inpLogin'], $_POST['inpHaslo'], $_POST['inpToken']);
	}
    }

    /**
     * strona zamkniecia edycji sal, przedmiotow, etc
     */
    public function action_doEditTimetables() {

	App_Auth::isLogged();

	$view = View::factory('_root_template');
	$view2 = view::factory('admin_doEditTimetables');

	$view->set('content', $view2->render());
	echo $view->render();
    }

    /**
     * potwierdza zamkniecie edycji sal, przedmiotow, etc
     */
    public function action_doEditTimetablesPOST() {

	App_Auth::isLogged();
	if (isset($_POST)) {
	    Isf2::Connect()->Update('rejestr', array('wartosc' => '0'))
		    ->Where(array('opcja' => 'edycja_danych'))
		    ->Execute();
	    insert_log('admin.doEditTimetables', 'Przejscie do modulu Planu Zajec');
	    Kohana_Request::factory()->redirect('default/index');
	}
    }

    /**
     * strona zamkniecia edycji planow zajec
     */
    public function action_doSaveTimetables() {

	App_Auth::isLogged(false);

	$view = View::factory('_root_template');
	$view2 = view::factory('admin_doSaveTimetables');

	$view->set('content', $view2->render());
	echo $view->render();
    }

    /**
     * potwierdza zamkniecie edycji planow
     */
    public function action_doSaveTimetablesPOST() {

	App_Auth::isLogged(false);
	if (isset($_POST)) {
	    $isf = new Kohana_Isf();
	    $isf->Connect(APP_DBSYS);
	    $isf->DbUpdate('rejestr', array('wartosc' => '3'), 'opcja=\'edycja_danych\'');
	    App_Globals::writeXmlTimetables();
	    insert_log('admin.doSavetimetablesPOST', 'Zamkniecie edycji Planow Zajec');
	    Kohana_Request::factory()->redirect('default/index');
	}
    }

    /**
     * Odnawia token
     */
    public function action_doRenewToken() {

	App_Auth::isLogged(false);

	$timestamp = (time() + 3600 * 3);
	Isf2::Connect()->Update('uzytkownicy', array('webapi_timestamp' => $timestamp))
		->Where(array('webapi_token' => $_SESSION['token']))
		->Execute();
	$_SESSION['token_time'] = App_Auth::showAuthTime($_SESSION['token']);
	insert_log('admin.doRenewToken', 'Uzytkownik ' . $_SESSION['user'] . ' odnowil token');
	Request::factory()->redirect('');
    }

    /**
     * wylogowuje
     */
    public function action_doLogout() {
	App_Auth::doLogout($_SESSION['token']);
	setcookie('login', '', time() - 3600, '/');
	session_destroy();
	insert_log('admin.doLogout', 'Uzytkownik ' . $_SESSION['user'] . ' wylogował się');
	Kohana_Request::factory()->redirect('default/index');
    }

    /**
     * strona usuwania planow
     */
    public function action_doTimetablesCleanup() {

	App_Auth::isLogged();

	if (!isset($_POST)) {
	    Kohana_Request::factory()->redirect('');
	    exit;
	}
	$db = Isf2::Connect();
	$db->Delete('planlek')->Execute();
	$db->Delete('plan_grupy')->Execute();
	$db->Delete('zast_id')->Execute();
	$db->Delete('zastepstwa')->Execute();
	$db->Update('rejestr', array('wartosc' => '0'))
		->Where(array('opcja' => 'edycja_danych'))
		->Execute();
	Kohana_Request::factory()->redirect('default/index');
    }

    /**
     * strona usuwania danych jak sale, etc
     */
    public function action_doCleanupSystem() {

	App_Auth::isLogged();

	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);

	$isf->JQUi();

	$view = View::factory('_root_template');
	$view2 = view::factory('admin_doCleanupSystem');

	$view->set('script', $isf->JQUi_MakeScript());
	$view->set('content', $view2->render());
	echo $view->render();
    }

    /**
     * usuwa dane jak sale, etc
     */
    public function action_doCleanupSystemPOST() {

	App_Auth::isLogged();

	$db = Isf2::Connect();
	$db->Delete('planlek')->Execute();
	$db->Delete('plan_grupy')->Execute();
	$db->Delete('zast_id')->Execute();
	$db->Delete('zastepstwa')->Execute();
	$db->Update('rejestr', array('wartosc' => '1'))
		->Where(array('opcja' => 'edycja_danych'))
		->Execute();
	if (isset($_POST['cl'])) {
	    $db->Delete('klasy')->Execute();
	    $db->Delete('nauczyciele')->Execute();
	    $db->Delete('nl_klasy')->Execute();
	    $db->Delete('nl_przedm')->Execute();
	    $db->Delete('przedmiot_sale')->Execute();
	    $db->Delete('przedmioty')->Execute();
	    $db->Delete('sale')->Execute();
	    $db->Update('rejestr', array('wartosc' => '1'))
		    ->Where(array('opcja' => 'ilosc_godzin_lek'))
		    ->Execute();
	}
	Kohana_Request::factory()->redirect('');
    }

    /**
     * strona zmiana danych szkoly, strony glownej
     */
    public function action_doEditSettings() {

	App_Auth::isLogged();

	$url = substr(URL::base(), 0, -1);
	/**
	 * Skrypt TinyMCE
	 */
	$script = <<< START
<script type='text/javascript' src='$url/lib/tiny_mce/tiny_mce.js'></script>
<script type='text/javascript'>
    tinyMCE.init({
        mode : "textareas",
	theme : "simple"
    });
</script>
START;

	$view = View::factory('_root_template');
	$view2 = view::factory('admin_doEditSettings');

	$view->set('script', $script);
	$view->set('content', $view2->render());
	echo $view->render();
    }

    /**
     * zmienia dane szkoly, strony glownej
     */
    public function action_doEditSettingsPOST() {

	App_Auth::isLogged();

	if (!isset($_POST)) {
	    Kohana_Request::factory()->redirect('');
	    exit;
	}
	$nazwa = $_POST['inpNazwa'];
	$text = $_POST['txtMsg'];

	$isf = Isf2::Connect();

	$isf->Update('rejestr', array('wartosc' => $nazwa))
		->Where(array('opcja' => 'nazwa_szkoly'))->Execute();
	$isf->Update('rejestr', array('wartosc' => $text))
		->Where(array('opcja' => 'index_text'))->Execute();

	Kohana_Request::factory()->redirect('default/index');
    }

    /**
     * strona zmiany hasla
     */
    public function action_doChangePassword($err=false) {

	App_Auth::isLogged(false);

	$view = View::factory('_root_template');
	$view2 = view::factory('admin_doChangePassword');

	if ($err != false) {
	    $view2->set('_tplerr', $err);
	} else {
	    $view2->set('_tplerr', '');
	}

	$view->set('content', $view2->render());
	echo $view->render();
    }

    /**
     * zmienia haslo
     */
    public function action_doChangePasswordPOST() {

	App_Auth::isLogged(false);

	if (isset($_POST)) {

	    $s = $_POST['inpSH'];
	    $n = $_POST['inpNH'];
	    $p = $_POST['inpPH'];

	    if (strlen($_POST['inpNH']) < 6) {
		Kohana_Request::factory()->redirect('admin/doChangePassword/false');
		exit;
	    }

	    if ($n != $p) {
		Kohana_Request::factory()->redirect('admin/doChangePassword/false');
		exit;
	    }
	    $act = App_Auth::doChangePass($_SESSION['token'], $s, $n);
	    if ($act == 'auth:failed') {
		Kohana_Request::factory()->redirect('admin/doChangePassword/false');
	    } else {
		insert_log('admin.chpass', 'Uzytkownik ' . $_SESSION['user'] . ' zmienil haslo');
		Kohana_Request::factory()->redirect('admin/doChangePassword/pass');
	    }
	} else {
	    Kohana_Request::factory()->redirect('');
	}
    }

    /**
     * 
     * Wyswietla strone z uzytkownikami
     */
    public function action_users() {

	App_Auth::isLogged();
	if (defined('ldap_enable') && ldap_enable == "true") {
	    Kohana_Request::factory()->redirect('');
	}
	$view = View::factory('_root_template');
	$view2 = new View('admin_users');
	$view->set('content', $view2->render());

	echo $view->render();
    }

    /**
     * Wyswietla strone logow systemowych
     *
     * @param integer $page strona logow
     */
    public function action_logs($page=1) {
	App_Auth::isLogged();
	$view = View::factory('_root_template');
	$view2 = new View('admin_logs');
	$view2->set('page', $page);
	$view->set('content', $view2->render());

	echo $view->render();
    }

    /**
     * 
     * Usuwa wszystkie logi systemowe
     */
    public function action_dellogs() {
	App_Auth::isLogged();
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$isf->DbDelete('log', 'id like \'%\'');
	Kohana_Request::factory()->redirect('admin/logs');
    }

    /**
     * 
     * Wywietla strone generujaca tokeny dla uzytkownika o danym numerze id
     *
     * @param integer $user id uzytkownika
     */
    public function action_token($user) {
	App_Auth::isLogged();
	$view = View::factory('admin_token');
	$view->set('id', $user);
	echo $view->render();
    }

    /**
     * 
     * Usuwa uzytkownika o numerze id
     *
     * @param integer $uid numer uzytkownika
     */
    public function action_userdel($uid) {
	App_Auth::isLogged();
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$u = $isf->DbSelect('uzytkownicy', array('*'), 'where uid=\'' . $uid . '\'');
	$isf->DbDelete('uzytkownicy', 'uid=\'' . $uid . '\'');
	$isf->DbDelete('tokeny', 'login=\'' . $u[0]['login'] . '\'');
	Kohana_Request::factory()->redirect('admin/users');
    }

    /**
     * 
     * Wyswietla strone dodawania uzytkownika
     *
     * @param string $err kod bledu do szablonu
     */
    public function action_adduser($err=null) {
	App_Auth::isLogged();
	if (defined('ldap_enable') && ldap_enable == "true") {
	    Kohana_Request::factory()->redirect('');
	}
	$view = View::factory('_root_template');
	$view2 = new View('admin_adduser');
	$view2->set('err', $err);
	$view->set('content', $view2->render());

	echo $view->render();
    }

    /**
     * 
     * Dodaje uzytkownika
     */
    public function action_douseradd() {
	App_Auth::isLogged();
	if (!isset($_POST)) {
	    Kohana_Request::factory()->redirect('');
	    exit;
	}
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$login = $_POST['inpLogin'];
	$haslo = $_POST['inpHaslo'];
	$uid = $_POST['inpUid'];
	if (strlen($login) < 5 || strlen($haslo) < 6) {
	    Kohana_Request::factory()->redirect('admin/adduser/leng');
	    exit;
	}
	if (preg_match('/([!@#$;%^&*()+| ])/i', $login)) {
	    Kohana_Request::factory()->redirect('admin/adduser/data');
	    exit;
	}
	$arr = array(
	    'uid' => $uid,
	    'login' => $login,
	    'haslo' => md5('plan' . sha1('lekcji' . $haslo))
	);
	$isf->DbInsert('uzytkownicy', $arr);
	Kohana_Request::factory()->redirect('admin/users');
    }

    /**
     * Odblokowuje uzytkownika
     *
     * @param integer $uid ID uzytkownika
     */
    public function action_userublock($uid) {
	$db = Isf2::Connect();
	$db->Update('uzytkownicy', array('ilosc_prob' => '0'))
		->Where(array('uid' => $uid))->Execute();
	Kohana_Request::factory()->redirect('admin/users');
    }

    /**
     * Wyswietla strone kopii zapasowej
     */
    public function action_backup() {
	$view = View::factory('_root_template');
	$view2 = new View('admin_backup');
	$view->set('content', $view2->render());
	echo $view->render();
    }

    /**
     * Wykonuje kopie zapasowa
     */
    public function action_dobackup() {

	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$xml = new XMLWriter();
	$xml->openMemory();
	$xml->startDocument('1.0', 'UTF-8');
	$xml->setIndent(4);
	$xml->writeComment('---WYGENEROWANO APLIKACJA BACKUP PLAN LEKCJI');
	$xml->writeComment('---WERSJA: testing, dnia ' . date('d.m.Y'));
	$xml->startElement('backup');
	foreach ($isf->DbSelect('sqlite_master', array('name'), 'where type=\'table\' order by name') as $row) {
	    $xml->startElement('table');
	    $xml->startAttribute('name');
	    $xml->text($row['name']);
	    $xml->endAttribute();

	    foreach ($isf->DbSelect($row['name'], array('*')) as $rowx) {
		$xml->startElement('row');
		foreach ($rowx as $attr => $value) {
		    if (!is_numeric($attr)) {
			$xml->startElement($attr);
			$xml->text($value);
			$xml->endElement();
		    }
		}
		$xml->endElement();
	    }
	    $xml->endAttribute();
	    $xml->endElement();
	}
	$xml->endElement();
	$xml->endDocument();

	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=backup.xml;");

	echo $xml->flush();
    }

}
