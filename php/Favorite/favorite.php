<?php 
// Simulate slow server
// sleep(2);

session_start();

// Mimic database data
if(!isset($_SESSION['favorites'])) {$_SESSION['favorites'] = [];}


function is_ajax_request() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
}

// Check if it is an ajax request
if(!is_ajax_request()) { exit; }

// Extract ID
$raw_id = isset($_POST['id']) ? $_POST['id'] : '';

// Match only numbers after blog-post-
if(preg_match("/blog-post-(\d+)/", $raw_id, $matches)) {
    $id = $matches[1];
    
    // Store in $_SESSION['favorites']
    if(!in_array($id, $_SESSION['favorites'])) {
        $_SESSION['favorites'][] = $id;
    }
    // print_r($_SESSION['favorites']);
    echo 'true';
} else {
    echo 'false';
}



?>