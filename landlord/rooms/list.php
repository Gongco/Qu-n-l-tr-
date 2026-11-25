<?php
require dirname(__DIR__, 2) . '/config/auth.php';
startPage('rooms');
?>
<?php
$rooms=sql('SELECT id,title,city,price,available,hidden FROM rooms WHERE owner=? AND archived=0 ORDER BY id DESC',[$user['id']])->fetchAll();?>
<div class="section-head"><h1>Phòng đang quản lý</h1><a class="btn primary" href="<?=url('room_add')?>">Đăng phòng</a></div><div class="rows">
<?php foreach($rooms as $r):?><article class="row"><div><h3><?=h($r['title'])?></h3><p><?=price($r['price'])?>/tháng · <?=h($r['city'])?> · <?=$r['hidden']?'Đang ẩn':($r['available']?'Còn trống':'Đã cho thuê')?></p></div><div class="actions"><a class="btn outline" href="<?=url('room_edit',['id'=>$r['id']])?>">Chỉnh sửa</a><a class="btn danger" href="<?=url('room_delete',['id'=>$r['id']])?>">Xóa</a></div></article><?php endforeach;if(!$rooms):?><p class="empty">Bạn chưa đăng phòng nào.</p><?php endif;?></div>


<?php require ROOT.'/components/footer.php'; ?>
