import os
import time
import sys
import paho.mqtt.client as mqtt
import json
import hashlib
from sense_hat import SenseHat
from datetime import datetime

sense = SenseHat()

HOST = 'test.mosquitto.org'

# Data capture and upload interval in seconds. Less interval will eventually hang the DHT22.
INTERVAL=5

sensor_data = {'device_id' : 0,'sensors':{'temperature': 0, 'humidity': 0, 'pressure' : 0, 'datetime' : 0}}

next_reading = time.time() 

client = mqtt.Client()
now = time.strftime('%Y-%m-%d %H:%M:%S')
date = time.strftime('%Y-%m-%d')
device_id = '1'

# Set access token

# Connect to ThingsBoard using default MQTT port and 60 seconds keepalive interval
client.connect(HOST, 1883, 60)

client.loop_start()

try:
    while True:
        temperature = sense.get_temperature()
        humidity = sense.get_humidity()
        pressure = sense.get_pressure()
        humidity = round(humidity, 2)
        temperature = round(temperature, 2)
        pressure = round(pressure, 2)
        sensor_data['sensors']['temperature'] = temperature
        sensor_data['sensors']['humidity'] = humidity
        sensor_data['sensors']['pressure'] = pressure
        sensor_data['sensors']['datetime'] = now
        sensor_data['device_id'] = device_id


        # Sending humidity and temperature data to ThingsBoard
        client.publish('umcomp2', json.dumps(sensor_data), 1)
        next_reading += INTERVAL
        sleep_time = next_reading-time.time()
        if sleep_time > 0:
            time.sleep(sleep_time)
except KeyboardInterrupt:
    pass

client.loop_stop()
client.disconnect()

