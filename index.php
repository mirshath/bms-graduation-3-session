<?php
// insert_student.php
include('./database/connection.php'); // Include your database connection file

// ---- Portal switch (portal_table.acive): 1 = open, anything else = closed ----
$portalOpen = false;
$portalRes = mysqli_query($conn, "SELECT acive FROM portal_table WHERE id = 1 LIMIT 1");
if ($portalRes && ($portalRow = mysqli_fetch_assoc($portalRes))) {
    $portalOpen = ((int)$portalRow['acive'] === 1);
}

// ---- Portal opening date & time (Sri Lanka time): change it here ----
$portalOpensAt  = new DateTime('2026-10-10 10:00:00', new DateTimeZone('Asia/Colombo'));
$portalTargetMs = $portalOpensAt->getTimestamp() * 1000;
$serverNowMs    = (int) round(microtime(true) * 1000);

// first paint of the countdown (the browser then takes over, synced to the server clock)
$pcLeft = max(0, intdiv($portalTargetMs - $serverNowMs, 1000));
$pcPad  = function ($n) {
    return str_pad((string) $n, 2, '0', STR_PAD_LEFT);
};

// progress rings: how full each ring is (days ring = up to 30 days, hours = of 24, minutes/seconds = of 60)
$pcC = 2 * M_PI * 44; // ring circumference (r = 44)
$pcD = intdiv($pcLeft, 86400);
$pcH = intdiv($pcLeft % 86400, 3600);
$pcM = intdiv($pcLeft % 3600, 60);
$pcS = $pcLeft % 60;
$pcFill = [
    'd' => min($pcD + $pcH / 24, 30) / 30,
    'h' => ($pcH + $pcM / 60) / 24,
    'm' => ($pcM + $pcS / 60) / 60,
    's' => $pcS / 60,
];
$pcOff = function ($k) use ($pcC, $pcFill) {
    return number_format($pcC * (1 - $pcFill[$k]), 2, '.', '');
};

// live status endpoint: index.php?portal_status=1  (polled by the page, no refresh needed)
if (isset($_GET['portal_status'])) {
    header('Content-Type: application/json');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    echo json_encode([
        'open' => $portalOpen,
        'now'  => (int) round(microtime(true) * 1000), // server clock: every visitor sees the same countdown
    ]);
    exit();
}

