<?php
    include '../sqlite_connect.php';
    include '../functions.php';
    sec_session_start();
    
    if(isset($_POST['username'], $_POST['password'])) { 
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        if(login($username, $password, $pdo) == true) {
            // Login success
            show_message("success", "Successfully logged in!");
            header("Location: ./index.php");
            exit();
        } else {
            show_message("danger", "Login failed");
        }
    }
?>
<html>
    <head>
        <title>Login</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        <link rel="stylesheet" href="main.css">
        <script src="http://code.jquery.com/jquery-2.2.0.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    </head>
    <body id="no-nav">
    <?php show_notices(); ?>
        <div class="col-lg-4 col-lg-offset-4">
            <div class="panel panel-default panel-login">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-user fa-fw"></i> Login</h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" method="post" id="login">
                        <div class="form-group">
                            <label for="username" class="col-sm-3 control-label required">Username:</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                    <input name="username" class="form-control" type="text" maxlength="20" placeholder="Username" autofocus required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password" class="col-sm-3 control-label required">Password:</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-asterisk"></i></span>
                                    <input name="password" class="form-control" type="password" maxlength="40" placeholder="Password" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-9 col-sm-offset-3">
                            <button class="btn btn-primary btn-lg" type="submit" value="Login" id="login" name="Submit">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        

<?php
    include ('../templates/footer.inc');
?>

