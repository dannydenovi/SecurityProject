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