include('./includes/header.php'); // Include your database connection file
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,500;6..72,600&family=Public+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    :root {
        --ink: #17233d;
        --muted: #5b6577;
        --line: #dde2ea;
        --paper: #f3f5f9;
        --card: #fff;
        --brand: #1f4bb6;
        --brand-d: #173a91;
        --tint: #e8eefb;
        --bad: #b3261e;
        --bad-bg: #fdecea;
    }

    body {
        /* background: var(--paper) !important; */
        font-family: 'Public Sans', system-ui, sans-serif;
        color: var(--ink);
    }

    .body_bg {
        background-image: url('./images/back.jpg');
        background-size: cover;
        background-attachment: fixed;
        background-position: center;
        background-repeat: no-repeat;
        background-color: #f3f3f3;
    }

    .reg {
        max-width: 760px;
        margin: 0 auto;
        padding: 0 12px 56px;
    }

    .bg_color_ds {
        background-color: rgba(238, 232, 232, 0.68);
        border-radius: 10px;
    }

    .reg-banner {
        width: 100%;
        height: auto;
        display: block;
        border-radius: 14px 14px 0 0;
    }

    .reg-head {
        background: var(--card);
        padding: 28px 32px 24px;
        border-bottom: 1px solid var(--line);
        border-radius: 0 0 0 0;
    }

    .reg-head h1 {
        font-family: 'Newsreader', Georgia, serif;
        font-weight: 600;
        font-size: clamp(1.9rem, 4.5vw, 2.6rem);
        line-height: 1.1;
        letter-spacing: -.02em;
        margin: 0 0 6px;
        text-wrap: balance;
    }

    .reg-head p {
        margin: 0;
        color: var(--muted);
        max-width: 56ch;
    }

    .reg form {
        background: var(--card);
        border-radius: 0 0 14px 14px;
        padding: 8px 32px 32px;
        box-shadow: 0 18px 40px -24px rgba(23, 35, 61, .35);
    }

    /* sections */
    .reg .card {
        border: 0;
        background: none;
        border-radius: 0;
        box-shadow: none;
        padding: 0;
        margin: 0 !important;
    }

    .reg .card-body {
        padding: 24px 0 8px;
        box-shadow: none !important;
    }

    .reg .card+.card .card-body {
        border-top: 1px solid var(--line);
        margin-top: 16px;
    }

    .reg .step {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0 0 18px;
        font-weight: 600;
        font-size: 1.05rem;
    }

    .reg .step b {
        display: grid;
        place-items: center;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: var(--brand);
        color: #fff;
        font-size: .9rem;
    }

    .reg .step small {
        font-weight: 400;
        color: var(--muted);
    }

    .reg .mb-2 {
        margin-bottom: 20px !important;
    }

    /* rows: label on the left, field on the right (stacks on mobile) */
    .reg .mb-2>.row {
        align-items: flex-start;
    }

    .reg .mb-2>.row>* {
        flex: 0 0 100%;
        max-width: 100%;
        width: 100%;
    }

    @media (min-width: 576px) {
        .reg .mb-2>.row>*:nth-child(1) {
            flex: 0 0 auto;
            width: 34%;
            display: flex;
            align-items: center;
            min-height: 46px;
            padding-top: 0;
            padding-right: 16px;
        }

        .reg .mb-2>.row>*:nth-child(2) {
            flex: 0 0 auto;
            width: 66%;
            min-width: 0;
        }

        .reg .mb-2>.row>*:nth-child(n+3) {
            flex: 0 0 100%;
            width: 100%;
        }

        .reg .mb-2 .form-label {
            margin-bottom: 0;
        }
    }

    .reg .form-label {
        font-weight: 600;
        font-size: .92rem;
        margin-bottom: 6px;
    }

    .reg .form-label span {
        color: var(--bad) !important;
    }

    /* inputs */
    .reg .input,
    .reg .form-select {
        width: 100%;
        height: 46px;
        padding: 0 14px;
        font: inherit;
        color: var(--ink);
        background: #fff;
        border: 1px solid #c4ccd9;
        border-radius: 8px;
        outline: 0;
        transition: border-color .15s, box-shadow .15s;
    }

    .reg .input:focus,
    .reg .form-select:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(31, 75, 182, .18);
    }

    .reg .input:disabled,
    .reg .form-select:disabled,
    .reg .input[readonly] {
        background: #eef1f6;
        color: #6b7384;
        cursor: not-allowed;
    }

    .reg .wrap-input-1 {
        position: relative;
        width: 100%;
        margin: 0 !important;
    }

    .reg .focus-border {
        display: none !important;
    }

    .reg small {
        display: block;
        margin-top: 6px;
        font-size: .84rem;
        line-height: 1.45;
    }

    .reg small.text-danger {
        color: var(--brand-d) !important;
    }

    .reg small.text-secondary,
    .reg small.text-muted {
        color: var(--muted) !important;
    }

    /* radio tiles */
    .reg .d-flex.gap-4 {
        gap: 10px !important;
        flex-wrap: wrap;
    }

    .reg .form-check {
        position: relative;
        padding: 0;
        margin: 0;
    }

    .reg .form-check-input {
        position: absolute;
        opacity: 0;
        inset: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        cursor: pointer;
    }

    .reg .form-check-label {
        display: block;
        min-width: 150px;
        padding: 11px 18px;
        text-align: center;
        font-weight: 500;
        border: 1px solid #c4ccd9;
        border-radius: 8px;
        background: #fff;
        transition: background .15s, border-color .15s;
    }

    .reg .form-check-input:checked+.form-check-label {
        background: var(--tint);
        border-color: var(--brand);
        color: var(--brand-d);
        box-shadow: inset 0 0 0 1px var(--brand);
    }

    .reg .form-check-input:focus-visible+.form-check-label {
        outline: 3px solid rgba(31, 75, 182, .35);
        outline-offset: 2px;
    }

    .reg .form-check-input:disabled+.form-check-label {
        opacity: .5;
    }

    .reg .form-check-input:disabled {
        cursor: not-allowed;
    }

    /* alerts + info */
    .reg .alert {
        padding: 10px 14px;
        font-size: .88rem;
        border: 0;
        border-left: 3px solid var(--bad);
        border-radius: 6px;
        background: var(--bad-bg);
        color: #7a1b15;
    }

    .reg .card-body>p {
        margin: 8px 0 0;
        padding: 14px 16px;
        background: var(--tint);
        border-radius: 8px;
        font-size: .9rem;
        color: var(--ink);
    }

    .reg .card-body>p center {
        text-align: left;
    }

    /* buttons */
    .reg .btn {
        font-weight: 600;
        border-radius: 8px;
        transition: background .15s, transform .1s;
    }

    .reg .btn:active {
        transform: scale(.98);
    }

    .reg #next_btn {
        background: var(--brand);
        border-color: var(--brand);
        color: #fff;
        padding: 9px 22px;
    }

    .reg #next_btn:hover {
        background: var(--brand-d);
        border-color: var(--brand-d);
    }

    .reg #submitBtn {
        height: 52px;
        font-size: 1.02rem;
        background: var(--brand);
        border-color: var(--brand);
        margin-top: 28px !important;
        margin-bottom: 0 !important;
    }

    .reg #submitBtn:hover:not(:disabled) {
        background: var(--brand-d);
        border-color: var(--brand-d);
    }

    .reg #submitBtn:disabled {
        background: #9aa8c9;
        border-color: #9aa8c9;
    }

    .reg a:focus-visible,
    .reg .btn:focus-visible {
        outline: 3px solid rgba(31, 75, 182, .35);
        outline-offset: 2px;
    }

    /* modals */
    .modal-content {
        border: 0;
        border-radius: 14px;
        overflow: hidden;
    }

    .modal-body h4 {
        font-family: 'Newsreader', Georgia, serif;
        font-size: 1.9rem !important;
        letter-spacing: -.01em;
        margin: 10px 0 12px;
    }

    .modal-background {
        background-image: none !important;
        background: #fff;
    }

    @media (max-width: 575px) {
        .reg-head {
            padding: 22px 20px 18px;
        }

        .reg form {
            padding: 4px 20px 24px;
        }

        .reg .form-check-label {
            min-width: 0;
        }

        .reg .form-check {
            flex: 1 1 calc(50% - 5px);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .reg * {
            transition: none !important;
        }
    }

    .reg .form-label {
        font-size: 18px !important;
    }

    /* sizing fixes */
    .reg .form-label {
        line-height: 1.3;
        margin: 0;
    }

    .reg .mb-2 .wrap-input-1 .input,
    .reg .mb-2 .form-select {
        min-width: 0;
        max-width: 100%;
    }

    .reg input[type="date"] {
        -webkit-appearance: none;
        appearance: none;
        display: block;
    }

    .reg .phone-cc {
        flex: 0 0 auto;
        width: 130px;
    }

    .reg .phone-num {
        flex: 1 1 0;
        min-width: 0;
        margin-left: 12px !important;
    }

    @media (max-width: 575px) {
        .reg .form-label {
            font-size: 16px !important;
            margin-bottom: 6px;
        }

        .reg .mb-2>.row>*:nth-child(1) {
            display: block;
            min-height: 0;
            padding-right: calc(var(--bs-gutter-x, 1.5rem) * .5);
        }

        .reg .phone-cc {
            width: 112px;
        }

        .reg .phone-num {
            margin-left: 8px !important;
        }

        .reg .form-check {
            flex: 1 1 100%;
        }

        .reg-head h1 {
            font-size: 30px !important;
        }

        .reg-head h1 span {
            font-size: 20px !important;
        }

        .reg #next_btn {
            width: 100%;
        }

        .reg .d-flex.justify-content-end {
            justify-content: stretch !important;
        }
    }

    /* mobile: meal + confirmation rows keep label (left) and choices (right) on one line */
    @media (max-width: 575px) {
        .reg .meal-row>.row {
            flex-wrap: nowrap;
            align-items: flex-start;
        }

        .reg .meal-row>.row>*:nth-child(1) {
            flex: 0 0 auto;
            width: 40%;
            display: flex;
            align-items: center;
            min-height: 44px;
            padding-right: 8px;
        }

        .reg .meal-row>.row>*:nth-child(2) {
            flex: 1 1 0;
            width: auto;
            min-width: 0;
        }

        .reg .meal-row .form-label {
            margin: 0;
            font-size: 15px !important;
            line-height: 1.3;
        }

        .reg .meal-row .d-flex.gap-4 {
            flex-direction: column;
            gap: 8px !important;
        }

        .reg .meal-row .form-check {
            flex: 0 0 auto;
            width: 100%;
        }

        .reg .meal-row .form-check-label {
            padding: 10px 8px;
            font-size: 14px;
        }
    }

    /* validation states */
    .reg .input.is-invalid,
    .reg .form-select.is-invalid {
        border-color: var(--bad);
        box-shadow: 0 0 0 3px rgba(179, 38, 30, .14);
    }

    .reg .group-invalid .form-check-label {
        border-color: var(--bad);
    }

    .reg .field-error {
        margin-top: 6px;
        padding: 8px 12px;
        font-size: .86rem;
        border-left: 3px solid var(--bad);
        border-radius: 6px;
        background: var(--bad-bg);
        color: #7a1b15;
    }
</style>

