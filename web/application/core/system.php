<?php

/**
 * Plik jadra IPL
 * 
 * @author Michal Bocian <mhl.bocian@gmail.com>
 * @license GNU GPL v3
 * @package ipl\core
 */

/**
 * Klasa modulu planu zajec
 *
 * @package ipl\core
 */
class MPZ {

    protected $CT;
    protected $DB;

    /**
     * Konstruktor klasy
     */
    public function __construct() {
        $this->CT = new Core_Tools();
        $this->DB = Kohana_Isf::factory();
        $this->DB->Connect(APP_DBSYS);
    }

    /**
     * Zwraca przedzial godzinowy lekcji
     *
     * @param int $lesson Lekcja
     * @return string 
     */
    public function getLessonHour($lesson) {
        $res = $this->DB->DbSelect('lek_godziny', array('godzina'), 'where lekcja=\'' . $lesson . '\'');
        if (count($res) == 0) {
            $return = 'fetched:none';
        } else {
            $return = $res[0]['godzina'];
        }

        return $return;
    }

    /**
     * Pobiera lekcje dla klasy
     *
     * @param string $class Klasa
     * @param string $day Dzien tygodnia
     * @param string $lesson Lekcja
     * @return mixed 
     */
    public function getLesson($class, $day, $lesson) {
        $single = $this->CT->getSingleLesson($class, $day, $lesson);
        if ($single == 'fetched:none') {
            return $this->CT->getGroupLesson($class, $day, $lesson);
        } else {
            return array('t_single' => $single);
        }
    }

}

/**
 * Uwierzytelnianie uzytkownikow
 */
class App_Auth {

    /**
     * Zmienia haslo uzytkownika
     *
     * @param string $token token
     * @param string $old stare haslo
     * @param string $new nowe haslo
     * @return string auth:chpasswd, auth:failed 
     */
    public static function doChangePass($token, $old, $new) {

        $db = Isf2::Connect();

        $oldm = md5('plan' . sha1('lekcji' . $old));
        $newm = md5('plan' . sha1('lekcji' . $new));

        $old_user = $db->Select('uzytkownicy', array('haslo'))
                        ->Where(array('haslo' => $oldm))
                        ->Execute()->FetchAll();

        if (count($old_user) != 1) {
            return 'auth:failed';
        } else {
            $db->Update('uzytkownicy', array('haslo' => $newm))
                    ->Where(array('webapi_token' => $token, 'haslo' => $oldm))
                    ->Execute();
            return 'auth:chpasswd';
        }
    }

    public static function showAuthTime($token) {
        $res = Isf2::Connect()->Select('uzytkownicy')
                        ->Where(array('webapi_token' => $token))
                        ->Execute()->fetchAll();
        if (count($res) != 1) {
            return 'auth:failed';
        } else {
            return date('Y-m-d H:i:s', $res[0]['webapi_timestamp']);
        }
    }

    public static function doLogout($token) {
        Isf2::Connect()->Update('uzytkownicy', array(
                    'webapi_timestamp' => '', 'webapi_token' => ''
                ))
                ->Where(array('webapi_token' => $token))
                ->Execute();
        return 'auth:logout';
    }

