<?php

/* gets the data from a URL */
function get_data($url) {
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}



$dml = new SimpleXMLElement(get_data('https://drexelforchrist.org' . $_SERVER['PATH_INFO'] . '.dml'));



try {
	if ($dml->document->title == "Free Hot Chocolate & Cookies") {
		header('Content-type: image/jpeg');
		echo file_get_contents('Cookies-opengraph.jpg');
		die();
	}
	if ($dml->document->title == "Alpha") {
		header('Content-type: image/jpeg');
		echo file_get_contents('alpha-opengraph.jpg');
		die();
	}
	if ($dml->document->title == "Valentine's Formal") {
		header('Content-type: image/jpeg');
		echo file_get_contents('Valentine-social.jpg');
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

if ($title == '') {
	$title = "Welcome";
	$subtitle = "";
}

//Set the Content Type
header('Content-type: image/jpeg');

// Create Image From Existing File
switch($template) {
	case 'church': // church
		$jpg_image = imagecreatefromjpeg('img-opengraph/churchDefaultOpengraph.jpg');
		break;
	case 'prayer': // prayer
		$jpg_image = imagecreatefromjpeg('img-opengraph/prayerDefaultOpengraph.jpg');
		break;
	case 'discipleship': // discipleship
		$jpg_image = imagecreatefromjpeg('img-opengraph/discipleshipDefaultOpengraph.jpg');
		break;
	case 'outreach': // discipleship
		$jpg_image = imagecreatefromjpeg('img-opengraph/outreachDefaultOpengraph.jpg');
		break;
	default:
		$jpg_image = imagecreatefromjpeg('img-opengraph/communityDefaultOpengraph.jpg');
}

// Allocate A Color For The Text
$white = imagecolorallocate($jpg_image, 255, 255, 255);

// Set Path to Font File
$font_path = 'fonts/Cabin-Regular-TTF.ttf';

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
imagejpeg($jpg_image, null, 100);

// Clear Memory
imagedestroy($jpg_image);
