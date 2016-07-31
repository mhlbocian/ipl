<img src="<?php echo URL::base(); ?>lib/images/logo.png" alt="IPL"/>
<p>Informacje systemowe</p>
<ul>
    <li><b>Wersja systemu:</b> <?php echo App_Globals::getRegistryKey('app_ver'); ?></li>
    <li><b>System bazy danych:</b> <?php echo APP_DBSYS; ?></li>
    <li><b>Wersja oprogramowania:</b> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></li>
    <li><b>Wersja PHP:</b> <?php echo phpversion(); ?></li>
</ul>
<p>Użyte oprogramowanie</p>
<ul>
    <li><b>jQuery</b> - BSD</li>
    <li><b>Kohana 3</b> - BSD</li>
    <li><b>NuSOAP</b> - GNU GPL</li>
</ul>
<p>Licencja Internetowego Planu Lekcji</p>
<pre>
    <?php
    $opts = array(
	'http' => array(
	    'method' => "GET",
	    'header' => "Accept-language: en\r\n" .
	    "Cookie: foo=bar\r\n"
	)
    );

    $context = stream_context_create($opts);

    echo file_get_contents('http://www.gnu.org/licenses/gpl-3.0.txt', false, $context);
    ?>
</pre>