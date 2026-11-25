<?php if(!defined('ROLES'))exit;$profile=$page==='profile';$reg=$page==='register';$title=$profile?'Thông tin cá nhân':($reg?'Đăng ký tài khoản':'Đăng nhập');?>
<section class="form-panel panel"><h1><?=$title?></h1><?php if(!$profile&&isset($_SESSION['next'])):?><p>Vui lòng đăng nhập để tiếp tục.</p><?php endif;?><form method="post"><?=csrf()?><input type="hidden" name="action" value="<?=$page?>">
<?php if($reg||$profile):?><label>Họ và tên<input name="name" maxlength="120" required value="<?=h($profile?$user['name']:'')?>"></label><?php endif;?>
<label>Email<input type="email" name="email" maxlength="150" required value="<?=h($profile?$user['email']:'')?>"></label>
<?php if($profile):?><label>Số điện thoại<input type="tel" name="phone" maxlength="24" value="<?=h($user['phone'])?>"></label><?php else:?><label>Mật khẩu<input type="password" name="password" required <?=$reg?'minlength="8" autocomplete="new-password"':'autocomplete="current-password"'?>></label><?php endif;?>
<?php if($reg):?><label>Vai trò<select name="role"><option value="renter">Người thuê</option><option value="landlord">Chủ trọ</option></select></label><?php endif;?>
<button class="btn primary"><?=$profile?'Lưu thay đổi':$title?></button></form><?php if(!$profile):?><p class="center"><a class="text-link" href="<?=url($reg?'login':'register')?>"><?=$reg?'Đã có tài khoản? Đăng nhập':'Chưa có tài khoản? Đăng ký ngay'?></a></p><?php endif;?></section>

