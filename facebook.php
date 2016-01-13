<?php

$dml = new SimpleXMLElement(file_get_contents('http://dev.drexelforchrist.org/'.$_SERVER['PATH_INFO'].'.dml'));


try {
	if ($dml->document->title = "Free Hot Chocolate &amp; Cookies") {
		header('Content-type: image/jpeg');
		$jpg_image = imagecreatefromjpeg('Cookies-opengraph.jpg');

		// Send Image to Browser
		imagejpeg($jpg_image);

		// Clear Memory
		imagedestroy($jpg_image);
		die();
	}
} catch (Exception $e) {
	// cool.
}


$title = $dml->document->title;

try {
	$subtitle = $dml->document->subtitle;
} catch (Exception $e) {
	$subtitle = null;
}

try {
	$template = $dml->document->template->name;
} catch (Exception $e) {
	$template = null;
}

//Set the Content Type
header('Content-type: image/jpeg');

// Create Image From Existing File
switch($template) {
	case 'church': // church
		$jpg_image = imagecreatefromjpeg('churchDefaultOpengraph.jpg');
		break;
	case 'prayer': // prayer
		$jpg_image = imagecreatefromjpeg('prayerDefaultOpengraph.jpg');
		break;
	case 'discipleship': // discipleship
		$jpg_image = imagecreatefromjpeg('discipleshipDefaultOpengraph.jpg');
		break;
	default:
		$jpg_image = imagecreatefromjpeg('communityDefaultOpengraph.jpg');
}

// Allocate A Color For The Text
$white = imagecolorallocate($jpg_image, 255, 255, 255);

// Set Path to Font File
$font_path = 'Cabin-Regular-TTF.ttf';

// Print Text On Image

$size = 100;
do {
	$corners = imagettfbbox ($size, 0, $font_path, $title);
} while ($corners[4] > 1080 && $size--);

imagettftext($jpg_image, $size, 0, 30, 180, $white, $font_path, $title);


// subtitle, too.
 if ($subtitle != null) {

	 $size = min(45, $size); // subtitle shouldn't be bigger than the title
	 do {
		 $corners = imagettfbbox ($size, 0, $font_path, $subtitle);
	 } while ($corners[4] > 1080 && $size--);

	// Print Text On Image
	imagettftext($jpg_image, $size, 0, 30, 270, $white, $font_path, $subtitle);
}

// Send Image to Browser
imagejpeg($jpg_image);

// Clear Memory
imagedestroy($jpg_image);
