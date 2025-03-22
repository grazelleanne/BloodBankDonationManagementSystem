<?php
// Start session and enable error reporting for debugging
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
include('includes/config.php');

// Redirect to login page if not logged in
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>BBDMS | Blood Requests Received</title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        /* Custom Styling */
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }

        .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }
    </style>
</head>

<body>
    <!-- Include header and sidebar -->
    <?php include('includes/header.php'); ?>
    <div class="ts-main-content">
        <?php include('includes/leftbar.php'); ?>

        <div class="content-wrapper">
            <div class="container-fluid">

                <h3>Blood Requests Received</h3>
                <hr />

                <div class="panel panel-default">
                    <div class="panel-heading">Blood Request Details</div>
                    <div class="panel-body">

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name of Donor</th>
                                    <th>Contact Number of Donor</th>
                                    <th>Name of Requirer</th>
                                    <th>Contact Number of Requirer</th>
                                    <th>Email of Requirer</th>
                                    <th>Blood Require For</th>
                                    <th>Message of Requirer</th>
                                    <th>Apply Date</th>
                                    <th>Approval</th> <!-- New Column -->
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                try {
                                    // Fetch all blood requests with donor info & donation status
                                    $sql = "SELECT 
                                                tblbloodrequirer.id AS RequestID,
                                                tblbloodrequirer.BloodDonarID, 
                                                tblbloodrequirer.name AS RequirerName, 
                                                tblbloodrequirer.EmailId AS RequirerEmail, 
                                                tblbloodrequirer.ContactNumber AS RequirerContact, 
                                                tblbloodrequirer.BloodRequirefor, 
                                                tblbloodrequirer.Message, 
                                                tblbloodrequirer.ApplyDate, 
                                                tblblooddonars.FullName AS DonorName, 
                                                tblblooddonars.MobileNumber AS DonorContact,
                                                tblblooddonars.DonationStatus
                                            FROM 
                                                tblbloodrequirer
                                            JOIN 
                                                tblblooddonars 
                                            ON 
                                                tblblooddonars.id = tblbloodrequirer.BloodDonarID
                                            ORDER BY 
                                                tblbloodrequirer.ApplyDate DESC";

                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);

                                    // Display fetched results
                                    $cnt = 1;
                                    if ($query->rowCount() > 0) {
                                        foreach ($results as $row) {
                                            echo "<tr>";
                                            echo "<td>" . htmlentities($cnt) . "</td>";
                                            echo "<td>" . htmlentities($row->DonorName) . "</td>";
                                            echo "<td>" . htmlentities($row->DonorContact) . "</td>";
                                            echo "<td>" . htmlentities($row->RequirerName) . "</td>";
                                            echo "<td>" . htmlentities($row->RequirerContact) . "</td>";
                                            echo "<td>" . htmlentities($row->RequirerEmail) . "</td>";
                                            echo "<td>" . htmlentities($row->BloodRequirefor) . "</td>";
                                            echo "<td>" . htmlentities($row->Message) . "</td>";
                                            echo "<td>" . htmlentities($row->ApplyDate) . "</td>";

                                            // Approval Status
                                            if ($row->DonationStatus == 'Donated') {
                                                echo "<td>
                                                        <a class='btn btn-success' href='approve_donation.php?id=" . $row->RequestID . "' onclick='return confirm(\"Are you sure you want to approve this donation?\");'>Approve</a>
                                                    </td>";
                                            } else {
                                                echo "<td><span style='color: red; font-weight: bold;'>Pending</span></td>";
                                            }

                                            echo "</tr>";
                                            $cnt++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='10'>No Blood Requests Found</td></tr>";
                                    }
                                } catch (PDOException $e) {
                                    echo "<tr><td colspan='10' class='errorWrap'>Error: " . $e->getMessage() . "</td></tr>";
                                }
                                ?>

                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>
</html>
