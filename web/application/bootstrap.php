<?php

/**
 * Bootloader Kohany
 * @package core
 * @license GNU GPL v3
 */
defined('SYSPATH') or die('No direct script access.');
@session_start();
if (!isset($_SESSION['app_theme'])) {
    $_SESSION['app_theme'] = 'domyslny';
}

require SYSPATH . 'classes/kohana/core' . EXT;
require SYSPATH . 'classes/kohana' . EXT;

setlocale(LC_ALL, 'pl_PL.utf-8');
spl_autoload_register(array('Kohana', 'auto_load'));
ini_set('unserialize_callback_func', 'spl_autoload_call');
I18n::lang('pl-pl');
if (isset($_SERVER['KOHANA_ENV'])) {
    Kohana::$environment = constant('Kohana::' . strtoupper($_SERVER['KOHANA_ENV']));
}

Kohana::init(array(
    'base_url' => global_app_path,
));
Kohana::$config->attach(new Config_File);
Route::set('default', '(<controller>(/<action>(/<id>)))')
        ->defaults(array(
            'controller' => 'default',
            'action' => 'index',
        ));
Route::set('sale', '(sale/usun/<sala>(/<usun>))')->defaults(array(
    'controller' => 'sale',
    'action' => 'usun',
        )
);
Route::set('przedmioty_usun', '(przedmioty/usun/<przedmiot>(/<usun>))')->defaults(array(
    'controller' => 'przedmioty',
    'action' => 'usun',
        )
);
Route::set('przedmioty_przypisusun', '(przedmioty/przypisusun/<przedmiot>/<sala>)')->defaults(array(
    'controller' => 'przedmioty',
    'action' => 'przypisusun',
        )
);
Route::set('sale_przedusun', '(sale/przedusun/<sala>/<przedmiot>)')->defaults(array(
    'controller' => 'sale',
    'action' => 'przedusun',
        )
);
Route::set('nl_usun', '(nauczyciele/usun/<nauczyciel>/<confirm>)')->defaults(array(
    'controller' => 'nauczyciele',
    'action' => 'usun',
        )
);
Route::set('nl_klwyp', '(nauczyciele/klwyp/<nauczyciel>/<klasa>)')->defaults(array(
    'controller' => 'nauczyciele',
    'action' => 'klwyp',
        )
);
Route::set('nl_przwyp', '(nauczyciele/przwyp/<nauczyciel>/<przedmiot>)')->defaults(array(
    'controller' => 'nauczyciele',
    'action' => 'przwyp',
        )
);
Route::set('pr_nlwyp', '(przedmioty/wypisz/<przedmiot>/<nauczyciel>)')->defaults(array(
    'controller' => 'przedmioty',
    'action' => 'wypisz',
        )
);
Route::set('plan_grpdel', '(plan/grpdel/<dzien>/<lekcja>/<klasa>/<grupa>)')->defaults(array(
    'controller' => 'plan',
    'action' => 'grpdel',
        )
);
Route::set('grupaajax', '(plan/grupaajax/<klasa>/<alternative>)')->defaults(array(
    'controller' => 'plan',
    'action' => 'grupaajax',
        )
);
Route::set('klasaajax', '(plan/klasaajax/<klasa>/<alternative>)')->defaults(array(
    'controller' => 'plan',
    'action' => 'klasaajax',
        )
);
