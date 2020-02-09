<?php 
// Simulate slow server
// sleep(2);

session_start();

// Mimic database data
if(!isset($_SESSION['favorites'])) {$_SESSION['favorites'] = [];}


function is_ajax_request() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
}

// A handy function to remove a single element from array
function array_remove($element, $array) {
    $index = array_search($element, $array);
    return array_splice($array, $index, 1);

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
        $_SESSION['favorites'] = array_remove($id, $_SESSION['favorites']);
    }
    // print_r($_SESSION['favorites']);
    echo 'true';
} else {
    echo 'false';
}



?>