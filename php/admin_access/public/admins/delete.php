<?php require_once('../../../private/initialize.php') ?>

<?php require_login(); ?>

<?php $id = $_GET['id'] ?? ''; ?>

<?php 
    if(!isset($id)) {
        echo "ID is not set";
        redirect_to(url_for('/staff/admins/index.php'));
    }
    
    if(is_post_request()) {
        $result = delete_admin($id);
        if($result === true) {
            $_SESSION['message'] = "Admin deleted";
            redirect_to(url_for('/staff/admins/index.php'));
            echo "redirect";
        } else {
            $errors = $result;
            echo $errors;
        }
    } else {
        $admin = find_admin_by_id($id);
        // print_r($admin);
    }
?>

<?php $page_title = "Delete Admin"; ?>
<?php include(SHARED_PATH . '/staff_header.php') ?>

<div id="content">
    <a href="<?php echo url_for('staff/admins/index.php')?>" class="back-link">&laquo; Back to List</a>

    <div class="admin delete">
        <h1>Delete Admin</h1>
        <p>Are you sure you want to delete this Admin</p>
        <p class="item"><?php echo h($admin['username']); ?></p>

        <form action="<?php echo url_for('staff/admins/delete.php?id=' . h(u($admin['id']))); ?>" method="post">
            <div id="operations">
                <input type="submit" value="Delete Admin" name="commit">
            </div>
        </form>
    </div>
</div>