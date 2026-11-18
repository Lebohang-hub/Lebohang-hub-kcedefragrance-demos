<?php
// PayFast Instant Transaction Notification (ITN). This endpoint must be publicly reachable over HTTPS in production.
require 'config.php';require 'includes/payfast.php';
http_response_code(200);
$data=$_POST;
if(!$data || !payfast_validate_itn($data)){error_log('PayFast ITN failed signature/server validation.');exit('Invalid notification');}
if (($data['merchant_id'] ?? '') !== PAYFAST_MERCHANT_ID) { error_log('PayFast ITN merchant ID mismatch.'); exit('Merchant mismatch'); }
$paymentId=trim((string)($data['m_payment_id']??''));$status=strtoupper(trim((string)($data['payment_status']??'')));$pfPaymentId=trim((string)($data['pf_payment_id']??''));$gross=(float)($data['amount_gross']??0);
if($paymentId==='')exit('Missing payment reference');
try{
 $pdo->prepare('INSERT IGNORE INTO payment_notifications(payment_id,payfast_payment_id,payment_status,raw_payload) VALUES(?,?,?,?)')->execute([$paymentId,$pfPaymentId,$status,json_encode($data,JSON_UNESCAPED_SLASHES)]);
 if($status!=='COMPLETE')exit('Notification recorded');
 if(str_starts_with($paymentId,'REG-')){
   $s=$pdo->prepare('SELECT id FROM users WHERE registration_payment_id=?');$s->execute([$paymentId]);$user=$s->fetch();
   if(!$user || abs($gross-REGISTRATION_FEE)>0.01)exit('Registration payment mismatch');
   $code='MEM'.str_pad((string)$user['id'],6,'0',STR_PAD_LEFT);
   $s=$pdo->prepare("UPDATE users SET registration_status='paid',member_code=COALESCE(member_code,?),registration_paid_at=NOW() WHERE id=? AND registration_payment_id=?");$s->execute([$code,$user['id'],$paymentId]);
 }elseif(str_starts_with($paymentId,'ORD-')){
   $s=$pdo->prepare('SELECT id,total FROM orders WHERE payment_id=?');$s->execute([$paymentId]);$order=$s->fetch();
   if(!$order || abs((float)$order['total']-$gross)>0.01)exit('Order payment mismatch');
   $s=$pdo->prepare("UPDATE orders SET payment_status='Paid',payfast_payment_id=?,paid_at=NOW() WHERE id=? AND payment_id=?");$s->execute([$pfPaymentId,$order['id'],$paymentId]);
 }
 echo 'OK';
}catch(Throwable $e){error_log('PayFast ITN database error: '.$e->getMessage());http_response_code(500);echo 'Processing error';}
