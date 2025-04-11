import requests

def gettext(message):
    # URL สำหรับส่งข้อความไปยัง LINE Notify
    url = "https://notify-api.line.me/api/notify"

    # ใส่ Access Token ของคุณ
    token = "XNncOAYDw6TuwOfeQLysSTzngpigVOGEjjlmy5bS6Ew"

    # กำหนด headers สำหรับการร้องขอ
    headers = {
        "Authorization": f"Bearer {token}"
    }

    # กำหนด payload สำหรับการส่งข้อความ
    payload = {
        "message": message
    }

    # ส่ง POST request ไปยัง LINE Notify
    response = requests.post(url, headers=headers, data=payload)

    # ตรวจสอบสถานะการส่งข้อความ
    if response.status_code == 200:
        print("ส่งข้อความสำเร็จ!")
    else:
        print("เกิดข้อผิดพลาดในการส่งข้อความ:", response.status_code, response.text)
