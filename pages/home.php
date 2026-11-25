<?php
require dirname(__DIR__) . '/config/auth.php';
startPage('home');
?>
<?php $browse=isset($_GET['browse']);if(!$browse):?><section class="hero"><div class="hero-copy"><div class="eyebrow"><i></i> KHÁM PHÁ KHÔNG GIAN SỐNG</div><h1>Tìm phòng trọ,<br><em>chọn tổ ấm.</em></h1><p>Khám phá phòng trọ phù hợp với nhu cầu<br class="desktop"> và ngân sách của bạn.</p></div><img class="hero-art" src="<?=asset('images/neighborhood.svg')?>" alt="Minh họa một góc phố Việt Nam"></section><?php else:?><div class="heading"><div class="eyebrow">DANH SÁCH PHÒNG</div><h1>Khám phá phòng trọ</h1></div><?php endif;require ROOT.'/components/listings.php'; ?>
<?php require ROOT.'/components/footer.php'; ?>
