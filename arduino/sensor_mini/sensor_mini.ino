0// กำหนดขาเชื่อมต่อ
const int waterLevelPin = A0; // ขาเชื่อมต่อของ Water Level Sensor
const int buzzerPin = 10;     // ขาเชื่อมต่อของ Buzzer
const int motorHighPin = 7;   // ขาเชื่อมต่อของ DC มอเตอร์ (มากกว่าเท่ากับ 80%)
const int motorLowPin = 6;    // ขาเชื่อมต่อของ DC มอเตอร์ (น้อยกว่าเท่ากับ 20%)

void setup() {
  pinMode(waterLevelPin, INPUT);  // กำหนดขา A0 เป็นขา Input
  pinMode(buzzerPin, OUTPUT);      // กำหนดขา 10 เป็นขา Output
  pinMode(motorHighPin, OUTPUT);   // กำหนดขา 7 เป็นขา Output
  pinMode(motorLowPin, OUTPUT);    // กำหนดขา 6 เป็นขา Output

  Serial.begin(9600);  // เริ่มต้น Serial communication ที่ 9600 bps
}

void loop() {
  int waterLevel = analogRead(waterLevelPin); // อ่านค่าจาก Water Level Sensor
  int waterPercentage = map(waterLevel, 0, 1023, 0, 100); // แปลงค่าให้อยู่ในช่วง 0-100%
  
  Serial.print("Water Level: ");
  Serial.print(waterPercentage);
  Serial.println("%");


  // ตรวจสอบระดับน้ำ
  if (waterPercentage >= 80) {
    digitalWrite(buzzerPin, LOW);    // เปิด Buzzer
    digitalWrite(motorHighPin, LOW);   // เปิด DC Motor สำหรับระดับน้ำสูง
    digitalWrite(motorLowPin, HIGH);   // ปิด DC Motor สำหรับระดับน้ำต่ำ
  } 
  else if (waterPercentage <= 20) {
    digitalWrite(buzzerPin, LOW);     // เปิด Buzzer
    digitalWrite(motorLowPin, LOW);    // ปิด DC Motor สำหรับระดับน้ำต่ำ
    digitalWrite(motorHighPin, HIGH);  // ปิด DC Motor สำหรับระดับน้ำสูง
  } 
  else {
    digitalWrite(buzzerPin, HIGH);      // ปิด Buzzer
    digitalWrite(motorHighPin, HIGH);  // ปิด DC Motor สำหรับระดับน้ำสูง
    digitalWrite(motorLowPin, HIGH);   // ปิด DC Motor สำหรับระดับน้ำต่ำ
  }

  delay(500); // หน่วงเวลา 0.5 วินาที
}
