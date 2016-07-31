<?php
define('_I_SYSVER', '2.0.0'); // wersja instalatora
define('MOBILE_LIB_PATH', '/lib/jquery/mobile/'); // sciezka do JQuery Mobile
define('C', '"'); // znak "
?>
<!DOCTYPE html>
<html>
    <head>
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" href="<?php echo MOBILE_LIB_PATH; ?>jquery.mobile-1.0.1.min.css" />
	<script src="<?php echo MOBILE_LIB_PATH; ?>jquery-1.6.4.min.js"></script>
	<script src="<?php echo MOBILE_LIB_PATH; ?>jquery.mobile-1.0.1.min.js"></script>        
	<script>
            $(document).ready(function() {
            });
        </script>
	<title>Instalator pakietu Internetowy Plan Lekcji <?php echo _I_SYSVER; ?></title>
    </head>
    <body>
	<div id="main" data-role="page" data-theme="a" data-content-theme="a">
	    <?php if (!isset($_POST['step2'])): ?>
    	    <div id="title" data-role="header">
    		<h3>Instalacja systemu Internetowy Plan Lekcji - krok 1</h3>
    	    </div>
    	    <div id="ct_step2_1" data-role="content" data-theme="c">
		    <?php
		    $r = $_SERVER['REQUEST_URI'];
		    $r = str_replace('index.php', '', $r);
		    $r = str_replace('install.php', '', $r);
		    $r = str_replace('?err', '', $r);
		    $r = str_replace('?reinstall', '', $r);
		    ?>
    		<h3>Instalator systetmu</h3>

    		<form action="" method="post" name="frmS1" id="frmS1">
    		    <div data-role="fieldcontain">
    			<label for="inpSzkola" id="lblSzkola">Nazwa szkoły</label>
    			<input type="text" name="inpSzkola" id="inpSzkola" value=""/>
    		    </div>
    		    <div data-role="fieldcontain">
    			<label for="inpPath" id="lglPath">Scieżka aplikacji</label>
    			<input type="text" name="inpPath" id="inpPath" value="<?php echo $r; ?>"/>
    		    </div>

    		    <div data-role="collapsible" data-collapsed="false" data-content-theme="e">
    			<h3>Ścieżka aplikacji - informacje</h3>
    			To element ścieżki HTTP, dzięki której można dostać się do aplikacji.
    			Wartość ta jest ustawiana automatycznie oraz nie zaleca się jej modyfikacji.
    		    </div>

    		    <div data-role="fieldcontain">
    			<label for="dbtype" class="select">Typ bazy danych</label>
    			<select name="dbtype" id="dbtype" data-native-menu="false">
    			    <option value="SQLite">SQLite</option>
    			    <option value="PgSQL">PosgreSQL</option>
    			</select>
    		    </div>

    		    <input type="hidden" name="step2" value="true"/><p/>

    		    <div data-role="collapsible" data-collapsed="true">
    			<h3>Dane serwera PostgreSQL - nie dotyczy SQLite</h3>
    			<div data-role="fieldcontain">
    			    <label for="dbHost" id="dbHost">Host</label>
    			    <input type="text" name="dbHost" id="dbHost" value=""/>
    			</div>
    			<div data-role="fieldcontain">
    			    <label for="dbLogin" id="dbLogin">Użytkownik</label>
    			    <input type="text" name="dbLogin" id="dbLogin" value=""/>
    			</div>
    			<div data-role="fieldcontain">
    			    <label for="dbHaslo" id="dbHaslo">Hasło</label>
    			    <input type="text" name="dbHaslo" id="dbHaslo" value=""/>
    			</div>
    			<div data-role="fieldcontain">
    			    <label for="dbBaza" id="dbBaza">Nazwa bazy</label>
    			    <input type="text" name="dbBaza" id="dbBaza" value=""/>
    			</div>
    		    </div>
    		    <button type="submit" name="btnSubmit">Zainstaluj aplikację</button>
    		</form>
		    <?php if (isset($_GET['err'])): ?>
			<span style="font-weight: bold; color:red;">Żadne pole nie może być puste!</span>
			<?php endif; ?>
    	    </div>

	    <?php else: ?>
    	    <div id="title" data-role="header">
    		<h3>Instalacja systemu Internetowy Plan Lekcji - krok 2</h3>
    	    </div>
    	    <div id="ct_step2_1" data-role="content" data-theme="c">
		    <?php
		    if ($_POST['dbtype'] == 'PgSQL') {
			if (empty($_POST['inpSzkola']) || $_POST['inpSzkola'] == ''
				|| empty($_POST['dbLogin']) || empty($_POST['dbHaslo'])
				|| empty($_POST['dbBaza']) || empty($_POST['dbHost'])) {
			    header('Location: install.php?err');
			    exit;
			}

			$szkola = $_POST['inpSzkola'];
			$customvars = array(
			    'host' => $_POST['dbHost'],
			    'user' => $_POST['dbLogin'],
			    'password' => $_POST['dbHaslo'],
			    'database' => $_POST['dbBaza'],
			);
			/**
			 * Tworzy plik konifugracyjny dla PostgreSQL
			 */
			$a = fopen(APP_ROOT . DS . 'resources' . DS . 'config.ini', 'w');

			$file = '[global]' . PHP_EOL;
			$file .= 'app_path = ' . C . $_POST['inpPath'] . C . PHP_EOL;
			$file .= 'app_dbsys = "pgsql"' . PHP_EOL . PHP_EOL;
			$file .= '[dbconfig]' . PHP_EOL;
			$file .= 'host = ' . C . $_POST['dbHost'] . C . PHP_EOL;
			$file .= 'dbname = ' . C . $_POST['dbBaza'] . C . PHP_EOL;
			$file .= 'user = ' . C . $_POST['dbLogin'] . C . PHP_EOL;
			$file .= 'password = ' . C . $_POST['dbHaslo'] . C;

			fputs($a, $file);
			fclose($a);

			Core_Tools::parseCfgFile();

			$App_Install = new Core_Install();
			$App_Install->Connect('pgsql');
			$res = $App_Install->DbInit($_POST['inpSzkola'], _I_SYSVER);
		    } else {
			if (empty($_POST['inpSzkola']) || $_POST['inpSzkola'] == '') {
			    header('Location: install.php?err');
			    exit;
			}
			/**
			 * Tworzy plik konfiguracyjny dla SQLite
			 */
			$a = fopen(APP_ROOT . DS . 'resources' . DS . 'config.ini', 'w');

			$file = '[global]' . PHP_EOL;
			$file .= 'app_path = ' . C . $_POST['inpPath'] . C . PHP_EOL;
			$file .= 'app_dbsys = "sqlite"';

			fputs($a, $file);
			fclose($a);

			Core_Tools::parseCfgFile();

			$App_Install = new Core_Install();
			$App_Install->Connect('sqlite');
			$res = $App_Install->DbInit($_POST['inpSzkola'], _I_SYSVER);
		    }
		    ?>
    		<h3 class="notice">Dziękujemy za instalację IPL <?php echo _I_SYSVER; ?></h3>
    		<h3>Twoje dane administratora</h3>
    		<p><b>Login: </b>root</p>
    		<p><b>Hasło: </b><?php echo $res['pass']; ?></p>
    		<p><b>Token: </b><?php echo $res['token']; ?></p>
    		<p class="info">Zapamiętaj dane do logowania.</p>
		<?php endif; ?>
	    </div>
	    <div id="footer" data-role="footer">
		<h3>&copy;<?php echo date('Y'); ?>, Internetowy Plan Lekcji</h3>
	    </div>
	</div>
    </body>
</html>