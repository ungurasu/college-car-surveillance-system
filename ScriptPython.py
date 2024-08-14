import sounddevice as sd
import numpy as np
import cv2
import os
import mysql.connector
import datetime
import time
import board
import busio
import adafruit_adxl34x
import math
from scipy.io.wavfile import write
from moviepy.editor import *
from multiprocessing import Process, Event
import serial

message = "Un eveniment a fost inregistrat in apropierea automobilului dumneavoastra! Inregistrarea audio-video este disponibila la http://raspberrypi.local:8000/dashboard"

mydb = mysql.connector.connect(
    host='localhost',
    username='root',
    password='elevcngm',
    database='car_surveillance_system'
)

i2c = busio.I2C(board.SCL, board.SDA)
accelerometer = adafruit_adxl34x.ADXL345(i2c)
gsm = serial.Serial("/dev/serial0", 115000)
gsm.flushInput()

def send_SMS(message, phone_no):
    gsm.write(("AT+CMGF=1\r\n").encode())
    time.sleep(1)
    received = gsm.read(gsm.inWaiting())
    print(received.decode())
    gsm.write((f'AT+CMGS="{phone_no}"\r\n').encode())
    time.sleep(2)
    received = gsm.read(gsm.inWaiting())
    print(received.decode())
    gsm.write(message.encode())
    time.sleep(0.2)
    received = gsm.read(gsm.inWaiting())
    print(received.decode())
    gsm.write(('\x1A').encode())
    time.sleep(0.2)
    gsm.write(''.encode())
    time.sleep(5)
    received = gsm.read(gsm.inWaiting())
    print(received.decode())
    
def get_acceleration_module():
    v0 = accelerometer.acceleration[0]
    v1 = accelerometer.acceleration[1]
    v2 = accelerometer.acceleration[2]

    module = math.sqrt(v0*v0 + v1*v1 + v2*v2)
    return module


def insert_recording_into_db(recording_title, recording_path):
    mycursor = mydb.cursor()
    
    sql = "INSERT INTO videos (video_title, video_path, created_at) VALUES (%s, %s, NOW())"
    values = (recording_title, recording_path)
    
    mycursor.execute(sql, values)
    mydb.commit()
    
    print(mycursor.rowcount, " record inserted.")
    

def get_phone_numbers():
    mycursor = mydb.cursor()
    
    sql = "SELECT phone FROM users GROUP BY phone"
    
    mycursor.execute(sql)
    
    phone_numbers = []
    results = mycursor.fetchall()
    
    for row in results:
        phone_numbers.append(row[0])
    
    return phone_numbers
    

def video_subprocess(file_name, duration_seconds, frame_rate, event_video_start, event_audio_start):
    first_run = True
    
    # Capture video from webcam
    cap = cv2.VideoCapture(0)
    frames = []
    
    for i in range(duration_seconds * int(frame_rate)):
        # Capture frame-by-frame
        frame_start_time = time.time()
        ret, frame = cap.read()

        if ret == True:
            frames.append(frame)
            # Write the frame into the file 'output.avi'
            if first_run == True:
                print(f"{datetime.datetime.now().time()} sent start signal!\n")
                event_video_start.set()
                event_audio_start.wait()
                print(f"{datetime.datetime.now().time()} Started recording video!\n")
                first_run = False

            # Display the resulting frame
            #cv2.imshow('frame',frame)
            #cv2.waitKey(1)
        else:
            break
        
        elapsed_time = time.time() - frame_start_time
        remaining_time = 1/frame_rate - elapsed_time
        time.sleep(max({remaining_time, 0}))
    
    print(f"{datetime.datetime.now().time()} Releasing!\n")
    
    cap.release()
    # Define the codec and create VideoWriter object
    fourcc = cv2.VideoWriter_fourcc(*'XVID')
    out = cv2.VideoWriter(f"{file_name}_raw.avi", fourcc, frame_rate, (640, 480))
    for frame in frames:
        out.write(frame)
    
    out.release()
    cv2.destroyAllWindows()
    
    print(f"{datetime.datetime.now().time()} Done recording video!")
    
    
def audio_subprocess(file_name, duration_seconds, audio_sample_rate, event_video_start, event_audio_start):
    event_video_start.wait()
    print(f"{datetime.datetime.now().time()} Received video start event!\n")
    audio_recording = sd.rec(int(duration_seconds*audio_sample_rate), samplerate=audio_sample_rate, channels=1)
    event_audio_start.set()
    print(f"{datetime.datetime.now().time()} Started recording audio!\n")
    sd.wait()
    print(f"{datetime.datetime.now().time()} Done recording audio!\n")
    write(f"{file_name}.wav", audio_sample_rate, audio_recording)
    print("Audio saved to disk!\n")


def video_capture(file_name='outputd', duration_seconds=30, frame_rate=10.0, audio_sample_rate=8000):
#     # Capture video from webcam
#     cap = cv2.VideoCapture(0)
# 
#     # Define the codec and create VideoWriter object
#     fourcc = cv2.VideoWriter_fourcc(*'XVID')
#     out = cv2.VideoWriter(f"{file_name}.avi", fourcc, frame_rate, (640, 480))
#     
#     audio_recording = sd.rec(int(duration_seconds*audio_sample_rate), samplerate=audio_sample_rate, channels=1)
#     
#     print("Started recording!")
#     for i in range(duration_seconds * int(frame_rate)):
#         # Capture frame-by-frame
#         ret, frame = cap.read()
# 
#         if ret == True:
#             # Write the frame into the file 'output.avi'
#             out.write(frame)
# 
#             # Display the resulting frame
#             #cv2.imshow('frame',frame)
#             #cv2.waitKey(1)
#         else:
#             break
#     
#     cap.release()
#     out.release()
#     cv2.destroyAllWindows()
#     
#     sd.wait()
#     print("Done recording!")
#     write(f"{file_name}.wav", audio_sample_rate, audio_recording)
    event_video_start = Event()
    event_audio_start = Event()

    process1 = Process(target=video_subprocess, args=(file_name, duration_seconds, frame_rate, event_video_start, event_audio_start))
    
    process1.start()
    
    audio_subprocess(file_name, duration_seconds, audio_sample_rate, event_video_start, event_audio_start)
    
    process1.join()
    
    videoclip = VideoFileClip(f"{file_name}_raw.avi")
    audioclip = AudioFileClip(f"{file_name}.wav")
    
    videoclip.audio = audioclip
    videoclip.write_videofile(f"storage/app/videos/{file_name}.mp4")
    os.remove(f"{file_name}_raw.avi")
    os.remove(f"{file_name}.wav")
    
    insert_recording_into_db(file_name + ".mp4", file_name + ".mp4")
    

def audio_read(duration_seconds, sample_rate=44100):    
    recording = sd.rec(int(duration_seconds*sample_rate), samplerate=sample_rate, channels=1)
    sd.wait()
    reading = np.max(recording)
    
    return reading
    

baseline = get_acceleration_module()

while True:
#while False:
    audio_peak = audio_read(0.2)
    acceleration_module = get_acceleration_module()
    delta = abs(baseline - acceleration_module)
    
    if audio_peak > 0.5 or delta > 0.2:
        print("Event detected!")
        
        video_capture("Evenimentul" + datetime.datetime.now().strftime("%Y-%m-%d-%H-%M-%S"))
        
        phone_nos = get_phone_numbers()
        for phone_no in phone_nos:
            send_SMS(message, phone_no)
        
        print("Resuming listening")
        exit()

#video_capture()
#insert_recording_into_db('test_title', 'test_path')
