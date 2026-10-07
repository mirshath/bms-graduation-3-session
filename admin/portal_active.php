<?php
session_start();

// Admin only
if (!isset($_SESSION['admin_id'])) {
    if (isset($_GET['status']) || ($_SERVER['REQUEST_METHOD'] === 'POST')) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['ok' => false, 'message' => 'Session expired. Please log in again.']);
        exit();
    }
    header("Location: login");
    exit();
}

try {
    include("../database/connection.php");
    if (!isset($conn) || !$conn) {
        throw new Exception("Database connection failed");
    }
} catch (Exception $e) {
    die("System Error: Unable to connect to database. Please contact administrator.");
}

if (empty($_SESSION['portal_csrf'])) {
    $_SESSION['portal_csrf'] = bin2hex(random_bytes(32));
}

function portal_read($conn): bool
{
    $res = mysqli_query($conn, "SELECT acive FROM portal_table WHERE id = 1 LIMIT 1");
    if ($res && ($row = mysqli_fetch_assoc($res))) {
        return ((int)$row['acive'] === 1);
    }
    return false;
}

function portal_json(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    echo json_encode($data);
    exit();
}

// ---------- AJAX: read current status (used for live sync) ----------
if (isset($_GET['status'])) {
    portal_json(['ok' => true, 'open' => portal_read($conn), 'time' => date('H:i:s')]);
}

// ---------- AJAX: turn ON / OFF ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string)($_POST['csrf'] ?? '');
    if (!hash_equals($_SESSION['portal_csrf'], $token)) {
        portal_json(['ok' => false, 'message' => 'Invalid request. Please refresh the page.'], 403);
    }
    $new = (isset($_POST['state']) && $_POST['state'] === '1') ? 1 : 0;

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO portal_table (id, acive) VALUES (1, ?) ON DUPLICATE KEY UPDATE acive = ?"
    );
    if ($stmt && mysqli_stmt_bind_param($stmt, "ii", $new, $new) && mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        portal_json(['ok' => true, 'open' => $new === 1, 'time' => date('H:i:s')]);
    }
    error_log("Portal toggle error: " . mysqli_error($conn));
    portal_json(['ok' => false, 'message' => 'Could not update the portal status.'], 500);
}

$isOpen = portal_read($conn);

include("includes/header.php");
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Geist+Mono:wght@500;600&display=swap" rel="stylesheet">

