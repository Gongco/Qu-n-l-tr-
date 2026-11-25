<?php
require dirname(__DIR__) . '/config/auth.php';
startPage('requests');
?>
<?php if(!defined('ROLES'))exit;
$requests=sql('SELECT q.*,r.title,r.archived,r.hidden FROM requests q JOIN rooms r ON r.id=q.room WHERE renter=? ORDER BY q.id DESC',[$user['id']])->fetchAll();?>
<h1>Yêu cầu thuê của tôi</h1><div class="rows"><?php foreach($requests as $q):?><article class="row"><div><h3><?=h($q['title'])?></h3><p><?=date('d/m/Y',strtotime($q['created_at']))?> · <?php if($q['archived']):?>Tin đã xóa<?php elseif($q['hidden']):?>Tin đang ẩn<?php else:?><a class="text-link" href="<?=url('browse',['id'=>$q['room']])?>">Xem phòng</a><?php endif;?></p></div><span class="status <?=$q['status']?>"><?=STATUS[$q['status']]?></span></article><?php endforeach;if(!$requests):?><p class="empty">Bạn chưa gửi yêu cầu thuê phòng.</p><?php endif;?></div>


<?php require ROOT.'/components/footer.php'; ?>
