<?php
require_once (dirname(realpath(__FILE__))) . '/ProspettoPDFLaureando.php';
require_once (dirname(realpath(__FILE__))) . '/ProspettoPDFCommissione.php';
require_once (dirname(realpath(__FILE__))) . '/GeneratoreEmail.php';
require_once (dirname(realpath(__FILE__))) . '/CarrieraLaureando.php';

class GeneratoreProspetti
{
    private $matricola_array;
    private $CdL;
    private $dataLaurea;

    public function __construct($matricola_i, $CdL_i, $dataLaurea_i){
        $this->matricola_array = $matricola_i;
        $this->CdL = $CdL_i;
        $this->dataLaurea = $dataLaurea_i;
    }

    public function GeneraProspettoLaureando()
    {
        for($i=0; $i<sizeof($this->matricola_array); $i++) // genero il prospetto per ogni laureando
        {
            $ProspettoLaureando = new ProspettoPDFLaureando($this->matricola_array[$i], $this->CdL, $this->dataLaurea);
            $ProspettoLaureando->generaProspetto();
        }
        // genero il prospetto per la commissione
        $ProspettoCommissione = new ProspettoPDFCommissione($this->matricola_array, $this->CdL, $this->dataLaurea);
        $ProspettoCommissione->generaProspettoCommissione();
    }
}



