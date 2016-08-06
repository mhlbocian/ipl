<?php

class Core_Install {

    public $Isf;
    public $type;

    public function Connect($type) {
        $this->type = $type;
        switch ($type) {
            case 'sqlite':
                $this->Isf = Isf2::Connect('sqlite', null, true);
                return 'db:cpass';
                break;
            case 'pgsql':
                $this->Isf = Isf2::Connect('pgsql');
                return 'db:cpass';
                break;
            default:
                return 'db:cfailed';
                break;
        }
    }

    public function DbInit($szkola, $ver) {

        try {

            $this->Isf->CreateTable('przedmioty', array(
                'przedmiot' => 'text not null'
            ))->Execute();

            $this->Isf->CreateTable('sale', array(
                'sala' => 'text not null'
            ))->Execute();

            $this->Isf->CreateTable('przedmiot_sale', array(
                'przedmiot' => 'text not null',
                'sala' => 'text not null'
            ))->Execute();

            $this->Isf->CreateTable('klasy', array(
                'klasa' => 'text not null'
            ))->Execute();

            $this->Isf->CreateTable('nauczyciele', array(
                'imie_naz' => 'text not null',
                'skrot' => 'text not null'
            ))->Execute();

            $this->Isf->CreateTable('nl_przedm', array(
                'nauczyciel' => 'text not null',
                'przedmiot' => 'text not null'
            ))->Execute();

            $this->Isf->CreateTable('nl_klasy', array(
                'nauczyciel' => 'text not null',
                'klasa' => 'text not null'
            ))->Execute();

            $this->Isf->CreateTable('rejestr', array(
                'opcja' => 'text not null',
                'wartosc' => 'text'
            ))->Execute();

            $this->Isf->CreateTable('planlek', array(
                'dzien' => 'text',
                'klasa' => 'text',
                'lekcja' => 'text',
                'przedmiot' => 'text',
                'sala' => 'text',
                'nauczyciel' => 'text',
                'skrot' => 'text'
            ))->Execute();

            $this->Isf->CreateTable('uzytkownicy', array(
                'uid' => 'numeric not null',
                'login' => 'text not null',
                'haslo' => 'text not null',
                'webapi_token' => 'text',
                'webapi_timestamp' => 'text',
                'ilosc_prob' => 'numeric'
            ))->Execute();

            $this->Isf->CreateTable('log', array(
                'id' => 'numeric not null',
                'data' => 'text not null',
                'modul' => 'text not null',
                'wiadomosc' => 'text',
            ))->Execute();

            $this->Isf->CreateTable('tokeny', array(
                'login' => 'text',
                'token' => 'text',
            ))->Execute();

            $this->Isf->CreateTable('plan_grupy', array(
                'dzien' => 'text',
                'lekcja' => 'text',
                'klasa' => 'text',
                'grupa' => 'text',
                'przedmiot' => 'text',
                'nauczyciel' => 'text',
                'skrot' => 'text',
                'sala' => 'text'
            ))->Execute();

            if ($this->type == 'sqlite') {
                $this->Isf->CreateTable('zast_id', array(
                    'zast_id' => 'integer primary key autoincrement',
                    'dzien' => 'text',
                    'za_nl' => 'text',
                    'info' => 'text',
                ))->Execute();
            } else {
                $this->Isf->CreateTable('zast_id', array(
                    'zast_id' => 'serial',
                    'dzien' => 'text',
                    'za_nl' => 'text',
                    'info' => 'text',
                ))->Execute();
            }

            $this->Isf->CreateTable('zastepstwa', array(
                'zast_id' => 'text',
                'lekcja' => 'text',
                'przedmiot' => 'text',
                'nauczyciel' => 'text',
                'sala' => 'text',
            ))->Execute();

            $this->Isf->CreateTable('lek_godziny', array(
                'lekcja' => 'text',
                'godzina' => 'text',
                'dl_prz' => 'text'
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'edycja_danych',
                'wartosc' => '1'
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'ilosc_godzin_lek',
                'wartosc' => '1'
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'dlugosc_lekcji',
                'wartosc' => '45'
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'nazwa_szkoly',
                'wartosc' => $szkola
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'index_text',
                'wartosc' => file_get_contents(APLDIR . DS . "static" . DS . "welcome.ht")
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'ilosc_grup',
                'wartosc' => '0'
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'godz_rozp_zaj',
                'wartosc' => '08:00'
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'installed',
                'wartosc' => '1'
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'app_ver',
                'wartosc' => $ver
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'randtoken_version',
                'wartosc' => $ver
            ))->Execute();

            $pass = substr(md5(@date('Y:m:d')), 0, 8);
            $pass = rand(1, 100) . $pass;

            $this->Isf->Insert('uzytkownicy', array(
                'uid' => 1,
                'login' => 'root',
                'haslo' => md5('plan' . sha1('lekcji' . $pass)),
            ))->Execute();

            $token = substr(md5(time() . 'plan'), 0, 6);

            $this->Isf->Insert('tokeny', array('login' => 'root', 'token' => md5('plan' . $token)))->Execute();

            return array('pass' => $pass, 'token' => $token);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

}
