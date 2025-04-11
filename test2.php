
from flask import Flask, request
from linebot import LineBotApi
from linebot.models import TextSendMessage as send_text
import serial  # ใช้สำหรับเชื่อมต่อกับ Arduino

app = Flask(__name__)

# LINE Bot Channel Access Token
channel_access_token = "QihOF5p9Nw0Vg8HoQQObpfYMURvRz4iESA+5777HWge/ZjrOjewwqcwzVLNd6V0xj6mpaCQxC4obH5dtedw0BayFv7FdggSeKvhjtEwE1raSmp2Tp5MI4HQwp+cuIjmdrnishP2F+8U9+ul5eyro7AdB04t89/1O/w1cDnyilFU="
line_bot_api = LineBotApi(channel_access_token)

# กำหนด Serial Port สำหรับเชื่อมต่อกับ Arduino
ser = serial.Serial('COM2', 9600)  # เปลี่ยน 'COM_PORT' เป็น port ที่เชื่อมต่อกับ Arduino

# ตัวแปรสำหรับการเก็บระดับน้ำ
previous_water_level = 0  # เก็บระดับน้ำก่อนหน้า

@app.route("/", methods=["GET", "POST"])
def home():
    global previous_water_level
    # อ่านค่าจาก Arduino
    if ser.in_waiting > 0:
        line = ser.readline().decode('utf-8').strip()  # อ่านข้อมูลจาก Serial
        if "Water Level: " in line:
            water_level = int(line.split(': ')[1].replace('%', ''))  # ดึงค่าระดับน้ำ
            print(f"Water Level: {water_level}%")  # แสดงค่าระดับน้ำใน Console

            # เช็คว่าระดับน้ำถึง 80% หรือไม่
            if water_level >= 80 and previous_water_level < 80:  # แจ้งเตือนเมื่อระดับน้ำขึ้นจากต่ำกว่า 80%
                line_bot_api.push_message('U153cf03cf9aeb0409eced71a1b711669', send_text(text="⚠️ ระดับน้ำสูงถึง 80% แล้ว! โปรดตรวจสอบ!"))
                
            previous_water_level = water_level  # อัพเดทค่าระดับน้ำก่อนหน้า

    return "OK"

if __name__ == "__main__":
    app.run()
