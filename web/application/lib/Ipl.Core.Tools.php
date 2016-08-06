<?php

class Core_Tools {

    protected $dbhandle;
    public function __construct() {
        $this->dbhandle = Kohana_Isf::factory();
        $this->dbhandle->Connect(APP_DBSYS);
    }

    public static function ShowError($message, $code = null, $fullPage = false, $return = false) {
        $searchArr = [
            "{{message}}", "{{code}}", "{{httpPath}}"
        ];
        $replaceArr = [
            $message, $code, "/",
        ];
        $errorPage = file_get_contents(APLDIR . DS . "static" . DS . "errorPage.ht");
        $page = str_replace($searchArr, $replaceArr, $errorPage);
        /* Zwroc caly dokument HTML */
        if ($fullPage) {
            header('Content-Type: text/html');
            $header = '<!DOCTYPE html><html><head><meta charset="UTF-8"/></head><body>';
            $footer = '</body></html>';
            echo $header . $page . $footer;
        } else if (!$return) {
            echo $page;
        } else {
            return $page;
        }
    }

    public static function CheckDirPermissions(array $directories) {
        foreach ($directories as $dir) {
            if (!is_writable($dir)) {
                self::ShowError("Katalog {$dir} musi posiadać prawo zapisu");
                exit;
            }
        }
    }

    public static function CheckRequiredModules(array $modules) {
        foreach ($modules as $module) {
            if (!extension_loaded($module)) {
                self::ShowError("Moduł {$module} nie jest zainstalowany");
                exit;
            }
        }
    }

    public static function CheckConfigFile($configFile) {
        $config = parse_ini_file($configFile, true)["global"];
        if (!isset($config["app_path"], $config["app_dbsys"])) {
            self::ShowError("Plik konfiguracyjny jest uszkodzony");
            exit;
        }
    }

    /**
     * Parsuje plik konfiguracyjny
     */
    public static function ParseConfigFile($configFile) {
        $cfg = parse_ini_file($configFile, true);
        foreach ($cfg as $group => $values) {
            foreach ($values as $name => $value) {
                define($group . '_' . $name, $value);
            }
        }
    }

    /**
     * Sprawdza czy system jest zainicjowany
     */
    public static function CheckIsInstalled() {
        self::CheckDirPermissions([
            RESDIR,
            RESDIR . DS . "timetables",
            APLDIR . DS . "logs",
            APLDIR . DS . "cache",
        ]);

        self::CheckRequiredModules([
            'pdo_sqlite', 'pdo_pgsql'
        ]);

        if (!file_exists(RESDIR . DS . 'config.ini')) {
            throw new Exception('IPL: Ready to install', 501);
        }

        self::CheckConfigFile(RESDIR . DS . "config.ini");
        self::ParseConfigFile(RESDIR . DS . "config.ini");
        try {
            Isf2::Connect()->Select('rejestr')
                    ->Where(array('opcja' => 'installed'))->Execute()->fetchAll();
        } catch (Exception $e) {
            self::ShowError("Błąd bazy danych. {$e->getMessage()}");
        }
    }

