'use strict';
const DB='tronha.data.v1', SESSION='tronha.session.v1';
const roles={renter:'Người thuê',landlord:'Chủ trọ',admin:'Quản trị viên'};
const states={pending:'Chờ xác nhận',accepted:'Đã chấp nhận',rejected:'Đã từ chối'};
const read=()=>JSON.parse(localStorage.getItem(DB));
const current=db=>db.users.find(u=>u.id===sessionStorage.getItem(SESSION));
const uid=()=>crypto.randomUUID();
const visible=r=>r&&!r.hidden&&!r.archived;
function check(ok,msg){if(!ok)throw Error(msg);}
function guard(u,role){check(u&&(!role||role===u.role),'Bạn không có quyền thực hiện thao tác này.');}
async function hashPassword(password,salt){
 const enc=new TextEncoder(),key=await crypto.subtle.importKey('raw',enc.encode(password),'PBKDF2',false,['deriveBits']);
 const bits=await crypto.subtle.deriveBits({name:'PBKDF2',iterations:120000,salt:enc.encode(salt),hash:'SHA-256'},key,256);
 return [...new Uint8Array(bits)].map(x=>x.toString(16).padStart(2,'0')).join('');
}
async function change(fn){return navigator.locks.request(DB,async()=>{const db=read();const result=await fn(db,current(db));localStorage.setItem(DB,JSON.stringify(db));return result;});}
async function seed(){await navigator.locks.request(DB,async()=>{
 if(read())return;
 const users=await Promise.all([['renter','Minh Anh','nguoithue'],['landlord','Hoàng An','chutro'],['admin','Quản trị','admin']].map(async([role,name,alias])=>{
 const salt=uid();return {id:role,name,email:alias+'@tronha.vn',role,phone:'',salt,hash:await hashPassword('Demo1234!',salt)};}));
 const samples=[
 ['Studio đón nắng có ban công','Hạ Long','Bãi Cháy',3500000,28,'Studio','Ban công riêng đón nắng sớm, có góc làm việc cạnh cửa sổ.'],
 ['Phòng gác lửng gần trường','Hà Nội','Cầu Giấy',2800000,24,'Phòng trọ','Khu ngủ trên gác, có chỗ để xe và gần tuyến xe buýt.'],
 ['Căn hộ mini đầy đủ nội thất','TP. Hồ Chí Minh','Bình Thạnh',5200000,38,'Căn hộ mini','Căn hộ có giường, tủ, bếp riêng và phòng tắm khép kín.'],
 ['Studio thoáng mát gần biển','Đà Nẵng','Sơn Trà',4200000,32,'Studio','Cửa sổ lớn nhìn ra sân, có thể đi bộ đến biển.'],
 ['Phòng trọ có bếp riêng','Hạ Long','Hồng Hải',2100000,20,'Phòng trọ','Phòng khép kín gần chợ và trường học.'],
 ['Căn hộ một phòng ngủ','Hà Nội','Thanh Xuân',6000000,45,'Căn hộ mini','Phòng ngủ riêng, phòng khách rộng và thang máy.']];
 const rooms=samples.map(([title,city,district,price,area,type,description],i)=>({id:'room-'+(i+1),owner:'landlord',title,city,district,price,area,type,description,amenities:['Wi-Fi','Bếp riêng',i%2?'Chỗ để xe':'Điều hòa'],image:'images/room-'+(i+1)+'.svg',available:i!==5,archived:false,hidden:false}));
 const requests=[['room-5','pending'],['room-6','accepted']].map(([room,status])=>({id:uid(),room,renter:'renter',status,date:new Date().toISOString()}));
 localStorage.setItem(DB,JSON.stringify({users,rooms,requests}));
});}
function owned(db,u,id){guard(u,'landlord');const r=db.rooms.find(x=>x.id===id);check(r&&!r.archived&&r.owner===u.id,'Bạn không thể quản lý phòng này.');return r;}
function rejectPending(db,id){db.requests.filter(q=>q.room===id&&q.status==='pending').forEach(q=>q.status='rejected');}
function act(db,u,action,id){
 if(action==='apply'){guard(u,'renter');const r=db.rooms.find(x=>x.id===id);check(visible(r)&&r.available,'Phòng hiện không nhận yêu cầu.');check(!db.requests.some(q=>q.room===id&&q.renter===u.id&&q.status==='pending'),'Bạn đã gửi yêu cầu cho phòng này.');db.requests.unshift({id:uid(),room:id,renter:u.id,status:'pending',date:new Date().toISOString()});return;}
 if(action==='delete'){const r=owned(db,u,id);if(db.requests.some(q=>q.room===id)){r.archived=true;rejectPending(db,id);}else db.rooms=db.rooms.filter(x=>x!==r);return;}
 if(['hide','restore'].includes(action)){guard(u,'admin');const r=db.rooms.find(x=>x.id===id);check(r&&!r.archived,'Tin không còn tồn tại.');r.hidden=action==='hide';return;}
 check(['accepted','rejected'].includes(action),'Thao tác không hợp lệ.');
 const q=db.requests.find(x=>x.id===id);check(q&&q.status==='pending','Yêu cầu đã được xử lý.');const r=owned(db,u,q.room);
 if(action==='accepted')check(visible(r)&&r.available,'Phòng hiện không nhận yêu cầu.');
 q.status=action;if(action==='accepted'){r.available=false;rejectPending(db,r.id);}
}

