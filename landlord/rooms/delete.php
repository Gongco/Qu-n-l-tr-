<?php
require dirname(__DIR__, 2) . '/config/auth.php';
startPage('room_delete');
?>
<?php $id=(int)($_GET['id']??0); $item=one('SELECT title AS name FROM rooms WHERE id=? AND owner=? AND archived=0',[$id,$user['id']]); ?>
<section class="form-panel panel"><?php if($item):?><h1>Xóa tin cho thuê?</h1><p><?=h($item['name'])?></p><div class="actions"><a class="btn outline" href="<?=url('rooms')?>">Hủy</a><?php actionForm('delete',$id,'Xác nhận xóa','danger');?></div><?php else:?><p>Không tìm thấy mục cần xóa.</p><?php endif;?></section>
<?php require ROOT.'/components/footer.php'; ?>
