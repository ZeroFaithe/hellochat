<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registration_submit'])) {
    
    // Start a transaction (Prevents half-saved data if something crashes)
    $pdo->beginTransaction();

    try {
        // --- STEP 1: Insert into tbl_account ---
        $sqlAcc = "INSERT INTO tbl_account (username, password, email) VALUES (?, ?, ?)";
        $stmtAcc = $pdo->prepare($sqlAcc);
        
        // Secure password hashing
        $hashedPass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $stmtAcc->execute([
            $_POST['username'], 
            $hashedPass, 
            $_POST['email']
        ]);

        // --- STEP 2: Capture the generated ID ---
        $newID = $pdo->lastInsertId();

        // --- STEP 3: Insert into tbl_user using that ID ---
        $sqlUser = "INSERT INTO tbl_user (account_id, firstname, middlename, lastname, gender, dob) 
                    VALUES (?, ?, ?, ?, ?, ?)";
        $stmtUser = $pdo->prepare($sqlUser);
        
        $stmtUser->execute([
            $newID, // This is the link to tbl_account
            $_POST['firstname'], 
            $_POST['middlename'], 
            $_POST['lastname'], 
            $_POST['gender'], 
            $_POST['dob']
        ]);

        // Success!
        $pdo->commit();
        echo "Registration successful for " . htmlspecialchars($_POST['firstname']) . "!";
        echo "<br><a href='form.php'>Return to Form</a>";

    } catch (Exception $e) {
        // Something went wrong, cancel everything
        $pdo->rollBack();
        echo "Registration failed: " . $e->getMessage();
    }
}
?>