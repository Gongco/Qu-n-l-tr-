<?php
require dirname(__DIR__) . '/config/auth.php';
startPage('logout');
?>
<h1>Đăng xuất</h1><?php actionForm('logout',0,'Xác nhận đăng xuất','primary'); ?>
<?php require ROOT.'/components/footer.php'; ?>
