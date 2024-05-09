<?php
session_name("sessionName");
session_start();
require("cfd.php");

if($_GET['key'] == "unique key goes here"){
//get date, time and type of upcoming LAN party
$sql = "SELECT date, time, type from events 
WHERE type LIKE '%LAN%' AND date > CURRENT_DATE
OR (
    date = CURRENT_DATE
    AND 
    time > CURRENT_TIME
)
ORDER BY date ASC, time ASC LIMIT 1;";
//get rows
$res = mysqli_query($db, $sql) or die (mysqli_error($db));
while($row = mysqli_fetch_assoc($res)){
    $d = $row['date'];
    $ti = $row['time'];
    $type = $row['type'];
}
echo $d;
if(!empty($d)){
//convert date and time to a different format
 $da = new DateTime($d);
 $date = $da ->format('m/d/Y');
 $time = date("g:i a", strtotime($ti));

//get day left until next lan
 $now = date("Y/m/d");
 $Eventdate = strtotime($d);
 $currdate = strtotime($now);
 $diff = $Eventdate - $currdate;
 echo "Difference:", $diff;
 
 $days = floor($diff / (60 * 60 * 24));
 echo "days: ", $days;
     if($days > 1){
        $countdown = $days;
        $countdown .= " days left until the ";
    }else if($days == 1){
        $countdown = $days;
        $countdown .= " day left until the ";
    }else{
        //$countdown = $days + " day left";
        $countdown = "Today is the !";
    }
//get a quote for the message
$sql1 = "SELECT eqid, quote, CURDATE() from events_quotes WHERE type = 'LAN' AND used = '0' AND archive = '0' ORDER BY RAND() LIMIT 1;";
$res1 = mysqli_query($db, $sql1) or die (mysqli_error($db));
while($row1 = mysqli_fetch_assoc($res1)){
    $getDate = $row1['CURDATE()'];
    $quote = $row1['quote'];
    $mark = $row1['eqid'];
}

//get description
$s = "SELECT t.description from events_type t INNER JOIN events e ON t.type = e.type where t.type = '$type';";
$r = mysqli_query($db, $s) or die (mysqli_error($db));
while($ro = mysqli_fetch_assoc($r)){
$desc = $ro['description'];
}


function discordmsg($msg, $webhook) {
  if($webhook != "") {
    $ch = curl_init($webhook);
    $msg = "payload_json=" . urlencode(json_encode($msg))."";
    
    if(isset($ch)) {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $result = curl_exec($ch);
      curl_close($ch);
      return $result;
    }
  }
}
// //random image
// $images = array ("gaming2.jpg","gaming.jpg");
// $index = rand(0,1);
// $img = $images[$index];

//random color
$digits = 8;
$c = (rand(0, 16777215));
$color = str_pad($c, $digits, '0', STR_PAD_LEFT);

//"thumbnail": {
//        "url": "https://hawkeyegamersclub.org/rachel/dnd.jpg"
//        },
//        "image": {
//        "url": "https://hawkeyegamersclub.org/rachel/dnd.jpg"
//         },
//change the color by using this site https://convertingcolors.com/decimal-color-15022871.html
// URL FROM DISCORD WEBHOOK SETUP


$webhook = "webhook goes here";
// this is your webhook url
//you can add url of other discord channel by right-clicking on the channel you want to reference
// this uses decimal color code
    $msg = json_decode('
{
  "username": "HGC",
  "embeds": [
    {
      "author": {
        "name": "'.$countdown.''.$type.' Party!",
        "url": "link to another channel goes here"
      },
      "title": "Join us on, '.$date.' at '.$time.' for the '.$type .' Party!",
        "url": "link to another channel goes here",
        "color": "'.$color.'",

      "fields": [
        {
          "name": "",
          "value": "'.$desc.'",
          "inline": false
          
        },
        {
          "name": "",
          "value": "'.$quote.'",
          "inline": false
        }
      ]
    }
  ]
} 
', true);
// //mark quote as used
// $sql2 = "UPDATE events_quotes SET used = 1, usedDate = '$getDate' WHERE eqid = $mark;";
// $res2 = mysqli_query($db, $sql2) or die (mysqli_error($db));

// // //check if any quotes should be unmarked as used
// $sql4 = "SELECT * from events_quotes WHERE usedDate <= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND usedDate != '0000-00-00 00:00:00';";
// $res4 = mysqli_query($db, $sql4) or die (mysqli_error($db));
// $row4 = mysqli_fetch_assoc($res4);
// //$row4['eqid'];
// if(!empty($row4)){
//     $unmark = $row4['eqid'];
//     //unmark used after 7 days
// $sql3 = "UPDATE events_quotes SET used = 0, usedDate = '0000-00-00 00:00:00' WHERE eqid = $unmark;";
// $res3 = mysqli_query($db, $sql3) or die (mysqli_error($db));
// }

print_r($msg);
// SENDS MESSAGE TO DISCORD
discordmsg($msg, $webhook);

}else{
    echo "No upcoming LAN events";
}
}
//Resources
//https://www.w3schools.com/howto/howto_js_countdown.asp//the countdown 
//https://www.gaisciochmagazine.com/articles/posting_to_discord_using_php_and_webhooks.html//webhooks
//https://stackoverflow.com/questions/17850967/run-a-curl-command-using-cron-jobs//curl command
//https://blog.cpanel.com/how-to-configure-a-cron-job/// cron jobs

/*
App: HGC
Author: Rachel Sokol
Description: This page sends a message to the HGC discord channel with information about the upcoming LAN party.
Editor: NA
Date Edited: 04/18/24
Last made Changes: NA
*/
?>
