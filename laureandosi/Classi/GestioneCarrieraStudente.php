<?php

class GestioneCarrieraStudente
{
    // Methods
    public function restituisciAnagraficaStudente($matricola)
    {
        if(realpath(dirname(dirname(__FILE__)) . '/data/' . $matricola . "_anagrafica.json") != null)
        {
            return file_get_contents(realpath(dirname(dirname(__FILE__))) . '/data/' . $matricola . "_anagrafica.json");
        }
        else
        {
            return null;
        }
    }

    public function restituisciCarrieraStudente($matricola)
    {
        if(realpath(dirname(dirname(__FILE__)) . '/data/' . $matricola . "_esami.json") != null)
        {
            return file_get_contents(realpath(dirname(dirname(__FILE__))) . '/data/' . $matricola . "_esami.json");
        }
        else
        {
            return null;
        }
    }
}