<?php

/*  1) Script to upload and parse the CSV format, dedup against
 *  current database, generate a row and ticket ID for every valid entry.
 *  
 *  2) Generate ticket graphics and produce a PDF
 *
 *  3) Email PDF to everyone
*/

function process_csv() {
  // Process the CSV into an array of associative arrays, using
  // the first row headers as keys.
  $rows = array_map('str_getcsv', file('myfile.csv'));
  $header = array_shift($rows);
  $csv = array();
  foreach ($rows as $row) {
      $csv[] = array_combine($header, $row);
  }
}

function generate_reg_code() {
    $i = 0;
    $lastvalue = '';
    $newstring = '';
    $lengthofstring = 12;
    while($i < $lengthofstring ) {
        $part = rand(1,4);
        switch($part) {
            case 1:
                $a=50;$b=57;      // Numbers 2-9
                break;
            case 2:
                $a=65;$b=72;      // Uppercase Letters A-H
                break;
            case 3:
                $a=74;$b=78;      // Uppercase Letters J-N (disallow I and O)
                break;
            case 4:         
                $a=80;$b=90;      // Uppercase letters P-Z
                break;
        }
        
        $value=rand($a,$b);
        while($value==$lastvalue){    // Disallow repeating characters
            $value=rand($a,$b);
        }
        $code_part=chr($value);
        $lastvalue=$value;
        
        $i++;
        $newstring = $newstring.$code_part;
        if(strlen($newstring) === 4) {
            $newstring = $newstring."-";
        } elseif(strlen($newstring) === 9) {
            $newstring = $newstring."-";
        }
    }
    return $newstring;
}


?>
