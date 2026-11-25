<?php
require dirname(__DIR__) . '/config/auth.php';
startPage('admin');
?>
<?php
$users=sql('SELECT name,email,role FROM users ORDER BY id')->fetchAll();
 $rooms=sql('SELECT id,title,hidden,archived FROM rooms ORDER BY id DESC')->fetchAll();
 $requests=sql('SELECT r.title,u.name,q.status FROM requests q JOIN rooms r ON r.id=q.room JOIN users u ON u.id=q.renter ORDER BY q.id DESC')->fetchAll();
?><div class="eyebrow">QUẢN TRỊ</div><h1>Tổng quan hệ thống</h1><div class="stats"><?php foreach(['Tài khoản'=>count($users),'Tin phòng'=>count(array_filter($rooms,fn($r)=>!$r['archived'])),'Yêu cầu thuê'=>count($requests)] as $label=>$n):?><div class="panel"><strong><?=$n?></strong><?=h($label)?></div><?php endforeach;?></div><h2>Người dùng</h2><div class="rows"><?php foreach($users as $u):?><article class="row"><div><b><?=h($u['name'])?></b><p><?=h($u['email'])?></p></div><?=ROLES[$u['role']]?></article><?php endforeach;?></div>
<h2>Kiểm duyệt phòng</h2><div class="rows"><?php foreach($rooms as $r):?><article class="row"><div><b><?=h($r['title'])?></b><p><?=$r['archived']?'Đã lưu trữ':($r['hidden']?'Đang ẩn':'Đang hiển thị')?></p></div><?php if(!$r['archived'])actionForm($r['hidden']?'restore':'hide',$r['id'],$r['hidden']?'Khôi phục':'Ẩn tin');?></article><?php endforeach;if(!$rooms):?><p>Chưa có phòng.</p><?php endif;?></div>
<h2>Yêu cầu thuê</h2><div class="rows"><?php foreach($requests as $q):?><article class="row"><div><b><?=h($q['title'])?></b><p><?=h($q['name'])?></p></div><?=STATUS[$q['status']]?></article><?php endforeach;if(!$requests):?><p>Chưa có yêu cầu thuê.</p><?php endif;?></div>
<?php require ROOT.'/components/footer.php'; ?>
