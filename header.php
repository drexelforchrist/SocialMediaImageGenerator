<?php
  //Set the Content Type
  header('Content-type: image/jpeg');

  // Create Image From Existing File
  $jpg_image = imagecreatefromjpeg('communityDefaultBackground.jpg');

  // Allocate A Color For The Text
  $white = imagecolorallocate($jpg_image, 255, 255, 255);

  // Set Path to Font File
  $font_path = 'Cabin-Regular-TTF.ttf';

  // Set Text to Be Printed On Image
  $text = $_GET['title'];

  // Print Text On Image
  imagettftext($jpg_image, 55, 0, 30, 100, $white, $font_path, $text);


 if (isset($_GET['subtitle']) && $_GET['subtitle']!="") {

  // Set Text to Be Printed On Image
  $text = $_GET['subtitle'];

  // Print Text On Image
  imagettftext($jpg_image, 30, 0, 30, 150, $white, $font_path, $text);

}

  // Send Image to Browser
  imagejpeg($jpg_image);

  // Clear Memory
  imagedestroy($jpg_image);
?>