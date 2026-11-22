<?php
require_once(realpath(dirname(__FILE__)) . '/GestioneCarrieraStudente.php');
require_once(realpath(dirname(__FILE__)) . '/Esame.php');

class CarrieraLaureando
{
    public $nome;
    public $cognome;
    public $email;
    public $matricola;
    public $media;
    public $crediti;
    public $formula;
    public $formulaStringa;
    public $cdl;
    public $curricolari;
    public $esami;  //array

    public function __construct($matricola, $cdl_in)
    {
        $this->matricola = $matricola;
        $gcs = new GestioneCarrieraStudente();
        $anagrafica_json = $gcs->restituisciAnagraficaStudente($matricola);
        $carriera_json = $gcs->restituisciCarrieraStudente($matricola);
        if($anagrafica_json == null || $carriera_json == null)
        {
            echo("Non sono stati trovati i dati relativi alla matricola " . $this->matricola );
            exit;
        }
        $configurazione = file_get_contents(realpath(dirname(dirname(__FILE__))) . '/file_configurazione/formule_cdl.json');
        $configurazione_json = json_decode($configurazione, true);

        $anagrafica = json_decode($anagrafica_json, true);
        $this->nome = $anagrafica["Entries"]["Entry"]["nome"];
        $this->cognome = $anagrafica["Entries"]["Entry"]["cognome"];
        $this->email = $anagrafica["Entries"]["Entry"]["email_ate"];

        $this->formula = $configurazione_json[$cdl_in]["Formula"];
        $this->formulaStringa = $configurazione_json[$cdl_in]["FormulaString"];
        $this->curricolariRichiesti = $configurazione_json[$cdl_in]["CFUCurriculari"];

        $carriera = json_decode($carriera_json, true);
        if ($cdl_in != $carriera["Esami"]["Esame"][0]["CORSO"]) // se il laureando non appartiene al corso di studi selezionato dall'unità didattica
        {
            echo("Attenzione: incongruenza nel corso di studi selezionato per la matricola " . $this->matricola );
            exit;
        }
        else {
            $this->cdl = $cdl_in;
        }
        $this->esami = array();
        // inserisco nel sistema tutti gli esami verbalizzati dal laureando
        for ($i = 0; $i < sizeof($carriera["Esami"]["Esame"]); $i++) {
        //aggiungo il nuovo esame all'array di esami
            $esame = $this->inserisciEsame($carriera["Esami"]["Esame"][$i]["DES"], $carriera["Esami"]["Esame"][$i]["VOTO"], $carriera["Esami"]["Esame"][$i]["PESO"]);
            if ($esame != null && is_string($esame->nomeEsame)) {
                $this->esami[] = $esame; //array_push($this->esami, $esame);
            }
        }
        // una volta aggiunti tutti gli esami calcolo la media
        $this->media = $this->calcolaMedia();
    }

    public function calcolaMedia()
    {
        /*
         * All'interno del calcolo della media inizializzo anche il numero di CFU curriculari che il laureando ha conseguito
         */
        $listaEsami = $this->esami;
        $totaleCFUcurricolari = 0;
        $totaleCFU = 0;
        $sommaVoti = 0;
        for($i=0; $i<sizeof($listaEsami);$i++)  //scorro tutti gli esami
        {
            if($listaEsami[$i]->curricolare == 1)
            {
                if ($listaEsami[$i]->inMedia == 1)
                {
                    $sommaVoti += intval($listaEsami[$i]->cfu * $listaEsami[$i]->votoEsame);
                    $totaleCFU += $listaEsami[$i]->cfu;
                }
                if($listaEsami[$i]->nomeEsame!="PROVA FINALE" && $listaEsami[$i]->nomeEsame!="LIBERA SCELTA PER RICONOSCIMENTI")
                {
                    $totaleCFUcurricolari += intval($listaEsami[$i]->cfu);
                }
            }
        }
        $this->curricolari = $totaleCFUcurricolari;
        $this->crediti = $totaleCFU;
        return round(floatval($sommaVoti/$totaleCFU), 4);
    }

    private function inserisciEsame($nome, $voto, $cfu)
    {
        // non metto esami con parametri malformati
        if ($nome != "TEST DI VALUTAZIONE DI INGEGNERIA" && $nome != null) {
            if ($voto == "30 e lode" || $voto == "30 e lode " || $voto == "30  e lode") // potrebbero esserci spazi extra
            {
                $voto = "33";
            }
            $esame = new Esame($nome, $voto, $cfu);
            $esame->inMedia = $this->inMedia($esame->nomeEsame, $esame->votoEsame);

            //assegno tutti gli esami con parametri NON malformati come esami curriculari
            $esame->curricolare = 1;

            $esame->esameInInformatici();
            return $esame;
        } else {
            return null;
        }
    }

    private function inMedia($nome, $voto)
    {
        if ($nome == "LIBERA SCELTA PER RICONOSCIMENTI" || $nome == "PROVA FINALE" || $nome ==  "TEST DI VALUTAZIONE DI INGEGNERIA" || $nome == "PROVA DI LINGUA INGLESE B2" || $voto == 0)
        {
            $faMedia = 0;
        }
        else
        {
            $faMedia = 1;
        }
        return $faMedia;
    }
}