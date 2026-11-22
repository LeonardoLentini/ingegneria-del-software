<?php
require_once(realpath(dirname(__FILE__)) . '/classi/GeneratoreProspetti.php');
require_once(realpath(dirname(__FILE__)) . '/classi/GeneratoreEmail.php');
require_once(realpath(dirname(__FILE__)) . '/classi/Testing.php');

//Eliminare il commento della prossima istruzione per eseguire il testing del sistema
//$test = new testing;

$eccezione = "";    // if(l'utente richiede di aprire i prostpetti ma questi non sono ancora stati creati)){ $eccezione = "Prospetti non ancora creati";}
$path = "";         // variabile che prenderà il path della cartella dove vengono salvati i prospetti della commissione
$file_log = realpath(dirname(__FILE__)) . '/file_configurazione/log.json';  // file per contenere i dati della sessione in corso

if((isset($_GET["laureandi"])) && isset($_GET["crea_prospetti"]) &&  ($_GET["data"] != null) && ($_GET["CdL"] != "Corso di Laurea"))
{
    $matricole_array = array_map("intval", explode(",", $_GET["laureandi"]));   //inserisco in un array le matricole dei laureandi inserite
    $generatore = new GeneratoreProspetti($matricole_array, $_GET["CdL"], $_GET["data"]);
    $generatore->GeneraProspettoLaureando();    //genero il prospetto del laureando

    $esito = "Esito: Prospetti Creati";     //i prospetti sono stati creati (per i controlli successivi)
    //aggiorno il file di log con i dati della sessione in corso
    $log = file_get_contents($file_log);
    $log = json_decode($log, true);
    $log["creati"] = true;
    $log["Matricole"] = $matricole_array;
    $log["cdl"] = $_GET["CdL"];
    $log["dataLaurea"] = $_GET["data"];
    $update_log = json_encode($log);
    file_put_contents($file_log, $update_log);
}

if(isset($_GET["apri_prospetti"]))
{
    $log_apri = file_get_contents($file_log);
    $log_apri = json_decode($log_apri, true);
    if($log_apri["creati"]) //se ho già creato i prospetti
    {
        $path = accediProspetti();
    }
    else
    {
        $eccezione = "Prospetti non ancora creati";
    }
}


if(isset($_GET["invia_prospetti"]))
{
    $log_invia = file_get_contents($file_log);
    $log_invia = json_decode($log_invia, true);
    if($log_invia["creati"])    //se ho già creato i prospetti
    {
        new GeneratoreEmail($log_invia["Matricole"], $log_invia["cdl"]);
        elimina_log($file_log); //dopo l'invio elimino i dati dei laureandi che mi ero salvato per la sessione in corso
    }
    else
    {
        $eccezione = "Prospetti non ancora creati";
    }
}

function accediProspetti()
{
    return realpath(dirname(__FILE__) . "/Prospetti/prospetti_commissione");
}

function elimina_log($file_log)
{
    $log = file_get_contents($file_log);
    $log = json_decode($log, true);
    $log["creati"] = false;
    $log["Matricole"] = null;
    $log["cdl"] = null;
    $log["dataLaurea"] = null;
    $update_log = json_encode($log);
    file_put_contents($file_log, $update_log);
}

?>

