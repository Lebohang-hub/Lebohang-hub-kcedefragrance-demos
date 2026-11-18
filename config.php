<?php
if (session_status() === PHP_SESSION_NONE) session_start();
define('DB_HOST','localhost');
define('DB_NAME','fragrance_shop');
define('DB_USER','root');
define('DB_PASS','');
define('SITE_NAME','KCE DeFragrance');
define('SITE_URL','http://localhost/fragrance_sales_system'); // Change to your public HTTPS website before going live.
define('REGISTRATION_FEE',50.00);

// PayFast sandbox settings. These three values must be used as one matching set.
// Default PayFast test set: ID 10000100, key 46f0cd694581a, passphrase jt7NOE43FZPn.
// If you use your own sandbox account, replace ALL THREE with that account's values.
define('PAYFAST_TEST_MODE', true);
define('PAYFAST_MERCHANT_ID', '10004002');
define('PAYFAST_MERCHANT_KEY', 'q1cd2rdny4a53');
define('PAYFAST_PASSPHRASE', 'payfast');
define('PAYFAST_PROCESS_URL', PAYFAST_TEST_MODE ? 'https://sandbox.payfast.co.za/eng/process' : 'https://www.payfast.co.za/eng/process');
define('PAYFAST_VALIDATE_URL', PAYFAST_TEST_MODE ? 'https://sandbox.payfast.co.za/eng/query/validate' : 'https://www.payfast.co.za/eng/query/validate');

define('ADMIN_EMAIL','admin@fragrance.local');
define('ADMIN_PASSWORD','ChangeMe123!'); // Change before hosting publicly.
try {
 $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,
 [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
} catch(PDOException $e) { exit('Database connection failed. Check config.php and import database.sql.'); }
