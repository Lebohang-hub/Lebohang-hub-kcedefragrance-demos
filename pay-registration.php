
<?php
require 'config.php';
require 'includes/functions.php';
require 'includes/payfast.php';

require_login();

$u = current_user($pdo);
verify_csrf();

if ($u['registration_status'] === 'paid') {
    $_SESSION['flash'] = 'Your registration fee is already confirmed.';
    redirect('member.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $paymentId = 'REG-' . $u['id'] . '-' . bin2hex(random_bytes(8));

    $stmt = $pdo->prepare("
        UPDATE users
        SET registration_status = 'pending_review',
            registration_payment_id = ?
        WHERE id = ?
    ");

    $stmt->execute([$paymentId, $u['id']]);

    // Correctly separate the first and last names.
    $parts = preg_split('/\s+/', trim($u['full_name']), 2);

    $data = [
        'merchant_id'       => PAYFAST_MERCHANT_ID,
        'merchant_key'      => PAYFAST_MERCHANT_KEY,
        'return_url'        => SITE_URL . '/payment-return.php?type=registration',
        'cancel_url'        => SITE_URL . '/payment-cancel.php?type=registration',
        'notify_url'        => SITE_URL . '/payfast-notify.php',
        'name_first'        => $parts[0] ?? $u['full_name'],
        'name_last'         => $parts[1] ?? '',
        'email_address'     => $u['email'],
        'm_payment_id'      => $paymentId,
        'amount'            => number_format(REGISTRATION_FEE, 2, '.', ''),
        'item_name'         => 'Fragrance shop registration fee',
        'item_description'  => 'R50 membership registration fee'
    ];

    // Generate and submit the PayFast payment form.
    payfast_form($data);
    exit;
}

$pageTitle = 'Registration payment';

require 'includes/header.php';
?>

<div class="form-wrap panel">

    <div class="eyebrow">Membership activation</div>

    <h1>Registration fee: <?= money(REGISTRATION_FEE) ?></h1>

    <p>
        Pay the R50 registration fee securely through PayFast.
        You can browse products and build a basket before paying,
        but checkout unlocks only after your registration payment
        has been verified.
    </p>

    <?php if ($u['registration_status'] === 'pending_review'): ?>
        <div class="notice">
            Your registration payment is awaiting confirmation.
            If you have already paid, please allow time for PayFast
            to notify the shop.
        </div>
    <?php endif; ?>

    <form method="post">
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrf_token()) ?>"
        >

        <button type="submit" class="btn">
            Pay R50 with PayFast
        </button>
    </form>

    <p class="muted small">
        Sandbox mode is enabled. Test payments do not transfer
        real money. Membership activation requires a verified
        payment notification.
    </p>

</div>

<?php require 'includes/footer.php'; ?>
