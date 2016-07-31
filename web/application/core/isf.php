<?php

/**
 * Modul ISF do obslugi baz danych dla Kohana 3
 * 
 *
 * @author Michal Bocian <mhl.bocian@gmail.com>
 */
class Kohana_Isf {

    /**
     * Zwraca wersje frameworka
     *
     * @return string
     */
    public function isf_version() {
        return '1.20';
    }

    /** @var string */
    protected $isf_path;

    /** @var object PDO */
    protected $dbhandle;

    /** @var string */
    protected $script;

    /** @var string */
    protected $jqpath;

    /** @var string */
    protected $system;

    /** @var string */
    protected $ie9script;

    /**
     * Zwraca obiekt ISF
     *
     * @return Kohana_Isf 
     */
    public static function factory() {
        return new Kohana_Isf();
    }

    /**
     * Laczy sie z wybrana baza danych
     * 
     * Dla SQLite
     * 
     * <code>
     * <?php
     * Kohana_Isf::factory()->Connect('sqlite', NULL);
     * // NULL oznacza domyslna nazwe pliku default.sqlite
     * </code>
     *
     * Dla PostgreSQL
     * <code>
     * <?php
     * // gdy $params nie jest zdefiniowany, pobiera wartosci z pliku config.php
     * $params['host']='localhost';
     * $params['dbname']='test';
     * $params['user']='user';
     * $params['password]='password';
     * Kohana_Isf::factory()->Connect('sqlite', $params);
     * </code>
     * 
     * @param string $system System DB
     * @param mixed $param Parametry dla PgSQL
     */
    public function Connect($system, $param = null) {
        $this->system = $system;
        switch ($system) {
            case 'sqlite':
                $this->SQLite_Connect($param);
                break;
            case 'pgsql':
                $this->PgSQL_Connect($param);
                break;
            default:
                die('<b>' . $system . '</b> not defined.');
                break;
        }
    }

