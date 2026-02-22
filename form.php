<?php
include_once("database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- ACCOUNT FORM SUBMISSION ---
    if (isset($_POST["account_submit"])) {
        $username   = test_input($_POST["username"]);
        $password   = sha1(test_input($_POST["password"]));
        $email      = test_input($_POST["email"]);
        $date_created = date("Y-m-d H:i:s");
        $date_updated = date("Y-m-d H:i:s");

        $insert_account = "INSERT INTO tbl_account (username, password, email, status, type, date_created, date_updated) 
                           VALUES ('$username', '$password', '$email', 1, 2, '$date_created', '$date_updated')";
        if ($conn->query($insert_account) === TRUE) {
            echo "New account record created successfully!";
        } else {
            echo "Error inserting account record: " . $conn->error;
        }
    }

    // --- USER FORM SUBMISSION ---
    if (isset($_POST["user_submit"])) {
        $account_id = test_input($_POST["account_id"]); // must reference an existing account
        $firstname  = test_input($_POST["firstname"]);
        $middlename = test_input($_POST["middlename"]);
        $lastname   = test_input($_POST["lastname"]);
        $gender     = test_input($_POST["gender"]); // 1 = Male, 2 = Female
        $dob        = test_input($_POST["dob"]);

        $insert_user = "INSERT INTO tbl_user (account_id, firstname, middlename, lastname, gender, dob) 
                        VALUES ('$account_id', '$firstname', '$middlename', '$lastname', '$gender', '$dob')";
        if ($conn->query($insert_user) === TRUE) {
            echo "New user record created successfully!";
        } else {
            echo "Error inserting user record: " . $conn->error;
        }
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!-- ACCOUNT FORM -->
<h2>Create Account</h2>
<form action="<?php echo htmlspecialchars('submit.php');?>" method="POST">
    <h3>Account Information</h3>
    Username: <input type="text" name="username" required><br/>
    Password: <input type="password" name="password" required><br/>
    Email: <input type="email" name="email" required><br/>


    <h3>Create</h3>
    Firstname: <input type="text" name="firstname" required><br/>
    Middlename: <input type="text" name="middlename"><br/>
    Lastname: <input type="text" name="lastname" required><br/>


    Gender: 
    <select name="gender" required>
    <option value="">Please select one</option>
    <option value="Male">Male</option>
    <option value="Female">Female</option>
    <</select><br/>
    
    Date of Birth: <input type="date" name="dob" required><br/>

    <br/>
    <input type="submit" name="registration_submit" value="Register Now"/>
</form>

<!-- Display joined table -->
<h2>Accounts with Users</h2>
<table border="1">
    <th>Account ID</th>
    <th>Username</th>
    <th>Email</th>
    <th>Firstname</th>
    <th>Middlename</th>
    <th>Lastname</th>
    <th>Gender</th>
    <th>DOB</th>
    <th>Date Created</th>
    <th>Date Updated</th>

    <?php
    $sql = "SELECT a.id, a.username, a.email, u.firstname, u.middlename, u.lastname, 
                   CASE WHEN u.gender=1 THEN 'Male' WHEN u.gender=2 THEN 'Female' END AS gender,
                   u.dob, a.date_created, a.date_updated
            FROM tbl_account a
            LEFT JOIN tbl_user u ON a.id = u.account_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
    ?>
    <tr>
        <td><?php echo $row["id"]; ?></td>
        <td><?php echo $row["username"]; ?></td>
        <td><?php echo $row["email"]; ?></td>
        <td><?php echo $row["firstname"]; ?></td>
        <td><?php echo $row["middlename"]; ?></td>
        <td><?php echo $row["lastname"]; ?></td>
        <td><?php echo $row["gender"]; ?></td>
        <td><?php echo date("F j, Y", strtotime($row["dob"])); ?></td>
        <td><?php echo $row["date_created"]; ?></td>
        <td><?php echo $row["date_updated"]; ?></td>
    </tr>
    <?php
      }
    }
    $conn->close();
    ?>
</table>