<!-- ===== Portal CLOSED panel (shown / hidden live) ===== -->
<link href="https://fonts.googleapis.com/css2?family=Geist+Mono:wght@400;500&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,600&display=swap" rel="stylesheet">
<style>
    .portal-closed {
        max-width: 760px;
        margin: 24px auto 56px;
        padding: 0 12px;
        font-family: 'Public Sans', system-ui, sans-serif;
        color: #17233d;
    }

    .portal-closed img {
        width: 100%;
        height: auto;
        display: block;
    }

    .portal-closed .box {
        background: #fff;
        padding: 48px 28px 44px;
        text-align: center;
    }

    .portal-closed .ic {
        position: relative;
        display: inline-grid;
        place-items: center;
        width: 78px;
        height: 78px;
        margin-bottom: 20px;
        border-radius: 50%;
        background: #fff4e0;
        color: #a85d00;
        font-size: 34px;
    }

    .portal-closed .ic::after {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 3px solid #e0a64a;
        opacity: 0;
        animation: pcRing 2.2s ease-out infinite;
    }

    @keyframes pcRing {
        0% {
            transform: scale(1);
            opacity: .7;
        }

        100% {
            transform: scale(1.6);
            opacity: 0;
        }
    }

    .portal-closed h1 {
        font-family: 'Newsreader', Georgia, serif;
        font-weight: 600;
        font-size: clamp(1.7rem, 4.5vw, 2.3rem);
        line-height: 1.2;
        margin: 0 0 8px;
        text-wrap: balance;
    }

    .portal-closed p.sub {
        margin: 0;
        color: #5b6577;
        font-size: 1.1rem;
    }

    .portal-closed .auto {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 26px;
        padding: 7px 14px;
        border-radius: 999px;
        background: #f4f6fb;
        color: #5b6577;
        font-size: .84rem;
        font-weight: 500;
    }

    .portal-closed .auto i {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #1b7f4b;
        animation: pcBlink 1.6s infinite;
    }

    @keyframes pcBlink {
        50% {
            opacity: .25;
        }
    }

    /* ---------- one card: banner + message + countdown ---------- */
    .pc-card {
        overflow: hidden;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 26px 48px -30px rgba(31, 75, 182, .4);
    }

    /* ---------- countdown: progress rings ---------- */
    .pc-clock {
        padding: 30px 28px 34px;
        text-align: center;
        background: #f6f8fd;
        border-top: 1px solid var(--line);
        box-shadow: inset 0 1px 0 #fff;
    }

    .pc-head,
    .pc-note {
        animation: pcRise .7s cubic-bezier(.16, 1, .3, 1) both;
        animation-delay: calc(var(--i, 0) * 90ms);
    }

    .pc-label {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        padding: 7px 15px 7px 13px;
        border: 1px solid var(--line);
        border-radius: 999px;
        background: #fff;
        color: var(--brand-d);
        font-size: .72rem;
        font-weight: 600;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .pc-dot {
        position: relative;
        flex: none;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--brand);
    }

    .pc-dot::after {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: var(--brand);
        animation: pcPing 1.8s cubic-bezier(0, 0, .2, 1) infinite;
    }

    @keyframes pcPing {
        0% {
            transform: scale(1);
            opacity: .55;
        }

        80%,
        100% {
            transform: scale(2.8);
            opacity: 0;
        }
    }

    .pc-units {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: clamp(8px, 2.6vw, 24px);
        max-width: 560px;
        margin: 26px auto 0;
    }

    .pc-unit {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
        min-width: 0;
        animation: pcRise .7s cubic-bezier(.16, 1, .3, 1) both;
        animation-delay: calc(var(--i, 0) * 90ms);
    }

    .pc-ring {
        position: relative;
        width: 100%;
        max-width: 124px;
        aspect-ratio: 1;
    }

    .pc-ring svg {
        display: block;
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
        overflow: visible;
    }

    .pc-ring circle {
        fill: none;
        stroke-width: 5;
        stroke-linecap: round;
    }

    .pc-trk {
        stroke: #dde5f6;
    }

    .pc-fg {
        stroke: var(--brand);
        stroke-dasharray: 276.46;
        animation: pcDraw 1.3s cubic-bezier(.16, 1, .3, 1) backwards;
        animation-delay: calc(var(--i, 0) * 90ms);
    }

    @keyframes pcDraw {
        from {
            stroke-dashoffset: 276.46px;
        }
    }

    .pc-ring b {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        font-family: 'Geist Mono', ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: clamp(1.45rem, .7rem + 3.6vw, 2.5rem);
        font-weight: 500;
        font-variant-numeric: tabular-nums;
        letter-spacing: -.05em;
        line-height: 1;
        color: var(--ink);
    }

    .pc-unit--s b {
        color: var(--brand);
    }

    .pc-unit>span {
        color: var(--muted);
        font-size: .68rem;
        font-weight: 600;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .pc-roll {
        animation: pcRoll .4s cubic-bezier(.16, 1, .3, 1);
    }

    @keyframes pcRoll {
        from {
            transform: translateY(24%);
            opacity: 0;
        }
    }

    .pc-note {
        max-width: 44ch;
        margin: 22px auto 0;
        color: var(--muted);
        font-size: .86rem;
        line-height: 1.55;
    }

    /* time reached, waiting for the admin switch: rings fill up and breathe */
    .pc-clock.is-done .pc-fg {
        stroke-dashoffset: 0 !important;
        animation: pcBreath 1.8s ease-in-out infinite;
    }

    @keyframes pcBreath {
        50% {
            opacity: .35;
        }
    }

    @keyframes pcRise {
        from {
            opacity: 0;
            transform: translateY(14px);
        }
    }

    @media (max-width: 575.98px) {
        .pc-clock {
            padding: 24px 14px 28px;
        }

        .pc-units {
            margin-top: 22px;
        }

        .pc-unit {
            gap: 10px;
        }

        .pc-ring circle {
            stroke-width: 6;
        }
    }

    .portal-fade {
        animation: pcFade .45s ease;
    }

    @keyframes pcFade {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
    }

    @media (prefers-reduced-motion: reduce) {

        .portal-closed .ic::after,
        .portal-closed .auto i,
        .pc-dot::after,
        .pc-roll,
        .pc-head,
        .pc-unit,
        .pc-note,
        .pc-fg,
        .pc-clock.is-done .pc-fg,
        .portal-fade {
            animation: none;
        }
    }
</style>
<div id="portalClosedWrap" style="display: <?php echo $portalOpen ? 'none' : 'block'; ?>;">
    <section class="portal-closed mt-4">
        <div class="pc-card">
            <img src="./images/graduation_nov_2026.jpg" alt="BMS DEGREE CONVOCATION 2026 banner">
            <div class="box">
                <div class="ic"><i class="bi bi-hourglass-split" aria-hidden="true"></i></div>
                <h1>Registration Portal Is Not Open Yet</h1>
                <p class="sub">Please wait. The registration portal will open soon.</p>
                <!-- <div class="auto"><i></i> This page updates automatically &mdash; no need to refresh</div> -->
            </div>

            <!-- ===== Countdown ===== -->
            <div class="pc-clock<?php echo $pcLeft === 0 ? ' is-done' : ''; ?>" id="pcClock" role="timer" aria-label="Time left until the registration portal opens">
                <div class="pc-head" style="--i:0">
                    <p class="pc-label"><i class="pc-dot"></i><span id="pcLabel"><?php echo $pcLeft === 0 ? 'Opening now' : 'Registration opens in'; ?></span></p>
                </div>

                <div class="pc-units">
                    <div class="pc-unit" style="--i:1">
                        <div class="pc-ring">
                            <svg viewBox="0 0 100 100" aria-hidden="true">
                                <circle class="pc-trk" cx="50" cy="50" r="44" />
                                <circle class="pc-fg" data-r="d" cx="50" cy="50" r="44" style="stroke-dashoffset: <?php echo $pcOff('d'); ?>" />
                            </svg>
                            <b data-u="d"><?php echo $pcPad($pcD); ?></b>
                        </div>
                        <span>Days</span>
                    </div>
                    <div class="pc-unit" style="--i:2">
                        <div class="pc-ring">
                            <svg viewBox="0 0 100 100" aria-hidden="true">
                                <circle class="pc-trk" cx="50" cy="50" r="44" />
                                <circle class="pc-fg" data-r="h" cx="50" cy="50" r="44" style="stroke-dashoffset: <?php echo $pcOff('h'); ?>" />
                            </svg>
                            <b data-u="h"><?php echo $pcPad($pcH); ?></b>
                        </div>
                        <span>Hours</span>
                    </div>
                    <div class="pc-unit" style="--i:3">
                        <div class="pc-ring">
                            <svg viewBox="0 0 100 100" aria-hidden="true">
                                <circle class="pc-trk" cx="50" cy="50" r="44" />
                                <circle class="pc-fg" data-r="m" cx="50" cy="50" r="44" style="stroke-dashoffset: <?php echo $pcOff('m'); ?>" />
                            </svg>
                            <b data-u="m"><?php echo $pcPad($pcM); ?></b>
                        </div>
                        <span>Minutes</span>
                    </div>
                    <div class="pc-unit pc-unit--s" style="--i:4">
                        <div class="pc-ring">
                            <svg viewBox="0 0 100 100" aria-hidden="true">
                                <circle class="pc-trk" cx="50" cy="50" r="44" />
                                <circle class="pc-fg" data-r="s" cx="50" cy="50" r="44" style="stroke-dashoffset: <?php echo $pcOff('s'); ?>" />
                            </svg>
                            <b data-u="s"><?php echo $pcPad($pcS); ?></b>
                        </div>
                        <span>Seconds</span>
                    </div>
                </div>

                <p class="pc-note" id="pcNote" style="--i:5" <?php echo $pcLeft === 0 ? '' : 'hidden'; ?>>Opening time reached. This page switches to the form by itself the moment the portal goes live.</p>
            </div>
        </div>
    </section>
</div>

<!-- ===== Registration form (shown only while the portal is ON) ===== -->
<div id="portalOpenWrap" style="display: <?php echo $portalOpen ? 'block' : 'none'; ?>;">
    <main class="reg mt-4 body_bg">
        <img src="./images/graduation_nov_2026.jpg" alt="BMS DEGREE CONVOCATION 2026 banner" class="reg-banner">
        <header class="reg-head">
            <h1 style="font-size: 35px; font-weight: 700; text-align: center;">BMS DEGREE CONVOCATION 2026 <br>
                <span style="font-size: 25px;">REGISTRATION FORM</span>
            </h1>

        </header>

        <form id="studentForm" novalidate>
            <div class="card">
                <div class="card-body">
                    <h2 class="step"><b>1</b> Verify your identity</h2>
                    <div class="mb-2">
                        <!-- -----------------------------------  -->
                        <div class="row">
                            <div class="col-12 col-sm-3">
                                <label style="font-size: 18px;" for="student_id" class="form-label"> <b>Student ID</b> <span style="color:red; font-weight: 
                                        bolder; ">*</span></label>
                            </div>
                            <div class="col-12 col-sm-9">
                                <div class="wrap-input-1">
                                    <input class="input" type="text" placeholder="Student ID" id="student_id"
                                        name="student_id" required>
                                    <span class="focus-border"></span>
                                    <div id="student_id_alert" class="alert alert-danger mt-2"
                                        style="display: none;">
                                        <strong>Warning!</strong> Student ID already registered.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="dob" class="form-label"> <b>Date of Birth</b>
                                    <span style="color:red; font-weight:bolder;">*</span>
                                </label>
                            </div>
                            <div class="col">
                                <div class="wrap-input-1">
                                    <input class="input" type="date" id="dob" name="dob" required
                                        placeholder="Date Of Birth">
                                    <span class="focus-border"></span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-3">
                                <a href="#" class="btn btn-sm btn-danger" id="next_btn">Next</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" id="data_forms">
                <div class="card-body">
                    <h2 class="step"><b>2</b> Confirm your details</h2>
                    <div class="mb-2">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="name_in_full" class="form-label"> <b>Name in full</b>
                                    <span style="color:red; font-weight:bolder;">*</span>
                                </label>
                            </div>
                            <div class="col">
                                <div class="wrap-input-1">
                                    <input class="input" id="name_in_full" name="name_in_full" disabled
                                        placeholder="Name in Full">
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- -------- Title ----------  -->
                    <!-- -------- Calling Name with Title ----------  -->
                    <div class="mb-2">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="calling_name" class="form-label"> <b>Calling Name</b>
                                    <span style="color:red; font-weight:bolder;">*</span>
                                </label>
                            </div>
                            <div class="col">
                                <div class="wrap-input-1 d-flex gap-2">
                                    <select class="form-select" id="title" name="title" required disabled
                                        style="max-width:100px;">
                                        <option value="">Title</option>
                                        <option value="Mr.">Mr.</option>
                                        <option value="Ms.">Ms.</option>
                                        <option value="Mrs.">Mrs.</option>
                                        <option value="Miss.">Miss.</option>
                                    </select>
                                    <input class="input" type="text" id="calling_name" name="calling_name"
                                        placeholder="e.g. Dilmi Navanjana" required maxlength="100" disabled>
                                    <span class="focus-border"></span>
                                </div>
                                <small class="text-danger"
                                    style="font-weight:500; display:block; margin-top:4px;">
                                    This name will be announced when you receive your certificate on stage
                                </small>
                                <small class="text-secondary"
                                    style="font-weight:500; display:block; margin-top:4px;">
                                    Example: If your full name is "Dilmi Navanjana Perera", your calling name
                                    might be Dilmi Navanjana. (Maximum 2 words)
                                </small>
                                <div id="calling_name_alert" class="alert alert-danger mt-2"
                                    style="display: none;">
                                    <strong>Warning!</strong> Please enter a valid calling name (letters and
                                    spaces only, maximum 2 words).
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- -------- programs ----------  -->

                    <div class="mb-2">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="program" class="form-label"> <b>Program</b>
                                    <span style="color:red; font-weight:bolder;">*</span>
                                </label>
                            </div>
                            <div class="col">
                                <div class="wrap-input-1">
                                    <input type="text" id="program" name="program" class="input"
                                        placeholder="Program" readonly>
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2" style="display: none;">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="session_display" class="form-label">Session</label>
                            </div>
                            <div class="col">
                                <div class="wrap-input-1">
                                    <input type="text" id="session_display" class="input" placeholder="Session" readonly>
                                    <input type="hidden" id="session" name="session">
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="email_address_given" name="email_address_given"
                        placeholder="Email">

                    <div class="mb-2">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="email_address" class="form-label"> <b>Email ID</b>
                                    Email ID <span style="color:red; font-weight:bolder;">*</span>
                                </label>
                            </div>
                            <div class="col">
                                <div class="wrap-input-1">
                                    <input class="input" type="email" id="email_address" name="email_address"
                                        disabled placeholder="Email">
                                    <span class="focus-border"></span>
                                    <small class="text-danger"
                                        style="font-weight:500; display:block; margin-top:4px; font-weight:500;">
                                        Should enter valid email, QR will be sent to this email
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="phone_no" class="form-label"> <b>Mobile</b> <span
                                        style="color:red; font-weight:bolder;">*</span></label>
                            </div>
                            <div class="col">
                                <div class="d-flex">
                                    <div class="d-flex align-items-center">
                                        <select class="form-select phone-cc" aria-label="Country Code">
                                            <option value="+94" selected>+94 (SL)</option>

                                        </select>
                                    </div>

                                    <div class="wrap-input-1 phone-num">
                                        <input class="input" type="text" placeholder="7X XXX XXXX" id="phone_no"
                                            name="phone_no">
                                        <span class="focus-border"></span>
                                        <small id="phone-error" style="color: red; display:none;">Please enter
                                            only numbers.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- -------- Student Meal Preference ----------  -->
                    <div class="mb-2 meal-row bg_color_ds">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label"> <b>Student meal preference</b>
                                    <span style="color:red; font-weight:bolder;">*</span>
                                </label>
                            </div>
                            <div class="col">
                                <div class="d-flex justify-content-ends gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="student_meals"
                                            id="student_meals_veg" value="Vegetarian" required disabled>
                                        <label class="form-check-label" for="student_meals_veg">Vegetarian</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="student_meals"
                                            id="student_meals_nonveg" value="Non-Vegetarian" disabled>
                                        <label class="form-check-label" for="student_meals_nonveg">Non-Vegetarian</label>
                                    </div>
                                </div>
                                <div id="student_meal_alert" class="alert alert-danger mt-2"
                                    style="display: none;">
                                    <strong>Warning!</strong> You must select your meal preference to proceed.
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ---------------------------------------------  -->

                    <!-- -------- Guest 01 meal preference ----------  -->
                    <div class="mb-2 meal-row">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label"> <b>Guest 01 meal preference</b>
                                    <span style="color:red; font-weight:bolder;">*</span>
                                </label>
                            </div>
                            <div class="col">
                                <div class="d-flex justify-content-ends gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="guest_meals"
                                            id="guest_meals_veg" value="Vegetarian" required disabled>
                                        <label class="form-check-label" for="guest_meals_veg">Vegetarian</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="guest_meals"
                                            id="guest_meals_nonveg" value="Non-Vegetarian" disabled>
                                        <label class="form-check-label" for="guest_meals_nonveg">Non-Vegetarian</label>
                                    </div>
                                </div>
                                <div id="guest_meal_alert" class="alert alert-danger mt-2"
                                    style="display: none;">
                                    <strong>Warning!</strong> You must select the guest meal preference to proceed.
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ---------------------------------------------  -->

                    <!-- -------- Guest 02 meal preference ----------  -->
                    <div class="mb-2 meal-row bg_color_ds">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label"> <b>Guest 02 meal preference</b>
                                    <span style="color:red; font-weight:bolder;">*</span>
                                </label>
                            </div>
                            <div class="col">
                                <div class="d-flex justify-content-ends gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="guest_meals_02"
                                            id="guest_meals_02_veg" value="Vegetarian" required disabled>
                                        <label class="form-check-label" for="guest_meals_02_veg">Vegetarian</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="guest_meals_02"
                                            id="guest_meals_02_nonveg" value="Non-Vegetarian" disabled>
                                        <label class="form-check-label" for="guest_meals_02_nonveg">Non-Vegetarian</label>
                                    </div>
                                </div>
                                <div id="guest_meal_02_alert" class="alert alert-danger mt-2"
                                    style="display: none;">
                                    <strong>Warning!</strong> You must select the Guest 02 meal preference to proceed.
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ---------------------------------------------  -->

                    <input type="hidden" id="crsfee_payment_status" name="crsfee_payment_status" class="input"
                        placeholder="Course Fee Payment Status">

                    <input type="hidden" id="graduation_payment_status" name="graduation_payment_status"
                        class="input" placeholder="Graduation Payment Status" value="Not-Completed">

                    <!-- -------- Confirmation ----------  -->
                    <div class="mb-2 meal-row">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="confirmation_yes" class="form-label"> <b>Confirmation</b>
                                    <span style="color:red; font-weight:bolder;">*</span>
                                </label>
                            </div>
                            <div class="col">
                                <div class="d-flex justify-content-ends gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="confirmation"
                                            id="confirmation_yes" value="1" required disabled>
                                        <label class="form-check-label" for="confirmation_yes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="confirmation"
                                            id="confirmation_no" value="0" disabled>
                                        <label class="form-check-label" for="confirmation_no">No</label>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-2">I confirm that the calling name entered
                                    above is accurate and should be used during the graduation ceremony.</small>
                                <div id="confirmation_alert" class="alert alert-danger mt-2"
                                    style="display: none;">
                                    <strong>Warning!</strong> You must confirm the calling name to proceed.
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <b>
                        <center>Your invitation will be sent to your email after submiting the registration
                            form. You will need to present the QR code provided in the email at the registration
                            table for entry</center>
                    </b>
                </div>
            </div>

            <!-- QR Code Image Box -->
            <div id="imgBox" class="mt-3" style="display: none;">
                <!-- <img src="" alt="QR Code" id="qrImage"> -->
            </div>

            <div class="p-0">
                <button type="submit" class="btn btn-primary w-100" id="submitBtn"
                    disabled>Complete registration</button>
            </div>
        </form>
    </main>
</div> <!-- /portalOpenWrap -->

<!-- JavaScript to scroll on click -->
<script>
    document.getElementById('next_btn').addEventListener('click', function(e) {
        e.preventDefault(); // Prevent the default link behavior
        document.getElementById('data_forms').scrollIntoView({
            behavior: 'smooth', // Smooth scroll animation
            block: 'start' // Align to the top of the element
        });
    });
</script>

<script>
    $(document).ready(function() {
        function toggleEmploymentDetails() {
            if ($('#employment_yes').is(':checked')) {
                $('#employment_details_wrapper').show();
                $('#employment_details').attr('required', 'required');
            } else {
                $('#employment_details_wrapper').hide();
                $('#employment_details').removeAttr('required');
                $('#employment_details').val(''); // Clear text when "No" is selected
            }
        }

        // On radio change
        $('input[name="employment_status"]').on('change', toggleEmploymentDetails);

        // Initial check on page load
        toggleEmploymentDetails();
    });
</script>

<script>
    document.getElementById('phone_no').addEventListener('input', function(e) {
        var phoneInput = this.value;
        var errorElement = document.getElementById('phone-error');

        // Allow only numbers, and remove non-numeric characters
        this.value = this.value.replace(/[^0-9]/g, '');

        // Check if the input contains any non-numeric characters (for further validation if needed)
        if (/[^0-9]/.test(phoneInput)) {
            errorElement.style.display = 'block';
        } else {
            errorElement.style.display = 'none';
        }
    });

    // Calling Name validation - letters, hyphens, apostrophes, dots; maximum 2 words
    var CALLING_NAME_MAX_WORDS = 2;
    window.getCallingNameError = function(value) {
        var name = value.trim().replace(/\s+/g, ' ');
        if (name.length === 0) return '';
        if (!/^[\p{L}\-'.]+(\s[\p{L}\-'.]+)*$/u.test(name)) {
            return 'Please enter a valid calling name (letters and spaces only).';
        }
        if (name.split(' ').length > CALLING_NAME_MAX_WORDS) {
            return 'Calling name can have only ' + CALLING_NAME_MAX_WORDS + ' words (e.g. Dilmi Navanjana).';
        }
        return '';
    };

    function checkCallingName() {
        var alertEl = document.getElementById('calling_name_alert');
        var err = window.getCallingNameError(document.getElementById('calling_name').value);
        if (err) {
            alertEl.innerHTML = '<strong>Warning!</strong> ' + err;
            alertEl.style.display = 'block';
        } else {
            alertEl.style.display = 'none';
        }
    }
    document.getElementById('calling_name').addEventListener('input', checkCallingName);
    document.getElementById('calling_name').addEventListener('blur', checkCallingName);
