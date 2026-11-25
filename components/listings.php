<?php
if(!defined('ROOT'))exit;
$cities=sql('SELECT DISTINCT city FROM rooms WHERE hidden=0 AND archived=0 ORDER BY city')->fetchAll(PDO::FETCH_COLUMN);
$where=['hidden=0','archived=0'];$params=[];
foreach(['city'=>'city=?','price'=>'price<=?','area'=>'area>=?'] as $key=>$clause){
 $value=$_GET[$key]??'';if($browse&&is_string($value)&&$value!==''){$where[]=$clause;$params[]=$value;}
}
$rooms=sql('SELECT * FROM rooms WHERE '.implode(' AND ',$where).' ORDER BY available DESC,id',$params)->fetchAll();
$fields=[
 ['city','Địa điểm','pin',[''=>'Tất cả khu vực']+array_combine($cities,$cities)],
 ['price','Giá thuê / tháng','coin',[''=>'Tất cả mức giá',3000000=>'Đến 3 triệu',5000000=>'Đến 5 triệu',7000000=>'Đến 7 triệu']],
 ['area','Diện tích','area',[''=>'Tất cả diện tích',25=>'Từ 25 m²',30=>'Từ 30 m²',40=>'Từ 40 m²']]
];?>
<form class="filters" method="get" action="<?=url('home')?>"><input type="hidden" name="browse" value="1">
<?php foreach($fields as [$key,$label,$symbol,$options]):?><label class="filter"><?=icon($symbol)?><span><?=h($label)?><select name="<?=$key?>"><?php foreach($options as $v=>$text):?><option value="<?=h($v)?>" <?=((string)$v===($_GET[$key]??''))?'selected':''?>><?=h($text)?></option><?php endforeach;?></select></span><?=icon('chev')?></label><?php endforeach;?><button class="btn primary search"><?=icon('search')?>Tìm phòng</button></form>
<section class="section"><div class="section-head"><div><?php if(!$browse):?><div class="eyebrow">GỢI Ý CHO BẠN</div><?php endif;?><h2><?=$browse?count($rooms).' phòng phù hợp':'Phòng trọ nổi bật'?></h2></div><a class="text-link" href="<?=url('browse')?>"><?=$browse?'Đặt lại bộ lọc':'Xem tất cả '.icon('arrow')?></a></div>
<?php if(!$rooms):?><p class="empty">Chưa tìm thấy phòng phù hợp.</p><?php endif;?><div class="cards"><?php foreach($rooms as $r):?><a class="card" href="<?=url('detail',['id'=>$r['id']])?>"><div class="card-img"><img loading="lazy" src="<?=h(photo($r))?>" alt="<?=h($r['title'])?>"><span class="badge <?=$r['available']?'green':'gray'?>"><i></i><?=$r['available']?'Còn trống':'Đã cho thuê'?></span></div><div class="card-body"><div class="meta"><span><?=h($r['type'])?></span><span><?=icon('area')?><?=(float)$r['area']?> m²</span></div><h3><?=h($r['title'])?></h3><div class="location"><?=icon('pin').h($r['district'].', '.$r['city'])?></div><div class="card-end"><span><strong><?=price($r['price'])?></strong> / tháng</span><b><?=icon('arrow')?></b></div></div></a><?php endforeach;?></div></section>

