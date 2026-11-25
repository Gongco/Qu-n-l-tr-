<?php
require dirname(__DIR__).'/config/auth.php';
startPage('dashboard');
$totals=one("SELECT COUNT(*) total, COALESCE(SUM(available),0) available FROM rooms WHERE owner=? AND archived=0",[$user['id']]);
$pending=one("SELECT COUNT(*) total FROM requests q JOIN rooms r ON r.id=q.room WHERE r.owner=? AND q.status='pending'",[$user['id']]);
?><div class="section-head"><h1>Tổng quan chủ trọ</h1><a class="btn primary" href="<?=url('room_add')?>">Đăng phòng</a></div><div class="stats">
<?php foreach([['rooms',$totals['total'],'Phòng đang quản lý'],['rooms',$totals['available'],'Phòng còn trống'],['inbox',$pending['total'],'Yêu cầu chờ xác nhận']] as [$key,$n,$label]):?><a class="panel" href="<?=url($key)?>"><strong><?=$n?></strong><?=$label?></a><?php endforeach;?></div>
<?php require ROOT.'/components/footer.php';?>
