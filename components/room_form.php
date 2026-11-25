<?php if(!defined('ROOT'))exit;
$id=$page==='room_edit'?(int)($_GET['id']??0):0;$r=$id?one('SELECT * FROM rooms WHERE id=? AND owner=? AND archived=0',[$id,$user['id']]):[];
if($id&&!$r){echo '<p class="empty">Không tìm thấy phòng của bạn.</p>';return;}?>
<a class="text-link" href="<?=url('rooms')?>">← Phòng đang quản lý</a><div class="panel form-panel wide"><h1><?=$id?'Chỉnh sửa phòng':'Đăng phòng cho thuê'?></h1>
<form method="post" enctype="multipart/form-data"><?=csrf()?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?=$id?>">
<?php foreach(['title'=>'Tiêu đề','city'=>'Tỉnh / thành phố','district'=>'Quận / khu vực','type'=>'Loại phòng','price'=>'Giá thuê (đ/tháng)','area'=>'Diện tích (m²)','amenities'=>'Tiện nghi'] as $k=>$label):?><label><?=$label?><input name="<?=$k?>" value="<?=h($_POST[$k]??$r[$k]??($k==='type'?'Phòng trọ':''))?>" <?=$k!=='amenities'?'required':''?> <?=in_array($k,['price','area'])?'type="number" min="1" step="'.($k==='price'?'1':'0.1').'"':''?>></label><?php endforeach;?>
<label>Mô tả<textarea name="description" maxlength="2400" required><?=h($_POST['description']??$r['description']??'')?></textarea></label><label>Tình trạng<select name="available"><option value="1">Còn trống</option><option value="0" <?=!($_POST['available']??$r['available']??1)?'selected':''?>>Đã cho thuê</option></select></label>
<label>Ảnh phòng (JPG, PNG, WebP · tối đa 500 KB)<input type="file" name="photo" accept="image/png,image/jpeg,image/webp"></label><button class="btn primary">Lưu phòng</button></form></div>
