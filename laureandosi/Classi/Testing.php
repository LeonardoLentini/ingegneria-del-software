<?php

require_once (dirname(realpath(__FILE__))) . '/CarrieraLaureando.php';
require_once (dirname(realpath(__FILE__))) . '/GestioneCarrieraStudente.php';

class Testing
{
    public function __construct()
    {
        $this->test();
    }

    private function test()
    {

        $string = file_get_contents(realpath(dirname(dirname(__FILE__))) . "./dati_test/dati_test_corretti.json");
        $dati_test = json_decode($string, true);
        $errore = false;


        for ($i = 0; $i < sizeof($dati_test); $i++) {
            $matricola = $dati_test["laureando" . $i]["matricola"];
            $cdl = $dati_test["laureando" . $i]["cdl"];

            if ($cdl != "INGEGNERIA INFORMATICA (IFO-L)") {
                $carriera = new CarrieraLaureando($matricola, $cdl);
            } else {
                $carriera = new LaureandoInformatica($matricola, $cdl);
            }

            // TEST 1: ANAGRAFICA LAUREANDO
            if ($dati_test["laureando" . $i]["nome"] != $carriera->nome) {
                $errore = true;
                echo("ERRORE TEST ANAGRAFICA: \n nome laureando errato\n expected: " . $dati_test["laureando" . $i]["nome"] . "\t ; received: " . $carriera->nome);
            }

            if ($dati_test["laureando" . $i]["cognome"] != $carriera->cognome) {
                $errore = true;
                echo("ERRORE TEST ANAGRAFICA: \n cognome laureando errato\n expected: " . $dati_test["laureando" . $i]["cognome"] . "\t ; received: " . $carriera->cognome);
            }

            if ($dati_test["laureando" . $i]["email_ate"] != $carriera->email) {
                $errore = true;
                echo("ERRORE TEST ANAGRAFICA: \n email di ateneo laureando errata\n expected: " . $dati_test["laureando" . $i]["email_ate"] . "\t ; received: " . $carriera->email);
            }

            // TEST 2: CARRIERA LAUREANDO

            if ($dati_test["laureando" . $i]["media_pesata"] != $carriera->media) {
                $errore = true;
                echo(" - ERRORE TEST CARRIERA: \n media_pesata laureando errato\n expected: " . $dati_test["laureando" . $i]["media_pesata"] . "\t ; received: " . $carriera->media);
            }

            if ($dati_test["laureando" . $i]["crediti_media"] != $carriera->crediti) {
                $errore = true;
                echo(" - ERRORE TEST CARRIERA: \n crediti_media laureando errati\n expected: " . $dati_test["laureando" . $i]["crediti_media"] . "\t ; received: " . $carriera->crediti);
            }

            if ($dati_test["laureando" . $i]["crediti_curriculari_conseguiti"] != $carriera->curricolari) {
                $errore = true;
                echo(" - ERRORE TEST CARRIERA: \n crediti_curriculari_conseguiti laureando errati\n expected: " . $dati_test["laureando" . $i]["crediti_curriculari_conseguiti"] . "\t ; received: " . $carriera->curricolari);
            }

            if(($cdl == "INGEGNERIA INFORMATICA (IFO-L)"))
            {
                if ($dati_test["laureando" . $i]["bonus"] != $carriera->bonus) {
                    $errore = true;
                    echo(" - ERRORE TEST CARRIERA: \n bonus laureando errato\n expected: " . $dati_test["laureando" . $i]["bonus"] . "\t ; received: " . $carriera->bonus);
                }

                if ($dati_test["laureando" . $i]["media_pesata_inf"] != $carriera->mediaInf()) {
                    $errore = true;
                    echo(" - ERRORE TEST CARRIERA: \n media_pesata_inf laureando errata\n expected: " . $dati_test["laureando" . $i]["media_pesata_inf"] . "\t ; received: " . $carriera->mediaInf());
                }
            }
        }
        if(!$errore) echo("TEST TERMINATI");
    }
}