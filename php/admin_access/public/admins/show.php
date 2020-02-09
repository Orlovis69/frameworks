<?php require_once('../../../private/initialize.php'); ?>

<?php
    require_login();
    
    $id = $_GET['id'] ?? '1';
    $admin = find_admin_by_id($id);
?>

<?php $page_title = 'Show Admin'; ?>
<?php require(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">
    <a href="<?php echo url_for('/staff/admins/index.php');?>" class="back-link">&laquo; Back to list</a>

    <div class="admin show">
        <h1>Admin: <?php echo h($admin['username']); ?></h1>

        <div class="actions">
            <a href="<?php echo url_for('/staff/admins/edit.php?id=' . h(u($id)));?>" class="action">Edit</a>
            <a href="<?php echo url_for('/staff/admins/delete.php?id=' . h(u($id)));?>" class="action">Delete</a>
        </div>

        <div class="attributes">
            <dl>
                <dt>First name: </dt>
                <dd><?php echo h($admin['first_name']); ?>
            </dl>
            <dl>
                <dt>Last name: </dt>
                <dd><?php echo h($admin['last_name']); ?>
            </dl>
            <dl>
                <dt>Email: </dt>
                <dd><?php echo h($admin['email']); ?>
            </dl>
            <dl>
                <dt>Username: </dt>
                <dd><?php echo h($admin['username']); ?>
            </dl>
        </div>
    </div>
</div>

<?php require(SHARED_PATH . '/staff_footer.php') ?>

