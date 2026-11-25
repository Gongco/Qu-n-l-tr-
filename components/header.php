<?php
if(!defined('ROOT'))exit;
require_once ROOT.'/components/icons.php';
$ownerArea=str_starts_with(ROUTES[$page],'landlord/');
$nav=['home'=>'Trang chủ','browse'=>'Khám phá phòng'];
if(($user['role']??'')==='renter')$nav['requests']='Yêu cầu thuê của tôi';
if(($user['role']??'')==='landlord'){$nav['dashboard']='Phòng đang quản lý';$nav['inbox']='Yêu cầu thuê';}
if(($user['role']??'')==='admin')$nav['admin']='Quản lý';
$active=$page==='home'&&isset($_GET['browse'])?'browse':($ownerArea&&$page!=='inbox'?'dashboard':$page);
?>
<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=h($nav[$active]??'Phòng trọ')?> · Trọ Nhà</title>
<link rel="stylesheet" href="<?=asset('css/style.css')?>"><link rel="stylesheet" href="<?=asset('css/home.css')?>"><?php if($ownerArea):?><link rel="stylesheet" href="<?=asset('css/landlord.css')?>"><?php endif;?><link rel="stylesheet" href="<?=asset('css/auth.css')?>"><script defer src="<?=asset('js/app.js')?>"></script></head><body>
<a class="skip" href="#main">Đến nội dung chính</a><header><div class="wrap top"><a class="brand" href="<?=url()?>"><span class="logo-mark"><?=icon('home')?></span><span>Trọ Nhà<b>.</b></span></a><nav><?php foreach($nav as $key=>$label):?><a class="<?=$active===$key?'active':''?>" href="<?=url($key)?>"><?=h($label)?></a><?php endforeach;?></nav><div class="account">
<?php if($user):?><a class="btn plain" href="<?=url('profile')?>"><?=icon('user').h($user['name'])?></a><form method="post" action="<?=url('logout')?>"><?=csrf()?><button class="btn outline" name="action" value="logout">Đăng xuất</button></form><?php else:?><a class="btn plain" href="<?=url('login')?>"><?=icon('door')?>Đăng nhập</a><a class="btn primary" href="<?=url('register')?>">Đăng ký</a><?php endif;?></div></div></header>
<?php if($ownerArea):?><div class="wrap landlord-layout"><?php require ROOT.'/components/sidebar.php';endif;?>
<main tabindex="-1" id="main" class="<?=$ownerArea?'':'wrap '?>page <?=$active==='home'?'homepage':''?>"><?php if(isset($_SESSION['message'])):?><p class="flash" role="status"><?=h($_SESSION['message'])?></p><?php unset($_SESSION['message']);endif;if(!empty($error)):?><p class="flash form-error" role="alert"><?=h($error)?></p><?php endif;?>