</script>

<!-- Modal for Invalid Student ID or DOB -->

<!-- Modal for Invalid Student ID or DOB -->
<div id="errorModal" class="modal fade" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-confirm">
        <div class="modal-content"> <!-- Modal Header -->
            <div class="modal-header border-0"> <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button> </div> <!-- Modal Body -->
            <div class="modal-body text-center modal-background"> <!-- Error Icon -->
                <div class="mb-3"> <i class="bi bi-exclamation-circle-fill error-icon"></i> </div> <!-- Title -->
                <h4 id="errorModalLabel" class="eb-garamond fw-bolder error-title"> Invalid Credentials </h4> <!-- Message -->
                <p class="error-message"> Please verify the <b>Student ID</b> and <b>Date of Birth</b>, and try again. </p>
                <p class="error-message"> If you require further assistance, please contact your coordinator. </p> <!-- Contact Section -->
                <div class="contact-container">
                    <div class="row g-3">

                        <!-- Undergraduate Business -->
                        <div class="col-12 col-md-6">
                            <div class="contact-box">
                                <div class="contact-title"> Undergraduate Business </div>
                                <a href="mailto:sharmila.r@bms.ac.lk" class="contact-email"> <i class="bi bi-envelope"></i> sharmila.r@bms.ac.lk </a>
                                <a href="tel:+947XXXXXXXX" style="font-size: 12px;" class="contact-phone">
                                    <i class="bi bi-telephone"></i> &nbsp;
                                    +94 704 011 685
                                </a>
                            </div>
                        </div>

                        <!-- Postgraduate Business -->
                        <div class="col-12 col-md-6">
                            <div class="contact-box">
                                <div class="contact-title"> Postgraduate Business </div>
                                <a href="mailto:thanuja.e@bms.ac.lk" class="contact-email"> <i class="bi bi-envelope"></i> thanuja.e@bms.ac.lk </a>
                                <a href="tel:+947XXXXXXXX" style="font-size: 12px;" class="contact-phone">
                                    <i class="bi bi-telephone"></i> &nbsp;
                                    +94 704 011 689
                                </a>
                            </div>
                        </div>
                        <!-- Undergraduate Science -->
                        <div class="col-12 col-md-6">
                            <div class="contact-box">
                                <div class="contact-title"> Undergraduate/Postgraduate Science </div>
                                <a href="mailto:bioadmin@bms.ac.lk" class="contact-email"> <i class="bi bi-envelope"></i> bioadmin@bms.ac.lk </a>
                                <a href="tel:+947XXXXXXXX" style="font-size: 12px;" class="contact-phone">
                                    <i class="bi bi-telephone"></i> &nbsp;
                                    +94 704 001 084
                                </a>
                            </div>
                        </div>

                        <!-- GDM -->

                        <div class="col-12 col-md-6">
                            <div class="contact-box">
                                <div class="contact-title">Graduate Diploma in Management</div>

                                <a href="mailto:ruwani.f@bms.ac.lk" class="contact-email">
                                    <i class="bi bi-envelope"></i>
                                    ruwani.f@bms.ac.lk
                                </a>

                                <a href="tel:+947XXXXXXXX" style="font-size: 12px;" class="contact-phone">
                                    <i class="bi bi-telephone"></i> &nbsp;
                                    +94 704 011 709
                                </a>
                            </div>
                        </div>

                        <style>
                            .contact-email,
                            .contact-phone {
                                text-decoration: none;
                                color: inherit;
                            }

                            .contact-email:hover,
                            .contact-phone:hover {
                                text-decoration: none;
                                color: inherit;
                            }
                        </style>




                    </div>
                </div> <!-- Go Back Button -->
                <div class="mt-4"> <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal"> Go Back </button> </div>
            </div>
        </div>
    </div>
