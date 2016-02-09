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
					<h3 class="panel-title"><i class="fa fa-upload fa-fw"></i>Upload CSV</h3>
				</div>
				<div class="panel-body">
					<form>
						<div class="form-group">
							<input type="file" id="exampleInputFile">
							<p class="help-block">Upload CSV of Ticket Purchases</p>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="col-lg-8">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-eye fa-fw"></i>Quick Stats</h3>
				</div>
				<div class="panel-body text-center">
					<a href="#" class="btn btn-success">
						<span class="bigger-150">0</span>
						<br>
						Tickets Sold
					</a>
					<a href="#" class="btn btn-info">
						<span class="bigger-150">0</span>
						<br>
						Tickets Remaining
					</a>
					<a href="#" class="btn btn-success">
						<span class="bigger-150">0</span>
						<br>
						Paid Orders
					</a>
					<a href="#" class="btn btn-warning">
						<span class="bigger-150">0</span>
						<br>
						Unpaid Orders
					</a>
					<a href="#" class="btn btn-danger">
						<span class="bigger-150">0</span>
						<br>
						Ungenerated Tickets
					</a>
				</div>
			</div>
		</div>

<?php
	include ('../templates/footer.inc');
?>