<?php
require_once (realpath(dirname(__FILE__)) . '/CarrieraLaureando.php');

class ProspettoPDFCommissione
{
    private $matricole; //array
    private $cdl;
    private $dataLaurea;

    public function __construct(array $matricole_in, $cdl_in, $dataLaurea_in)
    {
        $this->cdl = $cdl_in;
        $this->matricole = $matricole_in;
        $this->dataLaurea = $dataLaurea_in;
    }

    public function generaProspettoCommissione()
    {
        $pdf = new FPDF();
        $font = "Arial";
        $pdf->AddPage();
        $pdf->SetFont($font, "", 14);
// PRIMA PAGINA CON LA LISTA DI LAUREANDI
        $pdf->Cell(0, 6, $this->cdl, 0, 1, 'C');
        $pdf->Ln(2);
        $pdf->SetFont($font, "", 16);
        $pdf->Cell(0, 6, 'LISTA LAUREANDI', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont($font, "", 14);
        $dimensione_base = 190 / 4;
        $dimensione_altezza = 5;
        $pdf->Cell($dimensione_base, $dimensione_altezza, "COGNOME", 1, 0, 'C');
        $pdf->Cell($dimensione_base, $dimensione_altezza, "NOME", 1, 0, 'C');
        $pdf->Cell($dimensione_base, $dimensione_altezza, "CDL", 1, 0, 'C');
        $pdf->Cell($dimensione_base, $dimensione_altezza, "VOTO LAUREA", 1, 1, 'C');
        $pdf->SetFont($font, "", 12);

        for ($i = 0; $i < sizeof($this->matricole); $i++) {
            if ($this->cdl != "INGEGNERIA INFORMATICA (IFO-L)") {
                $carriera = new CarrieraLaureando($this->matricole[$i], $this->cdl);
            } else {
                $carriera = new LaureandoInformatica($this->matricole[$i], $this->cdl);
            }
            $pdf= $this->aggiungiRiga($pdf, $carriera); //aggiungo una riga alla lista con i laureandi
        }
        //aggiungo una pagina per ogni laureando e genero il suo prospetto con la simulazione
        for ($i = 0; $i < sizeof($this->matricole); $i++) {
            $pdf->AddPage();
            if ($this->cdl != "INGEGNERIA INFORMATICA (IFO-L)") {
                $carriera = new CarrieraLaureando($this->matricole[$i], $this->cdl);
            } else {
                $carriera = new LaureandoInformatica($this->matricole[$i], $this->cdl);
            }
            $tipo_informatico = 0;
            $pdf->SetFont($font, "", 16);
            $pdf->SetFont($font, "", 16);

//INTESTAZIONE

            $pdf->Cell(0, 6, $this->cdl, 0, 1, 'C');

            $pdf->Cell(0, 8, 'CARRIERA E SIMULAZIONE DEL VOTO DI LAUREA', 0, 1, 'C');
            $pdf->Ln(2);
//ANAGRAFICA

            $pdf->SetFont($font, "", 9);
            $anagrafica_stringa = "Matricola:                       " . $this->matricole[$i] .
                "\nNome:                            " . $carriera->nome .
                "\nCognome:                      " . $carriera->cognome .
                "\nEmail:                             " . $carriera->email .
                "\nData:                              " . $this->dataLaurea;
            if ($this->cdl == "INGEGNERIA INFORMATICA (IFO-L)") {
                $tipo_informatico = 1;
                $anagrafica_stringa .= "\nBonus:                            " . $carriera->bonus;
            }
            $pdf->MultiCell(0, 6, $anagrafica_stringa, 1, 'L');
            $pdf->Ln(3);


//ESAMI
// 1 pag = 190 = 21cm con bordi di 1cm
            $larghezza1 = 12;
            $altezza = 5.5;
            $larghezza2 = 190 - (3 * $larghezza1);
            if ($tipo_informatico != 1) {
                $pdf->Cell($larghezza2, $altezza, "ESAME", 1, 0, 'C');
                $pdf->Cell($larghezza1, $altezza, "CFU", 1, 0, 'C');
                $pdf->Cell($larghezza1, $altezza, "VOT", 1, 0, 'C');
                $pdf->Cell($larghezza1, $altezza, "MED", 1, 1, 'C');
            } else {
                $larghezza1 = 11;
                $larghezza2 = 190 - (4 * $larghezza1);
                $pdf->Cell($larghezza2, $altezza, "ESAME", 1, 0, 'C');
                $pdf->Cell($larghezza1, $altezza, "CFU", 1, 0, 'C');
                $pdf->Cell($larghezza1, $altezza, "VOT", 1, 0, 'C');
                $pdf->Cell($larghezza1, $altezza, "MED", 1, 0, 'C');
                $pdf->Cell($larghezza1, $altezza, "INF", 1, 1, 'C');
            }

            $altezza = 4;
            $pdf->SetFont($font, "", 8);
            for ($j = 0; $j < sizeof($carriera->esami); $j++) {
                $esame = $carriera->esami[$j];
                $pdf->Cell($larghezza2, $altezza, $esame->nomeEsame, 1, 0, 'L');
                $pdf->Cell($larghezza1, $altezza, $esame->cfu, 1, 0, 'C');
                $pdf->Cell($larghezza1, $altezza, $esame->votoEsame, 1, 0, 'C');
                if ($tipo_informatico != 1) {
                    $pdf->Cell($larghezza1, $altezza, ($esame->inMedia == 1) ? 'X' : '', 1, 1, 'C');
                } else {
                    $pdf->Cell($larghezza1, $altezza, ($esame->inMedia == 1) ? 'X' : '', 1, 0, 'C');
                    $pdf->Cell($larghezza1, $altezza, ($esame->inInformatici == 1) ? 'X' : '', 1, 1, 'C');
                }
            }
            $pdf->Ln(5);
//STATISTICHE
            $pdf->SetFont($font, "", 9);
            $string = "Media Pesata (M):                                                  " . $carriera->media .
                "\nCrediti che fanno media (CFU):                             " . $carriera->crediti  .
                "\nCrediti curriculari conseguiti:                                  " . $carriera->curricolari . "/" . $carriera->curricolariRichiesti .
                "\nVoto di tesi (T):                                                       " . 0 .
                "\nFormula calcolo voto di laurea:                               " . $carriera->formulaStringa;
            if ($tipo_informatico == 1) {
                $string .= "\nMedia pesata esami INF:                                        " . $carriera->mediaInf();
            }

            $pdf->MultiCell(0, 6, $string, 1, "L");

//SIMULAZIONE

            //prelevo i dati necessari per il calcolo del voto di laurea dal file di configurazione del corso di laurea
            $formule_json = file_get_contents( realpath(dirname(dirname(__FILE__))) .'/file_configurazione/formule_cdl.json');
            $formule = json_decode($formule_json, true);
            $TesiMin =  $formule[$carriera->cdl]["TesiMin"];
            $TesiMax =  $formule[$carriera->cdl]["TesiMax"];
            $TesiStep =  $formule[$carriera->cdl]["TesiStep"];
            $CommMin =  $formule[$carriera->cdl]["CommMin"];
            $CommMax =  $formule[$carriera->cdl]["CommMax"];
            $CommStep =  $formule[$carriera->cdl]["CommStep"];


            $pdf->Ln(4);
            $pdf->Cell(0, 5.5, "SIMULAZIONE DI VOTO DI LAUREA", 1, 1, 'C');
            $dimensione_base = 190 / 2;
            $dimensione_altezza = 4.5;

            /*  Le variabili CFU, M, T, C sono necessarie per la funzione eval() (riga 156 e 172)
            *   che permette di interpretare la formula di laurea inserendo tali variabili per calcolare il voto di laurea
            */
            $CFU = $carriera->crediti;
            if ($CommMin != 0) {
                $pdf->Cell($dimensione_base, $dimensione_altezza, "VOTO COMMISSIONE (C)", 1, 0, 'C');
                $pdf->Cell($dimensione_base, $dimensione_altezza, "VOTO LAUREA", 1, 1, 'C');
                $M = $carriera->media;
                $T = 0;

                for ($C = $CommMin; $C <= $CommMax; $C += $CommStep) {
                    $voto = 0;
                    eval("\$voto = " . $carriera->formula . ";");
                    $pdf->Cell($dimensione_base, $dimensione_altezza, $C, 1, 0, 'C');
                    $pdf->Cell($dimensione_base, $dimensione_altezza, $voto, 1, 1, 'C');
                }
            }
            if ($TesiMin != 0) {
                $pdf->Cell($dimensione_base, $dimensione_altezza, "VOTO TESI (T)", 1, 0, 'C');
                $pdf->Cell($dimensione_base, $dimensione_altezza, "VOTO LAUREA", 1, 1, 'C');
                $M = $carriera->media;
                $C = 0;

                for ($T = $TesiMin; $T <= $TesiMax; $T += $TesiStep) {
                    $voto = 0;

                    eval("\$voto = " . $carriera->formula . ";");
                    $pdf->Cell($dimensione_base, $dimensione_altezza, $T, 1, 0, 'C');
                    $pdf->Cell($dimensione_base, $dimensione_altezza, $voto, 1, 1, 'C');
                }
            }
        }
        $percorso_output = "Prospetti/prospetti_commissione\\";
        $nome_file = "Prospetti ".$this->dataLaurea.".pdf";
        $pdf->Output('F', $percorso_output . $nome_file);
    }

    private function aggiungiRiga($pdf, $carriera)
    {
        $dimensione_base = 190 / 4;
        $dimensione_altezza = 5;
        $pdf->Cell($dimensione_base, $dimensione_altezza, $carriera->cognome, 1, 0, 'L');
        $pdf->Cell($dimensione_base, $dimensione_altezza, $carriera->nome, 1, 0, 'L');
        $pdf->Cell($dimensione_base, $dimensione_altezza, "", 1, 0, 'C');
        $pdf->Cell($dimensione_base, $dimensione_altezza, "/110", 1, 1, 'C');
        return $pdf;
    }
}


