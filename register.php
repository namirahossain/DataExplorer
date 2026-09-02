```php
<?php

require_once('dbconnect.php');

if(isset($_POST['fname']) && isset($_POST['pass'])){

    $u = $_POST['fname'];
    $p = $_POST['pass'];

    // Get the highest existing numeric user_id
    $result = mysqli_query($conn,
        "SELECT MAX(CAST(user_id AS UNSIGNED)) AS max_id
         FROM user_profile"
    );

    $row = mysqli_fetch_assoc($result);

    // Generate the next user_id
    $next_id = ($row['max_id'] ?? 0) + 1;

    // Insert the new user
    $sql = "INSERT INTO user_profile (user_id, user_name, password, user_country)
            VALUES ('$next_id', '$u', '$p', 'Bangladesh')";

    if(mysqli_query($conn, $sql)){
        header("Location: index.php");
        exit();
    } else {
        echo "Database Error: " . mysqli_error($conn);
    }
}

?>
```
