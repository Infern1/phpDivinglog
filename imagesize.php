<?php
header ("Content-type: image/jpeg");
/*
JPEG / PNG Image Resizer
Parameters (passed via URL):

img = path / url of jpeg or png image file

percent = if this is defined, image is resized by it's
          value in percent (i.e. 50 to divide by 50 percent)

w = image width

h = image height

constrain = if this is parameter is passed and w and h are set
            to a size value then the size of the resulting image
            is constrained by whichever dimension is smaller

Requires the PHP GD Extension

Outputs the resulting image in JPEG Format

By: Michael John G. Lopez - www.sydel.net
Filename : imgsize.php
*/

$img = $_GET['img'];
$percent = $_GET['percent'];
$constrain = $_GET['constrain'];
$w = $_GET['w'];
$h = $_GET['h'];

// get image size of img
$x = @getimagesize($img);
// image width
$sw = $x[0];
// image height
$sh = $x[1];

if ($percent > 0) {
    // calculate resized height and width if percent is defined
    $percent = $percent * 0.01;
    $w = $sw * $percent;
    $h = $sh * $percent;
} else {
    if (isset ($w) AND !isset ($h)) {
        // autocompute height if only width is set
        $h = (100 / ($sw / $w)) * .01;
        $h = @round ($sh * $h);
    } elseif (isset ($h) AND !isset ($w)) {
        // autocompute width if only height is set
        $w = (100 / ($sh / $h)) * .01;
        $w = @round ($sw * $w);
    } elseif (isset ($h) AND isset ($w) AND isset ($constrain)) {
        // get the smaller resulting image dimension if both height
        // and width are set and $constrain is also set
        $hx = (100 / ($sw / $w)) * .01;
        $hx = @round ($sh * $hx);

        $wx = (100 / ($sh / $h)) * .01;
        $wx = @round ($sw * $wx);

        if ($hx < $h) {
            $h = (100 / ($sw / $w)) * .01;
            $h = @round ($sh * $h);
        } else {
            $w = (100 / ($sh / $h)) * .01;
            $w = @round ($sw * $w);
        }
    }
}

$im = @ImageCreateFromJPEG ($img) or // Read JPEG Image
$im = @ImageCreateFromPNG ($img) or // or PNG Image
$im = @ImageCreateFromGIF ($img) or // or GIF Image
$im = false; // If image is not JPEG, PNG, or GIF

if (!$im) {
    // We get errors from PHP's ImageCreate functions...
    // So let's echo back the contents of the actual image.
    readfile ($img);
} else {
    // Create the resized image destination
    $thumb = @ImageCreateTrueColor ($w, $h);
    // Copy from image source, resize it, and paste to image destination
    @ImageCopyResampled ($thumb, $im, 0, 0, 0, 0, $w, $h, $sw, $sh);
    // Output resized image
    @ImageJPEG ($thumb);
}
?>


