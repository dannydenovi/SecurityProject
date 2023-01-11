from RPi.GPIO import setmode, setwarnings, setup, output, BCM, HIGH, LOW, OUT
from time import sleep
from smbus import SMBus
from mlx90614 import MLX90614

bus = SMBus(1)
sleep(2)
sensor = MLX90614(bus, address=0x5A)

setmode(BCM)
setwarnings(False)
setup(23, OUT)
for _ in range(10):
    print("LED on")
    output(23, HIGH)
    sleep(1)
    print("LED off")
    output(23, LOW)
    #print(sensor.get_amb_temp())
    #print(sensor.get_obj_temp())
    sleep(1)
    

bus.close()