</div> <!-- Modal CSS -->

<style>
    /* Error Icon */
    .error-icon {
        font-size: 50px;
        color: red;
    }

    /* Modal Title */
    .error-title {
        font-size: 35px;
        margin-bottom: 15px;
    }

    /* Error Message */
    .error-message {
        margin-bottom: 10px;
        font-size: 15px;
    }

    /* Contact Container */
    .contact-container {
        width: 100%;
        max-width: 650px;
        margin: 20px auto 0;
    }

    /* Contact Box */
    .contact-box {
        background: #f8f9fa;
        border: 1px solid #e1e1e1;
        border-radius: 10px;
        padding: 13px 10px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        transition: all 0.2s ease;
    }

    /* Contact Box Hover */
    .contact-box:hover {
        border-color: #0d6efd;
        background: #f1f6ff;
    }

    /* Contact Title */
    .contact-title {
        font-weight: 700;
        font-size: 15px;
        color: #212529;
        margin-bottom: 5px;
    }

    /* Email */
    .contact-email {
        color: #0d6efd;
        text-decoration: none;
        font-size: 14px;
        word-break: break-word;
    }

    .contact-email:hover {
        text-decoration: underline;
    }

    /* Go Back Button */
    .contact-container+.mt-4 .btn {
        min-width: 110px;
    }

    /* Mobile Responsive */
    @media (max-width: 576px) {
        .error-title {
            font-size: 28px;
        }

        .error-message {
            font-size: 14px;
        }

        .contact-container {
            padding: 0 5px;
        }

        .contact-box {
            padding: 12px 8px;
        }

        .contact-title {
            font-size: 14px;
        }

        .contact-email {
            font-size: 13px;
        }
    }
