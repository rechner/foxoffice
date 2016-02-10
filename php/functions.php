<?php
define('SALT_LENGTH', 32);
define('TOTAL_SEATS', 151);

function sec_session_start() {
        $session_name = 'sec_session_id'; // Set a custom session name
        $secure = false; // Set to true if using https.
        $httponly = true; // This stops javascript being able to access the session id.

        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies.
        $cookieParams = session_get_cookie_params(); // Gets current cookies params.
        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
        session_name($session_name); // Sets the session name to the one set above.
        session_start(); // Start the php session
        session_regenerate_id(true); // regenerated the session, delete the old one.
}

function generate_hash($password, $salt = null) {
    if ($salt === null) {                                               //if there is no salt argument
        $salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);      //generate a new salt
    } else {                                                            //otherwise
        $salt = substr($salt, 0, SALT_LENGTH);                          //use the salt provided
    }
    return $salt . hash('sha512', $salt . $password);                   //return the sha512'd password appended to the end of the hash
}

function login($username, $password, $pdo) {
    if ($query = $pdo->prepare("SELECT uid, username, password FROM accounts WHERE username = ? LIMIT 1")) {
        $query->bindValue(1, $username); // Bind "$username" to parameter.

        $query->execute(); // Execute the prepared query.
        $result = $query->fetch();

        $user_id = $result['uid'];
        $username = $result['username'];
        $storedpass = $result['password'];

        $storedsalt = substr($storedpass, 0, 32); // break salt from stored hash

        $password = generate_hash($password, $storedsalt); // hash the attempted password with the unique salt from database.

        if($result) { // If the user exists
            if($storedpass == $password) { // Check if the password in the database matches the password the user submitted.
                // Password is correct!
                $ip_address = $_SERVER['REMOTE_ADDR']; // Get the IP address of the user.
                $user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.

                $user_id = preg_replace("/[^0-9]+/", "", $user_id); // XSS protection as we might print this value
                $_SESSION['user_id'] = $user_id;
                $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username); // XSS protection as we might print this value
                $_SESSION['username'] = $username;
                $_SESSION['login_string'] = hash('sha512', $password.$ip_address.$user_browser);
                // Login successful.
                return true;
            }
        }
    } else {
        // No user exists.
        return false;
    }
}

function login_check($pdo) {
    // Check if all session variables are set
    if(isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];
        $ip_address = $_SERVER['REMOTE_ADDR']; // Get the IP address of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.

        if($query = $pdo->prepare("SELECT password FROM accounts WHERE uid = ? LIMIT 1")) {
            $query->bindValue(1, $user_id); // Bind "$user_id" to parameter.
            $query->execute(); // Execute the prepared query.
            $result = $query->fetch();

            if($result) { // If the user exists
                $password = $result['password']; // get variables from result.
                $login_check = hash('sha512', $password.$ip_address.$user_browser);
                if($login_check == $login_string) {
                    // Logged In!!!!
                    return true;
                } else {
                    // Not logged in
                    return false;
                }
            } else {
                // Not logged in
                return false;
            }
        } else {
            // Not logged in
            return false;
        }
    } else {
        // Not logged in
        return false;
    }
}

function show_notices()
{
    if (in_array('notices', $_SESSION) and $_SESSION['notices'])
    {
        foreach ($_SESSION['notices'] as $notice)
        {
            echo "<div class='alert alert-{$notice['type']} alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>{$notice['message']}</div>";
        }
        unset($_SESSION['notices']);
    }
}

function show_message($type, $message)
{
    $_SESSION['notices'][] = array("type" => $type, "message" => $message);
}

function send_email($to, $subject, $message)
{
    require_once "Mail.php";

    $from = "Hamfurs <noreply@hamfurs.org>";
    $body = $message;

    $host = "ssl://smtp.zoho.com";
    $port = "465";
    $username = "noreply@hamfurs.org";
    $password = "password"; //change to real password

    $headers = array ('From' => $from,
      'To' => $to,
      'Subject' => $subject);
    $smtp = Mail::factory('smtp',
      array ('host' => $host,
        'port' => $port,
        'auth' => true,
        'username' => $username,
        'password' => $password));

    $mail = $smtp->send($to, $headers, $body);

    if (PEAR::isError($mail)) {
     // showMessage("danger", $mail->getMessage());
    } else {
      //showMessage("success", "Message successfully sent!");
    }
}

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
        $seats_message = "$current Seat";
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

