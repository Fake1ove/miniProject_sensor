from flask import Flask
import requests
import serial
import time

app = Flask(__name__)

# ตั้งค่าการเชื่อมต่อกับ LINE Notify
LINE_NOTIFY_TOKEN = "UnameKLSPojc4NEmaeot8Wu5oBF5HFk6C6Ec917dlDj"  # เปลี่ยนเป็น Token ของคุณ
LINE_NOTIFY_URL = "https://notify-api.line.me/api/notify"

def send_line_notify(message):
    headers = {
        "Authorization": "Bearer " + LINE_NOTIFY_TOKEN
    }
    data = {
        "message": message
    }
    response = requests.post(LINE_NOTIFY_URL, headers=headers, data=data)
    print(f"Sending message: {message}")  # แสดงข้อความที่ส่ง
    if response.status_code != 200:
        print(f"Error sending notification: {response.text}")
    else:
        print("Notification sent successfully!")

def read_water_level():
    # เปลี่ยน 'COM2' เป็น port ที่ใช้เชื่อมต่อเซ็นเซอร์ของคุณ
    ser = serial.Serial('COM2', 9600)
    time.sleep(2)  # รอให้เซ็นเซอร์เริ่มทำงาน
    level = ser.readline().decode().strip()
    ser.close()

    print(f"Raw water level reading: {level}")  # แสดงข้อมูลดิบจากเซ็นเซอร์
    
    # ตรวจสอบข้อความที่ได้รับและแปลงเป็นค่าระดับน้ำ
    if 'Water Level:' in level:
        try:
            level = level.split(': ')[1].replace('%', '')
            level = int(level)
            print(f"Processed water level: {level}%")  # แสดงค่าระดับน้ำที่ประมวลผลแล้ว
            return level
        except ValueError:
            print("Error: Could not convert water level to integer.")
            return 0
    else:
        print("Invalid water level reading format.")
        return 0
    
@app.route('/')
def index():
    return "Hello LINE"

@app.route('/check_level')
def check_level():
    water_level = read_water_level()
    print(f"Current water level: {water_level}%")

    # ตรวจสอบเงื่อนไขและแจ้งเตือน
    if water_level > 80:
        message = f"ระดับน้ำสูงเกิน 80% \n ขณะนี้อยู่ที่ {water_level}% กำลังทำการปั้มน้ำออก"
        print("Water level is greater than 80%, sending notification...")
        send_line_notify(message)

    elif water_level < 20:
        message = f"ระดับน้ำต่ำกว่า 20% \n ขณะนี้อยู่ที่ {water_level}% กำลังทำการปั้มน้ำเข้า"
        print("Water level is less than 20%, sending notification...")
        send_line_notify(message)
    
    return f"Current water level: {water_level}%"

if __name__ == '__main__':
    app.run(debug=True, port=5000)  # เปลี่ยนพอร์ตที่เซิร์ฟเวอร์ทำงาน