    /**
     * Sprawdza zalogowanie uzytkownika
     *
     * @param bool $only_root Dostep tylko dla root
     * @param bool $loginpage Pominiecie funkcji dla strony logowania
     * @return bool 
     */
    public static function isLogged($only_root = true, $loginpage = false) {

        if (!isset($_SESSION['token']) || !isset($_SESSION['user'])) {
            if ($loginpage == false) {
                Kohana_Request::factory()->redirect('admin/login');
                exit;
            }
            return false;
        } else {

            $auth = self::showAuthTime($_SESSION['token']);

            try {
                $res = Isf2::Connect()->Select('uzytkownicy', array('webapi_token'))
                        ->Where(array('login' => $_SESSION['user'], 'webapi_token' => $_SESSION['token']))
                        ->Execute();
            } catch (Exception $e) {
                echo Core_Tools::ShowError($e->getMessage(), $e->getCode());
            }

            if (count($res) == 0) {
                session_destroy();
                self::doLogout($_SESSION['token']);
                Kohana_Request::factory()->redirect('admin/login/exist');
                exit;
            }

            if (strtotime($_SESSION['token_time']) < time()) {
                session_destroy();
                self::doLogout($_SESSION['token']);
                Kohana_Request::factory()->redirect('admin/login/delay');
                exit;
            }

            if ($auth == 'auth:failed') {
                session_destroy();
                self::doLogout($_SESSION['token']);
                Kohana_Request::factory()->redirect('admin/login');
                exit;
            }

            if ($_SESSION['user'] != 'root' && $only_root == true) {
                Kohana_Request::factory()->redirect('');
                exit;
            }

            return true;
        }
    }

    /**
     * Zwraca token sesyjny
     *
     * @param string $uid login uzytkownika
     * @return string token
     */
    public static function generateToken($login) {
        return md5(sha1('1s#plan!!002' . $login . 'r98mMjs7^A2b' . rand(1000, 9999)) . time());
    }

    /**
     * Logowanie uzytkownika RAND TOKEN
     *
     * @param string $login nazwa uzytkownika
     * @param string $password haslo
     * @param string $token token logowania
     * @return string token lub auth:failed 
     */
    public static function doRandTokenLogin($login, $password, $token) {

        $dbn = Isf2::Connect();

        $token = md5('plan' . $token);
        $haslo = md5('plan' . sha1('lekcji' . $password));

        $userData = $dbn->Select('uzytkownicy')
                        ->Where(array('login' => $login))
                        ->Execute()->fetchAll();

        $userToken = $dbn->Select('tokeny')
                        ->Where(array(
                            'login' => $login,
                            'token' => $token,
                        ))->Execute()->fetchAll();

        $dbn->Update('uzytkownicy', array(
                    'webapi_token' => '',
                    'webapi_timestamp' => '',
                ))
                ->Where(array('login' => $_POST['inpLogin']))
                ->Execute();

        if (count($userData) != 1) {
            return 'auth:failed';
        } else if ($userData[0]['ilosc_prob'] >= 3 && $login != 'root') {
            return 'auth:locked';
        } else if ($userData[0]['haslo'] != $haslo) {
            if ($login != 'root') {
                $nr = $userData[0]['ilosc_prob'] + 1;
                $dbn->Update('uzytkownicy', array('ilosc_prob' => $nr))
                        ->Where(array('login' => $login))
                        ->Execute();
            }
            return 'auth:failed';
        } else if (count($userToken) == 0 && !defined('global_rand_enabled')) {
            if ($login != 'root') {
                $nr = $userData[0]['ilosc_prob'] + 1;
                $dbn->Update('uzytkownicy', array('ilosc_prob' => $nr))
                        ->Where(array('login' => $login))
                        ->Execute();
            }
            return 'auth:failed';
        } else {
            $timestamp = (time() + 3600 * 3);
            $sessionToken = App_Auth::generateToken($userData[0]['login']);

            if ($login != 'root') {
                $dbn->Delete('tokeny')
                        ->Where(array('login' => $login, 'token' => $token))
                        ->Execute();
            }

            $arr = array(
                'ilosc_prob' => '0',
                'webapi_token' => $sessionToken,
                'webapi_timestamp' => $timestamp
            );

            $dbn->Update('uzytkownicy', $arr)
                    ->Where(array('login' => $login))
                    ->Execute();
            insert_log('admin.login', 'Uzytkownik ' . $login . ' zalogowal sie');
            return $sessionToken;
        }
    }

