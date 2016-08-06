<?php

/**
 * ISF Framework 2.0
 * 
 * @author Michal Bocian <mhl.bocian@gmail.com>
 * @license GNU GPL v3
 * @package ISF2
 * @version 2.0
 */

/**
 * Klasa frameworka ISF2
 * 
 * @package ISF2
 */
class Isf2 {

    /**
     *
     * @var string Glowna czesc zapytania
     */
    protected $base_statement;

    /**
     *
     * @var string Dodatkowa czesc zapytania
     */
    protected $optional_statement;

    /**
     *
     * @var string Aktywna tabela
     */
    protected $table;

    /**
     *
     * @var array Kolumny z tabeli
     */
    protected $columns;

    /**
     *
     * @var array Wartosci w zapytaniu SQL
     */
    protected $values_array = array();

    /**
     *
     * @var PDO Uchwyt polaczenia z baza danych
     */
    protected $dbhandle;

    /**
     *
     * @var string Sciezka dostepu do pliku z baza
     */
    protected $isf_path;

    /**
     * Laczy sie z baza PostgreSQL
     * 
     * Dostep tylko dla klas potomnych
     *
     * @see Isf2::Connect
     * @access protected
     * @param array $customvars Parametry polaczenia
     */
    protected function PgSQL_Connect($customvars=null) {

	$my_cfg = array(
	    'host' => dbconfig_host,
	    'database' => dbconfig_dbname,
	    'user' => dbconfig_user,
	    'password' => dbconfig_password,
	);

	if (!class_exists('PDO') || !extension_loaded('pdo_pgsql')) {
	    $_err = 'ISF2: PDO PostgreSQL is not enabled';
	    throw new Exception($_err, 151);
	}

	if (is_array($customvars)) {
	    try {
		$this->dbhandle = new PDO('pgsql:host=' . $customvars['host'] . ';
                    dbname=' . $customvars['database'] . '',
				$customvars['user'],
				$customvars['password']);
	    } catch (Exception $e) {
		throw new Exception($e->getMessage(), 152);
	    }
	} else {
	    try {
		$cstring = 'pgsql:host=' . $my_cfg['host'] . ';
                    dbname=' . $my_cfg['database'] . '';
		$this->dbhandle = new PDO($cstring, $my_cfg['user'], $my_cfg['password']);
	    } catch (Exception $e) {
		throw new Exception('ISF2:' . $e->getMessage() . ' ' . $cstring . ' ', 153);
	    }
	}
    }

