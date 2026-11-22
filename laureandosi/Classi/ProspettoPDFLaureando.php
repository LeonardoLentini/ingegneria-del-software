<?php
require_once(realpath(dirname(__FILE__)) . '/../lib/fpdf184/fpdf.php');
require_once(realpath(dirname(__FILE__)) . '/CarrieraLaureando.php');
require_once(realpath(dirname(__FILE__)) . '/LaureandoInformatica.php');



class ProspettoPDFLaureando
{
    private $matricola;
    private $carriera;
    private $dataLaurea;
    private $informatico;

    public function __construct($matricola_in, $cdl_in, $dataLaurea_in)
    {
        if ($cdl_in != "INGEGNERIA INFORMATICA (IFO-L)") {
            $this->carriera = new CarrieraLaureando($matricola_in, $cdl_in);
        } else {
            $this->carriera = new LaureandoInformatica($matricola_in, $cdl_in);
        }
        $this->matricola = $matricola_in;
        $this->dataLaurea = $dataLaurea_in;
    }


    public function generaProspetto()
    {
        $font = "Arial";
        $this->informatico = 0;

        $prospetto = new FPDF();
        $prospetto->AddPage();
        $prospetto->SetFont($font, "", 16);
//INTESTAZIONE

        $prospetto->Cell(0, 6, $this->carriera->cdl, 0, 1, 'C');
        $prospetto->Cell(0, 8, 'CARRIERA E SIMULAZIONE DEL VOTO DI LAUREA', 0, 1, 'C');
        $prospetto->Ln(2);
//ANAGRAFICA

        $prospetto->SetFont($font, "", 9);

        $anagrafica = $this->anagraficaProspettoPDFLaureando();

        $prospetto->MultiCell(0, 6, $anagrafica, 1, 'L');
        $prospetto->Ln(3);
//ESAMI
// 1 pag = 190 = 21cm con bordi di 1cm
        $larghezza1 = 12;   // larghezza celle piccole
        $altezza = 5.5;
        $larghezza2 = 190 - (3 * $larghezza1);  // larghezza riquadro grande
        if ($this->informatico != 1) {
            $prospetto->Cell($larghezza2, $altezza, "ESAME", 1, 0, 'C');
            $prospetto->Cell($larghezza1, $altezza, "CFU", 1, 0, 'C');
            $prospetto->Cell($larghezza1, $altezza, "VOT", 1, 0, 'C');
            $prospetto->Cell($larghezza1, $altezza, "MED", 1, 1, 'C');
        } else {
            $larghezza1 = 11;
            $larghezza2 = 190 - (4 * $larghezza1);
            $prospetto->Cell($larghezza2, $altezza, "ESAME", 1, 0, 'C');
            $prospetto->Cell($larghezza1, $altezza, "CFU", 1, 0, 'C');
            $prospetto->Cell($larghezza1, $altezza, "VOT", 1, 0, 'C');
            $prospetto->Cell($larghezza1, $altezza, "MED", 1, 0, 'C');
            $prospetto->Cell($larghezza1, $altezza, "INF", 1, 1, 'C');
        }

        $altezza = 4;
        $prospetto->SetFont($font, "", 8);
        for ($i = 0; $i < sizeof($this->carriera->esami); $i++) {
            $esame = $this->carriera->esami[$i];
            if ($esame->curricolare == 1) {
                $prospetto->Cell($larghezza2, $altezza, $esame->nomeEsame, 1, 0, 'L');
                $prospetto->Cell($larghezza1, $altezza, $esame->cfu, 1, 0, 'C');
                $prospetto->Cell($larghezza1, $altezza, $esame->votoEsame, 1, 0, 'C');
                if ($this->informatico == 0) {
                    $prospetto->Cell($larghezza1, $altezza, ($esame->inMedia == 1) ? 'X' : '', 1, 1, 'C');
                }
                else
                {
                    $prospetto->Cell($larghezza1, $altezza, ($esame->inMedia == 1) ? 'X' : '', 1, 0, 'C');
                    $prospetto->Cell($larghezza1, $altezza, ($esame->inInformatici == 1) ? 'X' : '', 1, 1, 'C');
                }
            }
        }
        $prospetto->Ln(5);
//STATISTICHE
        $prospetto->SetFont($font, "", 9);
        $string = "Media Pesata (M):                                                  " . $this->carriera->media .
            "\nCrediti che fanno media (CFU):                             " . $this->carriera->crediti .
            "\nCrediti curriculari conseguiti:                                  " . $this->carriera->curricolari . "/" . $this->carriera->curricolariRichiesti .
            "\nVoto di tesi (T):                                                       " . 0 .
            "\nFormula calcolo voto di laurea:                               " . $this->carriera->formulaStringa;
        if ($this->informatico == 1) {
            $string .= "\nMedia pesata esami INF:                                        " . $this->carriera->mediaInf();
        }
        $prospetto->MultiCell(0, 6, $string, 1, "L");

        $percorso_output = "Prospetti\\";
        $nome_file = $this->matricola . "-prospetto.pdf";
        $prospetto->Output('F', $percorso_output . $nome_file);
    }

    private function anagraficaProspettoPDFLaureando()  //ritorna la stringa contenente l'anagrafica necessaria del laureando
    {
        $anagrafica = "Matricola:                       " . $this->matricola .
            "\nNome:                            " . $this->carriera->nome .
            "\nCognome:                      " . $this->carriera->cognome .
            "\nEmail:                             " . $this->carriera->email .
            "\nData:                              " . $this->dataLaurea;
        if ($this->carriera->cdl == "INGEGNERIA INFORMATICA (IFO-L)")
        {
            $this->informatico = 1;
            //if($this->carriera->bonus == "SI") { $this->carriera->esameBonus(); }
            $anagrafica .= "\nBonus:                            " . $this->carriera->bonus;
        }
        return $anagrafica;
    }
}