    /**
     * Logowanie LDAP
     *
     * @param string $login
     * @param string $password 
     * @return string token
     */
    public static function doLDAPLogin($login, $password) {
        $conn = ldap_connect(ldap_server);
        $dn = 'cn=' . $login . ',' . ldap_basedn;
        try {
            $bind = ldap_bind($conn, $dn, $password);

            if ($bind) {
                $timestamp = (time() + 3600 * 3);
                $token = App_Auth::generateToken($login);
                $isf = Isf2::Connect();
                $suildb = $isf->Select('uzytkownicy')
                                ->Where(array('login' => $login))
                                ->Execute()->fetchAll();
                if (count($suildb) == 0) {
                    $uid = $isf->Select('uzytkownicy', array('*'))
                                    ->OrderBy(array('uid' => 'desc'))
                                    ->Execute()->fetchAll();
                    $uid = $uid[0]['uid'] + 1;
                    $isf->Insert('uzytkownicy', array(
                        'uid' => $uid,
                        'login' => $login,
                        'haslo' => 'ldap_login',
                        'webapi_token' => $token,
                        'webapi_timestamp' => $timestamp,
                    ))->Execute();
                } else {
                    $isf->Update('uzytkownicy', array(
                                'webapi_token' => $token,
                                'webapi_timestamp' => $timestamp,
                            ))
                            ->Where(array('login' => $login))
                            ->Execute();
                }
                insert_log('admin.login.ldap', 'Autoryzacja ' . $dn . ': OK');
                $return = $token;
            } else {
                $return = 'auth:failed';
                insert_log('admin.login.ldap', 'Nieudana autoryzacja: ' . $dn);
            }
        } catch (Exception $e) {
            $return = 'auth:failed';
            insert_log('admin.login.ldap', 'Nieudana autoryzacja: ' . $dn);
        }
        return $return;
    }

    /**
     * Logowanie do IPL
     *
     * @param string $login
     * @param string $password
     * @param string $token 
     */
    public static function doLogin($login, $password, $token) {
        if (!defined('ldap_enable') || ldap_enable != "true") {
            $msg = self::doRandTokenLogin($login, $password, $token);
        } else {
            $msg = self::doLDAPLogin($login, $password);
        }

        if ($msg != 'auth:failed' && $msg != 'auth:locked') {
            $_SESSION['token'] = $msg;
            $_SESSION['user'] = $login;
            if (isset($token) && $login != 'root') {
                $_SESSION['usr_token'] = $token;
            }
            $_SESSION['token_time'] = self::showAuthTime($_SESSION['token']);

            Kohana_Request::factory()->redirect('');
        } else {
            if ($msg == 'auth:locked') {
                Kohana_Request::factory()->post('inpLogin', $login)
                        ->redirect('admin/login/locked');
            } else {
                Kohana_Request::factory()->post('inpLogin', $login)
                        ->redirect('admin/login/false');
            }
        }
    }

}

/**
 * Podstawowe metody zwiazane z IPL
 * 
 * @package ipl\core
 */
class App_Globals {

