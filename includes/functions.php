<?php
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function money($v): string { return 'R'.number_format((float)$v,2); }
function redirect(string $url): never { header('Location: '.$url); exit; }
function is_logged_in(): bool { return !empty($_SESSION['user_id']); }
function current_user(PDO $pdo): ?array {
 if (!is_logged_in()) return null;
 $s=$pdo->prepare('SELECT * FROM users WHERE id=?'); $s->execute([$_SESSION['user_id']]); return $s->fetch() ?: null;
}
function require_login(): void { if (!is_logged_in()) { $_SESSION['flash']='Please log in or register first.'; redirect('login.php'); } }
function require_paid(PDO $pdo): array {
 require_login(); $u=current_user($pdo);
 if (!$u || $u['registration_status']!=='paid') { $_SESSION['flash']='Pay the R50 registration fee and wait for confirmation before ordering.'; redirect('pay-registration.php'); }
 return $u;
}
function require_admin(): void { if (empty($_SESSION['is_admin'])) redirect('admin-login.php'); }
function flash(): string {
 if (empty($_SESSION['flash'])) return '';
 $m=$_SESSION['flash']; unset($_SESSION['flash']); return '<div class="notice">'.e($m).'</div>';
}
function csrf_token(): string {
 if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token']=bin2hex(random_bytes(32)); return $_SESSION['csrf_token'];
}
function verify_csrf(): void {
 if ($_SERVER['REQUEST_METHOD']==='POST' && !hash_equals($_SESSION['csrf_token']??'',$_POST['csrf_token']??'')) { http_response_code(419); exit('Session expired. Refresh and try again.'); }
}
function upload_image(string $field, string $folder='uploads/products/'): ?string {
 if (empty($_FILES[$field]['name'])) return null;
 if ($_FILES[$field]['error']!==UPLOAD_ERR_OK) throw new RuntimeException('Upload failed.');
 if ($_FILES[$field]['size']>5*1024*1024) throw new RuntimeException('Image must be 5MB or smaller.');
 $info=@getimagesize($_FILES[$field]['tmp_name']); if (!$info) throw new RuntimeException('Choose a valid image.');
 $types=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
 if (!isset($types[$info['mime']??''])) throw new RuntimeException('Use JPG, PNG, WEBP or GIF.');
 if (!is_dir($folder)) mkdir($folder,0775,true);
 $name=bin2hex(random_bytes(12)).'.'.$types[$info['mime']];
 if (!move_uploaded_file($_FILES[$field]['tmp_name'],$folder.$name)) throw new RuntimeException('Could not save image.');
 return $folder.$name;
}
