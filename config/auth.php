<?php
require_once __DIR__.'/database.php';
set_exception_handler(function(Throwable $e){
 error_log($e->getMessage());
 if($e instanceof PDOException)databaseError();
 http_response_code(500);echo 'Không thể hoàn tất yêu cầu.';
});
session_name('TRONHASESSID');
session_start(['cookie_httponly'=>true,'cookie_samesite'=>'Lax','use_strict_mode'=>true]);
$_SESSION['token'] ??= bin2hex(random_bytes(32));
const ROLES = ['renter'=>'Người thuê','landlord'=>'Chủ trọ','admin'=>'Quản trị viên'];
const STATUS = ['pending'=>'Chờ xác nhận','accepted'=>'Đã chấp nhận','rejected'=>'Đã từ chối'];
define('ROOT',dirname(__DIR__));
const ROUTES = [
    'home' => 'pages/home.php',
    'browse' => 'pages/home.php',
    'detail' => 'pages/room_detail.php',
    'login' => 'pages/login.php',
    'register' => 'pages/register.php',
    'logout' => 'pages/logout.php',
    'profile' => 'pages/profile.php',
    'requests' => 'pages/requests.php',
    'dashboard' => 'landlord/index.php',
    'rooms' => 'landlord/rooms/list.php',
    'room_add' => 'landlord/rooms/add.php',
    'room_edit' => 'landlord/rooms/edit.php',
    'room_delete' => 'landlord/rooms/delete.php',
    'inbox' => 'landlord/requests.php',
    'tenants' => 'landlord/tenants/list.php',
    'tenant_add' => 'landlord/tenants/add.php',
    'tenant_edit' => 'landlord/tenants/edit.php',
    'tenant_delete' => 'landlord/tenants/delete.php',
    'admin' => 'admin/index.php',
];
const ACCESS = [
    'dashboard' => 'landlord',
    'rooms' => 'landlord',
    'room_add' => 'landlord',
    'room_edit' => 'landlord',
    'room_delete' => 'landlord',
    'inbox' => 'landlord',
    'tenants' => 'landlord',
    'tenant_add' => 'landlord',
    'tenant_edit' => 'landlord',
    'tenant_delete' => 'landlord',
    'requests' => 'renter',
    'profile' => 'any',
    'logout' => 'any',
    'admin' => 'admin',
];
function h($value){return htmlspecialchars((string)$value,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function sql($query,$args=[]){$s=getConnection()->prepare($query);$s->execute($args);return $s;}
function one($query,$args=[]){return sql($query,$args)->fetch();}
function url($page='home',$args=[]){
 $script=str_replace(DIRECTORY_SEPARATOR,'/',$_SERVER['SCRIPT_NAME']);
 $file=str_replace(DIRECTORY_SEPARATOR,'/',$_SERVER['SCRIPT_FILENAME']);
 $relative=substr($file,strlen(str_replace(DIRECTORY_SEPARATOR,'/',ROOT)));
 $prefix=substr($script,0,strlen($script)-strlen($relative));
 if($page==='browse'){
  if(isset($args['id']))$page='detail';
  else $args=['browse'=>1]+$args;
 }
 return $prefix.'/'.ROUTES[$page].($args?'?'.http_build_query($args):'');
}
function redirectTo($page,$args=[]){header('Location: '.url($page,$args),true,303);exit;}
function notify($s){$_SESSION['message']=$s;}
function need($test,$message){if(!$test)throw new DomainException($message);}
function field($name,$limit,$required=true){$s=trim((string)($_POST[$name]??''));need((!$required||$s!=='')&&mb_strlen($s)<=$limit,'Kiểm tra lại các trường đã nhập.');return $s;}
function csrf(){return '<input type="hidden" name="token" value="'.h($_SESSION['token']).'">';}
function actionForm($action,$id,$label,$class='outline',$extra=''){
 echo '<form method="post" '.$extra.'>'.csrf().'<input type="hidden" name="action" value="'.h($action).'"><input type="hidden" name="id" value="'.(int)$id.'"><button class="btn '.h($class).'">'.h($label).'</button></form>';
}
function resumeAfterLogin($u){
 $target=$_SESSION['next']??['home',[]];unset($_SESSION['next']);
 if(isset(ACCESS[$target[0]])&&!in_array(ACCESS[$target[0]],['any',$u['role']],true))redirectTo('home');
 redirectTo($target[0],$target[1]);
}
function guardPage($page,$user){
 if(!isset(ACCESS[$page]))return;
 if(!$user){$_SESSION['next']=[$page,array_intersect_key($_GET,array_flip(['id','edit']))];redirectTo('login');}
 if(!in_array(ACCESS[$page],['any',$user['role']],true)){notify('Bạn không có quyền truy cập trang này.');redirectTo('home');}
}
function roleCheck($u,$role=null){need($u&&(!$role||$u['role']===$role),'Bạn không có quyền thực hiện thao tác này.');}
function startup($page){
 global $user,$error;
 try{
 $user=isset($_SESSION['uid'])?one('SELECT id,name,email,role,phone FROM users WHERE id=?',[$_SESSION['uid']]):null;
 require_role($page,$user);
 if($_SERVER['REQUEST_METHOD']==='POST'){
 need(hash_equals($_SESSION['token'],(string)($_POST['token']??'')),'Phiên làm việc đã hết hạn. Hãy tải lại trang.');
 require_once __DIR__.'/actions.php';
 processAction($user);
 }
 }catch(DomainException $e){$error=$e->getMessage();}
 catch(PDOException $e){if(isset($e->errorInfo[1])&&$e->errorInfo[1]===1062){$error='Email này đã được sử dụng.';}else{databaseError();}}
}
function databaseError(){http_response_code(503);exit('<!doctype html><html lang="vi"><meta charset="utf-8"><title>Kết nối MySQL</title><h1>Chưa thể đọc dữ liệu MySQL</h1><p>Bật MySQL trong XAMPP, nhập database/phongtro.sql qua phpMyAdmin rồi tải lại trang. Kiểm tra thông số kết nối trong config/database.php nếu bạn đã đổi tài khoản MySQL.</p></html>');}
function price($n){return number_format($n,0,',','.').' đ';}
function photo($room){return str_starts_with($room['image'],'data:image/')?$room['image']:asset($room['image']);}


function asset($path){return substr(url('home'),0,-strlen('pages/home.php')).$path;}
function require_login($page){
 if(!isset($_SESSION['uid'])){$_SESSION['next']=[$page,array_intersect_key($_GET,['id'=>true])];redirectTo('login');}
}
function require_role($page,$user){guardPage($page,$user);}
function startPage($key){
 global $page,$user,$error,$ownerArea;
 $page=$key;
 if(isset(ACCESS[$key]))require_login($key);
 startup($key);
 if($key==='login'&&isset($_GET['room'])&&!$user)$_SESSION['next']=['detail',['id'=>(int)$_GET['room']]];
 require ROOT.'/components/header.php';
}
