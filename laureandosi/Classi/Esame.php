<?php

class Esame
{
    public $nomeEsame;
    public $cfu;
    public $votoEsame;
    public $inMedia;
    public $inInformatici;
    public $curricolare;

    public function __construct($nome, $voto, $cfu_esame){
        $this->nomeEsame = $nome;
        $this->votoEsame = $voto;
        $this->cfu = $cfu_esame;
        $this->inInformatici=0;
    }

    public function esameInInformatici()
    {
        $informaticiJson= file_get_contents(realpath(dirname(dirname(__FILE__))) . '/file_configurazione/informatici.json');
        $informatici= json_decode($informaticiJson, true);
        for ($i = 0; $i < sizeof($informatici["INGEGNERIA INFORMATICA (IFO-L)"]);$i++)
            {
                if($this->nomeEsame == $informatici["INGEGNERIA INFORMATICA (IFO-L)"][$i])
                {
                    $this->inInformatici = 1;
                }
            }
    }

}