</style>

<!-- Modal for Registration Success -->
<div id="successModal" class="modal fade" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header">
                <!-- Close button -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center modal-background">
                <!-- Success Icon (Bootstrap Icons) -->
                <i class="bi bi-check-circle-fill" style="font-size: 50px; color: green;"></i>
                <h4 class="eb-garamond fw-bolder" style="font-size: 35px;">Registration Successful</h4>
                <p>Your invitation has been sent to your email.</p>
                <!-- 'Go Back' button to close the modal -->
                <button class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- check student over modal  -->
<div id="CheckStModal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center modal-background">
                <!-- Red Alert Icon (Bootstrap Icons) -->
                <i class="bi bi-exclamation-circle-fill" style="font-size: 50px; color: red;"></i>
                <h4 class="eb-garamond fw-bolder" style="font-size: 50px;">Registration Closed</h4>

                <p>Maximum number of participants registered.</p>
                <br><br>

            </div>
        </div>
    </div>
</div>

<!-- Error Modal for Missing Fields -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Error</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-background" class="eb-garamond fw-bolder" style="font-size: 35px;">
                <!-- The error message will be inserted here dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        // Initially hide buttons and forms
        $('#submitBtn, #data_forms, #next_btn').hide();

        // --- CHECK STUDENT COUNT ---
        function checkStudentCount() {
            $.ajax({
                url: 'count_students.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.count > 200) {
                        $('#studentForm').hide();
                        $('#CheckStModal').modal('show');
                    } else {
                        $('#studentForm').show();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error fetching student count: ", textStatus, errorThrown);
                }
            });
        }

        checkStudentCount();
        setInterval(checkStudentCount, 5000);

        // --- REAL-TIME CHECK STUDENT ID ---
        $('#student_id').on('input', function() {
            var student_id = $(this).val().trim();
            if (student_id.length > 0) {
                $.ajax({
                    url: 'check_student_id.php',
                    method: 'POST',
                    data: {
                        student_id: student_id
                    },
                    success: function(response) {
                        if (response == 'exists') {
                            $('#student_id_alert').show();
                            $('#submitBtn,#dob').prop('disabled', true).hide();
                            $('#data_forms, #next_btn').hide();
                        } else {
                            $('#student_id_alert').hide();
                            $('#submitBtn, #dob').prop('disabled', false);
                            $('#next_btn,#dob').show();
                        }
                    }
                });
            } else {
                $('#student_id_alert').hide();
                $('#submitBtn').prop('disabled', false);
            }
        });

        // --- VALIDATE STUDENT ID & DOB ---
        $('#student_id, #dob').on('blur', function() {
            var student_id = $('#student_id').val().trim();
            var dob = $('#dob').val().trim();

            if (student_id && dob) {
                $.ajax({
                    url: 'validate_student.php',
                    method: 'POST',
                    data: {
                        student_id: student_id,
                        dob: dob
                    },

                    // ------------------------------------------------ 
                    success: function(response) {
                        if (response.status === 'valid') {
                            $('#student_id').val(response.student_id);
                            $('#name_in_full').val(response.name);
                            $('#dob').val(response.dob);
                            $('#program').val(response.program_name);
                            // session of this program (from data_tables)
                            var sessionRaw = response.session || '';
                            var sessionLabel = sessionRaw ?
                                sessionRaw.replace(/_/g, ' ').toLowerCase().replace(/\b\w/g, function(ch) {
                                    return ch.toUpperCase();
                                }) :
                                'Not assigned yet';
                            $('#session').val(sessionRaw);
                            $('#session_display').val(sessionLabel);
                            $('#payment_status').val(response.payment_status);
                            $('#phone_no').val(response.mobile_number);
                            $('#email_address_given').val(response.given_email);
                            $('#crsfee_payment_status').val(response.crsfee_payment_status);

                            $('#name_in_full, #title, #calling_name, #email_address, #phone_no, #confirmation_yes, #confirmation_no, #student_meals_veg, #student_meals_nonveg, #guest_meals_veg, #guest_meals_nonveg, #guest_meals_02_veg, #guest_meals_02_nonveg, #submitBtn').prop('disabled', false);
                            $('#submitBtn').show();
                            $('#data_forms').show();
                            $('#next_btn').hide();
                        } else {
                            $('#errorModal').modal('show');
                            $('#submitBtn, #data_forms, #next_btn').hide();
                        }
                    }

                    // ------------------------------------------------ 
                });
            } else {
                $('#name_in_full, #address, #address_2, #city, #email_address, #phone_no, #professional, #student_meals_veg, #student_meals_nonveg, #guest_meals_veg, #guest_meals_nonveg, #guest_meals_02_veg, #guest_meals_02_nonveg, #submitBtn').prop('disabled', true);
            }
        });

        $('#errorModal').on('hidden.bs.modal', function() {
            window.location.reload();
        });

        // Form submission 
        $(document).ready(function() {

            // clear a field's error as soon as the user edits it
            $('#studentForm').on('input change', 'input, select', function() {
                var $row = $(this).closest('.mb-2');
                $(this).removeClass('is-invalid').removeAttr('aria-invalid');
                $row.find('.field-error').remove();
                $row.find('.group-invalid').removeClass('group-invalid');
                $row.find('.alert').not('#student_id_alert, #calling_name_alert').hide();
                if (this.id === 'title' && !window.getCallingNameError($('#calling_name').val())) {
                    $('#calling_name_alert').hide();
                }
            });
            $('#studentForm').on('submit', function(e) {
                e.preventDefault();

                var $form = $(this);
                var errors = [];
                var val = function(id) {
                    return $.trim($('#' + id).val() || '');
                };

                // reset previous messages
                $form.find('.field-error').remove();
                $form.find('.is-invalid').removeClass('is-invalid').removeAttr('aria-invalid');
                $form.find('.group-invalid').removeClass('group-invalid');
                $form.find('.alert').not('#student_id_alert').hide();

                // flag(focusEl, message, {mark, anchor, alert, group})
                function flag($focus, msg, o) {
                    o = o || {};
                    if (o.mark) o.mark.addClass('is-invalid').attr('aria-invalid', 'true');
                    if (o.group) o.group.addClass('group-invalid');
                    if (o.alert) {
                        $(o.alert).html('<strong>Warning!</strong> ' + msg).show();
                    } else if (o.anchor && msg) {
                        $('<div class="field-error" role="alert"></div>').text(msg).insertAfter(o.anchor);
                    }
                    errors.push($focus);
                }

                // Step 1: student ID + date of birth
                var $sid = $('#student_id'),
                    $dob = $('#dob');
                if (!val('student_id')) {
                    flag($sid, 'Enter your Student ID.', {
                        mark: $sid,
                        anchor: $sid.closest('.wrap-input-1')
                    });
                } else if ($('#student_id_alert').is(':visible')) {
                    flag($sid, '', {
                        mark: $sid
                    });
                }
                if (!val('dob')) {
                    flag($dob, 'Select your date of birth.', {
                        mark: $dob,
                        anchor: $dob.closest('.wrap-input-1')
                    });
                } else if (!errors.length && $('#name_in_full').prop('disabled')) {
                    flag($dob, 'Verify your Student ID and date of birth first.', {
                        mark: $dob,
                        anchor: $dob.closest('.wrap-input-1')
                    });
                }

                // Step 2: details (only when the form is unlocked)
                if (!errors.length) {
                    // title + calling name
                    var callingName = val('calling_name').replace(/\s+/g, ' ');
                    var callingErr = window.getCallingNameError(callingName);
                    var titleBad = !$('#title').val();
                    var nameBad = callingName.length < 2 || !!callingErr;
                    if (titleBad || nameBad) {
                        var m = (titleBad && nameBad) ? 'Select a title and enter your calling name.' :
                            titleBad ? 'Select a title.' :
                            (callingErr || 'Enter a calling name (at least 2 letters).');
                        flag(titleBad ? $('#title') : $('#calling_name'), m, {
                            mark: (titleBad ? $('#title') : $()).add(nameBad ? $('#calling_name') : $()),
                            alert: '#calling_name_alert'
                        });
                    } else {
                        $('#calling_name').val(callingName);
                    }

                    // email
                    var $em = $('#email_address'),
                        email = val('email_address');
                    if (!email) {
                        flag($em, 'Enter your email address.', {
                            mark: $em,
                            anchor: $em.closest('.wrap-input-1')
                        });
                    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) {
                        flag($em, 'Enter a valid email address, for example name@example.com.', {
                            mark: $em,
                            anchor: $em.closest('.wrap-input-1')
                        });
                    }

                    // mobile
                    var $ph = $('#phone_no');
                    if (val('phone_no').replace(/\D/g, '').length < 9) {
                        flag($ph, 'Enter a valid mobile number (9 digits, for example 7X XXX XXXX).', {
                            mark: $ph,
                            anchor: $ph.closest('.wrap-input-1').parent()
                        });
                    }

                    // meal choices
                    [
                        ['student_meals', '#student_meal_alert', 'You must select your meal preference to proceed.'],
                        ['guest_meals', '#guest_meal_alert', 'You must select the guest meal preference to proceed.'],
                        ['guest_meals_02', '#guest_meal_02_alert', 'You must select the Guest 02 meal preference to proceed.']
                    ].forEach(function(g) {
                        var $r = $('input[name="' + g[0] + '"]');
                        if (!$r.filter(':checked').length) {
                            flag($r.first(), g[2], {
                                alert: g[1],
                                group: $r.first().closest('.d-flex')
                            });
                        }
                    });

                    // calling-name confirmation (must be Yes)
                    var $c = $('input[name="confirmation"]');
                    if ($c.filter(':checked').val() !== '1') {
                        flag($c.first(), 'You must confirm (select Yes) that the calling name is accurate to proceed.', {
                            alert: '#confirmation_alert',
                            group: $c.first().closest('.d-flex')
                        });
                    }
                }

                // focus the first missing / invalid field
                if (errors.length) {
                    var $first = errors[0];
                    var row = $first.closest('.mb-2')[0] || $first[0];
                    $first[0].focus({
                        preventScroll: true
                    });
                    row.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    return;
                }

                var formData = $(this).serialize();

                // Show full-page preloader
                $('#preloaderOverlay').addClass('active').fadeIn(200);

                var $submitBtn = $('#submitBtn');
                $submitBtn.prop('disabled', true);
                var originalText = $submitBtn.text();
                $submitBtn.text('Submitting... Please wait');

                $.ajax({
                    url: 'insert_student.php',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        response = $.trim(response);

                        // Hide preloader
                        $('#preloaderOverlay').fadeOut(200, function() {
                            $(this).removeClass('active');
                        });

                        if (response === 'Registration successful') {
                            $('#successModal').modal('show');
                            $('#studentForm')[0].reset();
                            $submitBtn.prop('disabled', false).text(originalText);
                            setTimeout(() => window.location.reload(), 2000);
                        } else {
                            $('#errorModal').find('.modal-body').html(response);
                            $('#errorModal').modal('show');
                            $submitBtn.prop('disabled', false).text(originalText);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#preloaderOverlay').fadeOut(200, function() {
                            $(this).removeClass('active');
                        });
                        console.error('AJAX Error:', error);
                        alert('Something went wrong! Please try again.');
                        $submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });
        });
    });
