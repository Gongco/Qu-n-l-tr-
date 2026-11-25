<?php
require dirname(__DIR__,2).'/config/auth.php';
startPage('tenants');
$tenants=sql('SELECT t.*,r.title,r.archived FROM tenants t JOIN rooms r ON r.id=t.room WHERE r.owner=? ORDER BY t.id DESC',[$user['id']])->fetchAll();?>
<div class="section-head"><h1>Người thuê</h1><a class="btn primary" href="<?=url('tenant_add')?>">Thêm người thuê</a></div><div class="rows"><?php foreach($tenants as $t):?><article class="row"><div><h3><?=h($t['name'])?></h3><p><?=h($t['title'])?> · <?=h($t['start_date'])?> <?=h($t['phone'])?> <?=$t['archived']?'· Phòng đã lưu trữ':''?></p></div><div class="actions"><a class="btn outline" href="<?=url('tenant_edit',['id'=>$t['id']])?>">Chỉnh sửa</a><a class="btn danger" href="<?=url('tenant_delete',['id'=>$t['id']])?>">Xóa</a></div></article><?php endforeach;if(!$tenants):?><p class="empty">Chưa có hồ sơ người thuê.</p><?php endif;?></div><?php require ROOT.'/components/footer.php';?>
