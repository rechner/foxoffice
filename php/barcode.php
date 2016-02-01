<?php

function generate_ticket($name, $code, $current=1, $seats=1, $outfile='') {
    $name = stripcslashes($name);

    $canvas = imagecreatetruecolor(900, 450);
    imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));

    $qrcode = imagecreatefrompng("http://www.barcodes4.me/barcode/qr/file.png?value=$code&eccLevel=3&size=8");
    $barcode = imagecreatefrompng("http://www.barcodes4.me/barcode/c128b/$code.png?width=450&height=100&IsTextDrawn=1");

    $novafurs = imagecreatefrompng('images/novafurs.png');
    $mdfurs = imagecreatefromgif('images/marylandfurs.gif');

    # Draw background image 1.png - 8.png
    $bg_id = rand(1, 8);
    $background = imagecreatefrompng("images/backgrounds/$bg_id.png");
    imagecopymerge($canvas, $background, 0, 0, 0, 0, 900, 450, 40);

    $barcode = imagerotate($barcode, 90, 0);
    imagecopy($canvas, $qrcode, 550, 125, 0, 0, 200, 200);
    imagecopy($canvas, $barcode, 800, 0, 0, 0, 100, 450);

    imagecopyresized($canvas, $novafurs, 120, 10, 0, 0, 192, 100, 373, 194);
    imagecopy($canvas, $mdfurs, 330, 10, 0, 0, 100, 99);

    $code_formatted = substr($code, 0, 4) . '-' . substr($code, 4, 4) . '-' . substr($code, 8, 4);
    if ($seats > 1) {
        $seats_message = "$current of $seats Seats";
    } else {
        $seats_message = "$seats Seat";
    }

    $grey = imagecolorallocate($canvas, 80, 80, 80);
    imagettftext($canvas, 40, 0, 40, 400, 0, 'fonts/FreeMono.otf', $code_formatted);
    imagettftext($canvas, 35, 0, 40, 170, $grey, 'fonts/FreeSansBold.otf', 'Zootopia');
    imagettftext($canvas, 18, 0, 270, 150, $grey, 'fonts/FreeSans.otf', 'March 5, 2016');
    imagettftext($canvas, 18, 0, 270, 170, $grey, 'fonts/FreeSans.otf', '11:00');
    imagettftext($canvas, 18, 0, 40, 240, 0, 'fonts/FreeSansBold.otf', "$name\n$seats_message");
    imagettftext($canvas, 12, 0, 40, 430, 0, 'fonts/FreeMono.otf', 'Generated ' . date(DateTime::ISO8601));

    #if ($outfile !== '') {
    #  imagepng($canvas, $outfile);
    #}

    return $canvas;
}

/*
# Are we running from the command line?
if (php_sapi_name() === 'cli' OR defined('STDIN')) {
  if ($argc == 6) {
      generate_ticket($argv[1], $argv[2], $argv[3], $argv[4], $argv[5]);
  } else {
      echo "Usage: $argv[0] <name> <code> <seat of> <total seats> <output file>\n\n";
  }
} else {
  # Take arguments as GET parameters and return an image
  $ticket = generate_ticket($_GET['name'], $_GET['code'], $_GET['current'], $_GET['seats'], '');

  header("Content-type: image/png");
  imagepng($ticket);
}
 */

?>