</script>

<!-- Full Page Preloader -->
<div id="preloaderOverlay">
    <div class="loader-content">
        <img src="./images/animation.gif" alt="Loading..." class="preloader-gif">
        <!-- <div class="spinner-border text-light" role="status"></div> -->
        <p>Submitting... Please wait</p>
    </div>
</div>

<style>
    /* Full page preloader overlay */
    #preloaderOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.75);
        /* Hidden by default */
        display: none;
        z-index: 9999;
    }

    /* Flex centering only when shown */
    #preloaderOverlay.active {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        text-align: center;
    }

    #preloaderOverlay .loader-content {
        color: #fff;
        font-size: 1.2rem;
    }

    #preloaderOverlay p {
        margin-top: 15px;
        font-weight: 500;
    }
</style>
<!-- Bootstrap JS and dependencies (Optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Live portal ON/OFF + opening countdown (no refresh needed) -->
<script>
    (function() {
        var openWrap = document.getElementById('portalOpenWrap');
        var closedWrap = document.getElementById('portalClosedWrap');
        var current = <?php echo $portalOpen ? 'true' : 'false'; ?>;

        /* ---------- countdown, synced to the SERVER clock ---------- */
        var clock = document.getElementById('pcClock');
        var TARGET = <?php echo $portalTargetMs; ?>; // opening moment (ms)
        var offset = <?php echo $serverNowMs; ?> - Date.now(); // server clock minus browser clock
        var C = 276.46; // ring circumference (r = 44)
        var keys = ['d', 'h', 'm', 's'];
        var nums = {},
            rings = {};
        keys.forEach(function(k) {
            nums[k] = clock.querySelector('[data-u="' + k + '"]');
            rings[k] = clock.querySelector('[data-r="' + k + '"]');
        });
        var label = document.getElementById('pcLabel');
        var note = document.getElementById('pcNote');
        var shown = {},
            lastFill = {},
            doneNow = null;

        function pad(n) {
            return n < 10 ? '0' + n : String(n);
        }

        function put(k, v) {
            if (shown[k] === v) return;
            nums[k].textContent = v;
            if (shown[k] !== undefined) { // roll only when a number changes, not on first paint
                nums[k].classList.remove('pc-roll');
                void nums[k].offsetWidth;
                nums[k].classList.add('pc-roll');
            }
            shown[k] = v;
        }

        function ring(k, fill) { // rings drain smoothly; jump back to full without animating
            var r = rings[k];
            r.style.transition = (lastFill[k] === undefined || fill > lastFill[k]) ? 'none' : 'stroke-dashoffset 1s linear';
            r.style.strokeDashoffset = (C * (1 - fill)).toFixed(2);
            lastFill[k] = fill;
        }

        function tick() {
            if (current) return; // portal is open, the form is showing
            var left = Math.max(0, Math.floor((TARGET - (Date.now() + offset)) / 1000));
            var d = Math.floor(left / 86400),
                h = Math.floor(left % 86400 / 3600),
                m = Math.floor(left % 3600 / 60),
                s = left % 60;
            put('d', pad(d));
            put('h', pad(h));
            put('m', pad(m));
            put('s', pad(s));
            ring('d', Math.min(d + h / 24, 30) / 30);
            ring('h', (h + m / 60) / 24);
            ring('m', (m + s / 60) / 60);
            ring('s', s / 60);

            var done = left === 0;
            if (done !== doneNow) {
                doneNow = done;
                clock.classList.toggle('is-done', done);
                label.textContent = done ? 'Opening now' : 'Registration opens in';
                note.hidden = !done;
            }
        }

        function loop() { // fires just after every server-second boundary
            if (!document.hidden) tick();
            setTimeout(loop, 1000 - ((Date.now() + offset) % 1000) + 15);
        }

        /* ---------- ON / OFF switch ---------- */
        function apply(open) {
            if (open === current) return;
            current = open;
            if (open) {
                closedWrap.style.display = 'none';
                openWrap.style.display = 'block';
                openWrap.classList.remove('portal-fade');
                void openWrap.offsetWidth;
                openWrap.classList.add('portal-fade');
            } else {
                openWrap.style.display = 'none';
                closedWrap.style.display = 'block';
                closedWrap.classList.remove('portal-fade');
                void closedWrap.offsetWidth;
                closedWrap.classList.add('portal-fade');
                tick();
            }
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function check() {
            if (document.hidden) return;
            var t0 = Date.now();
            fetch(window.location.pathname + '?portal_status=1', {
                    cache: 'no-store'
                })
                .then(function(r) {
                    return r.json();
                })
                .then(function(d) {
                    if (d.now) offset = Math.round(d.now - (t0 + Date.now()) / 2); // re-sync the clock
                    apply(!!d.open);
                })
                .catch(function() {
                    /* network hiccup: keep current view, try again next tick */
                });
        }

        loop();
        setInterval(check, 3000);
        document.addEventListener('visibilitychange', function() {
            tick();
            check();
        });
    })();
</script>
</body>

</html>