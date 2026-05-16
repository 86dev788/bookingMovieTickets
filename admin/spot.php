<?php
include "config.php";

if (isset($_POST['submit'])) {
    $show_id = intval($_POST['show_id']);
    $fname = trim($_POST['fName']);
    $lname = trim($_POST['lName']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['pNumber']);
    $amount = floatval($_POST['amount']);
    $customerName = trim($fname . ' ' . $lname);

    $customer_id = null;
    $stmt = $con->prepare("SELECT customer_id FROM customer WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($customer_id);
    $stmt->fetch();
    $stmt->close();

    if (!$customer_id) {
        $passwordHash = password_hash('ChangeMe123', PASSWORD_DEFAULT);
        $insertCustomer = $con->prepare("INSERT INTO customer (name, email, phone, password_hash) VALUES (?, ?, ?, ?)");
        $insertCustomer->bind_param("ssss", $customerName, $email, $mobile, $passwordHash);
        $insertCustomer->execute();
        $customer_id = $con->insert_id;
        $insertCustomer->close();
    }

    $bookingStmt = $con->prepare("INSERT INTO booking (status, total_amount, customer_id, show_id) VALUES ('Confirmed', ?, ?, ?)");
    $bookingStmt->bind_param("dii", $amount, $customer_id, $show_id);

    if ($bookingStmt->execute()) {
        header('Location: add.php');
        exit;
    } else {
        echo "Error adding booking: " . $bookingStmt->error;
    }
    $bookingStmt->close();
} else {
    header('Location: add.php');
    exit;
}
