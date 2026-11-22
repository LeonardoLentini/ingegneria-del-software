<?php

require_once(realpath(dirname(__FILE__)) . '/CarrieraLaureando.php');

class LaureandoInformatica extends CarrieraLaureando
{
    private $annoImmatricolazione;
    private $dataChiusura;
    public $bonus;


    public function __construct($matricola_in, $cdl_in)
    {
        parent::__construct($matricola_in, $cdl_in);

        $anno = file_get_contents(realpath(dirname(dirname(__FILE__))) . '/data/' . $matricola_in . "_esami.json" );
        $anno = json_decode($anno, true);
        $this->annoImmatricolazione = $anno["Esami"]["Esame"][0]["ANNO_IMM"];
        $this->dataChiusura = $anno["Esami"]["Esame"][0]["DATA_CHIUSURA"];
        $this->bonus = $this->dirittoBonus();
    }

    public function mediaInf()  //viene usata quando si scrive il prospetto
    {
        $listaEsami = $this->esami;
        $cfu_informatici = 0;
        $totale_voto = 0;
        for ($i = 0; $i < sizeof($listaEsami); $i++) {
            if($listaEsami[$i]->inInformatici == 1)
            {
                $cfu_informatici += $listaEsami[$i]->cfu;
                $totale_voto += $listaEsami[$i]->votoEsame*$listaEsami[$i]->cfu;
            }
        }
        return round(floatval($totale_voto/$cfu_informatici),4);
    }

    public function dirittoBonus()
    {
        // se si laurea entro 3 anni e mezzo ha diritto all bonus
        $fineBonus = ($this->annoImmatricolazione + 4) . ("-05-01");
        if($fineBonus > $this->dataChiusura)
        {
            $this->esameBonus();
            return "SI";
        }
        else
        {
            return "NO";
        }
    }

    private function esameBonus()    //viene usata se il laureando ha diritto al bonus, elimina l'esame con il voto più basso dalla media
    {
        $listaEsami = $this->esami;
        $votoMin = $listaEsami[0]->votoEsame;
        $cfuEsame = $listaEsami[0]->cfu;
        $h=0;
        for ($i = 0; $i < sizeof($listaEsami); $i++) {
            if($listaEsami[$i]->inMedia == 1)
            {
                if($listaEsami[$i]->votoEsame < $votoMin)
                {
                    $h=$i;
                    $cfuEsame = $listaEsami[$i]->cfu;
                    $votoMin = $listaEsami[$i]->votoEsame;
                }
                else if($listaEsami[$i]->votoEsame == $votoMin)
                {
                    if($listaEsami[$i]->cfu > $cfuEsame)
                    {
                        $h=$i;
                        $cfuEsame = $listaEsami[$i]->cfu;
                        $votoMin = $listaEsami[$i]->votoEsame;
                    }
                }
            }
        }
        $listaEsami[$h]->inMedia = 0;   //tolgo l'esame selezionato dalla media degli esami
        $this->media = $this->calcolaMedia();   //ricalcolo la media
    }
}