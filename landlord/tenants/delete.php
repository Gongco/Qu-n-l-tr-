<?php
require dirname(__DIR__, 2) . '/config/auth.php';
startPage('tenant_delete');
?>
<?php $id=(int)($_GET['id']??0); $item=one('SELECT t.name FROM tenants t JOIN rooms r ON r.id=t.room WHERE t.id=? AND r.owner=?',[$id,$user['id']]); ?>
<section class="form-panel panel"><?php if($item):?><h1>Xóa người thuê?</h1><p><?=h($item['name'])?></p><div class="actions"><a class="btn outline" href="<?=url('tenants')?>">Hủy</a><?php actionForm('tenant_delete',$id,'Xác nhận xóa','danger');?></div><?php else:?><p>Không tìm thấy mục cần xóa.</p><?php endif;?></section>
<?php require ROOT.'/components/footer.php'; ?>
