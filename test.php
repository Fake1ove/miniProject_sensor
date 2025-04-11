<?php
include '_connect_line.php';
include 'Project_Data_cc.php';
include 'Project_Data_to.php';
include 'sendemail/sendEmail.php';;

function replyMessage($replyToken, $messageText)
{
    global $channel_access_token;
    $response = [
        'replyToken' => $replyToken,
        'messages' => [
            ['type' => 'text', 'text' => $messageText]
        ]
    ];

    $ch = curl_init('https://api.line.me/v2/bot/message/reply');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $channel_access_token
    ]);
    curl_exec($ch);
    curl_close($ch);
}

$request = file_get_contents('php://input');
$data = json_decode($request, true);

if (isset($data['events'])) {
    foreach ($data['events'] as $event) {
        if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
            $replyToken = $event['replyToken'];
            $Text_message = $event['message']['text'];

            if (substr($Text_message, 0, 9) == 'NC online') {
                $lines = explode("\n", $Text_message);
                foreach ($lines as $line) {
                    if (trim($line) === '') {
                        continue;
                    }

                    list($key, $value) = explode(" : ", $line);
                    $key = trim($key);
                    $value = trim($value);

                    if ($key == 'สาขาผู้รับ NC') {
                        $site = $value;
                    } elseif ($key == 'แผนกผู้รับ NC') {
                        $department = $value;
                    } elseif ($key == 'สรุปปัญหา/เหตุการณ์โดยย่อ') {
                        $problem = $value;
                    } elseif ($key == 'id') {
                        $id = $value;
                    }
                }
               
                         if ($site === null || $department === null || $problem === null || $id === null) {
                                replyMessage($replyToken, "การส่งเมลล์ล้มเหลว");

                         } else {$getData_to = Getdata_to();
                                $getDataArray_to = json_decode($getData_to, true);
                
                                $Datafilter = array();
                             foreach ($getDataArray_to as $data) {
                                if ($data['ฝ่าย/สังกัด'] == $site && $data['แผนก'] == $department) {
                                    array_push($Datafilter, $data['Email']);
                                }
                            }
                             foreach ($getDataArray_to as $type) {
                                if ($type['ฝ่าย/สังกัด'] == $site && $type['ตำแหน่ง'] == 'ผู้จัดการ') {
                                    array_push($Datafilter, $type['Email']);
                                }
                            }
                                
                            $getData_cc = Getdata_cc();
                            $getDataArray_cc = json_decode($getData_cc, true);
                
                            $email_cc = [];
                             foreach ($getDataArray_cc as $item) {
                                if (isset($item['Email'])) {
                                   $email_cc[] = $item['Email'];
                                }
                            }
                                
                                $url = "https://www.youtube.com/watch?v=r817RLqmLac";
                
                                $dataMail = [
                                    'from_name' => 'NC Online',
                                    'mail_to' => $Datafilter,
                                    'subject' => 'NC Online',
                                    'body' =>   'สรุปปัญหา/เหตุการณ์โดยย่อ : ' . $problem . '<br>' .
                                                'ID : '  . $id . '<br>' .
                                                'สาขาผู้รับ NC : '  . $site . '<br>' .
                                                'แผนกผู้รับ NC : ' .$department . '<br>' . 
                                                'ลิ้งค์สำหรับการแก้ไข : ' . $url,
                                    'mail_cc' => $email_cc 
                                ];
                
                                $sendResult = sendEmail($dataMail);
                
                                if ($sendResult) {
                                    replyMessage($replyToken, "ส่งเมลล์เรียบร้อย");
            
                        } 
                    }
                } 
            }
        }
    }

http_response_code(200);
echo 'OK';
?>