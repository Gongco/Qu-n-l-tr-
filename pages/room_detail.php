<?php
require dirname(__DIR__) . '/config/auth.php';
startPage('detail');
?>
<?php
$r=one('SELECT r.*,u.name landlord FROM rooms r JOIN users u ON u.id=r.owner WHERE r.id=? AND hidden=0 AND archived=0',[(int)($_GET['id']??0)]);
if(!$r){echo '<p class="empty">Tin phòng hiện không hiển thị.</p>';require ROOT.'/components/footer.php';return;}?>
<a class="text-link" href="<?=url('browse')?>">← Danh sách phòng</a><div class="detail"><div><div class="detail-img"><img src="<?=h(photo($r))?>" alt="<?=h($r['title'])?>"><span class="badge <?=$r['available']?'green':'gray'?>"><i></i><?=$r['available']?'Còn trống':'Đã cho thuê'?></span></div><section class="panel"><h2>Mô tả phòng</h2><p class="preserve"><?=h($r['description'])?></p><h3>Tiện nghi</h3><div class="chips"><?php foreach(array_filter(array_map('trim',explode(',',$r['amenities']))) as $amenity):?><span><?=h($amenity)?></span><?php endforeach;if(!$r['amenities']):?>Chưa cập nhật<?php endif;?></div></section></div><aside class="panel"><div class="eyebrow"><?=h($r['type'])?></div><h1><?=h($r['title'])?></h1><p class="location"><?=icon('pin').h($r['district'].', '.$r['city'])?></p><p><?=icon('area')?> <?=(float)$r['area']?> m²</p><p class="price"><?=price($r['price'])?> / tháng</p><p>Chủ trọ · <?=h($r['landlord'])?></p>
<?php if($r['available']&&!$user):?><a class="btn primary" href="<?=url('login',['room'=>$r['id']])?>">Gửi yêu cầu thuê</a>
<?php elseif($r['available']&&$user['role']==='renter'):
if(one("SELECT id FROM requests WHERE room=? AND renter=? AND status='pending'",[$r['id'],$user['id']])):?><a class="text-link" href="<?=url('requests')?>">Xem yêu cầu đã gửi</a>
<?php else:actionForm('apply',$r['id'],'Gửi yêu cầu thuê','primary');endif;endif;?></aside></div>
<?php require ROOT.'/components/footer.php'; ?>