    /**
     * Wykrywa przegladarke mobilna
     * @deprecated since version 2.0
     * @return boolean 
     */
    public static function is_mobile() {

        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $mobile_agents = Array(
            '240x320', 'acer', 'acoon',
            'acs-', 'abacho', 'ahong',
            'airness', 'alcatel', 'amoi',
            'android', 'anywhereyougo.com', 'applewebkit/525',
            'applewebkit/532', 'asus', 'audio',
            'au-mic', 'avantogo', 'becker',
            'benq', 'bilbo', 'bird',
            'blackberry', 'blazer', 'bleu',
            'cdm-', 'compal', 'coolpad',
            'danger', 'dbtel', 'dopod',
            'elaine', 'eric', 'etouch',
            'fly ', 'fly_', 'fly-',
            'go.web', 'goodaccess', 'gradiente',
            'grundig', 'haier', 'hedy',
            'hitachi', 'htc', 'huawei',
            'hutchison', 'inno', 'ipad',
            'ipaq', 'ipod', 'jbrowser',
            'kddi', 'kgt', 'kwc',
            'lenovo', 'lg ', 'lg2',
            'lg3', 'lg4', 'lg5',
            'lg7', 'lg8', 'lg9',
            'lg-', 'lge-', 'lge9',
            'longcos', 'maemo', 'mercator',
            'meridian', 'micromax', 'midp',
            'mini', 'mitsu', 'mmm',
            'mmp', 'mobi', 'mot-',
            'moto', 'nec-',
            'netfront', 'newgen', 'nexian',
            'nf-browser', 'nintendo', 'nitro',
            'nokia', 'nook', 'novarra',
            'obigo', 'palm', 'panasonic',
            'pantech', 'philips', 'phone',
            'pg-', 'playstation', 'pocket',
            'pt-', 'qc-', 'qtek',
            'rover', 'sagem', 'sama',
            'samu', 'sanyo', 'samsung',
            'sch-', 'scooter', 'sec-',
            'sendo', 'sgh-', 'sharp',
            'siemens', 'sie-', 'softbank',
            'sony', 'spice', 'sprint',
            'spv', 'symbian', 'tablet',
            'talkabout', 'tcl-', 'teleca',
            'telit', 'tianyu', 'tim-',
            'toshiba', 'tsm', 'up.browser',
            'utec', 'utstar', 'verykool',
            'virgin', 'vk-', 'voda',
            'voxtel', 'vx', 'wap',
            'wellco', 'wig browser', 'wii',
            'windows ce', 'wireless', 'xda',
            'xde', 'zte'
        );

        $is_mobile = false;

        foreach ($mobile_agents as $device) {

            if (stristr($user_agent, $device)) {

                $is_mobile = true;

                break;
            }
        }

        return $is_mobile;
    }

    /**
     * Pobiera pojedyncza lekcje
     *
     * @param string $class Klasa
     * @param string $day Dzien tyogdnia
     * @param string $lesson Lekcja
     * @return mixed 
     */
    public function getSingleLesson($class, $day, $lesson) {
        $condition = 'where klasa=\'' . $class . '\' and dzien=\'' . $day . '\' and lekcja=\'' . $lesson . '\'';
        $cols = array(
            'dzien',
            'klasa',
            'lekcja',
            'przedmiot',
            'skrot',
            'sala',
        );
        $result = $this->dbhandle->DbSelect('planlek', $cols, $condition);
        if (count($result) == 0) {
            $return = 'fetched:none';
        } else {
            $return = array(
                'dzien' => $result[0]['dzien'],
                'lekcja' => $result[0]['lekcja'],
                'przedmiot' => $result[0]['przedmiot'],
                'skrot' => $result[0]['skrot'],
                'sala' => $result[0]['sala'],
            );
        }

        return $return;
    }

    /**
     * Pobiera lekcje grupowa
     *
     * @param string $class Klasa
     * @param string $day Dzien tygodnia
     * @param string $lesson Lekcja
     * @return mixed 
     */
    public function getGroupLesson($class, $day, $lesson) {
        $condition = 'where klasa=\'' . $class . '\' and dzien=\'' . $day . '\' and lekcja=\'' . $lesson . '\' order by grupa asc';
        $cols = array(
            'dzien',
            'klasa',
            'grupa',
            'lekcja',
            'przedmiot',
            'skrot',
            'sala',
        );
        $result = $this->dbhandle->DbSelect('plan_grupy', $cols, $condition);
        if (count($result) == 0) {
            $return = 'fetched:none';
        } else {
            foreach ($result as $rowid => $rowcol) {
                $return[$rowcol['grupa']] = array(
                    'dzien' => $rowcol['dzien'],
                    'lekcja' => $rowcol['lekcja'],
                    'przedmiot' => $rowcol['przedmiot'],
                    'skrot' => $rowcol['skrot'],
                    'sala' => $rowcol['sala'],
                );
            }
        }

        return $return;
    }

}

class Core_Classes_Managment {

    public $dbhandle;

    public function __construct() {
        $this->dbhandle = Isf2::Connect();
    }

    /**
     * Pobiera klasy
     *
     * @return array
     */
    public function getClasses() {
        $result = $this->dbhandle->Select('klasy')->OrderBy(array('klasa' => 'asc'))
                        ->Execute()->FetchAll();
        return $result;
    }

    public function delClass($class) {
        $this->dbhandle->Delete('klasy')->Where(array('klasa' => $class))->Execute();
    }

}

class Core_Teacher_Managment {

    public $dbhandle;

    public function __construct() {
        $this->dbhandle = Isf2::Connect();
    }

    public function getTeachers() {
        return $this->dbhandle->Select('nauczyciele', array('*'))
                        ->OrderBy(array('skrot' => 'asc'))
                        ->Execute()->fetchAll();
    }

}