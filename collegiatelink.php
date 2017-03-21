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
// Presently, these are all the wrong sizes for DragonLink, but the code may be useful in the future...
//	if ($dml->document->title == "Free Hot Chocolate & Cookies") {
//		header('Content-type: image/jpeg');
//		echo file_get_contents('Cookies-opengraph.jpg');
//		die();
//	}
//	if ($dml->document->title == "Alpha") {
//		header('Content-type: image/jpeg');
//		echo file_get_contents('alpha-opengraph.jpg');
//		die();
//	}
//	if ($dml->document->title == "Valentine's Formal") {
//		header('Content-type: image/jpeg');
//		echo file_get_contents('Valentine-social.jpg');
//		die();
//	}
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

// Create Image From Existing File
switch($template) {
	case 'church':
		$image = imagecreatefrompng('img-clink/church.png');
		break;
	case 'discipleship':
		$image = imagecreatefrompng('img-clink/discipleship.png');
		break;
	default:
		$image = imagecreatefromjpeg('img-opengraph/communityDefaultOpengraph.jpg');
}

// Allocate A Color For The Text
$white = imagecolorallocate($image, 255, 255, 255);

// Set Path to Font Files
$font_book = 'fonts/novecentowide-book-TTF.ttf';
$font_bold = 'fonts/novecentowide-bold-TTF.ttf';

// Print Text On Image
if ($subtitle != null && $subtitle != '') {
	$size = 120;
	do {
		$corners = imagettfbbox($size, 0, $font_bold, $title);
	} while ($corners[4] > 1300 && $size--);

	imagettftext($image, $size, 0, 1985, 1140, $white, $font_bold, $title);

	// subtitle, too
	do {
		$corners = imagettfbbox($size, 0, $font_book, $subtitle);
	} while ($corners[4] > 1300 && $size--);
	imagettftext($image, $size, 0, 1985, 1170 + $size, $white, $font_book, $subtitle);

} else {

	// attempt to divide string in half-ish if there's a break there.
	$length = strlen($title);
	$space = 0;
	for ($front = (int)ceil($length/2); $front > 0; $front--) {
		if (substr($title,-$front,1) == ' ') {
			$space = $length-$front;
			break;
		}
		if ($title[$front] == ' ') {
			$space = $front;
			break;
		}
	}
	if ($space != 0) {
		$titleAr[0] = substr($title, 0, $space);
		$titleAr[1] = substr($title, $space+1);
		$title = $titleAr[0];

		$size = 120;
		do {
			$corners = imagettfbbox($size, 0, $font_bold, $titleAr[0]);
		} while ($corners[4] > 1300 && $size--);
		do {
			$corners = imagettfbbox($size, 0, $font_bold, $titleAr[1]);
		} while ($corners[4] > 1300 && $size--);

		imagettftext($image, $size, 0, 1985, 1140, $white, $font_bold, $titleAr[0]);
		imagettftext($image, $size, 0, 1985, 1170+$size, $white, $font_bold, $titleAr[1]);

	} else {
		$size = 190;
		do {
			$corners = imagettfbbox($size, 0, $font_bold, $title);
		} while ($corners[4] > 1300 && $size--);

		imagettftext($image, $size, 0, 1985, 1140+($size/2), $white, $font_bold, $title);
	}
}



// Send Image to Browser
header('Content-type: image/jpeg');
imagejpeg($image, null, 100);

// Clear Memory
imagedestroy($image);