function insert_db_row($dbh, $row) {
    $name = $row['Name'];
    $email = $row['E-mail'];

    # check if they are in the people table first
    $pid = person_exists($dbh, $name, $email);
    if (is_null($pid)) {
        $pid = insert_person($dbh, $name, $email);
    }

    # check number of tickets
    $tickets = get_tickets($dbh, $pid);
    if (count($tickets) != $row['Spaces']) {
        # Delete and regenerate all tickets
        delete_tickets($dbh, $pid);
        generate_tickets($dbh, $pid, $row);
    }

    # Return something useful to indicate what we just did
}

function generate_tickets($dhb, $person_id, $row) {
    $name = $row['Name'];
    $spaces = $row['Spaces'];

    # Generate code for each space
    # Generate ticket graphic for each space
    # Generate PDF and cache it to be emailed out
}

function get_tickets($dbh, $person_id) {
    $sth = $dbh->prepare("SELECT * FROM `tickets` WHERE `pid` = ?;");
    $sth->execute(array($person_id));
    return $sth->fetchAll();
}

function delete_tickets($dbh, $person_id) {
    $sth = $dbh->prepare("DELETE FROM `tickets` WHERE `pid` = ?;");
    return $sth->execute(array($person_id));
}

function get_person($dbh, $person_id) {
    $sth = $dbh->prepare("SELECT * FROM `people` WHERE `pid` = ?;");
    $sth->execute(array($person_id));
    return $sth->fetchAll();
}

function person_exists($dbh, $name, $email) {
    $sth = $dbh->prepare("SELECT `pid` FROM `people` WHERE `name` = ? AND `email` = ?;");
    $sth->execute(array($name, $email));
    $result = $sth->fetch();
    if ($result) {
        return $result['pid'];
    } else {
        return null;
    }
}

# Returns the ID of the person inserted
function insert_person($dbh, $name, $email) {
    $sth = $dbh->prepare("INSERT INTO `people`(`name`, `email`) VALUES (?, ?);");
    $sth->execute(array($name, $email));
    return $sth->lastInsertId();
}

class CSVException extends Exception { }

function process_csv($csv_string) {
  // Process the CSV into an array of associative arrays, using
  // the first row headers as keys.
  $rows = array_map('str_getcsv', file($csv_string)); // <----- This isnt doing anything

  // Ignore rows that are only one column (which the events plugin helpfully adds)
  do {
      $header = array_shift($rows);
  } while ($header[1] === '');

  if (!(in_array('Name', $header, true) and
        in_array('Spaces', $header, true) and
        in_array('E-mail', $header, true) and
        in_array('Status', $header, true))) {
    throw new CSVException('Expected headers were not found');
  }

  $csv = array();
  foreach ($rows as $row) {
      $csv[] = array_combine($header, $row);
  }

  return $csv;
}

function import_csv_data($csv_data) {
    $csv_data = process_csv($_FILES['csv']['tmp_name']);
    $success_list = array();
    $error_list = array();
    $report = "<ul>";

    # Loop through each row, check if person is in `people` table
    foreach ($csv_data as $row) {
       $email = $row['E-mail'];
       $row['Name'] = stripcslashes($row['Name']);

       if ($row['Status'] === 'Approved') {
           $success_list[] = $row;
           $status = '<span class="label label-success">' . $row['Status'] . "</span>";
           $report .= "<li>$status <span class=\"badge\">$row[Spaces]</span> $row[Name] &lt;$email&gt;</li>";
       } else {
           $error_list[] = $row;
       }
    }

    $report .= "</ul>";

    $error_report = "<ul>";
    foreach ($error_list as $row) {
       $email = $row['E-mail'];
       $status = '<span class="label label-danger">' . $row['Status'] . "</span>";
       $error_report .= "<li>$status <span class=\"badge\">$row[Spaces]</span> $row[Name] &lt;$email&gt;</li>";
    }
    $error_report .= "</ul>";

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
    #

    return array("success" => $report, "errors" => $error_report);

}

?>
