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
					:U
				</div>
			</div>
		</div>
		
<?php
	include ('../templates/footer.inc');
?>