<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include("../database/connection.php");
include("includes/header.php");
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Geist+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    body.scan-page {
        overflow-x: hidden;
    }

    #sidebarToggleMobile {
        border-radius: 12px;
        font-weight: 600;
    }

    #sidebarOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease;
        z-index: 1039;
    }

    body.sidebar-open #sidebarOverlay {
        opacity: 1;
        visibility: visible;
    }

    @media (max-width:991.98px) {
        body.scan-page #accordionSidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 260px;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 1040;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        body.scan-page.sidebar-open #accordionSidebar {
            transform: translateX(0);
        }

        body.scan-page #content-wrapper {
            margin-left: 0 !important;
        }
    }


    /* =========================================================
       Cloak return design (same system as the issuing page)
       ========================================================= */
    .sc,
    .sc-modal,
    .sc-toast {
        --ink: #17233d;
        --muted: #5f6b7e;
        --line: #e2e6ee;
        --soft: #f4f6fb;
        --brand: #1f4bb6;
        --brand-d: #173a91;
        --brand-bg: #e8eefb;
        --ok: #1b7f4b;
        --ok-bg: #e6f4ec;
        --bad: #c0372f;
        --bad-bg: #fdecea;
        --ease: cubic-bezier(0.16, 1, 0.3, 1);
        font-family: 'Public Sans', system-ui, sans-serif;
        color: var(--ink);
        -webkit-font-smoothing: antialiased;
    }

    .sc *,
    .sc-modal *,
    .sc-toast * {
        box-sizing: border-box;
    }

    .sc {
        max-width: 1180px;
        margin-inline: auto;
        padding-bottom: 36px;
        line-height: 1.5;
    }

    .sc [hidden] {
        display: none !important;
    }

    .sc-head {
        margin-bottom: 22px;
    }

    .sc-head h1 {
        margin: 0;
        font-size: clamp(24px, 3vw, 32px);
        font-weight: 700;
        letter-spacing: -.03em;
        line-height: 1.1;
    }

    .sc-head p {
        margin: 6px 0 0;
        font-size: 14px;
        color: var(--muted);
    }

    .sc-k {
        font-size: 13px;
        font-weight: 600;
        color: var(--muted);
    }

    /* buttons */
    .sc-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 18px;
        border: 1px solid #c4ccd9;
        border-radius: 10px;
        background: #fff;
        color: var(--ink);
        font: inherit;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.3;
        cursor: pointer;
        transition: background-color .15s ease, border-color .15s ease, color .15s ease, transform .1s ease;
    }

    .sc-btn:hover {
        border-color: var(--brand);
        color: var(--brand);
    }

    .sc-btn:active {
        transform: scale(.98);
    }

    .sc-btn:focus-visible {
        outline: 3px solid rgba(31, 75, 182, .35);
        outline-offset: 2px;
    }

    .sc-btn.solid {
        background: var(--brand);
        border-color: var(--brand);
        color: #fff;
    }

    .sc-btn.solid:hover {
        background: var(--brand-d);
        border-color: var(--brand-d);
        color: #fff;
    }

    .sc-btn.ok {
        background: var(--ok);
        border-color: var(--ok);
        color: #fff;
    }

    .sc-btn.ok:hover {
        background: #166a3f;
        border-color: #166a3f;
        color: #fff;
    }

    .sc-btn.bad {
        background: var(--bad);
        border-color: var(--bad);
        color: #fff;
    }

    .sc-btn.bad:hover {
        background: #a42f28;
        border-color: #a42f28;
        color: #fff;
    }

    .sc-btn:disabled {
        opacity: .65;
        cursor: not-allowed;
        transform: none;
    }

    .sc-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 16px;
    }

    /* stats strip */
    .rt-stats {
        padding: 6px 0 20px;
    }

    .rt-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
    }

    .rt-stat {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 20px 24px 14px;
    }

    .rt-stat+.rt-stat {
        border-left: 1px solid var(--line);
    }

    .rt-ic {
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        font-size: 18px;
    }

    .rt-stat.issued .rt-ic {
        background: var(--brand-bg);
        color: var(--brand);
    }

    .rt-stat.returned .rt-ic {
        background: var(--ok-bg);
        color: var(--ok);
    }

    .rt-stat.remaining .rt-ic {
        background: var(--bad-bg);
        color: var(--bad);
    }

    .rt-num {
        display: block;
        font-size: 32px;
        font-weight: 700;
        letter-spacing: -.02em;
        line-height: 1.1;
        font-variant-numeric: tabular-nums;
    }

    @keyframes rtPulse {
        50% {
            transform: scale(1.08);
        }
    }

    .rt-num.stat-updating {
        animation: rtPulse .5s ease-in-out;
    }

    .rt-progress {
        padding: 0 24px;
    }

    .rt-progress-top {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--muted);
    }

    .sc-bar {
        height: 8px;
        overflow: hidden;
        border-radius: 99px;
        background: var(--soft);
        border: 1px solid var(--line);
    }

    .sc-bar i {
        display: block;
        height: 100%;
        width: 0;
        border-radius: 99px;
        background: var(--ok);
        transition: width .6s var(--ease);
    }

    /* scan box */
    .sc-scan {
        margin-top: 20px;
        padding: clamp(22px, 4vw, 42px);
        text-align: center;
        background: radial-gradient(48rem 14rem at 50% -35%, rgba(31, 75, 182, .10), transparent 70%), #fff;
    }

    .sc-qr {
        display: inline-grid;
        place-items: center;
        width: 64px;
        height: 64px;
        margin-bottom: 14px;
        border-radius: 18px;
        background: var(--brand);
        color: #fff;
        font-size: 26px;
    }

    .sc-scan h2 {
        margin: 0;
        font-size: clamp(22px, 3vw, 30px);
        font-weight: 700;
        letter-spacing: -.025em;
    }

    .sc-scan p {
        margin: 6px 0 22px;
        font-size: 14px;
        color: var(--muted);
    }

    .sc-field {
        display: flex;
        gap: 10px;
        max-width: 640px;
        margin: 0 auto;
    }

    .sc-input {
        flex: 1 1 auto;
        min-width: 0;
        height: 60px;
        padding: 0 20px;
        border: 2px solid #c4ccd9;
        border-radius: 14px;
        background: #fff;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 24px;
        font-weight: 600;
        letter-spacing: .02em;
        text-align: center;
        color: var(--ink);
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .sc-input::placeholder {
        font-family: 'Public Sans', system-ui, sans-serif;
        font-size: 17px;
        font-weight: 500;
        letter-spacing: 0;
        color: #8b95a7;
    }

    .sc-input:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 4px rgba(31, 75, 182, .15);
        outline: 0;
    }

    .sc-go {
        flex: 0 0 auto;
        height: 60px;
        padding: 0 26px;
        border-radius: 14px;
        white-space: nowrap;
    }

    .sc-hint {
        margin: 12px 0 0 !important;
        font-size: 13px !important;
    }

    /* modals */
    .sc-modal .modal-content {
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(23, 35, 61, .28);
    }

    .sc-mhead {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--line);
    }

    .sc-modal.ok .sc-mhead {
        background: var(--ok-bg);
    }

    .sc-modal.bad .sc-mhead {
        background: var(--bad-bg);
    }

    .sc-mic {
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        color: #fff;
        font-size: 17px;
    }

    .sc-modal.ok .sc-mic {
        background: var(--ok);
    }

    .sc-modal.bad .sc-mic {
        background: var(--bad);
    }

    .sc-mhead>div {
        flex: 1 1 auto;
        min-width: 0;
    }

    .sc-mtitle {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        letter-spacing: -.01em;
    }

    .sc-msub {
        margin: 2px 0 0;
        font-size: 13px;
        color: var(--muted);
    }

    .sc-x {
        flex: 0 0 auto;
        width: 34px;
        height: 34px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: var(--muted);
        cursor: pointer;
    }

    .sc-x:hover {
        background: rgba(23, 35, 61, .08);
        color: var(--ink);
    }

    .sc-x:focus-visible {
        outline: 3px solid rgba(31, 75, 182, .35);
    }

    .sc-mbody {
        padding: 20px;
    }

    .sc-mfoot {
        display: flex;
        gap: 10px;
        padding: 14px 20px;
        border-top: 1px solid var(--line);
        background: var(--soft);
    }

    .sc-mfoot .sc-btn {
        flex: 1 1 auto;
    }

    .sc-errtext {
        margin: 0;
        font-size: 15px;
        color: var(--ink);
    }

    .sc-errid {
        display: inline-block;
        margin-top: 12px;
        padding: 6px 12px;
        border-radius: 8px;
        background: var(--soft);
        border: 1px solid var(--line);
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 15px;
        font-weight: 600;
    }

    .sc-inline-err {
        margin-bottom: 14px;
        padding: 10px 14px;
        border-radius: 10px;
        background: var(--bad-bg);
        color: var(--bad);
        font-size: 14px;
        font-weight: 600;
    }

    /* student hero: avatar, ID + session chips, programme (session colours: 1 pink, 2 purple, 3 green) */
    .cc-hero {
        --acc: #1f4bb6;
        --acc-bg: #eef2fb;
        --acc-line: #d5def3;
        --acc-ink: #173a91;
        --acc-chip: #dbe5fa;
        margin-bottom: 18px;
        padding: 16px 16px 14px;
        border: 1px solid var(--acc-line);
        border-top: 4px solid var(--acc);
        border-radius: 16px;
        background: var(--acc-bg);
    }

    .cc-hero.s1 {
        --acc: #d6336c;
        --acc-bg: #fde4ee;
        --acc-line: #f5bfd5;
        --acc-ink: #a61e4d;
        --acc-chip: #f8c8da;
    }

    .cc-hero.s2 {
        --acc: #7048c9;
        --acc-bg: #ede7fb;
        --acc-line: #d3c6f3;
        --acc-ink: #5f3dc4;
        --acc-chip: #dccff7;
    }

    .cc-hero.s3 {
        --acc: #1b9a5a;
        --acc-bg: #dff3e6;
        --acc-line: #b5dfc4;
        --acc-ink: #0b7a43;
        --acc-chip: #c4e8d1;
    }

    .cc-hero.s0 {
        --acc: #8a95a8;
        --acc-bg: #f4f6fb;
        --acc-line: #e2e6ee;
        --acc-ink: #5f6b7e;
        --acc-chip: #e6eaf2;
    }

    .cc-hero-top {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .cc-av {
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: var(--acc);
        color: #fff;
        font-size: 19px;
        font-weight: 700;
        box-shadow: 0 0 0 4px #fff, 0 0 0 6px var(--acc-bg);
    }

    .cc-hero-top>div {
        min-width: 0;
    }

    .cc-hname {
        display: block;
        font-size: 19px;
        font-weight: 700;
        letter-spacing: -.01em;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    .cc-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
    }

    .cc-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 10px;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: .01em;
    }

    .cc-chip.id {
        background: #fff;
        border: 1px solid #c4ccd9;
        color: var(--ink);
    }

    .cc-chip.ses {
        background: var(--acc-chip);
        border: 1px solid var(--acc-line);
        color: var(--acc-ink);
        text-transform: uppercase;
    }

    .cc-chip.ses::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--acc);
    }

    .cc-hprog {
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px dashed var(--acc-line);
    }

    .cc-hprog b {
        display: block;
        margin-top: 2px;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    /* item rows */
    .cc-items-h {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .cc-count {
        font-size: 13px;
        font-weight: 700;
        color: var(--ok);
    }

    .cc-prog {
        margin-bottom: 14px;
    }

    .cc-items {
        display: grid;
        gap: 10px;
    }

    .cc-item {
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 14px;
        padding: 12px 14px;
        border: 1px solid var(--line);
        border-radius: 12px;
        background: #fff;
    }

    .cc-item.is-returned {
        background: var(--ok-bg);
        border-color: #bfe3cd;
    }

    .cc-ic {
        display: grid;
        place-items: center;
        width: 42px;
        height: 42px;
        border-radius: 11px;
        background: var(--brand-bg);
        color: var(--brand);
        font-size: 18px;
    }

    .cc-item.is-returned .cc-ic {
        background: #fff;
        color: var(--ok);
    }

    .cc-name {
        display: block;
        font-size: 15px;
        font-weight: 700;
    }

    .cc-state {
        display: block;
        font-size: 13px;
        color: var(--muted);
    }

    .cc-done {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13.5px;
        font-weight: 700;
        color: var(--ok);
    }

    .sc-note {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 14px 0 0;
        padding: 10px 14px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
    }

    .sc-note.ok {
        background: var(--ok-bg);
        color: var(--ok);
    }

    .sc-note.bad {
        background: var(--bad-bg);
        color: var(--bad);
    }

    .sc-note.muted {
        background: var(--soft);
        color: var(--muted);
    }

    /* toast */
    .sc-toast {
        position: fixed;
        left: 50%;
        bottom: 28px;
        z-index: 2000;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        border-radius: 12px;
        background: var(--ink);
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 10px 30px rgba(23, 35, 61, .3);
        opacity: 0;
        visibility: hidden;
        transform: translate(-50%, 12px);
        transition: opacity .25s ease, transform .3s var(--ease), visibility .25s;
    }

    .sc-toast.show {
        opacity: 1;
        visibility: visible;
        transform: translate(-50%, 0);
    }

    .sc-toast i {
        color: #5fd69a;
    }

    @media (max-width: 767.98px) {
        .rt-stats-grid {
            grid-template-columns: 1fr;
        }

        .rt-stat+.rt-stat {
            border-left: 0;
            border-top: 1px solid var(--line);
        }

        .rt-stat {
            padding: 14px 20px;
        }

        .rt-progress {
            padding: 0 20px;
        }
    }

    @media (max-width: 575.98px) {
        .sc-field {
            flex-direction: column;
        }

        .sc-go {
            width: 100%;
        }

        .cc-item {
            grid-template-columns: auto 1fr;
        }

        .cc-item> :last-child {
            grid-column: 1 / -1;
        }

        .cc-item .sc-btn {
            width: 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .rt-num.stat-updating {
            animation: none;
        }

        .sc-bar i,
        .sc-toast {
            transition: none;
        }
    }

    /* =========================================================
       Compact modals: smaller, always fit the screen
       (placed last so it overrides the earlier modal sizes)
       ========================================================= */
    .sc-modal .modal-dialog {
        max-width: 400px;
        width: calc(100% - 24px);
        margin: 12px auto;
        min-height: calc(100% - 24px);
    }

    .sc-modal .modal-content {
        display: flex;
        flex-direction: column;
        width: 100%;
        max-height: calc(100vh - 24px);
        max-height: calc(100dvh - 24px);
        border-radius: 14px;
        box-shadow: 0 18px 44px rgba(23, 35, 61, .26);
    }

    .sc-mhead {
        flex: 0 0 auto;
        gap: 10px;
        padding: 12px 14px;
    }

    .sc-mic {
        width: 34px;
        height: 34px;
        font-size: 14px;
    }

    .sc-mtitle {
        font-size: 16px;
    }

    .sc-msub {
        margin: 0;
        font-size: 12px;
    }

    .sc-x {
        width: 30px;
        height: 30px;
    }

    .sc-mbody {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: 14px;
    }

    .sc-mfoot {
        flex: 0 0 auto;
        gap: 8px;
        padding: 10px 14px;
    }

    .sc-mfoot .sc-btn {
        padding: 8px 14px;
        font-size: 13.5px;
    }

    .sc-errtext {
        font-size: 14px;
    }

    .sc-errid {
        margin-top: 8px;
        padding: 4px 10px;
        font-size: 13.5px;
    }

    .sc-inline-err {
        margin-bottom: 10px;
        padding: 8px 12px;
        font-size: 13px;
    }

    /* student card */
    .cc-hero {
        margin-bottom: 12px;
        padding: 12px 12px 10px;
        border-radius: 12px;
    }

    .cc-hero-top {
        gap: 10px;
    }

    .cc-av {
        width: 44px;
        height: 44px;
        font-size: 15px;
        box-shadow: 0 0 0 3px #fff, 0 0 0 5px var(--acc-bg);
    }

    .cc-hname {
        font-size: 16px;
    }

    .cc-chips {
        gap: 6px;
        margin-top: 6px;
    }

    .cc-chip {
        gap: 6px;
        padding: 3px 9px;
        border-radius: 8px;
        font-size: 12.5px;
    }

    .cc-chip.ses::before {
        width: 7px;
        height: 7px;
    }

    .cc-hprog {
        margin-top: 10px;
        padding-top: 8px;
    }

    .cc-hprog b {
        font-size: 13.5px;
    }

    /* item rows */
    .cc-items-h {
        margin-bottom: 6px;
    }

    .cc-count {
        font-size: 12px;
    }

    .cc-items {
        gap: 8px;
    }

    .cc-item {
        gap: 10px;
        padding: 8px 10px;
        border-radius: 10px;
    }

    .cc-ic {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        font-size: 15px;
    }

    .cc-name {
        font-size: 14px;
    }

    .cc-state {
        font-size: 12px;
    }

    .cc-done {
        font-size: 12.5px;
    }

    .cc-item .sc-btn {
        padding: 6px 11px;
        border-radius: 8px;
        font-size: 12.5px;
    }

    .sc-note {
        margin-top: 10px;
        padding: 8px 12px;
        font-size: 13px;
    }

    /* very short screens (landscape phones, small laptops at high zoom) */
    @media (max-height: 640px) {
        .sc-modal .modal-dialog {
            margin: 8px auto;
            min-height: calc(100% - 16px);
        }

        .sc-modal .modal-content {
            max-height: calc(100vh - 16px);
            max-height: calc(100dvh - 16px);
        }

        .cc-av {
            width: 38px;
            height: 38px;
            font-size: 13px;
        }

        .cc-hero {
            padding: 10px;
            margin-bottom: 10px;
        }

        .cc-hprog {
            margin-top: 8px;
            padding-top: 6px;
        }
    }

    @media (max-width: 575.98px) {
        .sc-modal .modal-dialog {
            max-width: none;
            width: calc(100% - 16px);
        }
    }


    /* items laid out as columns (Cloak | Slashes | Hats) instead of rows */
    .sc-modal .modal-dialog {
        max-width: 440px;
    }

    .cc-items {
        grid-auto-flow: column;
        grid-auto-columns: minmax(0, 1fr);
        gap: 8px;
    }

    .cc-item,
    .cc-item.is-returned {
        grid-template-columns: 1fr;
        justify-items: center;
        align-content: start;
        gap: 8px;
        padding: 12px 8px 10px;
        text-align: center;
    }

    .cc-item> :last-child {
        grid-column: auto;
        align-self: end;
    }

    .cc-item .cc-ic {
        width: 40px;
        height: 40px;
        font-size: 17px;
    }

    .cc-item .cc-name,
    .cc-item .cc-state {
        overflow-wrap: anywhere;
    }

    .cc-item .cc-state {
        font-size: 11.5px;
        line-height: 1.3;
    }

    .cc-item .sc-btn,
    .cc-item .cc-done {
        width: 100%;
        justify-content: center;
        text-align: center;
    }

    .cc-item .sc-btn {
        flex-direction: column;
        gap: 2px;
        padding: 7px 4px;
        font-size: 12px;
        line-height: 1.2;
    }

    .cc-item .cc-done {
        flex-direction: column;
        gap: 2px;
        padding: 7px 0;
        font-size: 12px;
    }
</style>

<div id="wrapper">
    <?php include("nav.php"); ?>
    <div id="sidebarOverlay" class="d-md-none"></div>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include("includes/topnav.php"); ?>

            <div class="container-fluid mt-4">
                <button id="sidebarToggleMobile"
                    class="btn btn-light border d-inline-flex align-items-center gap-2 d-lg-none mb-3 shadow-sm">
                    <i class="fas fa-bars"></i>
                    <span class="fw-semibold">Menu</span>
                </button>

                <div class="sc">

                    <header class="sc-head">
                        <h1>Cloak return</h1>
                        <p>Scan the student's QR code to see which items they still need to give back.</p>
                    </header>

                    <!-- live totals -->
                    <section class="sc-card rt-stats" aria-label="Return totals">
                        <div class="rt-stats-grid">
                            <div class="rt-stat issued">
                                <span class="rt-ic" aria-hidden="true"><i class="fas fa-user-graduate"></i></span>
                                <div>
                                    <span class="sc-k">Total issued</span>
                                    <span class="rt-num" id="statIssued">0</span>
                                </div>
                            </div>
                            <div class="rt-stat returned">
                                <span class="rt-ic" aria-hidden="true"><i class="fas fa-check-circle"></i></span>
                                <div>
                                    <span class="sc-k">Returned</span>
                                    <span class="rt-num" id="statReturned">0</span>
                                </div>
                            </div>
                            <div class="rt-stat remaining">
                                <span class="rt-ic" aria-hidden="true"><i class="fas fa-hourglass-half"></i></span>
                                <div>
                                    <span class="sc-k">Remaining</span>
                                    <span class="rt-num" id="statRemaining">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="rt-progress">
                            <div class="rt-progress-top"><span>Return progress</span><span id="statPct">0%</span></div>
                            <div class="sc-bar" role="progressbar" aria-label="Return progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" id="statBar"><i></i></div>
                        </div>
                    </section>

                    <!-- scan box -->
                    <section class="sc-card sc-scan">
                        <span class="sc-qr" aria-hidden="true"><i class="fas fa-qrcode"></i></span>
                        <h2>Scan QR for cloak return</h2>
                        <p>Click the box and scan the code, or type the student ID and press Enter.</p>
                        <form id="studentIDForm" autocomplete="off">
                            <div class="sc-field">
                                <input type="text" class="sc-input" id="studentID" name="studentID"
                                    placeholder="Enter or scan student ID" aria-label="Student ID"
                                    autocapitalize="off" spellcheck="false" required>
                                <button type="submit" id="goBtn" class="sc-btn solid sc-go">
                                    <i class="fas fa-search" aria-hidden="true"></i> <span>Check student</span>
                                </button>
                            </div>
                        </form>
                        <p class="sc-hint">Using a handheld scanner? Keep the cursor in the box and scan. It checks automatically.</p>
                    </section>

                </div> <!-- /sc -->
            </div>
        </div> <!-- content -->
    </div> <!-- content-wrapper -->
</div> <!-- wrapper -->

<!-- Student found + items -->
<div class="modal fade sc-modal ok" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="sc-mhead">
                <span class="sc-mic" aria-hidden="true"><i class="fas fa-check"></i></span>
                <div>
                    <h5 class="sc-mtitle" id="successModalLabel">Student found</h5>
                    <p class="sc-msub">Verified successfully</p>
                </div>
                <button type="button" class="sc-x" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body sc-mbody">
                <div id="studentInfo"></div>
                <div class="sc-inline-err" id="markErr" role="alert" hidden></div>
                <div id="itemsList"></div>
            </div>
            <div class="modal-footer sc-mfoot">
                <button type="button" class="sc-btn" data-bs-dismiss="modal">Done</button>
            </div>
        </div>
    </div>
</div>

<!-- Error (not registered / server error) -->
<div class="modal fade sc-modal bad" id="errorModal" tabindex="-1" aria-labelledby="errorTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="sc-mhead">
                <span class="sc-mic" aria-hidden="true"><i class="fas fa-times"></i></span>
                <div>
                    <h5 class="sc-mtitle" id="errorTitle">Student not registered</h5>
                    <p class="sc-msub">Nothing was changed</p>
                </div>
                <button type="button" class="sc-x" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body sc-mbody">
                <p class="sc-errtext" id="errorText">Please check the registered students table.</p>
                <span class="sc-errid" id="errorId" style="display:none;"></span>
            </div>
            <div class="modal-footer sc-mfoot">
                <button type="button" class="sc-btn bad" data-bs-dismiss="modal">Try again</button>
            </div>
        </div>
    </div>
</div>

<div class="sc-toast" id="scToast" role="status" aria-live="polite"><i class="fas fa-check-circle" aria-hidden="true"></i><span></span></div>

<!-- Bootstrap 5 JS + jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        $('body').addClass('scan-page');

        $('#sidebarToggleMobile').on('click', function() {
            $('body').toggleClass('sidebar-open');
        });
        $('#sidebarOverlay').on('click', function() {
            $('body').removeClass('sidebar-open');
        });

        const $studentID = $('#studentID');
        const $goBtn = $('#goBtn');
        let busy = false;
        let scanSeq = 0; // answers for an older request are ignored
        let currentXhr = null;

        const ITEMS = {
            cloak: {
                name: 'Cloak',
                icon: 'fa-user-graduate'
            },
            slashes: {
                name: 'Slashes',
                icon: 'fa-ribbon'
            },
            hats: {
                name: 'Hat',
                icon: 'fa-graduation-cap'
            }
        };

        // ---------- helpers ----------
        function esc(v) {
            return $('<div>').text(v ?? '').html();
        }

        function initials(name) {
            const w = String(name ?? '').trim().split(/\s+/).filter(Boolean);
            if (!w.length) return '?';
            return (w[0][0] + (w.length > 1 ? w[w.length - 1][0] : '')).toUpperCase();
        }

        // plain ID, link (?student_id= or last path part) or JSON all work
        function extractId(raw) {
            const t = String(raw ?? '').replace(/[\r\n\t]+/g, '').trim();
            if (!t) return '';
            try {
                const j = JSON.parse(t);
                if (j && typeof j === 'object') {
                    const v = j.student_id ?? j.studentID ?? j.studentId ?? j.id;
                    if (v != null && String(v).trim()) return String(v).trim();
                }
            } catch (e) {}
            if (/^https?:\/\//i.test(t)) {
                try {
                    const u = new URL(t);
                    const keys = ['student_id', 'studentID', 'studentId', 'id'];
                    for (let i = 0; i < keys.length; i++) {
                        const v = u.searchParams.get(keys[i]);
                        if (v && v.trim()) return v.trim();
                    }
                    const seg = u.pathname.split('/').filter(Boolean).pop();
                    if (seg) return decodeURIComponent(seg).trim();
                } catch (e) {}
            }
            return t;
        }

        let toastTimer;

        function toast(msg) {
            const $t = $('#scToast');
            $t.find('span').text(msg);
            $t.addClass('show');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(function() {
                $t.removeClass('show');
            }, 3500);
        }

        function setBusy(b) {
            busy = b;
            $goBtn.prop('disabled', b).find('span').text(b ? 'Checking…' : 'Check student');
        }

        // ---------- live totals ----------
        function setStat(sel, value) {
            const $el = $(sel);
            if ($el.text() != value) {
                $el.text(value).addClass('stat-updating');
                setTimeout(function() {
                    $el.removeClass('stat-updating');
                }, 500);
            }
        }

        function updateCloakStats() {
            $.ajax({
                url: 'fetch_cloak_stats.php',
                type: 'GET',
                dataType: 'json',
                cache: false,
                success: function(r) {
                    if (r.status !== 'success') return;
                    setStat('#statIssued', r.total_issued);
                    setStat('#statReturned', r.total_returned);
                    setStat('#statRemaining', r.total_remaining);
                    const issued = Number(r.total_issued) || 0;
                    const pct = issued ? Math.min(100, Math.round(Number(r.total_returned) / issued * 100)) : 0;
                    $('#statPct').text(pct + '%');
                    $('#statBar').attr('aria-valuenow', pct).find('i').css('width', pct + '%');
                },
                error: function() {
                    console.error('Error fetching cloak stats');
                }
            });
        }

        updateCloakStats();
        setInterval(function() {
            if (!document.hidden) updateCloakStats();
        }, 5000);

        // ---------- render ----------
        // "SESSION_01" / "session 2" -> "Session 01" / "Session 2"; empty or "none" -> "—"
        function sessionLabel(v) {
            v = String(v ?? '').trim();
            if (!v || v.toLowerCase() === 'none') return '—';
            return v.replace(/_/g, ' ').toLowerCase().replace(/\b\w/g, function(c) {
                return c.toUpperCase();
            });
        }

        // trailing number 1-3 picks the colour (pink / purple / green)
        function sessionClass(v) {
            const m = String(v ?? '').match(/(\d+)\s*$/);
            const n = m ? parseInt(m[1], 10) : 0;
            return (n >= 1 && n <= 3) ? 's' + n : 's0';
        }

        function studentCard(d) {
            const rawSes = d.session ?? d.session_name ?? d.student_session ?? '';
            const ses = sessionLabel(rawSes);
            const hasSes = ses !== '—';
            const program = String(d.program_name ?? d.program ?? '').trim() || '—';
            return `
                <div class="cc-hero ${hasSes ? sessionClass(rawSes) : 's0'}">
                    <div class="cc-hero-top">
                        <span class="cc-av" aria-hidden="true">${esc(initials(d.student_name))}</span>
                        <div>
                            <strong class="cc-hname">${esc(d.student_name)}</strong>
                            <div class="cc-chips">
                                <span class="cc-chip id" title="Student ID">${esc(d.student_id)}</span>
                                <span class="cc-chip ses" title="Session">${hasSes ? esc(ses) : 'No session'}</span>
                            </div>
                        </div>
                    </div>
                    <div class="cc-hprog">
                        <span class="sc-k">Programme</span>
                        <b>${esc(program)}</b>
                    </div>
                </div>`;
        }

        function itemsHtml(d) {
            const keys = ['cloak', 'slashes', 'hats'].filter(function(k) {
                return d['collect_' + k];
            });

            if (!keys.length) {
                return '<p class="sc-note muted"><i class="fas fa-info-circle" aria-hidden="true"></i> No items have been collected by this student.</p>';
            }

            const returned = keys.filter(function(k) {
                return d['return_' + k];
            }).length;
            const pct = Math.round(returned / keys.length * 100);

            let html = '<div class="cc-items-h"><span class="sc-k">Items to return</span>' +
                '<span class="cc-count">' + returned + ' / ' + keys.length + ' returned</span></div>' +
                '<div class="sc-bar cc-prog" role="progressbar" aria-label="Items returned" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' + pct + '"><i style="width:' + pct + '%"></i></div>' +
                '<div class="cc-items">';

            keys.forEach(function(k) {
                const meta = ITEMS[k];
                const done = !!d['return_' + k];
                html += `
                    <div class="cc-item ${done ? 'is-returned' : ''}">
                        <span class="cc-ic" aria-hidden="true"><i class="fas ${meta.icon}"></i></span>
                        <div>
                            <span class="cc-name">${meta.name}</span>
                            <span class="cc-state">${done ? 'Returned' : 'Not returned yet'}</span>
                        </div>
                        ${done
                            ? '<span class="cc-done"><i class="fas fa-check-circle" aria-hidden="true"></i> Returned</span>'
                            : `<button type="button" class="sc-btn ok markReturnBtn" data-item="${k}" data-student="${esc(d.student_id)}"><i class="fas fa-check" aria-hidden="true"></i> <span>Mark as returned</span></button>`}
                    </div>`;
            });

            html += '</div>';

            if (returned === keys.length) {
                html += '<p class="sc-note ok"><i class="fas fa-check-circle" aria-hidden="true"></i> All items returned</p>';
            } else {
                const left = keys.length - returned;
                html += '<p class="sc-note bad"><i class="fas fa-exclamation-triangle" aria-hidden="true"></i> ' + left + ' item' + (left === 1 ? '' : 's') + ' still to return</p>';
            }
            return html;
        }

        function renderStudent(d) {
            $('#studentInfo').html(studentCard(d));
            $('#itemsList').html(itemsHtml(d));
        }

        function showError(title, text, id) {
            $('#errorTitle').text(title);
            $('#errorText').text(text);
            $('#errorId').text(id || '').toggle(!!id);
            $('#errorModal').modal('show');
        }

        // ---------- look up ----------
        function fetchStudent(studentID, onDone) {
            return $.ajax({
                url: 'fetchStudentCloth.php',
                type: 'POST',
                dataType: 'json',
                cache: false,
                data: {
                    studentID: studentID
                },
                success: onDone,
                error: function(xhr, status) {
                    if (status === 'abort') return;
                    showError('Server error', 'Could not reach the server. Please try again.');
                }
            });
        }

        function lookup(studentID) {
            const mySeq = ++scanSeq;
            setBusy(true);

            currentXhr = fetchStudent(studentID, function(res) {
                if (mySeq !== scanSeq) return;
                if (res.status === 'success') {
                    $('#markErr').prop('hidden', true).text('');
                    renderStudent(res.data);
                    $('#successModal').modal('show');
                } else if (res.status === 'not_registered') {
                    showError('Student not registered', 'Please check the registered students table.', studentID);
                } else {
                    showError('Student not found', res.message || 'Please check the ID and try again.', studentID);
                }
            }).always(function() {
                if (mySeq === scanSeq) setBusy(false);
            });
        }

        $('#studentIDForm').on('submit', function(e) {
            e.preventDefault();
            const id = extractId($studentID.val());
            if (!id || busy) return;
            lookup(id);
        });

        // ---------- mark one item as returned ----------
        $(document).on('click', '.markReturnBtn', function() {
            const $btn = $(this);
            if ($btn.prop('disabled')) return;
            const studentID = String($btn.data('student'));
            const item = $btn.data('item');
            const label = $btn.html();
            const mySeq = scanSeq;

            $('#markErr').prop('hidden', true).text('');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> <span>Saving…</span>');

            function fail(msg) {
                $('#markErr').text(msg || 'Failed to mark as returned.').prop('hidden', false);
                $btn.prop('disabled', false).html(label);
            }

            $.ajax({
                url: 'markReturnCloth.php',
                type: 'POST',
                dataType: 'json',
                cache: false,
                data: {
                    studentID: studentID,
                    item: item
                },
                success: function(res) {
                    if (mySeq !== scanSeq) return;
                    if (res.status !== 'success') {
                        fail(res.message);
                        return;
                    }
                    toast(ITEMS[item].name + ' returned');
                    updateCloakStats();
                    // pull the latest status so the modal shows what is really stored
                    fetchStudent(studentID, function(r) {
                        if (mySeq !== scanSeq) return;
                        if (r.status === 'success') renderStudent(r.data);
                        else fail('Saved, but could not refresh the list.');
                    });
                },
                error: function(xhr) {
                    let msg = 'Server error. Please try again.';
                    try {
                        const r = JSON.parse(xhr.responseText);
                        if (r.message) msg = r.message;
                    } catch (e) {}
                    fail(msg);
                }
            });
        });

        // ---------- after ANY modal closes: clean slate, cursor back in the ID box ----------
        function resetScreen() {
            scanSeq++; // ignore anything still on its way
            if (currentXhr) {
                currentXhr.abort();
                currentXhr = null;
            }
            $('#studentInfo, #itemsList').empty();
            $('#markErr').prop('hidden', true).text('');
            $('#studentIDForm')[0].reset();
            setBusy(false);
            $studentID.focus();
        }

        $('#successModal, #errorModal').on('hidden.bs.modal', resetScreen);

        // Escape clears the box when no modal is open
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && !$('.modal.show').length) resetScreen();
        });

        $studentID.focus();
    });
</script>

</body>

</html>