    /**
     * Laczy sie z baza danych SQLite
     * 
     * Tylko dla klas potomnych
     *
     * @see Isf2::Connect
     * @access protected
     * @param string $name Nazwa pliku
     */
    protected function SQLite_Connect($name) {

	if ($name == null) {
	    $name = 'default';
	}
	$this->isf_path = RESDIR . DS . $name . '.sqlite';

	if (!class_exists('PDO') || !extension_loaded('pdo_sqlite')) {
	    throw new Exception('ISF2: PDO SQLite is not enabled', 181);
	}

	try {
	    $this->dbhandle = new PDO('sqlite:' . $this->isf_path);
	    $this->dbhandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (Exception $e) {
	    throw new Exception('ISF2: ' . $e->getMessage(), 182);
	}

	if (!file_exists($this->isf_path))
	    throw new Exception('ISF2: ' . $this->isf_path . ' does not exsit', 182);
    }

    /**
     * Konstruktor klasy
     *
     * @param string $system System bazy danych
     * @param mixed $param Parametr polaczenia dla danego typu bazy danych
     */
    public function __construct($system=null, $param=null) {
	if ($system != null) {
	    switch ($system) {
		case 'sqlite':
		    $this->SQLite_Connect($param);
		    break;
		case 'pgsql':
		    $this->PgSQL_Connect($param);
		    break;
		default:
		    throw new Exception('ISF2: Unregistered database system', 100);
		    break;
	    }
	}
    }

    /**
     * Zwraca niepolaczony z baza obiekt
     * 
     * Dla zapytan except
     *
     * @return Isf2 
     */
    public static function Query() {
	return new Isf2();
    }

    /**
     * Laczy sie z baza danych
     * 
     * Domyslnie nie ma potrzeby podania parametru systemu bazy danych
     * (tylko dla aplikacji IPL)
     *
     * @param string $system System bazy danych
     * @param mixed $param Parametr polaczenia dla danego typu bazy danych
     * @return Isf2 Zwraca obiekt klasy ISF2
     */
    public static function Connect($system=global_app_dbsys, $param=null, $drop_file=false) {
	if ($drop_file == true) {
	    fopen(RESDIR . DS . 'default.sqlite', 'w');
	}
	return new Isf2($system, $param);
    }

    /**
     * Wykonuje zapytanie SELECT
     *
     * Domyslnie wymagane jest podanie tylko nazwy tabeli,
     * gdyz automatycznie system pobierze wszystkie kolumny.
     * 
     * <code>
     * <?php
     * Isf2::Connect()->Select('tabela');
     * </code>
     * 
     * Dla pobrania konkretnej liczby kolumn
     * 
     * <code>
     * $cols = array('kolumna1', 'kolumna2');
     * Isf2::Connect()->Select('tabela', $cols);
     * </code>
     * 
     * @param string $table Nazwa tabeli do pobrania
     * @param array $columns Kolumny do pobrania
     * @return Isf2 
     */
    public function Select($table, array $columns=array('*')) {

	if (!isset($table) || !is_string($table) || !is_array($columns)) {
	    throw new Exception('ISF2: Invalid Select statement syntax', 201);
	}

	$this->table = $table;
	$this->columns = $columns;

	$__stmt_columns = '';

	foreach ($columns as $column) {
	    $__stmt_columns .= $column . ', ';
	}

	$__stmt_columns = substr($__stmt_columns, 0, -2);

	$this->base_statement = 'select ' . $__stmt_columns . '  from ' . $this->table . ' ';
	return $this;
    }

    /**
     * Wykonuje zapytanie UPDATE
     *
     * @param string $table Nazwa tabeli
     * @param array $set Tablica ["kolumna"=>"nowa_wartosc"]
     * @return Isf2
     */
    public function Update($table, array $set) {

	if (!isset($table) || !is_string($table) || !is_array($set)) {
	    throw new Exception('ISF2: Invalid Update statement syntax', 202);
	}

	$this->table = $table;
	$this->columns = $set;

	$__stmt_columns = 'set ';

	foreach ($set as $column => $value) {
	    $this->values_array[] = $value;
	    $__stmt_columns .= $column . '=?, ';
	}

	$__stmt_columns = substr($__stmt_columns, 0, -2);

	$this->base_statement = 'update ' . $this->table . ' ' . $__stmt_columns . ' ';
	return $this;
    }

    /**
     * Operacja INSERT
     *
     * @param string $table Nazwa tabeli
     * @param array $colvals ["kolumna"=>"wartosc"]
     * @return Isf2 
     */
    public function Insert($table, array $colvals) {

	if (!isset($table) || !is_string($table) || !is_array($colvals)) {
	    throw new Exception('ISF2: Invalid Insert statement syntax', 250);
	}

	$this->table = $table;
	$this->columns = $colvals;

	$__stmt_values = 'values (';
	$__stmt_columns = '(';

	foreach ($colvals as $column => $value) {
	    $this->values_array[] = $value;
	    $__stmt_values .= '?, ';
	    $__stmt_columns .= $column . ', ';
	}

	$__stmt_values = substr($__stmt_values, 0, -2) . ')';
	$__stmt_columns = substr($__stmt_columns, 0, -2) . ')';

	$this->base_statement = 'insert into ' .
		$this->table . ' ' . $__stmt_columns . ' ' .
		$__stmt_values;

	return $this;
    }

    /**
     * Operacja DELETE
     *
     * @param string $table Nazwa tabeli
     * @return Isf2 
     */
    public function Delete($table) {
	if (!isset($table)) {
	    throw new Exception('ISF2: Invalid Delete statement syntax', 251);
	}

	$this->table = $table;

	$this->base_statement = 'delete from ' . $this->table . ' ';

	return $this;
    }

    /**
     * Operacja EXCEPT
     *
     * @param Isf2 $query
     * @return Isf2 
     */
    public function Except(Isf2 $query) {
	$this->optional_statement = ' except ' . $query->BuildQuery();
	if (count($query->values_array) > 0) {
	    foreach($query->values_array as $id => $value){
		$this->values_array[] = $value;
	    }
	}
	return $this;
    }

    /**
     * Tworzy tabele
     *
     * @param string $table_name Nazwa tabeli
     * @param array $columns [kolumna=>typ]
     * @return Isf2 
     */
    public function CreateTable($table_name, $columns) {
	if (empty($table_name) || !is_array($columns)) {
	    throw new Exception('ISF2: Invalid CreateTable syntax', 401);
	}
	$this->base_statement = 'create table if not exists ' . $table_name;
	$this->optional_statement = '(';
	foreach ($columns as $col => $type) {
	    $this->optional_statement .= '"' . $col . '" ' . $type . ', ';
	}
	$this->optional_statement = substr($this->optional_statement, 0, -2) . ')';
	return $this;
    }

    /**
     * Dodaje warunek WHERE kwerendy SQL
     * 
     * <p>Budowa tablicy $condition</p>
     * 
     * <code>
     * <?php
     * // Domyslnym operatorem matematycznym jest ==, a logicznym AND
     * $condition=array('kolumna'=>'wartosc');
     * // Wykorzystanie niestandardowych operacji WHERE
     * $condition=array(
     * 'nazwa_kolunny::operator'=>array('wartosc','operator_logiczny')
     * )
     * </code>
     * 
     *
     * @param array $condition
     * @return Isf2 
     */
    public function Where(array $condition) {
	if (!is_array($condition)) {
	    throw new Exception('ISF2: Invalid Where statement syntax', 203);
	}
	$__stmt_condition = 'where ';
	foreach ($condition as $column => $value) {

	    $__tmp_cols;
	    $__tmp_ecols = explode('::', $column);
	    $__tmp_vals;

	    if (count($__tmp_ecols) == 2) {
		$__tmp_cols = $__tmp_ecols[0] . ' ' . $__tmp_ecols[1] . ' ';
	    } else {
		$__tmp_cols = $column . ' = ';
	    }

	    if (is_array($value)) {
		$__tmp_vals = '? ' . $value[1] . ' ';
		$this->values_array[] = $value[0];
	    } else {
		$__tmp_vals = '? and ';
		$this->values_array[] = $value;
	    }

	    $__stmt_condition .= ' ' . $__tmp_cols . $__tmp_vals;
	}

	$__stmt_condition = explode(' ', $__stmt_condition);
	unset($__stmt_condition[count($__stmt_condition) - 2]);
	$__stmt_condition = implode(' ', $__stmt_condition);

	$this->optional_statement = $__stmt_condition;

	return $this;
    }

    /**
     * Dodaje zapytanie ORDER BY
     * 
     * <p>Przyklad tablicy</p>
     * <code>
     * <?php
     * $condition=array('kolumna'=>'wskaznik_sortowania');
     * </code>
     *
     * @param array $condition
     * @return Isf2 
     */
    public function OrderBy(array $condition) {
	if (!is_array($condition)) {
	    throw new Exception('ISF2: Invalid OrderBy statement syntax', 204);
	}
	$__tmp_statement = ' order by ';
	foreach ($condition as $col => $cond) {
	    $__tmp_statement .= $col . ' ' . $cond . ', ';
	}
	$__tmp_statement = substr($__tmp_statement, 0, -2);
	$this->optional_statement .= $__tmp_statement;

	return $this;
    }

    /**
     * Buduje zapytanie SQL
     *
     * @return string Zapytanie SQL
     */
    protected function BuildQuery() {
	if (is_null($this->base_statement)) {
	    throw new Exception('ISF2: Base statement does not exist', 301);
	}
	return $this->base_statement . $this->optional_statement;
    }

    /**
     * Wykonuje zapytanie SQL
     *
     * @return PDO Zwraca obiekt PDO
     */
    public function Execute() {
	try {
	    $return = $this->dbhandle->prepare($this->BuildQuery());
	    $return->execute($this->values_array);
	    $this->values_array = array();
	    $this->optional_statement = '';
	    return $return;
	} catch (PDOException $e) {
	    throw new Exception($e->getMessage() . '::' . $this->BuildQuery(), 999);
	}
    }

    public function __call($name, $arguments) {
	throw new Exception('ISF2: Method ' . $name . ' does not exist', 302);
    }

    public function __toString() {
	return $this->BuildQuery();
    }

}