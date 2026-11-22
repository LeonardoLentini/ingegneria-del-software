<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once(realpath(dirname(__FILE__)) . '/../lib/PHPMailer/src/PHPMailer.php');
require_once(realpath(dirname(__FILE__)) . '/../lib/PHPMailer/src/Exception.php');
require_once(realpath(dirname(__FILE__)) . '/../lib/PHPMailer/src/SMTP.php');
require_once (realpath(dirname(__FILE__)) . '/GestioneCarrieraStudente.php');

class GeneratoreEmail
{

    private $matricola; //array
    private $email;     //array
    private $messaggio; //array
    private $CdL;

    public function __construct($matricole_array, $CdL)
    {
        $this->CdL = $CdL;
        for($i=0; $i < sizeof($matricole_array); $i++)
        {
            $this->matricola[$i] = $matricole_array[$i];
            $gcs = new GestioneCarrieraStudente();
            //prelevo l'anagrafica dello studente per risalire alla sua email
            $anagrafica = $gcs->restituisciAnagraficaStudente($this->matricola[$i]);
            $anagrafica = json_decode($anagrafica, true);
            $this->email[$i] = $anagrafica["Entries"]["Entry"]["email_ate"];

            $this->messaggio[$i] = new PHPMailer();
            $this->generaEmail($i);
            $this->inviaEmail($i);
        }
        echo("Tutte le email con i prospetti sono state inviate correttamente");
    }

    private function generaEmail($i)
    {
        $this->messaggio[$i]->IsSMTP();
        $this->messaggio[$i]->Host = "mixer.unipi.it";
        $this->messaggio[$i]->SMTPSecure = "tls";
        $this->messaggio[$i]->SMTPAuth = false;
        $this->messaggio[$i]->Port = 25;
        $this->messaggio[$i]->From='no-reply-laureandosi@ing.unipi.it';
        $this->messaggio[$i]->AddAddress($this->email[$i]);


        $this->messaggio[$i]->Subject=': Appello di laurea in ' . $this->CdL . ' - indicatori per voto di laurea';
        $testoDelMessaggio = "Gentile laureando/laureanda, 
        \nAllego un prospetto contenente: la sua carriera, gli indicatori e la formula che la commissione adoperera' per determinare il voto di laurea.
        \nLa prego di prendere visione dei dati relativi agli esami.
        \nIn caso di dubbi scrivere a: segreteria.didattica@example.com
        \nAlcune spiegazioni:
        \n- gli esami che non hanno un voto in trentesimi, hanno voto nominale zero al posto di giudizio o idoneita', in quanto non contribuiscono al calcolo della media ma solo al numero di crediti curriculari;
        \n- gli esami che non fanno media (pur contribuendo ai crediti curriculari) non hanno la spunta nella colonna MED;
        \n- il voto di tesi (T) appare nominalmente a zero in quanto verra' determinato in sede di laurea, e va da 18 a 30.
        \nCordiali saluti
        \nUnita' Didattica DII";
        $this->messaggio[$i]->Body=stripslashes($testoDelMessaggio);
        $path_prospetto = realpath(dirname(__FILE__)) . '/../Prospetti/' . $this->matricola[$i] . '-prospetto.pdf';
        if(!$this->messaggio[$i]->addAttachment($path_prospetto))
        {
            echo("Non è presente alcun prospetto per la matricola " . $this->matricola[$i] );
            exit;
        }
        //else: file allegato con successo
    }

    private function inviaEmail($i)
    {
        if(!$this->messaggio[$i]->Send())
        {
            echo("Errore invio email per la matricola " . $this->matricola[$i]);
            exit ;
        }
        else
        {
            echo("Email inviata correttamente.\n");
        }
    }
}