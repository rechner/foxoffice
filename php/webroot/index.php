<?php
include '../sqlite_connect.php';
include '../functions.php';
sec_session_start();
if(!login_check($pdo)){
    header("Location: ./login.php");
    exit();
}

// Generate Stats
    $query = $pdo->prepare("SELECT SUM(seats) AS totalorders FROM orders WHERE status = '1' OR status = '2'");
    $query->execute();
    $result = $query->fetch();
    if ($result) {
        $totalorders = $result['totalorders'];
    } else {
        $totalorders = 0;
    }

    $query = $pdo->prepare("SELECT SUM(seats) AS totalunpaid FROM orders WHERE status = '2'");
    $query->execute();
    $result = $query->fetch();
    if ($result) {
        $totalunpaid = $result['totalunpaid'];
    } else {
        $totalunpaid = 0;
    }

    $query = $pdo->prepare("SELECT SUM(seats) AS totalpaid FROM orders WHERE status = '1'");
    $query->execute();
    $result = $query->fetch();
    if ($result) {
        $totalpaid = $result['totalpaid'];
    } else {
        $totalpaid = 0;
    };

    $query = $pdo->prepare("SELECT SUM(CASE WHEN emailed=0 THEN 1 ELSE 0 END) AS ungenerated FROM orders");
    $query->execute();
    $result = $query->fetch();
    if ($result) {
        $ungenerated = $result['ungenerated'];
    } else {
        $ungenerated = 0;
    }


if (isset($_FILES['csv'])) {
    $errors = array();
    $filename = explode('.', $_FILES['csv']['name']);
    $file_ext = strtolower(end($filename));
    if ($file_ext !== 'csv') {
        $errors[] = "File extension must be CSV.";
    }

    if (empty($errors) == true) {
        try {
            $csv_data = process_csv($_FILES['csv']['tmp_name']);
            $report = import_csv_data($csv_data);

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
            show_message("danger", "Error while processing file: " . $e->getMessage());
            $error = $e->getMessage();
        }
    }
}

include ('../templates/header.inc');
?>
        <div class="row">
            <div class="col-lg-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-upload fa-fw"></i>Upload CSV</h3>
                    </div>
                    <div class="panel-body">
                        <form class="form-inline" action="" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <input type="file" id="csv" name="csv" accept=".csv">
                                <p class="help-block">Upload CSV of Ticket Purchases</p>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary btn-block" type="submit">Upload</button>
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
                            <span class="bigger-150">
                                <?php
                                    echo $totalorders;
                                ?>
                            </span>
                            <br>
                            Total Orders
                        </a>
                        <a href="#" class="btn btn-info">
                            <span class="bigger-150">
                                <?php
                                    echo TOTAL_SEATS - $totalorders;
                                ?>
                            </span>
                            <br>
                            Tickets Remaining
                        </a>
                        <a href="#" class="btn btn-success">
                            <span class="bigger-150">
                                <?php
                                    echo $totalpaid;
                                ?>
                            </span>
                            <br>
                            Paid Orders
                        </a>
                        <a href="#" class="btn btn-warning">
                            <span class="bigger-150">
                                <?php
                                    echo $totalunpaid;
                                ?>
                            </span>
                            <br>
                            Unpaid Orders
                        </a>
                        <a href="#" class="btn btn-danger">
                            <span class="bigger-150">
                                <?php
                                    echo $ungenerated;
                                ?>
                            </span>
                            <br>
                            Ungenerated Tickets
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($report)) { ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Report</h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <h2>Imported</h2>
                                <?php echo $report['success']; ?>
                            </div>
                            <div class="col-md-6">
                                <h2>Unprocessed</h2>
                                <?php echo $report['errors']; ?>
                            </div>
                        </div>
                </div>
            </div>
        </div>
<?php
    }
    include ('../templates/footer.inc');
?>