    /**
     * Pobiera motywy systemu
     *
     * @return array
     */
    public static function getThemes() {
        $handle = opendir(DOCROOT . 'lib' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'themes');
        $themes = array();
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' xor $file != '..' xor $file != '.svn') {
                $file = str_replace('.css', '', $file);
                $themes[] = $file;
            }
        }
        return $themes;
    }

    /**
     * Pobiera stan systemu
     * 
     * <ul>
     * <li>0 - edycja planow zajec</li>
     * <li>1 - edycja sal, przedmiotow</li>
     * <li>3 - zapisane plany zajec, zastepstwa</li>
     * </ul>
     *
     * @return string 
     */
    public static function getSysLv() {
        $isf = new Kohana_Isf();
        $isf->Connect(APP_DBSYS);
        $a = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'edycja_danych\'');
        return $a[0]['wartosc'];
    }

    /**
     * Pobiera wartosc klucza rejestru
     *
     * @param string $key Klucz rejestru
     * @return string 
     */
    public static function getRegistryKey($key) {
        $a = Isf2::Connect()->Select('rejestr')
                        ->Where(array('opcja' => $key))
                        ->Execute()->fetchAll();
        if (count($a) == 0) {
            return 'registry:key not exists';
        } else {
            return $a[0]['wartosc'];
        }
    }

    /**
     * Pobiera symbol n-l na podstawie imienia i nazwiska
     *
     * @param string $teacher Imie i nazwisko nauczyciela
     * @return string 
     */
    public static function getTeacherSym($teacher) {
        $isf = new Kohana_Isf();
        $isf->Connect(APP_DBSYS);
        $res = $isf->DbSelect('nauczyciele', array('skrot'), 'where imie_naz=\'' . $teacher . '\'');
        if (count($res) == 0) {
            $return = 'fetched:none';
        } else {
            $return = $res[0]['skrot'];
        }
        return $return;
    }

    /**
     * Pobiera imie i nazwisko n-l na podstawie symbolu
     *
     * @param string $sym Symbol n-l
     * @return string 
     */
    public static function getTeacherName($sym) {
        $isf = new Kohana_Isf();
        $isf->Connect(APP_DBSYS);
        $res = $isf->DbSelect('nauczyciele', array('imie_naz'), 'where skrot=\'' . $sym . '\'');
        if (count($res) == 0) {
            $return = 'fetched:none';
        } else {
            $return = $res[0]['imie_naz'];
        }
        return $return;
    }

    /**
     * Zapisuje plany zajec do postaci XML
     */
    public static function writeXmlTimetables() {
        $CrTools = new Core_Classes_Managment();
        $CTools = new Core_Tools();

        $klasy = $CrTools->getClasses();
        $dni = array('Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek');
        $lekcje = App_Globals::getRegistryKey('ilosc_godzin_lek');

        foreach ($klasy as $k_rowid => $k_rowcol) {
            $klasa = $k_rowcol['klasa'];
            $xml = new XMLWriter();
            $xml->openMemory();
            $xml->setIndent(4);
            $xml->startDocument('1.0', 'UTF-8');
            $xml->writeComment('Wygenerowano aplikacją Internetowy Plan Lekcji');
            $xml->startElement('timetable');
            $xml->startAttribute('version');
            $xml->text(App_Globals::getRegistryKey('app_ver'));
            $xml->endAttribute();
            $xml->startAttribute('class');
            $xml->text($klasa);
            $xml->endAttribute();
            foreach ($dni as $dzien) {
                $xml->startElement('day');
                $xml->startAttribute('name');
                $xml->text($dzien);
                $xml->endAttribute();
                for ($l = 1; $l <= $lekcje; $l++) {
                    $xml->startElement('lesson');
                    $xml->startAttribute('l');
                    $xml->text($l);
                    $xml->endAttribute();

                    if (($lz = $CTools->getSingleLesson($klasa, $dzien, $l)) != 'fetched:none') {
                        $xml->startAttribute('s');
                        $xml->text($lz['przedmiot']);
                        $xml->endAttribute();
                        $xml->startAttribute('t');
                        $xml->text($lz['skrot']);
                        $xml->endAttribute();
                        $xml->startAttribute('c');
                        $xml->text($lz['sala']);
                        $xml->endAttribute();
                    } else if (($lg = $CTools->getGroupLesson($klasa, $dzien, $l)) != 'fetched:none') {
                        foreach ($lg as $grupa => $zaj) {
                            $xml->startElement('group');
                            $xml->startAttribute('g');
                            $xml->text($grupa);

                            $xml->startAttribute('s');
                            $xml->text($zaj['przedmiot']);
                            $xml->endAttribute();
                            $xml->startAttribute('t');
                            $xml->text($zaj['skrot']);
                            $xml->endAttribute();
                            $xml->startAttribute('c');
                            $xml->text($zaj['sala']);
                            $xml->endAttribute();

                            $xml->endElement();
                        }
                    }

                    $xml->endElement();
                }
                $xml->endElement();
            }
            $xml->endElement();
            $xml->endDocument();
            $fh = fopen(APP_ROOT . DS . 'resources/timetables/' . $klasa . '.xml', 'w');
            fputs($fh, $xml->flush());
            fclose($fh);
        }
    }

}