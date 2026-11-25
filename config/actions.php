<?php
function processAction($u){
 $a=$_POST['action']??'';$id=(int)($_POST['id']??0);
 if($a==='logout'){$_SESSION=[];session_regenerate_id(true);redirectTo('home');}
 if($a==='login'||$a==='register'){
  $email=field('email',150);$pass=(string)($_POST['password']??'');
  need(filter_var($email,FILTER_VALIDATE_EMAIL),'Vui lòng nhập email hợp lệ.');
  if($a==='register'){
   $role=field('role',10);need(in_array($role,['renter','landlord'],true)&&strlen($pass)>=8&&strlen($pass)<=72,'Kiểm tra vai trò và mật khẩu (8–72 byte).');
   sql('INSERT INTO users(name,email,hash,role) VALUES(?,?,?,?)',[field('name',120),$email,password_hash($pass,PASSWORD_DEFAULT),$role]);
  }
  $account=one('SELECT id,role,hash FROM users WHERE email=?',[$email]);
  need($account&&password_verify($pass,$account['hash']),'Email hoặc mật khẩu chưa đúng.');
  session_regenerate_id(true);$_SESSION['uid']=$account['id'];notify('Đăng nhập thành công.');resumeAfterLogin($account);
 }
 roleCheck($u);
 if($a==='profile'){
  $email=field('email',150);need(filter_var($email,FILTER_VALIDATE_EMAIL),'Email không hợp lệ.');
  sql('UPDATE users SET name=?,email=?,phone=? WHERE id=?',[field('name',120),$email,field('phone',24,false),$u['id']]);notify('Đã lưu thông tin cá nhân.');redirectTo('profile');
 }
 $db=getConnection();$db->beginTransaction();
 try{
  if(in_array($a,['tenant_save','tenant_delete'],true)){
   roleCheck($u,'landlord');
   if($id){
    $tenant=one('SELECT t.id FROM tenants t JOIN rooms r ON r.id=t.room WHERE t.id=? AND r.owner=? FOR UPDATE',[$id,$u['id']]);
    need($tenant,'Không tìm thấy người thuê của bạn.');
   }
   if($a==='tenant_delete'){
    need($id>0,'Thiếu người thuê.');sql('DELETE FROM tenants WHERE id=?',[$id]);
   }else{
    $room=(int)($_POST['room']??0);
    need(one('SELECT id FROM rooms WHERE id=? AND owner=? AND archived=0 FOR UPDATE',[$room,$u['id']]),'Hãy chọn phòng đang quản lý.');
    $date=field('start_date',10);$valid=DateTime::createFromFormat('!Y-m-d',$date);
    need($valid&&$valid->format('Y-m-d')===$date,'Ngày bắt đầu không hợp lệ.');
    $values=[$room,field('name',120),field('phone',24,false),$date];
    if($id)sql('UPDATE tenants SET room=?,name=?,phone=?,start_date=? WHERE id=?',[...$values,$id]);
    else sql('INSERT INTO tenants(room,name,phone,start_date) VALUES(?,?,?,?)',$values);
   }
   $next='tenants';
  }elseif($a==='save'){
   roleCheck($u,'landlord');
   $room=$id?one('SELECT * FROM rooms WHERE id=? AND owner=? AND archived=0 FOR UPDATE',[$id,$u['id']]):null;
   need(!$id||$room,'Không tìm thấy phòng của bạn.');
   $image=$room['image']??'images/room-1.svg';
   $file=$_FILES['photo']??null;
   if($file&&$file['error']!==UPLOAD_ERR_NO_FILE){
    need($file['error']===UPLOAD_ERR_OK&&$file['size']<=500000,'Ảnh tải lên phải nhỏ hơn 500 KB.');
    $meta=getimagesize($file['tmp_name']);need($meta&&in_array($meta['mime'],['image/png','image/jpeg','image/webp'],true),'Ảnh không hợp lệ.');
    $ext=['image/png'=>'png','image/jpeg'=>'jpg','image/webp'=>'webp'][$meta['mime']];
    $image='uploads/rooms/'.bin2hex(random_bytes(16)).'.'.$ext;
    need(move_uploaded_file($file['tmp_name'], ROOT.'/'.$image),'Không lưu được ảnh phòng.');
    $newUpload=ROOT.'/'.$image;
   }
   $price=filter_var($_POST['price']??'',FILTER_VALIDATE_INT);$area=filter_var($_POST['area']??'',FILTER_VALIDATE_FLOAT);
   need($price>=1&&$price<=999999999&&$area>=1&&$area<=10000,'Giá hoặc diện tích không hợp lệ.');
   $available=(int)(($_POST['available']??'')==='1');
   $values=[field('title',160),field('city',100),field('district',120),$price,$area,field('type',50),field('description',2400),field('amenities',500,false),$image,$available];
   if($id){sql('UPDATE rooms SET title=?,city=?,district=?,price=?,area=?,type=?,description=?,amenities=?,image=?,available=? WHERE id=?',[...$values,$id]);if(!$available)rejectOthers($id);}
   else sql('INSERT INTO rooms(title,city,district,price,area,type,description,amenities,image,available,owner) VALUES(?,?,?,?,?,?,?,?,?,?,?)',[...$values,$u['id']]);
   $next='rooms';
  }else{
   $room=one('SELECT * FROM rooms WHERE id=? AND archived=0 FOR UPDATE',[$id]);need($room,'Phòng không còn tồn tại.');
   switch($a){
    case 'apply':
     roleCheck($u,'renter');need(!$room['hidden']&&$room['available'],'Phòng hiện không nhận yêu cầu.');
     need(!one("SELECT id FROM requests WHERE room=? AND renter=? AND status='pending'",[$id,$u['id']]),'Bạn đã gửi yêu cầu cho phòng này.');
     sql('INSERT INTO requests(room,renter) VALUES(?,?)',[$id,$u['id']]);$next='requests';break;
    case 'hide':case 'restore':
     roleCheck($u,'admin');sql('UPDATE rooms SET hidden=? WHERE id=?',[(int)($a==='hide'),$id]);$next='admin';break;
    case 'delete':case 'accepted':case 'rejected':
     roleCheck($u,'landlord');need($room['owner']==$u['id'],'Phòng không thuộc tài khoản của bạn.');
     if($a==='delete'){
      if(one('SELECT id FROM requests WHERE room=? LIMIT 1',[$id]) || one('SELECT id FROM tenants WHERE room=? LIMIT 1',[$id])){sql('UPDATE rooms SET archived=1 WHERE id=?',[$id]);rejectOthers($id);}
      else sql('DELETE FROM rooms WHERE id=?',[$id]);
      $next='rooms';break;
     }
     if($a==='accepted')need($room['available']&&!$room['hidden'],'Phòng hiện không nhận yêu cầu.');
     need(sql("UPDATE requests SET status=? WHERE id=? AND room=? AND status='pending'",[$a,(int)($_POST['request']??0),$id])->rowCount()===1,'Yêu cầu đã được xử lý.');
     if($a==='accepted'){sql('UPDATE rooms SET available=0 WHERE id=?',[$id]);rejectOthers($id);}
     $next='inbox';break;
    default:throw new DomainException('Thao tác không hợp lệ.');
   }
  }
  $db->commit();
 }catch(Throwable $e){$db->rollBack();if(isset($newUpload))unlink($newUpload);throw $e;}
 notify('Đã cập nhật thành công.');redirectTo($next);
}
function rejectOthers($id){sql("UPDATE requests SET status='rejected' WHERE room=? AND status='pending'",[$id]);}

