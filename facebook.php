<?php

// Open Database Connection
$db = new PDO("mysql:host=brilliant-core.c4z81yuf0edn.us-east-1.rds.amazonaws.com:3306;dbname=drexelforchrist;charset=utf8","drexelforc_rApp","jaAMamGFKr8zpvY5");

// Query the database
$stmt = $db->prepare("SELECT name, subtitle, parent, calendarID FROM drexelforchrist.events WHERE eventId=?");
$stmt->execute(array(intval($_GET['eid'])));
$eventInfo = $stmt->fetch(PDO::FETCH_ASSOC);

$lastEid = intval($_GET['eid']);

while(($eventInfo['name']==null || $eventInfo['subtitle']==null) && $lastEid != $eventInfo['calendarID']) {

	$stmt->execute(array(intval($eventInfo['parent'])));
	$lastEid = intval($eventInfo['parent']);
	$parentInfo = $stmt->fetch(PDO::FETCH_ASSOC);
	$eventInfo['name'] = $eventInfo['name'] ?: $parentInfo['name'];
	$eventInfo['subtitle'] = $eventInfo['subtitle'] ?: $parentInfo['subtitle'];

	$eventInfo['parent'] = $parentInfo['parent'];
}

//Set the Content Type
header('Content-type: image/jpeg');
header('x-event-name: ' . $eventInfo['calendarID']);

// Create Image From Existing File
switch($eventInfo['calendarID']) {
	case 2: // church
		$jpg_image = imagecreatefromjpeg('churchDefaultOpengraph.jpg');
		break;
	case 3: // prayer
		$jpg_image = imagecreatefromjpeg('prayerDefaultOpengraph.jpg');
		break;
	default:
		$jpg_image = imagecreatefromjpeg('communityDefaultOpengraph.jpg');
}

// Allocate A Color For The Text
$white = imagecolorallocate($jpg_image, 255, 255, 255);

// Set Path to Font File
$font_path = 'Cabin-Regular-TTF.ttf';

// Set Text to Be Printed On Image
$text = $eventInfo['name'];

// Print Text On Image
imagettftext($jpg_image, 100, 0, 30, 180, $white, $font_path, $text);

// subtitle, too.
 if (isset($eventInfo['subtitle']) && $eventInfo['subtitle']!="") {

	// Set Text to Be Printed On Image
	$text = $eventInfo['subtitle'];

	// Print Text On Image
	imagettftext($jpg_image, 45, 0, 30, 270, $white, $font_path, $text);

}

// Send Image to Browser
imagejpeg($jpg_image);

// Clear Memory
imagedestroy($jpg_image);
