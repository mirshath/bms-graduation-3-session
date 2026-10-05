<?php
session_start();
include '../database/connection.php';

$error = '';
$email = '';

// Logo used above "Sign in" and as the repeating background. Replace with a local file when you have one, e.g. './img/logo.png'
$logoUrl = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRGkKtuN4jwLcLnigNTh1sGRqxzyEdf_HGDow&s';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Enter your email and password.';
    } else {
        $sql = "SELECT * FROM admin WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // One generic message for both cases, so the page never reveals which emails exist
        $admin = $result->num_rows > 0 ? $result->fetch_assoc() : null;

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['admin_name'];
            $_SESSION['role'] = $admin['role'];
            header("Location: index");
            exit();
        } else {
            $error = 'Incorrect email or password.';
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Admin sign in | BMS Graduations</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,500;6..72,600&family=Public+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --ink: #17233d;
            --muted: #5b6577;
            --line: #dde2ea;
            --paper: #f3f5f9;
            --brand: #1f4bb6;
            --brand-d: #173a91;
            --bad: #b3261e;
            --bad-bg: #fdecea;
            --logo-url: url('<?php echo htmlspecialchars($logoUrl, ENT_QUOTES); ?>');
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            font-family: 'Public Sans', system-ui, sans-serif;
            color: var(--ink);
            background: var(--paper);
        }

        .shell {
            min-height: 100dvh;
            display: grid;
            grid-template-columns: minmax(300px, 5fr) 7fr;
        }

        /* brand side */
        .brand {
            background: var(--brand-d);
            color: #fff;
            padding: 48px 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .logo {
            width: 64px;
            height: 64px;
            margin-bottom: 24px;
            border-radius: 16px;
            background: #fff;
            border: 1px solid var(--line);
            box-shadow: 0 8px 20px -10px rgba(23, 58, 145, .35);
            display: grid;
            place-items: center;
            overflow: hidden;
            font-weight: 600;
            color: var(--brand-d);
            letter-spacing: .02em;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 8px;
        }

        .brand .copy {
            margin-block: auto;
        }

        .brand h1 {
            font-family: 'Newsreader', Georgia, serif;
            font-weight: 600;
            font-size: clamp(2rem, 3.4vw, 3rem);
            line-height: 1.08;
            letter-spacing: -.02em;
            margin: 0 0 14px;
            text-wrap: balance;
        }

        .brand p {
            margin: 0;
            max-width: 34ch;
            color: #c9d6f4;
            line-height: 1.6;
        }

        .brand small {
            color: #9fb2e3;
            font-size: .8rem;
        }

        /* form side */
        .panel {
            position: relative;
            isolation: isolate;
            display: grid;
            place-items: center;
            padding: 32px 20px;
            background: var(--paper);
        }

        /* repeating logo, kept faint so the form stays readable */
        .panel::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            background-image: var(--logo-url);
            background-repeat: space;
            background-size: 200px 200px;
            background-position: center;
            opacity: .07;
            pointer-events: none;
        }

        .card {
            width: 100%;
            max-width: 420px;
        }

        .card h2 {
            font-family: 'Newsreader', Georgia, serif;
            font-weight: 600;
            font-size: 2.1rem;
            letter-spacing: -.02em;
            margin: 0 0 6px;
        }

        .card .sub {
            margin: 0 0 28px;
            color: var(--muted);
        }

        .error {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            margin-bottom: 20px;
            padding: 11px 14px;
            font-size: .92rem;
            background: var(--bad-bg);
            color: #7a1b15;
            border-left: 3px solid var(--bad);
            border-radius: 6px;
        }

        .error i {
            margin-top: 3px;
        }

        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
            font-size: .95rem;
        }

        .control {
            position: relative;
        }

        .control>i.lead {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #8a93a6;
            font-size: .9rem;
            pointer-events: none;
        }

        .control input {
            width: 100%;
            height: 48px;
            padding: 0 44px 0 40px;
            font: inherit;
            color: var(--ink);
            background: #fff;
            border: 1px solid #c4ccd9;
            border-radius: 8px;
            outline: 0;
            transition: border-color .15s, box-shadow .15s;
        }

        .control input:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(31, 75, 182, .18);
        }

        .control input[aria-invalid="true"] {
            border-color: var(--bad);
        }

        .toggle {
            position: absolute;
            right: 4px;
            top: 4px;
            width: 40px;
            height: 40px;
            border: 0;
            border-radius: 6px;
            background: none;
            color: #6b7384;
            cursor: pointer;
        }

        .toggle:hover {
            color: var(--ink);
            background: #eef1f6;
        }

        .toggle:focus-visible,
        .submit:focus-visible {
            outline: 3px solid rgba(31, 75, 182, .35);
            outline-offset: 2px;
        }

        .hint {
            display: none;
            margin-top: 6px;
            font-size: .84rem;
            color: #8a5a00;
        }

        .hint.on {
            display: block;
        }

        .submit {
            width: 100%;
            height: 50px;
            margin-top: 6px;
            border: 0;
            border-radius: 8px;
            background: var(--brand);
            color: #fff;
            font: inherit;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s, transform .1s;
        }

        .submit:hover {
            background: var(--brand-d);
        }

        .submit:active {
            transform: scale(.98);
        }

        .submit:disabled {
            background: #9aa8c9;
            cursor: wait;
        }

        .foot {
            margin-top: 28px;
            text-align: center;
            font-size: .8rem;
            color: var(--muted);
        }

        @media (max-width: 820px) {
            .shell {
                grid-template-columns: 1fr;
                grid-template-rows: auto 1fr;
            }

            .brand {
                flex-direction: row;
                align-items: center;
                gap: 16px;
                padding: calc(20px + env(safe-area-inset-top, 0px)) 20px 20px;
            }

            .brand h1 {
                font-size: 1.35rem;
                margin: 0;
            }

            .brand p,
            .brand small {
                display: none;
            }

            .panel {
                align-items: start;
                padding: 32px 20px calc(24px + env(safe-area-inset-bottom, 0px));
            }

            .card h2 {
                font-size: 1.8rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                transition: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="shell">
        <aside class="brand">
            <div class="copy">
                <h1>BMS Graduations</h1>
                <p>Admin portal for the  <b>BMS DEGREE CONVOCATION 2026: </b> registrations, payments and invitations.</p>
            </div>
            <small>&copy; 2026 BMS Graduation. All rights reserved.</small>
        </aside>

        <main class="panel">
            <div class="card">
                <div class="logo">
                    <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="BMS logo"
                        onerror="this.remove(); this.parentNode.textContent='BMS';">
                </div>

                <h2>Sign in</h2>
                <p class="sub">Use your admin account to continue.</p>

                <?php if ($error): ?>
                    <div class="error" role="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" id="loginForm" novalidate>
                    <div class="field">
                        <label for="username">Email</label>
                        <div class="control">
                            <i class="fas fa-user lead"></i>
                            <input type="text" id="username" name="username" inputmode="email"
                                autocomplete="username" autocapitalize="none" spellcheck="false"
                                placeholder="name@bms.ac.lk" required
                                value="<?php echo htmlspecialchars($email); ?>"
                                <?php echo $error ? 'aria-invalid="true"' : ''; ?>
                                <?php echo ($error && $email) ? '' : 'autofocus'; ?>>
                        </div>
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <div class="control">
                            <i class="fas fa-lock lead"></i>
                            <input type="password" id="password" name="password"
                                autocomplete="current-password" placeholder="Enter your password" required
                                <?php echo $error ? 'aria-invalid="true"' : ''; ?>
                                <?php echo ($error && $email) ? 'autofocus' : ''; ?>>
                            <button type="button" class="toggle" id="togglePassword"
                                aria-label="Show password" aria-pressed="false">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="hint" id="capsHint" role="status">Caps Lock is on.</div>
                    </div>

                    <button type="submit" class="submit" id="submitBtn">Sign in</button>
                </form>

                <p class="foot">Trouble signing in? Contact the graduation team.</p>
            </div>
        </main>
    </div>

    <script>
        (function() {
            var pw = document.getElementById('password');
            var user = document.getElementById('username');
            var btn = document.getElementById('togglePassword');
            var icon = btn.querySelector('i');
            var caps = document.getElementById('capsHint');
            var form = document.getElementById('loginForm');
            var submit = document.getElementById('submitBtn');

            // show / hide password
            btn.addEventListener('click', function() {
                var show = pw.type === 'password';
                pw.type = show ? 'text' : 'password';
                icon.className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
                btn.setAttribute('aria-pressed', show ? 'true' : 'false');
                btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                pw.focus();
            });

            // caps lock warning
            function checkCaps(e) {
                caps.classList.toggle('on', !!(e.getModifierState && e.getModifierState('CapsLock')));
            }
            pw.addEventListener('keydown', checkCaps);
            pw.addEventListener('keyup', checkCaps);
            pw.addEventListener('blur', function() {
                caps.classList.remove('on');
            });

            // clear the error styling once the person edits a field
            [user, pw].forEach(function(el) {
                el.addEventListener('input', function() {
                    el.removeAttribute('aria-invalid');
                });
            });

            // basic check, then show progress while signing in
            form.addEventListener('submit', function(e) {
                if (!user.value.trim()) {
                    e.preventDefault();
                    user.setAttribute('aria-invalid', 'true');
                    user.focus();
                    return;
                }
                if (!pw.value) {
                    e.preventDefault();
                    pw.setAttribute('aria-invalid', 'true');
                    pw.focus();
                    return;
                }
                submit.disabled = true;
                submit.textContent = 'Signing in…';
            });
        })();
    </script>
</body>

</html>