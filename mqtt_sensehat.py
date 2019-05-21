import os
import time
import sys
import paho.mqtt.client as mqtt
import json
from sense_hat import SenseHat

sense = SenseHat()

MOSQUITTO_HOST = 'test.mosquitto.org'

# Data capture and upload interval in seconds. Less interval will eventually hang the DHT22.
INTERVAL=

sensor_data = {'temperature': 0, 'humidity': 0, 'pressure' : 0}

next_reading = time.time() 

client = mqtt.Client()

# Set access token

# Connect to ThingsBoard using default MQTT port and 60 seconds keepalive interval
client.connect(MOSQUITTO_HOST, 1883, 60)

client.loop_start()

try:
    while True:
        temperature = sense.get_temperature()
        humidity = sense.get_humidity()
        pressure = sense.get_pressure()
        humidity = round(humidity, 2)
        temperature = round(temperature, 2)
        pressure = round(pressure, 2)
        print(u"Temperature: {:g}\u00b0C, Humidity: {:g}%, Pressure: {:g}%".format(temperature, humidity, pressure))
        sensor_data['temperature'] = temperature
        sensor_data['humidity'] = humidity
        sensor_data['pressure'] = pressure

        # Sending humidity and temperature data to ThingsBoard
        client.publish('juanbrz92/devices/001', json.dumps(sensor_data), 1)
        next_reading += INTERVAL
        sleep_time = next_reading-time.time()
        if sleep_time > 0:
            time.sleep(sleep_time)
except KeyboardInterrupt:
    pass

client.loop_stop()
client.disconnect()


