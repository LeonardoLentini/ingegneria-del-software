# Laureandosi

## 📋 Descrizione del Progetto
**Laureandosi** è un'applicazione web per la **gestione dei prospetti di laurea**, sviluppata come progetto per il corso di **Ingegneria del Software**. Il sistema è pensato per l'unità didattica, che a partire dalle matricole dei laureandi genera automaticamente i prospetti con la carriera e gli indicatori per il voto di laurea, e li invia via email agli studenti.

Il progetto copre sia la fase di **analisi e progettazione** (modellata in UML con Visual Paradigm) sia l'**implementazione** in PHP.

Il sistema offre:
* **Generazione dei Prospetti:** Inserite le matricole, il corso di laurea e la data dell'appello, viene creato un prospetto PDF per ogni laureando e un prospetto riepilogativo per la commissione.
* **Calcolo degli Indicatori:** Media pesata, crediti che fanno media e crediti curriculari conseguiti, con gestione di esami a giudizio/idoneità e del "30 e lode".
* **Formule per Corso di Laurea:** Formula del voto di laurea, CFU richiesti e parametri di tesi e commissione configurabili per ognuno degli 11 corsi supportati tramite file JSON.
* **Regole per Ingegneria Informatica:** Calcolo della media sugli esami informatici e **bonus** per chi si laurea entro tre anni e mezzo, con l'esclusione dalla media dell'esame con il voto più basso.
* **Invio Email:** Invio automatico a ogni laureando del proprio prospetto in allegato.
* **Testing:** Verifica automatica di anagrafica e indicatori di carriera rispetto a un insieme di risultati attesi.

## 📐 Documentazione di Progetto
La cartella `docs/` contiene la modellazione UML del sistema:
* **Casi d'uso e Scenari:** Accedere, generare e inviare i prospetti di laurea.
* **CRC Card e Classi di Progetto:** Individuazione delle responsabilità e delle collaborazioni tra classi.
* **Diagramma di Classe e Diagramma di Deployment.**
* **Wireframe** dell'interfaccia web.

Il file `Documentazione.pdf` è l'esportazione completa del progetto, mentre `Laureandosi.vpp` è il file sorgente apribile con [Visual Paradigm](https://www.visual-paradigm.com/).

## 🛠️ Stack Tecnologico
* **Frontend:** HTML5, CSS3.
* **Backend:** PHP.
* **Librerie:** [FPDF](http://www.fpdf.org/) 1.84 (generazione PDF), [PHPMailer](https://github.com/PHPMailer/PHPMailer) 6.6.5 (invio email), già incluse nella cartella `lib/`.
* **Dati:** File JSON (simulano le risposte del sistema di gestione delle carriere studenti).
* **Modellazione:** Visual Paradigm (UML).
* **Ambiente Server:** XAMPP.

---

## 🚀 Guida all'Installazione (Localhost con XAMPP)

### 1. Preparazione dell'Ambiente
1.  Scaricare e installare [XAMPP](https://www.apachefriends.org/it/index.html).
2.  Avviare il **XAMPP Control Panel**.
3.  Cliccare su **Start** in corrispondenza del modulo **Apache** (il database non è necessario).

### 2. Configurazione dei File
1.  Navigare nella cartella di installazione di XAMPP (solitamente `C:\xampp\htdocs` su Windows).
2.  Copiare all'interno la cartella `laureandosi` della repository.
    * *Percorso finale atteso:* `C:\xampp\htdocs\laureandosi\index.php`.

### 3. Avvio dell'Applicazione
Digitare nel browser:
`http://localhost/laureandosi/index.php`.

### 4. Utilizzo
1.  Selezionare il **Corso di Laurea** dal menu a tendina.
2.  Inserire la **data dell'appello** di laurea.
3.  Inserire le **matricole** dei laureandi separate da virgola (es. `123456,345678`).
4.  Cliccare su **Crea Prospetti**: i PDF vengono salvati nella cartella `Prospetti/`, quello per la commissione in `Prospetti/prospetti_commissione/`.
5.  Cliccare su **Apri Prospetti** per visualizzare il percorso dei prospetti generati, e su **Invia Prospetti** per inviarli via email.

Matricole di test disponibili in `data/`:

| Matricola | Corso di Laurea |
|-----------|-----------------|
| `123456` | INGEGNERIA INFORMATICA (IFO-L) |
| `234567` | INGEGNERIA ELETTRONICA (WIE-LM) |
| `345678` | INGEGNERIA INFORMATICA (IFO-L) |
| `456789` | INGEGNERIA DELLE TELECOMUNICAZIONI (WIT-LM) |
| `567890` | CYBERSECURITY (WCY-LM) |

> ℹ️ **Nota:** I dati dei laureandi presenti in `data/` e `dati_test/` sono **fittizi** e forniti a scopo di test.

### 5. Esecuzione dei Test
Per eseguire il testing del sistema, rimuovere il commento dalla seguente riga in `index.php` e ricaricare la pagina:
```php
//$test = new testing;
```
I risultati attesi sono definiti in `dati_test/dati_test_corretti.json`.

---

## ⚠️ Note e Limitazioni

### Invio delle email
La configurazione SMTP in `Classi/GeneratoreEmail.php` punta al server di posta dell'università (`mixer.unipi.it`, porta 25, senza autenticazione), raggiungibile solo dalla rete di ateneo. Per provare l'invio in locale è necessario sostituire host, porta e credenziali con quelli di un proprio server SMTP.

### Compatibilità
L'applicazione è stata sviluppata e testata su **XAMPP per Windows**: alcuni percorsi nel codice usano il separatore `\` e la distinzione tra maiuscole e minuscole nei nomi delle cartelle non è uniforme, quindi su Linux e macOS potrebbe non funzionare senza modifiche.

---

## 📂 Struttura della Repository
```text
/
├── docs/
│   ├── Documentazione.pdf       # Esportazione completa della modellazione UML
│   └── Laureandosi.vpp          # Progetto sorgente Visual Paradigm
└── laureandosi/
    ├── Classi/                  # Logica applicativa (carriera, prospetti PDF, email, testing)
    ├── data/                    # Anagrafiche e carriere dei laureandi di test (JSON)
    ├── dati_test/               # Risultati attesi per il testing
    ├── file_configurazione/     # Formule per CdL, esami informatici e log di sessione
    ├── lib/                     # Librerie esterne (FPDF, PHPMailer)
    ├── Prospetti/               # Output dei prospetti PDF generati
    └── index.php                # Punto di ingresso dell'applicazione
```
