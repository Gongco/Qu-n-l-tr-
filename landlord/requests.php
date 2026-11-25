<?php
require dirname(__DIR__) . '/config/auth.php';
startPage('inbox');
?>
<?php if(!defined('ROLES'))exit;
$requests=sql('SELECT q.*,r.title,r.hidden,r.archived,r.available,u.name FROM requests q JOIN rooms r ON r.id=q.room JOIN users u ON u.id=q.renter WHERE r.owner=? ORDER BY q.id DESC',[$user['id']])->fetchAll();?>
<h1>Xử lý yêu cầu thuê</h1><div class="rows"><?php foreach($requests as $q):?><article class="row"><div><h3><?=h($q['title'])?></h3><p><?=h($q['name'])?> · <?=date('d/m/Y',strtotime($q['created_at']))?> <?=$q['archived']?'· Tin đã xóa':($q['hidden']?'· Tin đang ẩn':'')?></p></div><div class="actions"><span class="status <?=$q['status']?>"><?=STATUS[$q['status']]?></span>
<?php if($q['status']==='pending'):?><form method="post"><?=csrf()?><input type="hidden" name="id" value="<?=$q['room']?>"><input type="hidden" name="request" value="<?=$q['id']?>"><?php if(!$q['hidden']&&$q['available']&&!$q['archived']):?><button class="btn primary" name="action" value="accepted">Chấp nhận</button><?php endif;?> <button class="btn outline" name="action" value="rejected">Từ chối</button></form><?php endif;?></div></article><?php endforeach;if(!$requests):?><p class="empty">Chưa có yêu cầu thuê phòng.</p><?php endif;?></div>


<?php require ROOT.'/components/footer.php'; ?>
