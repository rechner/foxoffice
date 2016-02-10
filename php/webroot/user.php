<?php
include '../sqlite_connect.php';
include '../functions.php';
sec_session_start();
if(!login_check($pdo)){
    header("Location: ./login.php");
    exit();
}
include ('../templates/header.inc');
?>
        <div class="col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-user-plus fa-fw"></i>New User</h3>
                </div>
                <div class="panel-body">
                    <form>
                        <div class="form-group">
                            Username, password, confirm<br>Submit
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-users fa-fw"></i>Users</h3>
                </div>
                <div class="panel-body">
                    <table class="table">
                    	<tr>
							<th>Username</th>
							<th class="text-center">Change Password</th>
							<th class="text-center">Delete User</th>
						</tr>
							<?php 
								$query = $pdo->prepare("SELECT uid, username FROM accounts");

								$query->execute();
								$result = $query->fetchAll(PDO::FETCH_ASSOC);
								foreach ($result as $row) {
									echo "<input type=\"hidden\" name=\"uid\" id=\"uid\" value=\"{$row['uid']}\"";
									echo "\n\t\t\t\t<tr>\n\t\t\t\t\t<td>{$row['username']}</td>\n\t\t\t\t\t<td class=\"text-center\"><a href=\"#\" class=\"btn btn-warning\"><i class=\"fa fa-key fa-fw\"></td>\n\t\t\t\t\t<td class=\"text-center\"><a href=\"#\" class=\"btn btn-danger\"><i class=\"fa fa-user-times fa-fw\"></td>\n\t\t\t\t</tr>\n";
								}
							?>
                    </table>
                </div>
            </div>
        </div>
        
<?php
    include ('../templates/footer.inc');
?>