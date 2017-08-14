<?php

require_once('db_fns.php');

function register($username, $email, $password) {
    // register new person with db
    // return true or error message

        // connect to db
        $conn = db_connect();

        // check if username is unique
        $result = $conn->query("SELECT * FROM user WHERE username='".$username."'");
        if (!$result) {
            throw new Exception('Could not execute query');
        }

        if ($result->num_rows>0) {
            throw new Exception('That username is taken - go back and choose another one.');
        }

        // if ok, put in db
        $result = $conn->query("INSERT INTO user VALUES 
                                ('".$username."', sha1('".$password."'), '".$email."')");
        if (!$result) {
            throw new Exception('Could not register you in database - please try again later.');
        }

        return true;
}

function login($username, $password) {
// check username and password with db
// if yes, return true
 // else throw exception

    // connect to db
    $conn = db_connect();

    // check if username is unique
    $result = $conn->query("SELECT * FROM user 
                             WHERE username='".$username."'
                             AND passwd = sha1('".$password."')");
     if (!$result) {
        throw new Exception('Could not log you in.');
    }

    if ($result->num_rows>0) {
        return true;
    } else {
         throw new Exception('Could not log you in.');
    }
}

function check_valid_user() {
// see if somebody is logged in and notify them if not
    if (isset($_SESSION['valid_user'])) {
        echo "Logged in as ".$_SESSION['valid_user'].".<br>;"
    } else {
        // they are not logged in
        do_html_header('Problem:');
        echo 'You are not logged in.<br>';
        do_html_url('login.php', 'Login');
        do_html_footer();
        exit;
    }
}

function change_password($username, $old_password, $new_password) {
// change password for username/old_password to new_password
// retrun true of false

    // if the old password is right
    // change their password to new_password and return true
    // else throw an exception
    login($username, $old_password);
    $conn = db_connect();
    $result = $conn->query("UPDATE user
                            SET passwd = sha1('".$new_password."')
                            WHERE username = '".$username."'");
    if (!$result) {
        throw new Exception('Password could not be changed.');
    } else {
        return true; // changed successfully
    }
}

function get_random_word($min_length, $max_length) {
// grab a random word from dictionary between the two lengths
// and return it

    // generate a random word
    $word = '';
    // remember to change this path to suit your system
    $dictionary = '/usr/dict/words';  // the ispell dictionary
    $fp = @fopen($dictionary, 'r');
    if (!$fp) {
        return false;
    }
    $size = filesize($dictionary);

    // go to a random location in dictionary
    $random_location = rand(0, $size);
    fseek($fp, $rand_location);

    // get the next whole word of the right length in the file
    while ((strlen($word) < $min_length) || (strlen($word)>$max_length) || (strstr($word, "'"))) {
        if (feof($fp)) {
            fseek($fp, 0);          // if at end, go to start
        }
        $word = fgets($fp, 80);     // skip first word as it could be partial
        $word = fgets($fp, 80);     // the potential password
    }
    $word = trim($word);    // trim the trailing \n from fgets
    return $word;
}

function reset_password($username) {
// set password for username to a random value
// return the new password or false on failure
    // get a random dictionary word b/w 6 and 13 chars in length
    $new_password = get_random_word(6,13);

    if ($new_password == false) {
        // give a default password
        $new_password = "changeMe!";
    }

    // add a number between 0 and 999 to it
}