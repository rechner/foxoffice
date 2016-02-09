<?php
	try {
		$pdo = new PDO("sqlite:../tickets.db");
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}
?>