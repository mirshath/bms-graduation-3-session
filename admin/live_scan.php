<?php
session_start();

if (!isset($_SESSION['admin_id']) || ($_SESSION['admin_name'] ?? '') === 'Unknown Admin') {
    header("Location: login");
    exit();
}

include("../database/connection.php");
// Find the live panel file: admin/includes/ first, then the same folder as this page
$lvPanel = null;
foreach ([__DIR__ . '/includes/student_live_panel.php', __DIR__ . '/student_live_panel.php'] as $cand) {
    if (is_file($cand)) {
        $lvPanel = $cand;
        break;
    }
}
if ($lvPanel === null) {
    http_response_code(500);
    exit('student_live_panel.php was not found. Put it in ' . __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . ' (or next to this file).');
}
include_once $lvPanel;

$role = $_SESSION['role'] ?? '';

// token for the "issue extra tickets" button
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

include("includes/header.php");
?>

<?php echo lv_styles(); ?>

<style>
    body.dashboard-page {
        overflow-x: hidden;
    }

    #sidebarOverlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .4);
        opacity: 0;
        visibility: hidden;
        transition: opacity .3s;
        z-index: 1039;
    }

    body.sidebar-open #sidebarOverlay {
        opacity: 1;
        visibility: visible;
    }

    @media (max-width: 991.98px) {
        body.dashboard-page #accordionSidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 260px;
            transform: translateX(-100%);
            transition: transform .3s;
            z-index: 1040;
            overflow-y: auto;
        }

        body.dashboard-page.sidebar-open #accordionSidebar {
            transform: translateX(0);
        }

        body.dashboard-page #content-wrapper {
            margin-left: 0 !important;
        }
    }

    .ls {
        --ink: #17233d;
        --muted: #5f6b7e;
        --line: #e2e6ee;
        --soft: #f4f6fb;
        --brand: #1f4bb6;
        --brand-d: #173a91;
        font-family: 'Public Sans', system-ui, sans-serif;
        color: var(--ink);
        max-width: 1100px;
        margin: 0 auto;
        padding-bottom: 48px;
    }

    .ls * {
        box-sizing: border-box;
    }

    .ls-head h1 {
        font-family: 'Newsreader', Georgia, serif;
        font-weight: 600;
        font-size: 2rem;
        letter-spacing: -.02em;
        margin: 0 0 4px;
    }

    .ls-head p {
        margin: 0 0 20px;
        color: var(--muted);
        max-width: 62ch;
    }

    .ls-scan {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        align-items: stretch;
        padding: 18px;
        margin-bottom: 18px;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 14px;
    }

    .ls-manual {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 8px;
    }

    .ls-manual label {
        font-weight: 600;
        font-size: .95rem;
    }

    .ls-row {
        display: flex;
        gap: 10px;
    }

    .ls-row input {
        flex: 1;
        min-width: 0;
        height: 48px;
        padding: 0 14px;
        font: inherit;
        font-size: 1.05rem;
        color: var(--ink);
        border: 1px solid #c4ccd9;
        border-radius: 8px;
        outline: 0;
        background: #fff;
        transition: border-color .15s, box-shadow .15s;
    }

    .ls-row input:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(31, 75, 182, .18);
    }

    .ls-hint {
        margin: 0;
        font-size: .86rem;
        color: var(--muted);
        min-height: 1.3em;
    }

    .ls-hint.bad {
        color: #9c2a22;
    }

    .ls-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 46px;
        padding: 0 18px;
        font: inherit;
        font-weight: 600;
        font-size: .94rem;
        color: var(--ink);
        background: #fff;
        border: 1px solid #c4ccd9;
        border-radius: 8px;
        cursor: pointer;
        transition: background .15s, transform .1s;
    }

    .ls-btn:hover {
        background: #f1f4f9;
    }

    .ls-btn:active {
        transform: scale(.98);
    }

    .ls-btn.solid {
        background: var(--brand);
        border-color: var(--brand);
        color: #fff;
    }

    .ls-btn.solid:hover {
        background: var(--brand-d);
    }

    .ls-btn:focus-visible {
        outline: 3px solid rgba(31, 75, 182, .35);
        outline-offset: 2px;
    }

    .ls-link {
        align-self: flex-start;
        padding: 0;
        border: 0;
        background: none;
        color: var(--brand);
        font: inherit;
        font-weight: 600;
        cursor: pointer;
    }

    .ls-empty {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 34px 26px;
        color: var(--muted);
        border: 1px dashed #c4ccd9;
        border-radius: 14px;
        background: #fff;
    }

    .ls-empty i {
        display: grid;
        place-items: center;
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: var(--soft);
        font-size: 19px;
        color: #7d8aa6;
    }

    .ls-empty.bad {
        border-color: #f0c4c0;
        background: #fdecea;
        color: #7a1b15;
    }

    .ls-empty.bad i {
        background: #fff;
        color: #d0453c;
    }

    .ls-empty strong {
        display: block;
        color: var(--ink);
    }

    #out,
    .lv-prog {
        scroll-margin-top: 14px;
    }

    .lv.is-refresh .lv-card,
    .lv.is-refresh .lv-rec {
        animation: none;
    }

    .ls-toast {
        position: fixed;
        left: 50%;
        bottom: calc(24px + env(safe-area-inset-bottom, 0px));
        transform: translate(-50%, 20px);
        z-index: 2000;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        border-radius: 12px;
        background: #17663f;
        color: #fff;
        font-weight: 600;
        box-shadow: 0 14px 34px rgba(0, 0, 0, .25);
        opacity: 0;
        visibility: hidden;
        transition: opacity .25s, transform .25s, visibility .25s;
    }

    .ls-toast.show {
        opacity: 1;
        visibility: visible;
        transform: translate(-50%, 0);
    }

    .ls-toast.bad {
        background: #9c2a22;
    }

    .ls-reload {
        display: block;
        margin-top: 8px;
        font-size: .8rem;
        font-weight: 600;
        opacity: .85;
    }

    @media (max-width: 767px) {
        .ls-scan {
            grid-template-columns: 1fr;
            padding: 14px;
        }

        .ls-row {
            flex-direction: column;
        }

        .ls-row .ls-btn {
            width: 100%;
        }

        .ls-head h1 {
            font-size: 1.7rem;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .ls * {
            transition: none !important;
        }
    }
</style>

<div id="wrapper">
    <?php include("nav.php"); ?>
    <div id="sidebarOverlay" class="d-md-none"></div>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include("includes/topnav.php"); ?>

            <div class="container-fluid mt-4">
                <button id="sidebarToggleMobile" class="btn btn-light border d-inline-flex align-items-center gap-2 d-lg-none mb-3 shadow-sm">
                    <i class="fas fa-bars"></i><span class="fw-semibold">Menu</span>
                </button>

                <div class="ls">
                    <header class="ls-head">
                        <h1>Live scan</h1>
                        <p>Scan a student's QR code with the scanner to see their programme, session, graduation payment and extra tickets. The panel keeps updating by itself while it is on screen.</p>
                    </header>

                    <section class="ls-scan">
                        <form id="idForm" class="ls-manual" autocomplete="off">
                            <label for="sid">Student ID</label>
                            <div class="ls-row">
                                <input type="text" id="sid" name="sid" placeholder="Scan the QR code or type the Student ID" autofocus>
                                <button type="submit" class="ls-btn solid">Look up</button>
                            </div>
                            <p class="ls-hint" id="hint">Click the box, then scan the student's QR code with the scanner.</p>
                            <button type="button" id="clearBtn" class="ls-link" hidden>Clear and scan next student</button>
                        </form>
                    </section>

                    <div id="out" aria-live="polite">
                        <div class="ls-empty"><i class="fas fa-qrcode" aria-hidden="true"></i>
                            <span><strong>Waiting for a QR scan</strong>The student's details will appear here.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ls-toast" id="toast" role="status"><i class="fas fa-circle-check" aria-hidden="true"></i><span></span></div>

<script>
    (function() {
        var POLL_MS = 3000;
        var out = document.getElementById('out');
        var sid = document.getElementById('sid');
        var hint = document.getElementById('hint');
        var clearBtn = document.getElementById('clearBtn');
        var toastEl = document.getElementById('toast');
        var cur = '',
            ver = '',
            timer = null,
            busy = false,
            online = true,
            stamp = 0;
        var RELOAD_SECS = 3; // "not found" / "make the payment first" reload the page after this many seconds
        var rl = {
            t: null,
            left: 0,
            note: null
        };

        // ---------- sidebar (same behaviour as the other admin pages) ----------
        document.body.classList.add('dashboard-page');
        document.getElementById('sidebarToggleMobile').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-open');
        });
        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            document.body.classList.remove('sidebar-open');
        });

        // ---------- helpers ----------
        function say(msg, bad) {
            hint.textContent = msg;
            hint.className = 'ls-hint' + (bad ? ' bad' : '');
        }

        function toast(msg, bad) {
            toastEl.classList.toggle('bad', !!bad);
            toastEl.querySelector('i').className = 'fas ' + (bad ? 'fa-circle-exclamation' : 'fa-circle-check');
            toastEl.querySelector('span').textContent = msg;
            toastEl.classList.add('show');
            clearTimeout(toast.t);
            toast.t = setTimeout(function() {
                toastEl.classList.remove('show');
            }, 4500);
        }

        function setLive(on) {
            online = on;
            var badge = out.querySelector('.lv-live');
            if (!badge) return;
            badge.classList.toggle('is-off', !on);
            badge.querySelector('.lv-live-txt').textContent = on ? 'Live' : 'Reconnecting…';
        }

        function showMsg(title, text, bad) {
            out.innerHTML = '<div class="ls-empty' + (bad ? ' bad' : '') + '"><i class="fas ' + (bad ? 'fa-user-slash' : 'fa-spinner fa-spin') +
                '" aria-hidden="true"></i><span><strong></strong></span></div>';
            out.querySelector('strong').textContent = title;
            if (text) out.querySelector('span').appendChild(document.createTextNode(text));
        }

        // ---------- auto reload after an error message ----------
        function paintReload() {
            rl.note.textContent = 'Page reloads in ' + rl.left + ' s';
        }

        function startReload(host, secs) {
            if (!host) return;
            if (!rl.t) { // already counting? keep the same countdown, just re-attach the note
                rl.left = secs;
                rl.note = document.createElement('span');
                rl.note.className = 'ls-reload';
                rl.t = setInterval(function() {
                    rl.left--;
                    if (rl.left <= 0) {
                        clearInterval(rl.t);
                        rl.t = null;
                        location.reload();
                        return;
                    }
                    paintReload();
                }, 1000);
            }
            paintReload();
            host.appendChild(rl.note);
        }

        function stopReload() {
            clearInterval(rl.t);
            rl.t = null;
        }

        // ---------- render: first load and every change ----------
        function render(html) {
            var box = document.createElement('div');
            box.innerHTML = html.trim();
            var next = box.firstElementChild;
            var old = out.querySelector('#lvRoot');
            var sameStudent = old && old.dataset.student === next.dataset.student;

            if (sameStudent) {
                next.classList.add('is-refresh'); // no entrance animation on live updates
                var flashed = 0;
                next.querySelectorAll('[data-k]').forEach(function(n) {
                    var o = old.querySelector('[data-k="' + n.dataset.k + '"]');
                    if (o && o.dataset.v !== n.dataset.v) {
                        n.classList.add('lv-flash');
                        flashed++;
                    }
                });
                if (!flashed) {
                    var rec = next.querySelector('.lv-rec');
                    if (rec) rec.classList.add('lv-flash');
                }
                if (old.dataset.paid === '0' && next.dataset.paid === '1') toast('Graduation payment received for this student');
                if (old.dataset.rec === '0' && next.dataset.rec === '1') toast('Payment recorded for this student');
            } else if (next.dataset.rec === '0') {
                toast('Make the payment first', true);
            }
            out.replaceChildren(next);
            setLive(online);
            if (next.dataset.rec === '0') startReload(next.querySelector('.lv-alert div'), RELOAD_SECS);
            else stopReload();
            if (pendingScroll) requestAnimationFrame(goToProgramme);
        }

        // ---------- realtime loop ----------
        function schedule(now) {
            clearTimeout(timer);
            if (cur) timer = setTimeout(tick, now ? 0 : POLL_MS);
        }

        function tick() {
            if (!cur) return;
            if (busy || document.hidden) {
                schedule(false);
                return;
            }
            busy = true;
            var asked = cur;
            fetch('student_live_status.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        student_id: asked,
                        version: ver
                    })
                })
                .then(function(r) {
                    if (r.status === 401) {
                        location.href = 'login';
                        throw new Error('auth');
                    }
                    return r.json();
                })
                .then(function(j) {
                    if (asked !== cur) return; // another student was scanned meanwhile
                    if (!j.ok) throw new Error('bad');
                    if (!j.found) {
                        showMsg('No registered student found for "' + asked + '"', 'Check the ID or ask the student to register.', true);
                        startReload(out.querySelector('.ls-empty span'), RELOAD_SECS);
                        say('Not found. Scan again or type another ID.', true);
                        cur = '';
                        clearTimeout(timer);
                        pendingScroll = false;
                        out.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        return;
                    }
                    setLive(true);
                    if (j.changed) {
                        ver = j.version;
                        render(j.html);
                    } else {
                        var s = out.querySelector('[data-live-synced]');
                        if (s) s.textContent = j.server_time;
                        var root = out.querySelector('#lvRoot');
                        if (root && root.dataset.rec === '0') startReload(root.querySelector('.lv-alert div'), RELOAD_SECS);
                        if (pendingScroll) goToProgramme();
                    }
                })
                .catch(function(e) {
                    if (e.message !== 'auth') setLive(false);
                })
                .finally(function() {
                    busy = false;
                    schedule(false);
                });
        }

        // after a lookup / scan: bring the Programme section to the top and pulse it
        // (keyboard focus stays in the ID box so the next scanner read goes straight in)
        var pendingScroll = false;

        function goToProgramme() {
            pendingScroll = false;
            // the "make the payment first" alert sits right above Programme, so scrolling to it keeps both in view
            var el = out.querySelector('.lv-alert') || out.querySelector('[data-k="program"]') || out;
            var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
            el.scrollIntoView({
                behavior: reduce ? 'auto' : 'smooth',
                block: 'start'
            });
            if (el.dataset && el.dataset.k) {
                el.classList.remove('lv-flash');
                void el.offsetWidth;
                el.classList.add('lv-flash');
            }
        }

        function lookup(raw) {
            var id = String(raw || '').trim();
            if (!id) return;
            stopReload();
            pendingScroll = true;
            if (id === cur && out.querySelector('#lvRoot')) {
                tick();
                return;
            } // same QR scanned again
            cur = id;
            ver = '';
            sid.value = id;
            clearBtn.hidden = false;
            say('Loading ' + id + '…');
            showMsg('Looking up ' + id + '…', '');
            sid.focus({
                preventScroll: true
            });
            schedule(true);
        }

        function reset() {
            cur = '';
            ver = '';
            clearTimeout(timer);
            stopReload();
            sid.value = '';
            clearBtn.hidden = true;
            say('Click the box, then scan the student\'s QR code with the scanner.');
            out.innerHTML = '<div class="ls-empty"><i class="fas fa-qrcode" aria-hidden="true"></i><span><strong>Waiting for a QR scan</strong>The student\'s details will appear here.</span></div>';
            sid.focus();
        }

        // ---------- issue extra tickets (one click) ----------
        var CSRF = '<?php echo htmlspecialchars($_SESSION['csrf'], ENT_QUOTES); ?>';
        out.addEventListener('click', function(e) {
            var btn = e.target.closest('.lv-issue-btn');
            if (!btn || btn.disabled) return; // disabled right away, so a double click sends only one request
            var label = btn.querySelector('span');

            btn.dataset.text = label.textContent;
            btn.disabled = true;
            label.textContent = 'Saving…';
            fetch('issue_extra_tickets.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        student_id: btn.dataset.student,
                        csrf: CSRF
                    })
                })
                .then(function(r) {
                    if (r.status === 401) {
                        location.href = 'login';
                        throw new Error('auth');
                    }
                    return r.json();
                })
                .then(function(j) {
                    if (!j.ok) throw new Error(j.error || 'Could not save.');
                    toast(j.already ? 'These tickets were already marked as issued' : 'Extra tickets issued by ' + (j.by || 'you'));
                    ver = '';
                    schedule(true); // refresh the panel right away
                })
                .catch(function(err) {
                    if (err.message === 'auth') return;
                    toast(err.message || 'Could not save. Try again.', true);
                    if (btn.isConnected) {
                        btn.disabled = false;
                        label.textContent = btn.dataset.text;
                    }
                });
        });

        document.getElementById('idForm').addEventListener('submit', function(e) {
            e.preventDefault();
            lookup(sid.value);
        });
        clearBtn.addEventListener('click', reset);
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && cur) schedule(true);
        });
        window.addEventListener('online', function() {
            if (cur) schedule(true);
        });

    })();
</script>

</body>

</html>