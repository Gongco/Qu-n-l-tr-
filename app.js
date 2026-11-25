'use strict';
const $=s=>document.querySelector(s), adminPage=location.pathname.endsWith('/admin.html');
const e=v=>String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fmt=n=>Number(n).toLocaleString('vi-VN');
const paths={home:'m3 12 9-8 9 8M5 11v10h5v-7h4v7h5V11',pin:'M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0z M14 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0z',coin:'M3 6h18v13H3z M3 10h18m-6 5h3',area:'M3 9V3h6m6 0h6v6m0 6v6h-6m-6 0H3v-6',search:'M16 16l5 5 M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0z',arrow:'M4 12h15m-6-6 6 6-6 6',chev:'m7 10 5 5 5-5',door:'M10 4h10v16H10M2 12h13m-5-5 5 5-5 5',user:'M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0z M3 21a9 9 0 0 1 18 0',plus:'M12 4v16M4 12h16'};
const icon=k=>'<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="'+paths[k]+'"/></svg>';
const url=p=>p==='admin'?'admin.html':(adminPage?'index.html':'')+'#'+p;
const link=(p,label,cls='text-link')=>'<a class="'+cls+'" href="'+e(url(p))+'">'+label+'</a>';
const action=(a,id,t,cls='outline')=>'<button class="btn '+cls+'" data-act="'+a+'" data-id="'+e(id)+'">'+t+'</button>';
const access={profile:'any',manage:'landlord',edit:'landlord',inbox:'landlord',requests:'renter',apply:'renter',admin:'admin'};
const route=()=>{const [p,q]=location.hash.slice(1).split('?');return {p:adminPage?'admin':p||'home',q:new URLSearchParams(q)};};
const permitted=(p,u)=>!access[p]||!!u&&(access[p]==='any'||access[p]===u.role);
function next(dest,u){const p=dest.split('?')[0];return ['home','browse','room',...Object.keys(access)].includes(p)&&permitted(p,u)?dest:'home';}
function go(p){if(location.hash==='#'+p&&!adminPage)render();else location.href=url(p);}
let timer;
function note(t,bad=false){const n=$('#notice');n.textContent=t;n.className=bad?'show error':'show';clearTimeout(timer);timer=setTimeout(()=>n.className='',4200);}
function header(u,p){
 const navs=[['home','Trang chủ'],['browse','Khám phá phòng']];
 if(u?.role==='landlord')navs.push(['manage','Phòng đang quản lý'],['inbox','Yêu cầu thuê']);
 if(u?.role==='renter')navs.push(['requests','Yêu cầu thuê của tôi']);
 if(u?.role==='admin')navs.push(['admin','Quản lý']);
 $('#header').innerHTML='<div class="wrap top">'+link('home','<span class="logo-mark">'+icon('home')+'</span><span>Trọ Nhà<b>.</b></span>','brand')+'<nav>'+navs.map(([k,t])=>link(k,t,k===p?'active':'')).join('')+'</nav><div class="account">'+(u?link('profile',icon('user')+e(u.name),'btn plain')+action('logout','','Đăng xuất'):link('login',icon('door')+'Đăng nhập','btn plain')+link('register','Đăng ký','btn primary'))+'</div></div>';
}
const empty=t=>'<div class="empty">'+icon('home')+'<p>'+e(t)+'</p></div>';
const opts=(pairs,v)=>pairs.map(([k,t])=>'<option value="'+e(k)+'" '+(String(k)===String(v)?'selected':'')+'>'+e(t)+'</option>').join('');
function filters(rooms,q){
 const field=(name,label,k,pairs)=>'<label class="filter">'+icon(k)+'<span>'+label+'<select name="'+name+'">'+opts(pairs,q.get(name)||'')+'</select></span>'+icon('chev')+'</label>';
 const cities=[...new Set(rooms.filter(visible).map(r=>r.city))].sort((a,b)=>a.localeCompare(b,'vi'));
 return '<form id="filters" class="filters">'+field('city','Địa điểm','pin',[['','Tất cả khu vực'],...cities.map(c=>[c,c])])+field('price','Giá thuê / tháng','coin',[['','Tất cả mức giá'],[3000000,'Đến 3 triệu'],[5000000,'Đến 5 triệu'],[7000000,'Đến 7 triệu']])+field('area','Diện tích','area',[['','Tất cả diện tích'],[25,'Từ 25 m²'],[30,'Từ 30 m²'],[40,'Từ 40 m²']])+'<button class="btn primary search">'+icon('search')+'Tìm phòng</button></form>';
}
const hero='<section class="hero"><div class="hero-copy"><div class="eyebrow"><i></i> KHÁM PHÁ KHÔNG GIAN SỐNG</div><h1>Tìm phòng trọ,<br><em>chọn tổ ấm.</em></h1><p>Khám phá phòng trọ phù hợp với nhu cầu<br class="desktop"> và ngân sách của bạn.</p></div><img class="hero-art" src="images/neighborhood.svg" alt="Minh họa một góc phố Việt Nam"></section>';
const badge=r=>'<span class="badge '+(r.available?'green':'gray')+'"><i></i>'+(r.available?'Còn trống':'Đã cho thuê')+'</span>';
const photo=r=>'<img loading="lazy" src="'+e(/^(images\/room-[1-6]\.svg$|data:image\/(png|jpeg|webp);base64,)/.test(r.image)?r.image:'images/room-1.svg')+'" alt="'+e(r.title)+'">';
document.addEventListener('error',ev=>{const img=ev.target;if(img.tagName==='IMG'&&!img.src.endsWith('/images/room-1.svg'))img.src='images/room-1.svg';},true);
function card(r){return link('room?id='+encodeURIComponent(r.id),'<div class="card-img">'+photo(r)+badge(r)+'</div><div class="card-body"><div class="meta"><span>'+e(r.type)+'</span><span>'+icon('area')+e(r.area)+' m²</span></div><h3>'+e(r.title)+'</h3><div class="location">'+icon('pin')+e(r.district)+', '+e(r.city)+'</div><div class="card-end"><span><strong>'+fmt(r.price)+' đ</strong> / tháng</span><b>'+icon('arrow')+'</b></div></div>','card');}
function listing(db,p,q){
 const home=p==='home';const rooms=db.rooms.filter(r=>visible(r)&&(home||((!q.get('city')||r.city===q.get('city'))&&(!q.get('price')||r.price<=+q.get('price'))&&(!q.get('area')||r.area>=+q.get('area')))));
 return (home?hero:'<div class="heading"><div class="eyebrow">DANH SÁCH PHÒNG</div><h1>Khám phá phòng trọ</h1></div>')+filters(db.rooms,q)+'<section class="section"><div class="section-head"><div>'+(home?'<div class="eyebrow">GỢI Ý CHO BẠN</div><h2>Phòng trọ nổi bật</h2>':'<h2>'+rooms.length+' phòng phù hợp</h2>')+'</div>'+link('browse',home?'Xem tất cả '+icon('arrow'):'Đặt lại bộ lọc')+'</div>'+(rooms.length?'<div class="cards">'+rooms.map(card).join('')+'</div>':empty('Chưa tìm thấy phòng phù hợp.'))+'</section>';
}
function detail(db,u,id){
 const r=db.rooms.find(x=>x.id===id);if(!visible(r))return empty('Phòng hiện không hiển thị.');
 const pending=u&&db.requests.some(q=>q.room===id&&q.renter===u.id&&q.status==='pending');
 const send=!r.available?'<p>Phòng hiện đã cho thuê.</p>':pending?link('requests','Xem yêu cầu đã gửi','btn outline'):!u?link('apply?id='+id,'Gửi yêu cầu thuê','btn primary'):u.role==='renter'?action('apply',id,'Gửi yêu cầu thuê','primary'):'';
 return link('browse','← Danh sách phòng')+'<div class="detail"><div><div class="detail-img">'+photo(r)+badge(r)+'</div><div class="panel"><h2>Mô tả phòng</h2><p class="preserve">'+e(r.description)+'</p><h3>Tiện nghi</h3><div class="chips">'+(r.amenities.map(a=>'<span>'+e(a)+'</span>').join('')||'Chưa cập nhật')+'</div></div></div><aside class="panel"><div class="eyebrow">'+e(r.type)+'</div><h1>'+e(r.title)+'</h1><p class="location">'+icon('pin')+e(r.district)+', '+e(r.city)+'</p><p>'+icon('area')+' '+e(r.area)+' m²</p><p class="price">'+fmt(r.price)+' đ <small>/ tháng</small></p><p>Chủ trọ · '+e(db.users.find(x=>x.id===r.owner)?.name)+'</p>'+send+'</aside></div>';
}
const input=(label,name,v='',type='text',attrs='required maxlength="120"')=>'<label>'+label+'<input name="'+name+'" type="'+type+'" value="'+e(v)+'" '+attrs+'></label>';
const select=(label,name,pairs,v)=>'<label>'+label+'<select name="'+name+'">'+opts(pairs,v)+'</select></label>';
const formEnd=t=>'<p class="form-error" role="alert" hidden></p><button class="btn primary">'+t+'</button></form>';
function account(p,u,q){
 const reg=p==='register',profile=p==='profile',title=reg?'Đăng ký tài khoản':profile?'Thông tin cá nhân':'Đăng nhập',suffix=q.get('next')?'?next='+encodeURIComponent(q.get('next')):'';
 return '<div class="form-panel panel"><div class="eyebrow">TÀI KHOẢN TRỌ NHÀ</div><h1>'+title+'</h1>'+(!reg&&!profile&&suffix?'<p>Vui lòng đăng nhập để tiếp tục.</p>':'')+'<form id="'+p+'">'+(reg||profile?input('Họ và tên','name',profile?u.name:''):'')+input('Email','email',profile?u.email:'','email','required maxlength="150" autocomplete="email"')+(profile?input('Số điện thoại','phone',u.phone,'tel','maxlength="20"'):input('Mật khẩu','password','','password',reg?'required minlength="8" placeholder="Ít nhất 8 ký tự" autocomplete="new-password"':'required autocomplete="current-password"'))+(reg?select('Loại tài khoản','role',[['renter','Người thuê'],['landlord','Chủ trọ']],'renter'):'')+formEnd(profile?'Lưu thay đổi':title)+(profile?'':'<p class="center">'+(reg?'Đã có tài khoản? ':'Chưa có tài khoản? ')+link((reg?'login':'register')+suffix,reg?'Đăng nhập':'Đăng ký ngay')+'</p>')+'</div>';
}
function manage(db,u){
 const rooms=db.rooms.filter(r=>!r.archived&&r.owner===u.id);
 return '<div class="section-head"><h1>Phòng đang quản lý</h1>'+link('edit',icon('plus')+'Đăng phòng','btn primary')+'</div>'+(rooms.length?'<div class="rows">'+rooms.map(r=>'<article class="row"><div><h3>'+e(r.title)+'</h3><p>'+fmt(r.price)+' đ/tháng · '+e(r.city)+' · '+(r.hidden?'Đang bị ẩn':r.available?'Còn trống':'Đã cho thuê')+'</p></div><div class="actions">'+link('edit?id='+r.id,'Chỉnh sửa','btn outline')+action('delete',r.id,'Xóa','danger')+'</div></article>').join('')+'</div>':empty('Bạn chưa đăng phòng nào.'));
}
function editor(db,u,id){
 const r=id?owned(db,u,id):{available:true,amenities:[]};
 return link('manage','← Phòng đang quản lý')+'<div class="form-panel panel wide"><h1>'+(id?'Chỉnh sửa phòng':'Đăng phòng cho thuê')+'</h1><form id="edit">'+input('Tiêu đề','title',r.title)+'<div class="form-grid">'+input('Tỉnh / thành phố','city',r.city)+input('Quận / huyện, khu vực','district',r.district)+input('Giá thuê (đ/tháng)','price',r.price,'number','required min="1" max="999999999"')+input('Diện tích (m²)','area',r.area,'number','required min="1" max="10000" step="0.1"')+select('Loại phòng','type',['Phòng trọ','Studio','Căn hộ mini'].map(t=>[t,t]),r.type)+select('Tình trạng','available',[['true','Còn trống'],['false','Đã cho thuê']],String(r.available))+'</div><label>Mô tả<textarea required maxlength="2400" name="description">'+e(r.description)+'</textarea></label>'+input('Tiện nghi (cách nhau bằng dấu phẩy)','amenities',r.amenities.join(', '),'text','maxlength="350"')+'<label>Ảnh phòng (JPG, PNG, WebP · tối đa 500 KB)<input type="file" name="photo" accept="image/jpeg,image/png,image/webp"></label>'+formEnd('Lưu phòng')+'</div>';
}
function historyList(db,u,p){
 const list=db.requests.filter(q=>p==='admin'||(p==='requests'?q.renter===u.id:db.rooms.find(r=>r.id===q.room)?.owner===u.id));
 return (p==='admin'?'':'<h1>'+(p==='requests'?'Yêu cầu thuê của tôi':'Yêu cầu thuê phòng')+'</h1>')+(list.length?'<div class="rows">'+list.map(q=>{
 const r=db.rooms.find(x=>x.id===q.room),person=db.users.find(x=>x.id===q.renter);
 return '<article class="row"><div><h3>'+(visible(r)?link('room?id='+r.id,e(r.title),'room-link'):e(r.title))+'</h3><p>'+e(person.name)+' · '+new Date(q.date).toLocaleDateString('vi-VN')+(r.archived?' · Tin đã xóa':r.hidden?' · Tin đang ẩn':'')+'</p></div><div class="actions"><span class="status '+q.status+'">'+states[q.status]+'</span>'+(p==='inbox'&&q.status==='pending'?((visible(r)&&r.available?action('accepted',q.id,'Chấp nhận','primary'):'')+action('rejected',q.id,'Từ chối')):'')+'</div></article>';}).join('')+'</div>':empty('Chưa có yêu cầu thuê phòng.'));
}
function dashboard(db,u){
 return '<div class="eyebrow">QUẢN TRỊ</div><h1>Tổng quan hệ thống</h1><div class="stats">'+[[db.users.length,'Tài khoản'],[db.rooms.filter(r=>!r.archived).length,'Tin phòng'],[db.requests.length,'Yêu cầu thuê']].map(([n,t])=>'<div class="panel"><strong>'+n+'</strong>'+t+'</div>').join('')+'</div><h2>Người dùng</h2><div class="rows">'+db.users.map(x=>'<div class="row"><div><h3>'+e(x.name)+'</h3><p>'+e(x.email)+'</p></div>'+roles[x.role]+'</div>').join('')+'</div><h2>Kiểm duyệt phòng</h2><div class="rows">'+(db.rooms.map(r=>'<div class="row"><div><h3>'+e(r.title)+'</h3><p>'+(r.archived?'Đã lưu trữ':r.hidden?'Đang ẩn':'Đang hiển thị')+'</p></div>'+(r.archived?'':action(r.hidden?'restore':'hide',r.id,r.hidden?'Khôi phục':'Ẩn tin'))+'</div>').join('')||empty('Chưa có phòng.'))+'</div><h2>Yêu cầu thuê</h2>'+historyList(db,u,'admin');
}
function render(){
 const db=read(),u=current(db),{p,q}=route(),id=q.get('id');
 if(!permitted(p,u)){if(!u)location.replace(url('login?next='+encodeURIComponent(adminPage?'admin':location.hash.slice(1))));else{note('Bạn không có quyền truy cập.',true);go('home');}return;}
 if(p==='admin'&&!adminPage){go('admin');return;}if(p==='apply'){go('room?id='+encodeURIComponent(id));return;}
 if(u&&['register','login'].includes(p)){go(next(q.get('next')||'home',u));return;}
 header(u,p);const main=$('#main');main.className='wrap page '+(p==='home'?'homepage':'');
 try{switch(p){case 'home':case 'browse':main.innerHTML=listing(db,p,q);break;case 'room':main.innerHTML=detail(db,u,id);break;case 'login':case 'register':case 'profile':main.innerHTML=account(p,u,q);break;case 'manage':main.innerHTML=manage(db,u);break;case 'edit':main.innerHTML=editor(db,u,id);break;case 'requests':case 'inbox':main.innerHTML=historyList(db,u,p);break;case 'admin':main.innerHTML=dashboard(db,u);break;default:main.innerHTML=empty('Trang không tồn tại.');}}catch(err){main.innerHTML=empty(err.message);}
 document.title=(main.querySelector('h1')?.innerText.replace(/\n/g,' ')||'Tìm phòng trọ')+' · Trọ Nhà';
}
async function readPhoto(file){if(!file?.size)return '';check(file.size<=500000&&['image/jpeg','image/png','image/webp'].includes(file.type),'Vui lòng chọn ảnh đúng định dạng, tối đa 500 KB.');return new Promise((resolve,reject)=>{const reader=new FileReader();reader.onload=()=>resolve(reader.result);reader.onerror=()=>reject(Error('Không đọc được ảnh.'));reader.readAsDataURL(file);});}
document.addEventListener('submit',async ev=>{
 const f=ev.target;if(f.method==='dialog')return;ev.preventDefault();
 const fd=new FormData(f),v=k=>String(fd.get(k)||'').trim(),q=route().q;
 if(f.id==='filters'){const params=new URLSearchParams();for(const [k,val] of fd)if(val)params.set(k,val);go('browse'+(params.size?'?'+params:''));return;}
 const submit=f.querySelector('button');submit.disabled=true;
 try{
 if(f.id==='login'){const u=read().users.find(x=>x.email===v('email').toLowerCase());check(u&&await hashPassword(fd.get('password'),u.salt)===u.hash,'Email hoặc mật khẩu chưa đúng.');sessionStorage.setItem(SESSION,u.id);go(next(q.get('next')||'home',u));note('Đăng nhập thành công.');}
 if(['register','profile'].includes(f.id)){
 const reg=f.id==='register',name=v('name'),email=v('email').toLowerCase();check(name,'Vui lòng nhập họ và tên.');
 const result=await change(async(db,u)=>{if(!reg)guard(u);check(!db.users.some(x=>x.email===email&&(reg||x.id!==u.id)),'Email này đã được sử dụng.');if(!reg){Object.assign(u,{name,email,phone:v('phone')});return u;}
 const role=v('role');check(['renter','landlord'].includes(role),'Vai trò không hợp lệ.');const salt=uid(),account={id:uid(),role,name,email,phone:'',salt,hash:await hashPassword(fd.get('password'),salt)};db.users.push(account);return account;});
 if(reg){sessionStorage.setItem(SESSION,result.id);go(next(q.get('next')||'home',result));}else render();note(reg?'Đăng ký thành công.':'Đã lưu thông tin cá nhân.');}
 if(f.id==='edit'){
 const photo=await readPhoto(fd.get('photo')),id=q.get('id'),data={title:v('title'),city:v('city'),district:v('district'),price:+v('price'),area:+v('area'),type:v('type'),available:v('available')==='true',description:v('description'),amenities:v('amenities').split(',').map(s=>s.trim()).filter(Boolean)};
 check(data.title&&data.city&&data.district&&data.description&&data.price>0&&data.area>0,'Vui lòng nhập đủ thông tin phòng.');
 await change((db,u)=>{guard(u,'landlord');if(id){const r=owned(db,u,id);Object.assign(r,data);if(photo)r.image=photo;if(!r.available)rejectPending(db,id);}else db.rooms.unshift({...data,id:uid(),owner:u.id,hidden:false,archived:false,image:photo||'images/room-1.svg'});});go('manage');note('Đã lưu thông tin phòng.');}
 }catch(err){const msg=f.querySelector('.form-error');msg.hidden=false;msg.textContent=err.name==='QuotaExceededError'?'Bộ nhớ đầy. Hãy chọn ảnh nhỏ hơn.':err.message;}finally{submit.disabled=false;}
});
function confirmRemoval(){const d=document.createElement('dialog');d.className='panel confirm-dialog';d.innerHTML='<form method="dialog"><h2>Xóa tin cho thuê?</h2><p>Lịch sử yêu cầu thuê sẽ được giữ lại.</p><div class="actions"><button value="cancel" class="btn outline" autofocus>Hủy</button><button value="delete" class="btn primary">Xóa tin</button></div></form>';document.body.append(d);return new Promise(resolve=>{d.onclose=()=>{const ok=d.returnValue==='delete';d.remove();resolve(ok);};d.showModal();});}
document.addEventListener('click',async ev=>{
 if(ev.target.closest('.skip')){ev.preventDefault();$('#main').focus();return;}
 const b=ev.target.closest('[data-act]');if(!b)return;const a=b.dataset.act;
 if(a==='logout'){sessionStorage.removeItem(SESSION);go('home');note('Đã đăng xuất.');return;}
 if(a==='delete'&&!await confirmRemoval())return;b.disabled=true;
 try{await change((db,u)=>act(db,u,a,b.dataset.id));render();note(({apply:'Đã gửi yêu cầu thuê.',accepted:'Đã chấp nhận yêu cầu.',rejected:'Đã từ chối yêu cầu.',delete:'Đã xóa tin.',hide:'Đã ẩn tin.',restore:'Đã khôi phục tin.'})[a]);}catch(err){note(err.message,true);}finally{b.disabled=false;}
});
addEventListener('hashchange',()=>{render();window.scrollTo(0,0);});
addEventListener('storage',ev=>{if(ev.key===DB)render();});
seed().then(render).catch(()=>{$('#main').textContent='Hãy mở trang qua localhost và cho phép trình duyệt lưu dữ liệu.';});

