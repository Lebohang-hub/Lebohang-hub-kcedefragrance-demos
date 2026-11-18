SCENT HAVEN - FRAGRANCE SHOP (PLAIN PHP + MYSQL + PAYFAST)

INSTALL / UPDATE
1. Extract the project into C:\xampp\htdocs\fragrance_sales_system.
2. Start Apache and MySQL in XAMPP.
3. If this is a NEW installation, import database.sql in phpMyAdmin.
4. If you already have the earlier version and want to KEEP your users/products/orders, select the fragrance_shop database in phpMyAdmin, click Import, and import upgrade_payfast.sql ONCE.
5. Open http://localhost/fragrance_sales_system/.

SHOPPING FLOW
- Click a product to view its own details and add a quantity to the basket.
- Basket allows quantity changes and removing items.
- Registration fee is R50. Browsing and adding to basket is allowed before paying; checkout requires a confirmed registration payment.
- Product checkout redirects to PayFast. Order payment status is updated automatically only after the server verifies a PayFast ITN.
- Admin can view registration and order payment statuses. Admin may update fulfilment status but cannot manually mark an order as paid.

PAYFAST SANDBOX
config.php defaults to PayFast sandbox credentials published in PayFast's developer docs. For best testing, create your own sandbox account and use its credentials/passphrase.
PAYFAST_TEST_MODE is true by default. Sandbox payments are not real money.

IMPORTANT LOCALHOST LIMITATION
PayFast's server cannot send an ITN notification to your private localhost address. In local testing, the redirect back to the site may work but automatic payment confirmation will not happen until the notification endpoint is publicly reachable. To test ITNs, use a secure public HTTPS staging URL/tunnel configured as SITE_URL, and set notify_url to https://YOUR-PUBLIC-HOST/fragrance_sales_system/payfast-notify.php. Do not use plain HTTP or a private localhost URL for production.

BEFORE GOING LIVE
- Register/activate your own PayFast merchant account and replace merchant ID, merchant key and passphrase in config.php.
- Set PAYFAST_TEST_MODE to false and SITE_URL to your real HTTPS domain.
- Confirm the public notify URL is reachable and PayFast ITN validation works.
- Change the default admin credentials in config.php and use a secure deployment setup.
- This is an integration starter, not a completed production security audit. Have the site/security configuration reviewed before accepting real customer payments.

Admin login: http://localhost/fragrance_sales_system/admin-login.php
Default admin email: admin@fragrance.local
Default admin password: ChangeMe123!
Change these before deployment.


PAYFAST SIGNATURE MISMATCH TROUBLESHOOTING
- Use a matching Merchant ID, Merchant Key, and passphrase from the SAME sandbox account.
- The included default test set is ID 10000100, key 46f0cd694581a, passphrase jt7NOE43FZPn.
- Do not combine your own sandbox ID/key with the default passphrase.
- The signature helper excludes blank values from both the signature and submitted form.