<style>
    body.at-page {
        overflow-x: hidden;
    }

    #sidebarOverlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .4);
        opacity: 0;
        visibility: hidden;
        transition: opacity .3s ease;
        z-index: 1039;
    }

    body.sidebar-open #sidebarOverlay {
        opacity: 1;
        visibility: visible;
    }

    .at-menu {
        display: none;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
        padding: 9px 16px;
        border: 1px solid #c4ccd9;
        border-radius: 12px;
        background: #fff;
        color: #17233d;
        font-family: 'Public Sans', system-ui, sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }

    @media (max-width: 991.98px) {
        .at-menu {
            display: inline-flex;
        }

        body.at-page #accordionSidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 260px;
            transform: translateX(-100%);
            transition: transform .3s ease;
            z-index: 1040;
            overflow-y: auto;
        }

        body.at-page.sidebar-open #accordionSidebar {
            transform: translateX(0);
        }

        body.at-page #content-wrapper {
            margin-left: 0 !important;
        }
    }

    @media (min-width: 992px) {
        body.at-page #content-wrapper {
            flex: 1 1 auto;
            min-width: 0;
            width: auto;
        }
    }

    /* ================= portal page ================= */
    .pt {
        --ink: #17233d;
        --muted: #5f6b7e;
        --line: #e2e6ee;
        --soft: #f4f6fb;
        --ok: #1b7f4b;
        --ok-bg: #e6f4ec;
        --off: #c0372f;
        --off-bg: #fdecea;
        --brand: #1f4bb6;
        font-family: 'Public Sans', system-ui, sans-serif;
        color: var(--ink);
        max-width: 760px;
        margin-inline: auto;
        padding: 8px 0 36px;
        line-height: 1.5;
    }

    .pt *,
    .pt *::before,
    .pt *::after {
        box-sizing: border-box;
    }

    .pt-head {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 22px;
    }

    .pt h1 {
        margin: 0;
        font-size: clamp(24px, 3vw, 32px);
        font-weight: 700;
        letter-spacing: -.03em;
        line-height: 1.1;
    }

    .pt-sub {
        margin: 6px 0 0;
        font-size: 14px;
        color: var(--muted);
    }

    .pt-live {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border: 1px solid var(--line);
        border-radius: 999px;
        background: #fff;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--muted);
    }

    .pt-live i {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--ok);
        animation: ptBlink 1.6s infinite;
    }

    .pt-live.err i {
        background: var(--off);
        animation: none;
    }

    .pt-live .mono {
        font-family: 'Geist Mono', ui-monospace, monospace;
        color: var(--ink);
    }

    @keyframes ptBlink {
        50% {
            opacity: .25;
        }
    }

    .pt-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 30px -18px rgba(23, 35, 61, .25);
    }

    .pt-hero {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 28px;
        flex-wrap: wrap;
        transition: background .35s ease;
        background: var(--off-bg);
    }

    .pt-hero.on {
        background: var(--ok-bg);
    }

    .pt-ic {
        position: relative;
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        width: 68px;
        height: 68px;
        border-radius: 50%;
        font-size: 28px;
        color: #fff;
        background: var(--off);
        transition: background .35s ease;
    }

    .pt-hero.on .pt-ic {
        background: var(--ok);
    }

    .pt-ic::after {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 3px solid currentColor;
        color: var(--off);
        opacity: 0;
    }

    .pt-hero.on .pt-ic::after {
        color: var(--ok);
        animation: ptRing 2s ease-out infinite;
    }

    @keyframes ptRing {
        0% {
            transform: scale(1);
            opacity: .6;
        }

        100% {
            transform: scale(1.55);
            opacity: 0;
        }
    }

    .pt-txt {
        flex: 1 1 220px;
        min-width: 0;
    }

    .pt-txt small {
        display: block;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .pt-txt h2 {
        margin: 2px 0 2px;
        font-size: 26px;
        font-weight: 700;
        letter-spacing: -.02em;
        color: var(--off);
        transition: color .35s;
    }

    .pt-hero.on .pt-txt h2 {
        color: var(--ok);
    }

    .pt-txt p {
        margin: 0;
        font-size: 14px;
        color: var(--muted);
    }

    /* switch */
    .pt-switch {
        position: relative;
        flex: 0 0 auto;
        width: 124px;
        height: 58px;
        margin: 0;
        cursor: pointer;
    }

    .pt-switch input {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }

    .pt-switch input:disabled {
        cursor: progress;
    }

    .pt-track {
        position: absolute;
        inset: 0;
        border-radius: 999px;
        background: #b9c1cf;
        transition: background .25s;
    }

    .pt-track::before {
        content: "OFF";
        position: absolute;
        right: 18px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 14px;
        font-weight: 700;
        letter-spacing: .04em;
        color: #fff;
    }

    .pt-track::after {
        content: "";
        position: absolute;
        top: 5px;
        left: 5px;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 2px 6px rgba(23, 35, 61, .3);
        transition: transform .25s cubic-bezier(.3, .9, .3, 1.2);
    }

    .pt-switch input:checked~.pt-track {
        background: var(--ok);
    }

    .pt-switch input:checked~.pt-track::before {
        content: "ON";
        right: auto;
        left: 20px;
    }

    .pt-switch input:checked~.pt-track::after {
        transform: translateX(66px);
    }

    .pt-switch input:focus-visible~.pt-track {
        outline: 3px solid rgba(31, 75, 182, .4);
        outline-offset: 3px;
    }

    .pt-switch.busy .pt-track {
        opacity: .6;
    }

    .pt-body {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
        border-top: 1px solid var(--line);
    }

    .pt-opt {
        padding: 20px 28px;
        font-size: 13.5px;
        color: var(--muted);
    }

    .pt-opt+.pt-opt {
        border-left: 1px solid var(--line);
    }

    .pt-opt b {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
        font-size: 13px;
        color: var(--ink);
    }

    .pt-opt b span {
        width: 9px;
        height: 9px;
        border-radius: 50%;
    }

    .pt-opt.is-on b span {
        background: var(--ok);
    }

    .pt-opt.is-off b span {
        background: var(--off);
    }

    .pt-opt.cur {
        background: var(--soft);
    }

    @media (max-width: 575px) {
        .pt-hero {
            padding: 22px 18px;
        }

        .pt-switch {
            margin-left: auto;
            margin-right: auto;
        }

        .pt-body {
            grid-template-columns: 1fr;
        }

        .pt-opt+.pt-opt {
            border-left: 0;
            border-top: 1px solid var(--line);
        }

        .pt-opt {
            padding: 16px 18px;
        }
    }

    /* toast */
    .pt-toast {
        position: fixed;
        right: 22px;
        bottom: 22px;
        z-index: 2000;
        display: flex;
        align-items: center;
        gap: 10px;
        max-width: calc(100vw - 44px);
        padding: 13px 18px;
        border-radius: 12px;
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 14px 34px rgba(23, 35, 61, .3);
        transform: translateY(20px);
        opacity: 0;
        pointer-events: none;
        transition: transform .25s, opacity .25s;
        font-family: 'Public Sans', system-ui, sans-serif;
    }

    .pt-toast.show {
        transform: none;
        opacity: 1;
    }

    .pt-toast.ok {
        background: #1b7f4b;
    }

    .pt-toast.off {
        background: #4a5568;
    }

    .pt-toast.bad {
        background: #c0372f;
    }

    /* confirm dialog */
    .pt-dlg {
        position: fixed;
        inset: 0;
        z-index: 2100;
        display: none;
        place-items: center;
        padding: 18px;
        background: rgba(23, 35, 61, .5);
        font-family: 'Public Sans', system-ui, sans-serif;
    }

    .pt-dlg.show {
        display: grid;
    }

    .pt-dlg-box {
        width: 100%;
        max-width: 400px;
        padding: 26px;
        border-radius: 16px;
        background: #fff;
        text-align: center;
        box-shadow: 0 24px 60px rgba(0, 0, 0, .3);
        animation: ptPop .18s ease-out;
    }

    @keyframes ptPop {
        from {
            transform: scale(.94);
            opacity: 0;
        }
    }

    .pt-dlg-ic {
        display: inline-grid;
        place-items: center;
        width: 54px;
        height: 54px;
        margin-bottom: 12px;
        border-radius: 50%;
        font-size: 22px;
    }

    .pt-dlg-ic.on {
        background: var(--ok-bg, #e6f4ec);
        color: #1b7f4b;
    }

    .pt-dlg-ic.off {
        background: #fdecea;
        color: #c0372f;
    }

    .pt-dlg h3 {
        margin: 0 0 6px;
        font-size: 20px;
        font-weight: 700;
        color: #17233d;
    }

    .pt-dlg p {
        margin: 0 0 20px;
        font-size: 14px;
        color: #5f6b7e;
    }

    .pt-dlg-btns {
        display: flex;
        gap: 10px;
    }

    .pt-dlg-btns button {
        flex: 1;
        padding: 11px 14px;
        border-radius: 10px;
        font: inherit;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        border: 1px solid #c4ccd9;
        background: #fff;
        color: #17233d;
    }

    .pt-dlg-btns button:hover {
        background: #f4f6fb;
    }

    .pt-dlg-btns .go {
        border-color: transparent;
        color: #fff;
    }

    .pt-dlg-btns .go.on {
        background: #1b7f4b;
    }

    .pt-dlg-btns .go.off {
        background: #c0372f;
    }

    .pt-dlg-btns .go:hover {
        filter: brightness(.92);
    }
</style>

<div id="wrapper">
    <?php include("nav.php"); ?>
    <div id="sidebarOverlay"></div>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include("includes/topnav.php"); ?>

            <div class="container-fluid mt-4">
                <button type="button" id="sidebarToggleMobile" class="at-menu" aria-label="Open menu">
                    <i class="fas fa-bars" aria-hidden="true"></i> <span>Menu</span>
                </button>

                <div class="pt">
                    <div class="pt-head">
                        <div>
                            <h1>Registration Portal</h1>
                            <p class="pt-sub">Switch the student registration page ON or OFF. Changes apply instantly.</p>
                        </div>
                        <span class="pt-live" id="ptLive"><i></i> Live &middot; updated <span class="mono" id="ptTime">--:--:--</span></span>
                    </div>

                    <div class="pt-card">
                        <div class="pt-hero <?php echo $isOpen ? 'on' : ''; ?>" id="ptHero">
                            <div class="pt-ic"><i class="fas <?php echo $isOpen ? 'fa-lock-open' : 'fa-lock'; ?>" id="ptIcon" aria-hidden="true"></i></div>
                            <div class="pt-txt">
                                <small>Current status</small>
                                <h2 id="ptTitle"><?php echo $isOpen ? 'Portal is OPEN' : 'Portal is CLOSED'; ?></h2>
                                <p id="ptDesc"><?php echo $isOpen ? 'Students can register right now.' : 'Students see "Still Not Open the portal to Register. Please wait."'; ?></p>
                            </div>
                            <label class="pt-switch" id="ptSwitchWrap" title="Turn the portal ON / OFF">
                                <input type="checkbox" id="ptSwitch" <?php echo $isOpen ? 'checked' : ''; ?> aria-label="Portal ON or OFF">
                                <span class="pt-track"></span>
                            </label>
                        </div>

                        <div class="pt-body">
                            <div class="pt-opt is-on <?php echo $isOpen ? 'cur' : ''; ?>" id="ptOptOn">
                                <b><span></span> ON</b>
                                The registration form is shown. Students can verify their ID and register.
                            </div>
                            <div class="pt-opt is-off <?php echo $isOpen ? '' : 'cur'; ?>" id="ptOptOff">
                                <b><span></span> OFF</b>
                                The form is hidden and students see the "Please wait" message.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="pt-toast" id="ptToast" role="status" aria-live="polite"></div>

<div class="pt-dlg" id="ptDlg" role="dialog" aria-modal="true">
    <div class="pt-dlg-box">
        <div class="pt-dlg-ic" id="ptDlgIc"><i class="fas"></i></div>
        <h3 id="ptDlgTitle"></h3>
        <p id="ptDlgText"></p>
        <div class="pt-dlg-btns">
            <button type="button" id="ptDlgNo">Cancel</button>
            <button type="button" class="go" id="ptDlgYes">Confirm</button>
        </div>
    </div>
</div>

<script>
    (function() {
        var CSRF = <?php echo json_encode($_SESSION['portal_csrf']); ?>;
        var URL_ = window.location.pathname;
        var current = <?php echo $isOpen ? 'true' : 'false'; ?>;
        var busy = false;

        var $ = function(id) {
            return document.getElementById(id);
        };
        var sw = $('ptSwitch'),
            swWrap = $('ptSwitchWrap'),
            hero = $('ptHero');

        function render(open) {
            current = open;
            sw.checked = open;
            hero.classList.toggle('on', open);
            $('ptIcon').className = 'fas ' + (open ? 'fa-lock-open' : 'fa-lock');
            $('ptTitle').textContent = open ? 'Portal is OPEN' : 'Portal is CLOSED';
            $('ptDesc').textContent = open ? 'Students can register right now.' :
                'Students see "Still Not Open the portal to Register. Please wait."';
            $('ptOptOn').classList.toggle('cur', open);
            $('ptOptOff').classList.toggle('cur', !open);
        }

        var toastTimer;

        function toast(msg, type) {
            var t = $('ptToast');
            t.textContent = msg;
            t.className = 'pt-toast ' + type + ' show';
            clearTimeout(toastTimer);
            toastTimer = setTimeout(function() {
                t.classList.remove('show');
            }, 3200);
        }

        function stamp(time, ok) {
            $('ptTime').textContent = time || new Date().toLocaleTimeString('en-GB');
            $('ptLive').classList.toggle('err', ok === false);
        }

        // ----- confirm dialog -----
        function confirmDialog(open) {
            return new Promise(function(resolve) {
                var dlg = $('ptDlg');
                $('ptDlgIc').className = 'pt-dlg-ic ' + (open ? 'on' : 'off');
                $('ptDlgIc').firstElementChild.className = 'fas ' + (open ? 'fa-lock-open' : 'fa-lock');
                $('ptDlgTitle').textContent = open ? 'Turn the portal ON?' : 'Turn the portal OFF?';
                $('ptDlgText').textContent = open ? 'Students will be able to register immediately.' :
                    'Students will immediately see the "Please wait" message and cannot register.';
                var yes = $('ptDlgYes'),
                    no = $('ptDlgNo');
                yes.className = 'go ' + (open ? 'on' : 'off');
                yes.textContent = open ? 'Yes, turn ON' : 'Yes, turn OFF';
                dlg.classList.add('show');
                yes.focus();

                function done(v) {
                    dlg.classList.remove('show');
                    yes.onclick = no.onclick = dlg.onclick = null;
                    document.removeEventListener('keydown', esc);
                    resolve(v);
                }

                function esc(e) {
                    if (e.key === 'Escape') done(false);
                }
                yes.onclick = function() {
                    done(true);
                };
                no.onclick = function() {
                    done(false);
                };
                dlg.onclick = function(e) {
                    if (e.target === dlg) done(false);
                };
                document.addEventListener('keydown', esc);
            });
        }

        // ----- save (no page reload) -----
        sw.addEventListener('change', function() {
            var want = sw.checked;
            sw.checked = current; // keep old look until confirmed
            confirmDialog(want).then(function(yes) {
                if (!yes) return;
                busy = true;
                sw.disabled = true;
                swWrap.classList.add('busy');
                render(want); // instant feedback

                var body = new URLSearchParams();
                body.set('csrf', CSRF);
                body.set('state', want ? '1' : '0');

                fetch(URL_, {
                        method: 'POST',
                        body: body,
                        credentials: 'same-origin',
                        cache: 'no-store'
                    })
                    .then(function(r) {
                        return r.json();
                    })
                    .then(function(d) {
                        if (!d.ok) throw new Error(d.message || 'Failed');
                        render(d.open);
                        stamp(d.time, true);
                        toast(d.open ? 'Portal is now ON. Students can register.' : 'Portal is now OFF.', d.open ? 'ok' : 'off');
                    })
                    .catch(function(err) {
                        render(!want); // roll back
                        stamp(null, false);
                        toast(err.message && err.message !== 'Failed' && !/JSON/.test(err.message) ?
                            err.message : 'Could not save. Check your connection or log in again.', 'bad');
                    })
                    .finally(function() {
                        busy = false;
                        sw.disabled = false;
                        swWrap.classList.remove('busy');
                    });
            });
        });

        // ----- live sync: picks up changes made elsewhere (another admin / tab) -----
        function poll() {
            if (busy || document.hidden) return;
            fetch(URL_ + '?status=1', {
                    credentials: 'same-origin',
                    cache: 'no-store'
                })
                .then(function(r) {
                    return r.json();
                })
                .then(function(d) {
                    if (!d.ok) throw new Error();
                    if (d.open !== current) {
                        render(d.open);
                        toast('Status was changed: portal is now ' + (d.open ? 'ON' : 'OFF') + '.', d.open ? 'ok' : 'off');
                    }
                    stamp(d.time, true);
                })
                .catch(function() {
                    stamp(null, false);
                });
        }
        setInterval(poll, 3000);
        document.addEventListener('visibilitychange', poll);
        stamp(<?php echo json_encode(date('H:i:s')); ?>, true);

        // ----- mobile sidebar -----
        document.body.classList.add('at-page');
        var t = $('sidebarToggleMobile');
        if (t) t.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-open');
        });
        $('sidebarOverlay').addEventListener('click', function() {
            document.body.classList.remove('sidebar-open');
        });
    })();
</script>

</body>

</html>