<!DOCTYPE html>
<html>
<head>
    <style = "text/css">
        body {
            background-color: #ffffff;
            margin:auto;
            width: 100%;
            height: 100%;
        }

        table {
            background-color: #ffffff;
            margin: auto;
        }

        #container{
            margin: auto;
        }

        #title {
            font-family: "Calibri";
            font-size: 25pt;
            color: #ffffff;
            font-weight: 700;
            margin-left: 400px;
            margin-top: 30px;
        }

        div.text {
            color: #0001CD;
            font-size: 13pt;
            font-weight: 700;
            text-align:center;
            font-family: 'Times New Roman', Times, serif;
        }

        .altro {
            border-width: 3px;
            border-style: solid;
            border-color: #4E55B2;
        }

        .textarea{
            height: 60%;
            width: 100%;
        }

        .date{
            width: 100%;
            height: 50px;
            background-color: #B0E0E6;
            font-family: 'Times New Roman', Times, serif;
            font-size: 13pt;
        }

        .menu{
            height: 50px;
            width: 350px;
            background-color: #B0E0E6;
            font-family:'Times New Roman', Times, serif;
            font-size: 13pt;
        }

        button {
            border:#0073AA;
            background-color: #0073AA;
            color: #FFFFFF;
            font-size: 16pt;
            font-family: "Calibri";
            border-radius: 5px;
            height: 50px;
            width: 350px;
        }

        .pulsante_titolo{
            border:#0073AA;
            background-color: #0073AA;
            color: #FFFFFF;
            font-size: 16pt;
            font-family: "Calibri";
            border-radius: 5px;
            height: 75px;
            width: 700px;
        }

        #cdl{
            height: 20px;
            width: 350px;
            padding-bottom: 10px;
            padding-top: 70px;
        }
        #data{
            padding-bottom: 10px;
            padding-top: 200px;
        }
        #matricole
        {
            text-align: left;
            margin-left: 68px;
            height: 330px;
            margin-bottom: 20px;
        }
        #logo{
            margin-right: 10px;
        }
        #invia{
            margin-bottom: 60px;
            margin-left: 100px;
        }
        #crea{
            margin-top: 40px;
            margin-left: 100px;
        }
        #apri{
            margin-bottom: 35px;
            margin-left: 100px;
        }
    </style>
</head>

<body>
<header>
    <img id = "logo" width="80" height="80" align="right" alt="Università Di Pisa" src='https://upload.wikimedia.org/wikipedia/it/e/e2/Stemma_unipi.svg'/>
    <button class="pulsante_titolo" id = "title">GESTIONE PROSPETTI LAUREA</button>
    </th>
</header>
<div id = "container">
    <form action = "index.php" method = "get">
        <table>
            <tr>
                <th height = "20%" width = "30%">
                    <label for = "Cdl">
                        <div class = "text" id="cdl">Corso di Laurea:</div>
                    </label>
                    <select name = "CdL" class = "altro menu">
                        <option>Corso di Laurea</option>
                        <option name = "CdL">ARTIFICIAL INTELLIGENCE AND DATA ENGINEERING (WAI-LM)</option>
                        <option name = "CdL">INGEGNERIA BIOMEDICA (IBM-L)</option>
                        <option name = "CdL">INGEGNERIA BIOMEDICA (WIB-LM)</option>
                        <option name = "CdL">INGEGNERIA DELLE TELECOMUNICAZIONI (ITC-L)</option>
                        <option name = "CdL">INGEGNERIA DELLE TELECOMUNICAZIONI (WIT-LM)</option>
                        <option name = "CdL">INGEGNERIA ELETTRONICA (IEL-L)</option>
                        <option name = "CdL">INGEGNERIA ELETTRONICA (WIE-LM)</option>
                        <option name = "CdL">INGEGNERIA INFORMATICA (IFO-L)</option>
                        <option name = "CdL">INGEGNERIA ROBOTICA E DELL'AUTOMAZIONE (WIM-LM)</option>
                        <option name = "CdL">CYBERSECURITY (WCY-LM)</option>
                        <option name = "CdL">COMPUTER ENGINEERING (WCN-LM)</option>
                    </select>
                </th>
                <th rowspan = 3 >
                    <div class = "text" >Matricole:</div>
                    <textarea class = "altro textarea" id="matricole" name = "laureandi"></textarea>
                </th>
                <th>
                    <button id="crea" type = "submit" value = "Crea Prospetti" name = "crea_prospetti">Crea Prospetti</button>
                    <div>
                        <?php echo($eccezione); // eventualmente da usare se risolvo ?>
                    </div>
                </th>
            </tr>
            <tr>
                <th height = "50%">
                    <div class = "text" id="data">Data di Laurea:</div>
                    <input type = "date" class = "altro date" name = "data"/>
                </th>
                <th>
                    <button id="apri" type = "submit" value = "Apri Prospetti" name = "apri_prospetti">Apri prospetti</button>
                    <div>
                        <?php echo($path); ?>
                    </div>
                </th>
            </tr>
            <tr>
                <th></th>
                <th>
                    <button id= "invia" type = "submit" value = "Invia Prospetti" name = "invia_prospetti">Invia Prospetti</button>
                    <div>
                        <?php if(isset($esito))
                        {
                            echo($esito);
                        } ?>
                    </div>
                </th>
            </tr>
        </table>
    </form>
</div>

</body>
</html>

