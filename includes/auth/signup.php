<?php

    // instruction: call DB class
    $db = new DB();

    // instruction: get all POST data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    /* 
        instruction: retrieve the user based on the email provided to make sure there is no duplication of email in the users table
    */
    $user = $db->fetch( 
        "SELECT * FROM users where email = :email", 
        [
            'email' => $email
        ]
    );


    /* 
        instruction: do error checking 
        - make sure all the fields are not empty
        - make sure password is match
        - make sure password is at least 8 chars.
        -  make sure email provided is not already exists in the users table
    */
    if ( empty( $name ) || empty($email) || empty($password) || empty($confirm_password)  ) {
        $error = 'All fields are required';
    } else if ( $password !== $confirm_password ) {
        // 2. make sure password is match
        $error = 'The password is not match.';
    } else if ( strlen( $password ) < 8 ) {
        // 3. make sure password is at least 8 chars.
        $error = "Your password must be at least 8 characters";
    } else if ( $user ) {
        // 4. make sure email provided is not already exists in the users table
        $error = "The email you inserted has already been used by another user. Please insert another email.";
    }


    // instruction: if error found, set error into session and redirect user back to signup page
    if ( isset( $error ) ) {
        // store the error message in session
        $_SESSION['error'] = $error;
        // redirect the user back to /login
        header("Location: /login");
        exit;
    }

    // instruction: if no error found, process to account creation
    else {
        header("Location: /signup");
    }


    // instruction: retrieve the newly signup user data
    $sql = "INSERT INTO users ( `name`, `email`, `password` )
    VALUES (:name, :email, :password)";
     $db->insert( $sql, [
        'name' => $name,
        'email' => $email,
        'password' => password_hash( $password, PASSWORD_DEFAULT ) // convert user's password to random string
    ] );

    // instruction: set the user data into session

    $_SESSION["user"] = $user;

    // set success message into session
    $_SESSION["success"] = "Account created successfully. You can now submit your answers";

    // instruction: redirect user to home page
    header("Location: /");
    exit;
