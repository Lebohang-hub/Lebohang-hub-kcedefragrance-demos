<?php $pageTitle=$pageTitle??SITE_NAME; ?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=e($pageTitle)?> | <?=e(SITE_NAME)?></title><link rel="stylesheet" href="assets/style.css"></head><body>
<header class="topbar"><a class="brand" href="index.php"><?=e(SITE_NAME)?></a><nav><a href="index.php">Shop</a><a href="cart.php">Basket (<?=array_sum($_SESSION['cart']??[])?>)</a>
<?php if(is_logged_in()):?><a href="member.php">My Account</a><a href="my-orders.php">My Orders</a><a href="logout.php">Log out</a>
<?php else:?><a href="login.php">Log in</a><a class="nav-cta" href="register.php">Register</a><?php endif;?></nav></header><main class="container"><?=flash()?>
