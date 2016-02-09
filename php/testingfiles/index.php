<?php

/*  1) Script to upload and parse the CSV format, dedup against
 *  current database, generate a row and ticket ID for every valid entry.
 *  
 *  2) Generate ticket graphics and produce a PDF
 *
 *  3) Email PDF to everyone
*/

class CSVException extends Exception { }

function process_csv($csv_string) {
  // Process the CSV into an array of associative arrays, using
  // the first row headers as keys.
  $rows = array_map('str_getcsv', file($csv_string));

  // Ignore rows that are only one column (which the events plugin helpfully adds)
  do {
    $header = array_shift($rows);
  } while ((sizeof($header) > 1) or ($array[1] === ''));
  
  if (!(in_array('Name', $header, true) and
        in_array('Spaces', $header, true) and
        in_array('Email', $header, true) and
        in_array('Status', $header, true))) {
    throw new CSVException('Expected headers were not found');
  }

  $csv = array();
  foreach ($rows as $row) {
      $csv[] = array_combine($header, $row);
  }

  return $csv;
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


<html>
   <body>
      <h1>Upload CSV</h1>
      
      <form action="" method="POST" enctype="multipart/form-data">
         <input type="file" name="csv" accept=".csv" />
         <input type="submit"/>
      </form>
      
        <?php
        if (isset($_FILES['csv'])) {
          $errors = array();
          $file_ext = strtolower(end(explode('.', $_FILES['csv']['name'])));
          if ($file_ext !== 'csv') {
            $errors[] = "File extension must be CSV.";
          }

          if (empty($errors) == true) {
            try {
              $csv_data = process_csv($_FILES['csv']['tmp_name']);

              # Loop through each row, check if person is in `people` table

              # Status is 'Approved':
                # Person exists in `people` table:
                  # Check that person has correct number of seats (both rows and `tickets.seats`)
                  # Generate more if needed
                  # Email ticket PDF
                # ELSE person does not exist:
                  # Add to `people` table, generate ticket codes and row
                  # Email ticket PDF
              # Status is 'Cancelled':
                # Person exists in `people` table:
                  # Remove associated rows from `tickets`
                  # Remove row from `people`


            } catch (CSVException $e) {
              $errors[] = "Error while processing file: " . $e->getMessage();
            }
          }
        }

        ?>
   </body>
</html>
