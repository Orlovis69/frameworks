<?php require_once('../../../private/initialize.php') ?>

<?php
require_login();

if (!isset($_GET['id'])) {
    redirect_to(url_for('/staff/admins/index.php'));
}

$id = $_GET['id'];

if (is_post_request()) {
    $admin = [];
    $admin['id'] = $id;
    $admin['first_name'] = $_POST['first_name'] ?? '';
    $admin['last_name'] = $_POST['last_name'] ?? '';
    $admin['email'] = $_POST['email'] ?? '';
    $admin['username'] = $_POST['username'] ?? '';
    $admin['password'] = $_POST['password'] ?? '';
    $admin['confirm_password'] = $_POST['confirm_password'] ?? '';
    // print_r($admin);

    $result = update_admin($admin);
    if ($result === true) {
        // echo $result;
        $_SESSION['message'] = "Admin updated.";
        redirect_to(url_for('/staff/admins/show.php?id=' . $id));
    } else {
        $errors = $result;
    }
} else {
    $admin = find_admin_by_id($id);
    // echo "Not a post request";
}
?>

<?php $page_title = "Edit admin"; ?>
<?php include(SHARED_PATH . '/staff_header.php') ?>

<div id="content">
    <a href="<?php echo url_for('/staff/admins/index.php'); ?>" class="back-link">&laquo; Back to list</a>

    <div class="admin new">
        <h1>Update admin</h1>

        <?php echo display_errors($errors); ?>

        <form action="<?php echo url_for('/staff/admins/edit.php?id=' . $id); ?>" method="post">
            <dl>
                <dt>First name</dt>
                <dd>
                    <input type="text" name="first_name" value="<?php echo $admin['first_name']; ?>">
                </dd>
            </dl>
            <dl>
                <dt>Last name</dt>
                <dd>
                    <input type="text" name="last_name" value="<?php echo $admin['last_name']; ?>">
                </dd>
            </dl>
            <dl>
                <dt>Email</dt>
                <dd>
                    <input type="email" name="email" value="<?php echo $admin['email']; ?>">
                </dd>
            </dl>
            <dl>
                <dt>Username</dt>
                <dd>
                    <input type="text" name="username" value="<?php echo $admin['username']; ?>">
                </dd>
            </dl>
            <dl>
                <dt>Password</dt>
                <dd>
                    <input type="text" name="password" value="<?php //echo $admin['password']; ?>">
                </dd>
            </dl>
            <dl>
                <dt>Repeat Password</dt>
                <dd>
                    <input type="text" name="confirm_password" value="">
                </dd>
            </dl>
            <div class="operations">
                <input type="submit" value="Update admin">
            </div>
        </form>
    </div>
</div>

<?php require(SHARED_PATH . '/staff_footer.php') ?>