    /**
     * Laczy sie z baza PostgreSQL
     * 
     * Dostep tylko dla klas potomnych
     *
     * @see Kohana_Isf::Connect
     * @access protected
     * @param array $customvars Parametry polaczenia
     */
    protected function PgSQL_Connect($customvars = null) {

        $my_cfg = array(
            'host' => dbconfig_host,
            'database' => dbconfig_dbname,
            'user' => dbconfig_user,
            'password' => dbconfig_password,
        );

        if (!class_exists('PDO') || !extension_loaded('pdo_pgsql')) {
            $_err = 'Aby korzystac z obslugi PDO PostgreSQL, nalezy wlaczyc jego obsluge w PHP. ';
            die($_err);
        }

        if (is_array($customvars)) {
            try {
                $this->dbhandle = new PDO('pgsql:host=' . $customvars['host'] . ';
                    dbname=' . $customvars['database'] . '', $customvars['user'], $customvars['password']);
            } catch (Exception $e) {
                die($e->getMessage());
            }
        } else {
            try {
                $cstring = 'pgsql:host=' . $my_cfg['host'] . ';
                    dbname=' . $my_cfg['database'] . '';
                $this->dbhandle = new PDO($cstring, $my_cfg['user'], $my_cfg['password']);
            } catch (Exception $e) {
                die('<p>' . $cstring . '</p>' . $e->getMessage());
            }
        }
    }

    /**
     * Laczy sie z baza danych SQLite
     * 
     * Tylko dla klas potomnych
     *
     * @see Kohana_Isf::Connect
     * @access protected
     * @param string $name Nazwa pliku
     */
    protected function SQLite_Connect($name) {

        if ($name == null) {
            $name = 'default';
        }
        $this->isf_path = RESDIR . DS;

        if (!class_exists('PDO') || !extension_loaded('pdo_sqlite')) {
            $_err = '<b>PDO_SQLite</b> nie jest wlaczany.';
            die($_err);
        }

        try {
            $this->dbhandle = new PDO('sqlite:' . $this->isf_path . $name . '.sqlite');
        } catch (Exception $e) {
            die('<h1>Wystapil blad</h1><p>' . $e->getMessage() . '</p><p><b>' . $this->isf_path . '</b></p>');
        }

        if (!file_exists($this->isf_path . $name . '.sqlite'))
            die('<b>' . $this->isf_path . $name . '.sqlite' . '</b> nie istnieje!');
    }

    /**
     * Funkcja pobiera rekordy w bazie danych
     *
     * SQL: wszystkie kolumny: $columns=array('*');
     *
     * Uzycie funkcji:
     * <code>
     * <?php
     * //utworzenie obiektu Db_Connect
     * $db->DbSelect('tabela', array('*');
     * </code>
     *
     * @param string $table Nazwa tabeli
     * @param array $columns Tablica z kolumnami
     * @param string $condition Warunek kwerendy SQL
     * @return array Tablica z rekordami
     */
    public function DbSelect($table, $columns, $condition = null) {
        if (empty($table) || empty($columns)) {
            die('Niepoprawny parametr $table lub $columns');
        }
        $cols = '';
        foreach ($columns as $col) {
            $cols .= $col . ', ';
        }
        $cols = substr($cols, 0, -2);
        $query = 'select ' . $cols . ' from ' . $table;
        if ($condition != null)
            $query .= ' ' . $condition;
        $r = 1;
        $ret = array();
        try {
            $return = $this->dbhandle->prepare($query);
            $return->execute();
            return $return->fetchAll();
        } catch (SQLiteException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Dodaje rekord do bazy danych
     *
     * Domyslnie uzywa funkcji htmlspecialchars, jest mozliwosc wylaczenia
     * jej. 'htmlspecialchars' zamienia symbole, jak np. znaczniki na zwykle
     * kody ASCII, UTF, wiec nie sa interpretowane przez przegladarke.
     * Dla bezpieczenstwa jest wlaczana ta funkcja.
     *
     * Przyklad uzycia:
     * <code>
     * <?php
     * //utworzenie obiektu Db_Connect
     * $Db->DbInsert('table', array(
     * 'kol1'=>'wartosc1',
     * 'kol2'=>'wartosc2',
     * 'kol3'=>'wartosc3',
     * );
     * </code>
     *
     * Aby wylaczyc obsluge <b>htmlspecialchars</b> jako trzeci argument nalezy
     * podac <b>false</b> bez cydzyslowia.
     *
     * @param string $table Nazwa tabeli
     * @param array $col_val Tablica w postaci kolumna=>wartosc
     * @param boolean $specialchars Czy uzyc funckji <b>htmlspecialchars</b>
     * @return boolean
     */
    public function DbInsert($table, $col_val, $specialchars = true) {
        if (!is_array($col_val) || empty($table))
            die('Nieprawidlowy argument dla funkcji insert');
        $query = 'insert into ' . $table . ' (';
        $valuesArray = array();
        foreach ($col_val as $col => $val) {
            $query .= '' . $col . ', ';
        }
        $query = substr($query, 0, -2);
        $query .= ') values (';
        foreach ($col_val as $col => $val) {
            if ($specialchars == true)
                $val = htmlspecialchars($val);
            $valuesArray[] = $val;
            $query .= '?, ';
        }
        $query = substr($query, 0, -2);
        $query .= ')';
        try {
            $this->dbhandle->prepare($query)->execute($valuesArray);
        } catch (SQLiteException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Wykonuje zapytanie UPDATE jezyka SQL
     *
     * Uaktualnia rekord o warunku $cond
     *
     * Przyklad uzycia:
     * <code>
     * //utworzenie obiektu Db_Connect
     * $Db->DbUpdate('tabela', array(
     *   'kolumna'=>'wartosc',
     *  ), 'id=33');
     * </code>
     *
     * @param string $table Nazwa tabeli
     * @param array $colvals Tablica kolumna=>wartosc do zmiany
     * @param string $cond Warunek <b>where</b>
     * @param string $usehtmlsc Czy zapisywac tagi HTML jako tekst (true)
     * @return bool Sprawdza poprawnosc zapytania
     */
    public function DbUpdate($table, $colvals, $cond, $usehtmlsc = true) {
        if (empty($table) || !is_array($colvals) || empty($cond))
            die('Sprawdz parametry funkcji <b>update</b>!');
        $query = 'update ' . $table . ' set ';
        $valuesArray = array();
        foreach ($colvals as $col => $val) {
            if ($usehtmlsc == true)
                $val = htmlspecialchars($val);
            $valuesArray[] = $val;
            $query .= $col . '=?, ';
        }
        $query = substr($query, 0, -2);
        $query .= ' where ' . $cond;

        try {
            $res = $this->dbhandle->prepare($query);
            $res->execute($valuesArray);
        } catch (SQLiteException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Usuwa rekord o danym warunku
     *
     * Przyklad uzycia:
     * <code>
     * //utworzenie obiektu Db_Connect
     * $Db->DbDelete('tabela', 'kolumna=wartosc');
     * </code>
     *
     * @param string $table Nazwa tabeli
     * @param string $cond Warunek <b>where</b>
     * @return bool Sprawdza poprawnosc kwerendy
     */
    public function DbDelete($table, $cond) {
        if (empty($table) || empty($cond))
            die('Sprawdz parametry funkcji <b>delete</b>');
        $query = 'delete from ' . $table . ' where ' . $cond;
        try {
            $this->dbhandle->exec($query);
        } catch (SQLiteException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Tworzy tabele
     *
     * Nazwa $name, tablica $columns (kolumna=>typ)
     *
     * Przyklad uzycia:
     * <code>
     * //utworzenie obiektu Db_Connect
     * $cols=array(
     *  'kolumna1'=>'typ',
     *  'kolumna2'=>'typ',
     *  'kolumna3'=>'typ',
     * );
     * $Db->DbTblCreate('nazwa', $cols);
     * </code>
     *
     * @param string $name Nazwa tabeli
     * @param array $columns Tablica kolumn i typow danych
     * @return bool Poprawnosc kwerendy SQL
     */
    public function DbTblCreate($name, $columns) {
        if (empty($name) || !is_array(($columns)))
            die('Sprawdz parametr funkcji tbl_create');
        $query = 'create table ' . $name . '(';
        foreach ($columns as $col => $type) {
            $query .= '"' . $col . '" ' . $type . ', ';
        }
        $query = substr($query, 0, -2);
        $query .= ')';
        try {
            $this->dbhandle->exec($query);
        } catch (SQLiteException $e) {
            echo $e->getMessage();
        }
    }

    public function detect_ie() {
        if (isset($_SERVER['HTTP_USER_AGENT']) &&
                (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
            return true;
        else
            return false;
    }

    /**
     * Tworzy nowy obiekt JQueryUI
     * 
     * Gdy parametr $style jest pusty, ladowany jest domyslny szablon
     * zdefiniowany w pliku defines.php, stala JQUI_DEF_THEME
     * 
     * Przykladowe uzycie:
     * <code>
     * use isf\jquery\Ui;
     * $jqui = new Ui(); // mozna dodac parametr $style
     * </code>
     *
     * @param string $style Nazwa stylu w katalogu <b>/templates/css</b>
     */
    public function JQUi($style = 'smoothness') {
        $respath = URL::base(true) . 'lib/jquery';
        $path = array(
            1 => $respath . '/css/' . $style . '/style.css',
            2 => $respath . '/js/jquery-ui.js',
            3 => $respath . '/js/jquery.js',
            4 => URL::base(true) . 'lib/jquery-ui-timepicker-addon.js',
        );
        $this->jqpath = URL::base() . 'lib/jquery';
        $this->script = '
            <link type="text/css" href="' . $path[1] . '" rel="stylesheet" />
            <script type="text/javascript" src="' . $path[3] . '"></script>
            <script type="text/javascript" src="' . $path[2] . '"></script>
            <script type="text/javascript" src="' . $path[4] . '"></script>   
            <script type="text/javascript">
            $(function(){
            ';
    }

    /**
     * Tworzy unikalny identyfikator obiektu na podstawie nazwy
     *
     * @param string $name Nazwa obiektu
     * @return text Unikalny identyfikator obiektu
     */
    private function hashname($name) {
        $name = $name . 'isf';
        $name = $name . md5($name);
        $name = substr($name, 0, -15);
        return $name;
    }

    /**
     * Mozliwosc dodania wlasnej funkcji JavaScript
     * 
     * <b>UWAGA!</b> Gdy drugi parametr $ui_script (domyslnie true),
     * bedzie mial wartosc domyslna, wowczas skrypt dodany zostanie
     * do glownego skryptu JQuery UI, wykonywany podczas zaladowania strony.
     * Aby tego uniknac i wstawic skrypt do dowolnego miejsca w kodzie,
     * parametr $ui_script, powinien miec wartosc false
     * 
     * Przyklad uzycia:
     * <code>
     * //tworzenie obiektu Ui w zmiennej $Ui
     * $Ui->customfunc(' alert("test"); '); //nalezy dodawac srednik na koncu operacji
     * </code>
     *
     * @param string $function Funkcja w JavaScript
     * @param bool $ui_script Czy umiescic kod w skrypcie JQuery UI
     * @return text Zwraca kod, gdy $ui_script jest false
     */
    public function JQUi_CustomFunction($function, $ui_script = true) {
        if ($ui_script == true) {
            $this->script .= '' . $function . '';
        } else {
            return $function;
        }
    }

    /**
     * Zamyka okno dialogowe
     * 
     * Gdy parametr $name nie jest ustawiony, domysla wartosc
     * <b>this</b>, wskazuje na aktualnie otwarte okienko.
     * 
     * <b>UWAGA!</b> Tej funkcji nalezy uzywac w stosunku do innej funkcji
     * wskazujacej, np. na zdarzenie obslugujace hiperlacze (patrz: funkcja
     * <b>anchor_action</b>, w klasie isf\jquery\Ui)
     * 
     * Przyklad uzycia:
     * <code>
     * //tworzenie obiektu Ui w zmiennej $Ui
     * $Ui->dialog_close(); // zamyka otwarte okienko
     * $Ui->dialog_close('okienko'); // zamyka okienko o nazwie 'okienko' (przyklad)
     * </code>
     *
     * @param string $name Domyslnie 'this'
     * @return text Zwraca skrypt
     */
    public function JQUi_DialogClose($name = 'this') {
        if ($name != 'this') {
            return '$("#isf_dialog_' . $this->hashname($name) . '").dialog("close");';
        } else {
            return '$(this).dialog("close");';
        }
    }

    /**
     * Otwiera okienko dialogowe
     * 
     * Gdy parametr $name nie jest okreslony, wowczas otwiera
     * okiengo, ktore jest okreslone w skrypcie (?)
     * 
     * <b>UWAGA!</b> Tej funkcji nalezy uzywac w stosunku do innej funkcji
     * wskazujacej, np. na zdarzenie obslugujace hiperlacze (patrz: funkcja
     * <b>anchor_action</b>, w klasie isf\jquery\Ui)
     * 
     * Przyklad uzycia:
     * <code>
     * //tworzenie obiektu Ui w zmiennej $Ui
     * $Ui->dialog_open('test'); // otwiera okienko o nazwie <b>test</b> (przyklad)
     * </code>
     *
     * @param string $name
     * @return text Zwraca skrypt
     */
    public function JQUi_DialogOpen($name = 'this') {
        if ($name != 'this') {
            return '$("#isf_dialog_' . $this->hashname($name) . '").dialog("open");';
        } else {
            return '$(this).dialog("open");';
        }
    }

    /**
     * Tworzy okienko dialogowe
     * 
     * <b>UWAGA!</b> Oprocz utworzenia stosownego kodu JQuery UI, zwraca rowniez
     * kod HTML, ktory nalezy np. z uzyciem systemu szablonow, dopisac
     * do okreslonej zmiennej.
     * 
     * Przy parametrach $autoopen oraz $modal, ktorych wartoscia jest
     * <b>ciag znakow</b> true lub false, nie mozna uzywac ich, jako
     * zmiennej typu <b>boolean</b>, gdyz jest to zmienna typu <b>string</b>
     * 
     * Przyklad uzycia z systemem szablonow:
     * <code>
     * //tworzenie obiektu Ui w zmiennej $Ui
     * //zalecane uzycie osobnej tablicy
     * $wind['title']='tytul okienka';
     * $wind['content']='tresc okienka';
     * $wind_b=array(
     *  'Zamknij'=>$Ui->dialog_close(), //uzywa funkcji <b>dialog_close</b>
     * ); // tablica przyciskow
     * $wind_html = $Ui->dialog('wind', $wind, $wind_b);
     * $tpl->assign('wind', $wind_html); // przypisuje do zmiennej kod HTML 
     * </code>
     *
     * @param string $name Nazwa okienka dialogowego
     * @param array $content Tablica zawartosci (title, content)
     * @param array $buttons Tablica przyciskow (nazwa, akcja)
     * @param string $autoopen Autootwieranie
     * @param string $modal Przyciemnianie strony
     * @param integer $height Wysokosc okienka
     * @return string Zwraca kod HTML
     */
    public function JQUi_DialogCreate($name, $content, $buttons, $autoopen = false, $modal = false, $height = null) {
        $name = 'isf_dialog_' . $this->hashname($name);

        if ($autoopen == false)
            $autoopen = 'false';
        else
            $autoopen = 'true';

        if ($modal == false)
            $modal = 'false';
        else
            $modal = 'true';

        $script = '$(\'#' . $name . '\').dialog({
            autoOpen: ' . $autoopen . ',
            width: 500,';
        if ($height != null) {
            $script .= '
        	height: ' . $height . ',
        	';
        }
        $script .= '
            show: "fade",
            hide: "fade",
            modal: ' . $modal . ',
            buttons: {';
        foreach ($buttons as $aname => $action) {
            $script .= '
                "' . $aname . '": function(){
                ' . $action . '
                },';
        }
        $script = substr($script, 0, -1);
        $script .= '
            }
            });';
        $this->script .= $script;
        $return = '<div id="' . $name . '" title="' . $content['title'] . '">' . $content['content'] . '</div>';
        return $return;
    }

    /**
     * Tworzy kod obslugi zdarzen dla hiperlacza o danym <b>id</b>
     * 
     * Przyklad uzycia:
     * <code>
     * //tworzenie obiektu Ui w zmiennej $Ui
     * $Ui->anchor_action('okienko_a', $Ui->dialog_open('wind'));
     * //Dla hiperlacza o id='okienko_a', zdarzeniem jest otwarcie okienka wind
     * </code>
     *
     * @param type $name
     * @param type $action 
     */
    public function JQUi_AnchorAction($name, $action) {
        $this->script .= '
            $("#' . $name . '").click(function(){
                ' . $action . '
            });';
    }

    /**
     * Tworzy przycisk z elementu o danym id
     *
     * @param string $name Nazwa elementu HTML
     */
    public function JQUi_ButtonCreate($name) {
        $this->script .= '
            $("#' . $name . '").button();';
    }

    /**
     * Tworzy widget <b>tabs</b>
     * 
     * Funkcja zwraca kod HTML, dlatego nalezy uzywac jej, np.
     * z systemem szablonow, aby kod przypisac do zmiennej w szablonie.
     * 
     * Tablica $content (title, content)
     * <code>
     * //zalecane zdefiniowanie tablicy
     * $tabs['nazwa_zakladki']=array(
     *  'title'=>'tytul',
     *  'content'=>'tresc zakladki'
     * );
     * </code>
     * 
     * Przykladowe uzycie:
     * <code>
     * //tworzenie obiektu Ui w zmiennej $Ui
     * //utworzenie tablicy $content opisanej wyzej
     * //zdefiniowanie obiektu szablonu $tpl
     * $tabs=$Ui->tabs_create('nazwa_elementu', $tablica_zawartosci);
     * $tpl->assign('nazwa_zmiennej', $tabs);
     * </code>
     *
     * @param string $name Nazwa elementu
     * @param array $content Tablica zawartosci
     * @return text Zwraca kod HTML 
     */
    public function JQUi_TabsCreate($name, $content) {
        $name = $this->hashname($name);
        $this->script .= '
            $("#isf_tabs_' . $name . '").tabs();';
        $code = '<div id="isf_tabs_' . $name . '"><ul>';
        foreach ($content as $tabname => $tabcont) {
            $code .= '<li><a href="#isf_tabs_' . $name . '_' . $tabname . '">' . $tabcont['title'] . '</a></li>';
        }
        $code .= '</ul>';
        foreach ($content as $tabname => $tabcont) {
            $code .= '<div id="isf_tabs_' . $name . '_' . $tabname . '">' . $tabcont['content'] . '</div>';
        }
        $code .= '</div>';
        return $code;
    }

    /**
     * Tworzy pasek postepu
     * 
     * Funkcja zwraca kod HTML, dlatego nalezy jej uzyc np. z systemem
     * szablonow
     * 
     * <b>Funkcja nie jest w pelni sprawna</b>
     *
     * @param string $name Nazwa paska postepu
     * @param string $options Opcje funkcji JQuery UI
     */
    public function JQUi_Progressbar($name, $options = null) {
        $name = 'isf_pgbar_' . $this->hashname($name);
        if ($options == null) {
            $this->script .= '
                $("#' . $name . '").progressbar();';
        } else {
            $this->script .= '
                $("#' . $name . '").progressbar({' . $options . '});';
        }
        $return = '<div id="' . $name . '"></div>';
    }

    /**
     * Tworzy widget <b>accordion</b>
     * 
     * Zalecany sposob zdefinowania zmiennej $content
     * <code>
     * $content[]=array(
     *  'title'=>'tytul sekcji',
     *  'content'=>'tresc sekcji',
     * );
     * $content[]=array(
     *  'title'=>'tytul sekcji2',
     *  'content'=>'tresc sekcji2',
     * );
     * </code>
     * 
     * Przyklad uzycia z systemem szablonow:
     * <code>
     * //tworzenie obiektu Ui w zmiennej $Ui
     * //tworzenie obiektu Template
     * $tpl->assing('zmienna', $Ui->accordion_create('nazwa', $content));
     * </code>
     *
     * @param string $name
     * @param array $content
     * @return text Zwraca kod HTML 
     */
    public function JQUi_AccordionCreate($name, $content) {
        $name = $this->hashname($name);
        $this->script .= '
            $("#isf_accor_' . $name . '").accordion({autoHeight:false});';
        $return = '<div id="isf_accor_' . $name . '">';
        foreach ($content as $number => $colval) {
            $return .= '<h3><a href="#">' . $colval['title'] . '</a></h3>
                <div>' . $colval['content'] . '</div>';
        }
        $return .= '</div>';
        return $return;
    }

    /**
     * Tworzy element HTML, specjalnie przygotowany do obslugi
     * zapytan AJAX (domyslnie z paskiem postepu, opcjonalnie z przyciskiem
     * ukrycia elementu).
     *
     * Funkcja generuje gotowy kod HTML, dlatego nalezy uzyc jej w kontekscie
     * innej funkcji, np. przypisania wartosci zmiennej do szablonu.
     * 
     * Przyklad uzycia z systemem szablonow:
     * <code>
     * //tworzenie obiektu Ui w zmiennej $Ui
     * //tworzenie obiektu Template
     * $tpl->assign('zmienna', ajaxdiv_create('nazwa', true, true));
     * // Tworzy element div o unikalnej nazwie, z paskiem postepu, oraz
     * // opcjonalnym przyciskiem ukrycia elementu
     * </code>
     * 
     * @param string $name Nazwa elementu
     * @param bool $progressgif Wyswietlanie animowanego gif-a
     * @param bool $hiddenbtn Pokazanie przycisku ukrycia elementu
     * @return text Zwraca kod HTML
     */
    public function JQUi_AjaxdivCreate($name, $progressgif = true, $hiddenbtn = false, $customtext = false, $customload = '') {
        $name = $this->hashname($name);
        $script = '<div id="isf_adiv_' . $name . '" style="display: none;">';
        if ($progressgif == true) {
            $script .= '<div id="isf_adc_' . $name . '">';
            $script .= '<img src="' . URL::base() . 'lib/jquery/css/load.gif" id="isf_adl_' . $name . '">';
        } else {
            $script .= '<div id="isf_adc_' . $name . '">';
        }
        if ($customload != '') {
            $script .= ' ' . $customload . ' ';
        }
        if ($customtext != false) {
            $script .= '<p>' . $customtext . '</p>';
        }
        $script .= '</div>';
        if ($hiddenbtn == true) {
            $script .= '<p><a href="#" id="isf_ada_' . $name . '">Ukryj panel</a></p>';
            $this->button_create('isf_ada_' . $name);
            $this->anchor_action('isf_ada_' . $name, '$("#isf_adiv_' . $name . '").hide("slow");');
        }
        $script .= '</div>';
        return $script;
    }

    /**
     * Wykonuje zapytanie AJAX dla elementu <b>ajaxdiv</b>
     * 
     * Funkcja generuje czysty kod JQuery UI, dlatego nalezy uzyc jej w
     * konteksie innej funkcji, np. obslugi zdarzenia dla hiprelacza.
     *
     * @param string $divname Nazwa elementu <b>ajaxdiv</b>
     * @param string $url Adres URL strony z zapytaniem
     * @return text Zwraca kod JQuery
     */
    public function JQUi_AjaxdivDoAjax($divname, $url, $to_mscript = false) {
        $divname = $this->hashname($divname);
        $script = '
            $("#isf_adiv_' . $divname . '").show("slow", function(){
                $.ajax({
                    url: \'' . $url . '\',
                    success: function(data){
                        $("#isf_adl_' . $divname . '").fadeOut("slow");
                        $("#isf_adc_' . $divname . '").html(data);
                    }
                });
            });';
        if ($to_mscript == false)
            return $script;
        else
            $this->script .= $script;
    }

    /**
     * Generuje niestandardowy kod zapytania AJAX
     * 
     * Funkcje nalezy uzyc w kontekscie innej funkcji, np. do obslugi
     * zdarzen hiperlacza (<b>zobacz</b>: anchor_action)
     *
     * @param string $url Adres URL strony z zapytaniem
     * @param string $success Funkcja JS, gdy zapytanie zostanie wykonane
     * @return text Zwraca kod JQuery zapytania AJAX
     */
    public function JQUi_DoAjax($url, $success) {
        $script = '
            $.ajax({
                url: \'' . $url . '\',
                success: function(data){
                    ' . $success . '
                }
            });';
        return $script;
    }

    /**
     * Generuje kod JQuery UI
     * 
     * Funkcji nalezy uzywac, np. z systemem szablonow, aby przypisac
     * jej wartosc do zmiennej w <b>head</b>
     * 
     * Przyklad uzycia:
     * <code>
     * //tworzenie obiektu Ui w zmiennej $Ui
     * //tworzenie obiektu Template
     * $tpl->assign('nazwa_zmiennej_head', $Ui->make_script();
     * </code>
     *
     * @return text Zwraca gotowy kod JQuery UI
     */
    public function JQUi_MakeScript() {
        $this->script .= '
            });
            </script>';
        return $this->script;
    }

    /**
     * Znaczniki umozliwiajace podpiecie aplikacji do paska zadan Windows 7
     * 
     * Tylko Internet Explorer 9
     * 
     * Przyklad uzycia:
     * <code>
     * //tworzenie obiektu np. $tools = new \isf\utils\WebResources();
     * $tools->IE9_WebAPP('Moja strona', 'Otwórz moją stronę');
     * </code>
     *
     * @param string $app_name Nazwa aplikacji
     * @param string $tooltip Opis strony
     * @param string $s_url Adres aplikacji, domyslnie /
     * @param array $win Wymiary okna (szer, wys)
     */
    public function IE9_WebAPP($app_name, $tooltip, $s_url = '/', $win = array(800, 600)) {
        $this->ie9script .= '
            <meta name="application-name" content="' . $app_name . '"/>';
        $this->ie9script .= '
            <meta name="msapplication-tooltip" content="' . $tooltip . '"/>';
        $this->ie9script .= '
            <meta name="msapplication-starturl" content="' . $s_url . '"/>';
        $this->ie9script .= '
            <meta name="msapplication-window" content="width=' . $win[0] . ';height=' . $win[1] . '"/>';
    }

    /**
     * Ustawia ikone aplikacji
     * 
     * Domyslnie [adres_http_aplikacji]/favicon.ico
     * 
     * Przyklad uzycia:
     * <code>
     * //tworzenie obiektu np. $tools = new \isf\utils\WebResources();
     * $tools->favicon_set(); //ustawia domyslna sciezke
     * </code>
     *
     * @param string $path Adres ikony
     */
    public function IE9_faviconset($path = null) {
        if ($path == null)
            $path = URL::base() . 'favicon.ico';
        $this->ie9script .= '
            <link rel="shortcut icon" href="' . $path . '" />';
    }

    /**
     * Tworzy zadanie dla paska zadan Windows 7
     * 
     * Tylko Internet Explorer 9
     * 
     * Przyklad uzycia:
     * <code>
     * //tworzenie obiektu np. $tools = new \isf\utils\WebResources();
     * //odwolanie do metody IE9_WebAPP
     * $tools->IE9_apptask('Moje zadanie', '/strona.php');
     * </code>
     *
     * @param string $name Nazwa zadania
     * @param string $uri Adres pliku. Np. /index.php
     * @param string $icon Adres ikony, domyslnie favicon.ico
     */
    public function IE9_apptask($name, $uri, $icon = null) {
        if ($icon == null)
            $icon = URL::base() . 'favicon.ico';
        $this->ie9script .= '
            <meta name="msapplication-task" content="name=' . $name . ';action-uri=' . $uri . ';icon-uri=' . $icon . '"/>';
    }

    /**
     * Zwraca gotowy skrypt
     * 
     * Przyklad uzycia z <b>systemem szablonow</b>
     * <code>
     * //tworzenie obiektu np. $tools = new \isf\utils\WebResources();
     * //szablon strony np. zmienna $tpl
     * $tpl->assign('zmienna_w_head', $tools->make_script());
     * </code>
     *
     * @return text Zwraca gotowy skrypt
     */
    public function IE9_make() {
        return $this->ie9script;
    }

}
