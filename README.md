# Sicurezza dei Sistemi

## Sicurezza tramite sensori biometrici

## Claudio Anchesi (513605) - Danny De Novi (517611)

---

## Introduzione

Il progetto consiste nell'implementare il riconoscimento facciale misto ad un controllo della temperatura per far si che un led, il quale simula un qualsiasi dispositivo hardware, si accenda al riconoscimento di un volto associato ad un utente. L'hardware si compone di 4 parti: un Raspberry Pi 4B, una webcam, un sensore di temperatura connesso tramite pin al Raspberry, e un led, anch'esso posizionato su una breadboard.

### Raspberry Pi 4B

Il Raspberry Pi è un *Single Board Computer* molto utiizzato in ambito *Internet of Things*, in quanto ad un costo relativamente basso si può avere la potenza di un microprocessore ARM, una RAM da diversi tagli (in base ai casi di utilizzo) e dei Pin GPIO che permettono in modo semplice l'interfacciamento di hardware esterno. Il sistema operativo è GNU/Linux a 64 bit, in particolare una versione allegerita di Debian creata ad hoc per Raspberry, Raspberry Pi OS.

### Hardware secondario

Può essere utilizzata una generica webcam USB oppure una webcam connessa tramite interfaccia apposita collocata sull'SBC.

Il **sensore di temperatura** è nello specifico un MLX90614ESF GY-906, è connesso al raspberry tramite *jumper wires* ai GPIO nel seguente modo:

| GPIO | PIN | Sensore |
| --- | --- | --- |
| 5V  | 2/4 | VIN |
| GND | 6/9/14/20/25/30/34/39 | GND |
| GPIO 1 (SCL0) | 28  | SCL |
| GPIO 0 (SDA0) | 27  | SDA |

Il **LED** connesso serve semplicemente a raffigurare un qualunque dispositivo funzionante solo in caso di riconoscimento del volto. È connesso in serie ad una resistenza da 220K nel seguente modo:

| GPIO | PIN | LED |
| --- | --- | --- |
| GND | 6/9/14/20/25/30/34/39 | Negativo |
| GPIO 23 | 16  | Positivo |

## Implementazione

### Web

È stato sviluppato un client web accessibile esclusivamente all'amministratore, il quale è contrassegnato con l'ID 1 nel database. Nel client web è possibile inserire gli utenti contrassegnandone il nome, cognome, mail ed un'immagine che può essere sia scattata sul momento che caricata, la quale servirà allo script per apprendere il volto dell'utente. È possibile la modifica degli utenti e l'eliminazione.

#### Backend

Il backend consiste in un webserver Apache containerizzato con supporto a PHP, al quale abbiamo effettuato un binding con una cartella specifica dove sono presenti i file html, css, js e php insieme agli script Python e i volti degli utenti registrati. Come database si è preferito utilizzare MySQL per la semplicità e lo schematismo della base di dati e Phpmyadmin per la consultazione in fase di sviluppo. Le immagini dei volti sono salvate in una cartella specifica (``\scripts\faces``) e contrassegnate con l'ID dell'utente.

### Face recognition

Il face recognition è un'applicazione dell'intelligenza artificiale, utilizzata in biometria, che tramite l'analisi di frame di video (o immagini) riconosce la natura di un volto umano effettuando una classificazione con dei dati pregressi.

La tecnica di face recognition è stata implementata tramite il seguente script Python tramite i framework OpenCV e face_recognition.

```python
import face_recognition
import os, sys
import cv2
import numpy as np
import math
from smbus2 import SMBus
from mlx90614 import MLX90614
from RPi import GPIO
from time import sleep

def face_confidence(face_distance, face_match_threshold=0.6):
    range = (1.0 - face_match_threshold)
    linear_val = (1.0 - face_distance) / (range * 2.0)

    if face_distance > face_match_threshold:
        return str(round(linear_val * 100, 2)) + '%'
    else:
        value = (linear_val + ((1.0 - linear_val) * math.pow((linear_val - 0.5) * 2, 0.2))) * 100
        return str(round(value, 2)) + '%'


class FaceRecognition:
    face_locations = []
    face_encodings = []
    face_names = []
    known_face_encodings = []
    known_face_names = []
    process_current_frame = True
    precedent_listdir = []
    i = 0
    temp = 0

    def __init__(self):
        self.encode_faces()

    # Funzione di codifica dei volti
    def encode_faces(self):
        # Salva in array una lista dei file
        self.precedent_listdir = os.listdir('faces')
        # Inizializzazione array
        self.known_face_encodings = []
        self.known_face_names = []
        # Per ciascuna immagine nella directory esegue la codifica aggiungendo anche il nome dell'immagine
        for image in os.listdir('faces'):
            face_image = face_recognition.load_image_file(f"faces/{image}")
            face_encoding = face_recognition.face_encodings(face_image)[0]

            self.known_face_encodings.append(face_encoding)
            self.known_face_names.append(image)
        print(self.known_face_names)

    def run_recognition(self):
        # Inizializzazione della webcam
        video_capture = cv2.VideoCapture(0)

        if not video_capture.isOpened():
            sys.exit('Video source not found...')
        
        # Inizializzazione del LED
        led = 23
        GPIO.setmode(GPIO.BCM)
        GPIO.setup(led, GPIO.OUT)

        # Inizializzazione del sensore di temperatura
        bus = SMBus(1)
        sleep(2)
        sensor = MLX90614(bus, address=0x5A)
        sleep(2)


        while True:
            # Cattura un frame
            ret, frame = video_capture.read()

            # Effettua un'analisi ogni due frame
            if self.process_current_frame:
                self.i = self.i + 1

                # Resize del frame ad 1/4 per velocizzare l'analisi
                small_frame = cv2.resize(frame, (0, 0), fx=0.25, fy=0.25)

                # Conversione dell'immagine da BGR (OpenCV) ad RGB (face_recognition)
                rgb_small_frame = small_frame[:, :, ::-1]

                # trova le facce nel frame e le codifica
                self.face_locations = face_recognition.face_locations(rgb_small_frame)
                self.face_encodings = face_recognition.face_encodings(rgb_small_frame, self.face_locations)

                self.face_names = []
                # Per ciascuna faccia codificata
                for face_encoding in self.face_encodings:
                    # Controlla se vi è un match
                    matches = face_recognition.compare_faces(self.known_face_encodings, face_encoding)
                    
                    # Valori di default
                    name = "Unknown"
                    confidence = '???'

                    # Calcola la distanza minore per aumentare l'accuratezza
                    face_distances = face_recognition.face_distance(self.known_face_encodings, face_encoding)
                    
                    # Seleziona il migliore match
                    best_match_index = np.argmin(face_distances)

                    # Se vi è un match
                    if matches[best_match_index]:
                        # Mostra il nome e la percentuale di confidenza
                        name = self.known_face_names[best_match_index]
                        confidence = float(face_confidence(face_distances[best_match_index])[:-1])
                        
                        # Acquisizione della temperatura
                        temp = sensor.get_obj_temp()
                        if confidence > 95 and temp > 30 and temp < 45: 
                            GPIO.output(led, GPIO.HIGH)
                            sleep(0.3)
                            GPIO.output(led, GPIO.LOW)

                    self.face_names.append(f'{name} ({confidence}%)')

            # Ogni 10 frame analizzati verifica se vi sono stati cambiamenti nella cartella
            if self.i == 10: 
                # Se ciò avviene allora ricodifica le immagini presenti
                if os.listdir('faces') != self.precedent_listdir:
                    self.encode_faces()
                self.i = 0
            # Imposta il frame successivo come l'opposto del corrente
            # Questo serve ad evitare rallentamenti dovuti all'analisi di ogni frame
            self.process_current_frame = not self.process_current_frame

            # Mostra i risultati
            for (top, right, bottom, left), name in zip(self.face_locations, self.face_names):
                # Esegue l'upscaling dell'immagine nuovamente per riportarla all'origine
                top *= 4
                right *= 4
                bottom *= 4
                left *= 4

                # Crea un frame con il nome
                cv2.rectangle(frame, (left, top), (right, bottom), (0, 0, 255), 2)
                cv2.rectangle(frame, (left, bottom - 35), (right, bottom), (0, 0, 255), cv2.FILLED)
                cv2.putText(frame, name, (left + 6, bottom - 6), cv2.FONT_HERSHEY_DUPLEX, 0.8, (255, 255, 255), 1)

            # Mostra il risultato
            cv2.imshow('Face Recognition', frame)

            # Con la pressione del tasto q si interrompe l'esecuzione
            if cv2.waitKey(1) == ord('q'):
                break

        # Rilascia le risorse
        video_capture.release()
        cv2.destroyAllWindows()


if __name__ == '__main__':
    fr = FaceRecognition()
    fr.run_recognition() 
```

È fondamentale per il funzionamento del sensore di temperatura dover abilitare l'interfaccia I2C dalle impostazioni del Raspberry visualizzabili con il comando da terminale `sudo raspi-config`.

## Guida all'implementazione

Per poter implementare il progetto bisogna come precedentemente detto attivare l'interfaccia I2C. Successivamente è fondamentale installare il docker engine, in quanto per l'implementazione dei servizi web si è deciso un approccio containerizzato.

In seguito bisogna clonare la [repository](https://github.com/dannydenovi/SecurityProject.git):

`git clone https://github.com/dannydenovi/SecurityProject.git`

Bisogna navigare nella cartella "client" ed in seguito dare il seguente comando da terminale: `sudo docker compose up --detach`. Verrà effettuata la build dell'immagine ed in seguito lanciata l'esecuzione dei vari servizi. L'interfaccia web sarà disponibile sulla porta 80, il database MySQL sulla 3306 e Phpmyadmin sulla porta 8080.

Viene di default importato un dump di base che contiene la tabella degli utenti necessaria al funzionamento della piattaforma ed un utente amministratore utilizzabile con mail: `admin@admin.com` e password: `prova`.

Una volta effettuato l'accesso si potranno gestire gli utenti tramite interfaccia grafica via browser.

Per lanciare lo script che si occupa del riconoscimento bisognerà eseguire il seguente comando specificato dalla cartella client per comodità:`python3 www/scripts/main.py`

## Vulnerabilità

Le vulnerabilità di un sistema tale possono essere numerose. La più grave per semplicità di applicazione e disastrosità può essere il **bruteforce**, in quanto il client, progettato per un utilizzo locale, non possiede controlli 2FA e blocchi IP per numerosi accessi. Il client non possiede nemmeno un log con gli accessi, il quale rende più difficile l'individuazione di eventuali intrusi. Vi si possono anche tentare accessi con una foto se quest'ultima viene accuratamente riscaldata per farla rientrare nel range in quanto non vi è l'utilizzo di un sensore **LIDAR** che verifichi la profondità del campo visivo della webcam. Eventuali vulnerabilità possono essere dovuti all'applicazione dei container o vulnerabilità dovute al sistema operativo. Immaginando un'intrusione con successivo privilege escalation, basterebbe inserire una foto all'interno della cartella che contiene i volti per poter accedere e poi eliminare l'immagine.
