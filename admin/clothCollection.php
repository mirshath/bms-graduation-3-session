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

    @media (max-width: 991.98px) {
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
       Scan page design  (tokens are on .sc, .sc-modal and .sc-toast
       because the modals + toast live outside the .sc wrapper)
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
        --warn: #a85d00;
        --warn-bg: #fff4e0;
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

    /* session colours: 01 pink, 02 purple, 03 green */
    .sc .s1 {
        --acc: #d6336c;
        --acc-bg: #ffffff;
        --ses-bg: #fdeaf2;
        --ses-line: #f5c9dc;
    }

    .sc .s2 {
        --acc: #7048c9;
        --acc-bg: #ffffff;
        --ses-bg: #f0ebfc;
        --ses-line: #d9ccf4;
    }

    .sc .s3 {
        --acc: #1b9a5a;
        --acc-bg: #ffffff;
        --ses-bg: #e5f6ec;
        --ses-line: #bfe3cd;
    }

    /* header */
    .sc-head {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 22px;
    }

    .sc-head h1 {
        margin: 0;
        font-size: clamp(24px, 3vw, 32px);
        font-weight: 700;
        letter-spacing: -.03em;
        line-height: 1.1;
        color: var(--ink);
    }

    .sc-head p {
        margin: 6px 0 0;
        font-size: 14px;
        color: var(--muted);
    }

    /* buttons (page + modals) */
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
        text-decoration: none;
        cursor: pointer;
        transition: background-color .15s ease, border-color .15s ease, color .15s ease, transform .1s ease;
    }

    .sc-btn:hover {
        border-color: var(--brand);
        color: var(--brand);
        text-decoration: none;
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

    /* cards */
    .sc-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 16px;
    }

    /* scan box */
    .sc-scan {
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
        color: var(--ink);
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

    /* section titles */
    .sc-sec {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: 32px 0 14px;
    }

    .sc-sec h2 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.01em;
        color: var(--ink);
    }

    .sc-live {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 600;
        color: var(--ok);
    }

    .sc-live::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--ok);
        animation: sc-live 1.8s ease-out infinite;
    }

    @keyframes sc-live {
        0% {
            box-shadow: 0 0 0 0 rgba(27, 127, 75, .45);
        }

        100% {
            box-shadow: 0 0 0 9px rgba(27, 127, 75, 0);
        }
    }

    /* session stat cards */
    .sc-stats {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        gap: 16px;
    }

    .sc-stats .sc-ses {
        flex: 1 1 260px;
        min-width: 0;
    }

    .sc-card.sc-ses {
        padding: 20px 22px 22px;
        background: var(--ses-bg);
        border: 1px solid var(--ses-line);
        border-top: 4px solid var(--acc);
    }

    .sc-ses .sc-bar {
        background: rgba(255, 255, 255, .75);
    }

    .sc-ses .sc-num span {
        color: #566174;
    }

    .sc-ses-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .sc-ses h3 {
        display: flex;
        align-items: center;
        gap: 9px;
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: var(--ink);
    }

    .sc-ses h3::before {
        content: "";
        width: 10px;
        height: 10px;
        border-radius: 3px;
        background: var(--acc);
    }

    .sc-pct {
        padding: 3px 10px;
        border-radius: 999px;
        background: var(--acc-bg);
        color: var(--acc);
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 12px;
        font-weight: 700;
    }


    /* Live / Hidden toggle per session */
    .sc-toggle {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 5px 12px;
        border: 1px solid rgba(27, 127, 75, .25);
        border-radius: 999px;
        background: #fff;
        color: var(--ok);
        font: inherit;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: background .2s, color .2s, border-color .2s;
    }

    .sc-toggle .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
    }

    .sc-toggle.is-live .dot {
        animation: sc-live 1.8s ease-out infinite;
    }

    .sc-toggle:not(.is-live) {
        background: rgba(255, 255, 255, .6);
        border-color: rgba(23, 35, 61, .15);
        color: var(--muted);
    }

    .sc-toggle:focus-visible {
        outline: 2px solid var(--brand);
        outline-offset: 2px;
    }

    .sc-pct-row {
        margin: 14px 0 0;
    }

    .sc-ses.is-hidden .sc-ses-body {
        display: none;
    }

    /* hidden sessions: small chips in their own row below, aligned to the right */
    .sc-stats-hidden {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 10px;
    }

    .sc-stats-hidden:not(:empty) {
        margin-top: 14px;
    }

    .sc-stats-hidden .sc-ses {
        flex: 0 0 auto;
    }

    .sc-stats .sc-card.sc-ses.is-hidden,
    .sc-stats-hidden .sc-card.sc-ses.is-hidden {
        flex: 0 0 auto;
        padding: 8px 12px;
        border-top-width: 3px;
        border-radius: 12px;
    }

    .sc-ses.is-hidden .sc-ses-top {
        gap: 14px;
    }

    .sc-ses.is-hidden h3 {
        font-size: 13px;
    }

    .sc-ses.is-hidden .sc-toggle {
        padding: 3px 10px;
        font-size: 11px;
    }

    .sc-bar {
        height: 8px;
        margin: 16px 0 18px;
        border-radius: 99px;
        background: var(--soft);
        overflow: hidden;
    }

    .sc-bar i {
        display: block;
        height: 100%;
        border-radius: 99px;
        background: var(--acc, var(--brand));
        transition: width .6s var(--ease);
    }

    .sc-bar.sm {
        height: 6px;
        margin: 10px 0 9px;
    }

    .sc-nums {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
    }

    .sc-num span {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
    }

    .sc-num strong {
        display: block;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: clamp(24px, 2.6vw, 32px);
        font-weight: 700;
        letter-spacing: -.03em;
        line-height: 1.2;
        font-variant-numeric: tabular-nums;
        color: var(--ink);
    }

    .sc-num.att strong {
        color: var(--ok);
    }

    .sc-num.rem strong {
        color: var(--bad);
    }

    /* =========================================================
       Modals: Student found / Already attended / Error
       ========================================================= */
    .sc-modal.ok {
        --t: var(--ok);
        --t-bg: var(--ok-bg);
    }

    .sc-modal.warn {
        --t: var(--warn);
        --t-bg: var(--warn-bg);
    }

    .sc-modal.bad {
        --t: var(--bad);
        --t-bg: var(--bad-bg);
    }

    .sc-modal .modal-dialog {
        max-width: 440px;
    }

    .sc-modal.fade .modal-dialog {
        transform: translateY(18px) scale(.97);
        transition: transform .28s var(--ease), opacity .2s ease;
    }

    .sc-modal.show .modal-dialog {
        transform: none;
    }

    .sc-modal .modal-content {
        border: 0;
        border-radius: 20px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 30px 70px rgba(15, 23, 42, .35);
    }

    .sc-mhead {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 20px 22px;
        background: var(--t-bg);
    }

    .sc-mic {
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--t);
        color: #fff;
        font-size: 20px;
        box-shadow: 0 0 0 6px rgba(255, 255, 255, .6);
    }

    .sc-modal.show .sc-mic {
        animation: sc-pop .5s cubic-bezier(.2, 1.4, .4, 1);
    }

    @keyframes sc-pop {
        0% {
            transform: scale(.5);
            opacity: 0;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .sc-mtitle {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        letter-spacing: -.02em;
        color: var(--t);
    }

    .sc-msub {
        margin: 2px 0 0;
        font-size: 13px;
        color: var(--muted);
    }

    .sc-x {
        margin-left: auto;
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        border: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, .7);
        color: var(--muted);
        font-size: 14px;
        cursor: pointer;
        transition: background-color .15s ease, color .15s ease;
    }

    .sc-x:hover {
        background: #fff;
        color: var(--ink);
    }

    .sc-x:focus-visible {
        outline: 3px solid rgba(31, 75, 182, .35);
    }

    .sc-mbody {
        padding: 20px 22px 8px;
    }

    .sc-mfoot {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        padding: 14px 22px 22px;
        border: 0;
    }

    .sc-mfoot .sc-btn {
        flex: 1 1 0;
        margin: 0;
    }

    .sc-mfoot .sc-btn.grow {
        flex: 2 1 0;
    }

    /* student summary inside the modal */
    .sc-who {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }

    .sc-ini {
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: var(--brand-bg);
        color: var(--brand);
        font-size: 16px;
        font-weight: 700;
    }

    .sc-who strong {
        display: block;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    .sc-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px 8px;
        margin-top: 4px;
    }

    .sc-prog {
        margin-bottom: 10px;
        padding: 10px 14px;
        border-radius: 12px;
        border: 1px solid var(--line);
    }

    .sc-prog b {
        display: block;
        margin-top: 2px;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    .sc-who span.id {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 6px;
        background: var(--soft);
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
    }

    .sc-seat {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 10px;
        margin-bottom: 14px;
    }

    .sc-seat>div {
        padding: 12px 14px;
        border-radius: 12px;
        background: var(--soft);
        min-width: 0;
    }

    .sc-seat .main {
        background: var(--ink);
        color: #fff;
    }

    .sc-seat .main .sc-k {
        color: rgba(255, 255, 255, .62);
    }

    .sc-seatno {
        display: block;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: clamp(28px, 7vw, 36px);
        font-weight: 700;
        letter-spacing: -.03em;
        line-height: 1.15;
        overflow-wrap: anywhere;
    }

    .sc-seat b {
        display: block;
        margin-top: 4px;
        font-size: 16px;
        font-weight: 700;
    }

    .sc-k {
        display: block;
        font-size: 11.5px;
        font-weight: 600;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .sc-dl {
        margin: 6px 0 0;
    }

    .sc-dl>div {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 10px 0;
        border-top: 1px solid var(--line);
        font-size: 14px;
    }

    .sc-dl dt {
        flex: 0 0 auto;
        font-weight: 500;
        color: var(--muted);
    }

    .sc-dl dd {
        margin: 0;
        font-weight: 600;
        text-align: right;
        overflow-wrap: anywhere;
    }

    .sc-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .sc-tag.ok {
        background: var(--ok-bg);
        color: var(--ok);
    }

    .sc-tag.warn {
        background: var(--warn-bg);
        color: var(--warn);
    }

    .sc-cloth {
        padding: 12px 0 6px;
        border-top: 1px solid var(--line);
    }

    .sc-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
    }

    .sc-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 11px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
    }

    .sc-chip.ok {
        background: var(--ok-bg);
        color: var(--ok);
    }

    .sc-chip.bad {
        background: var(--bad-bg);
        color: var(--bad);
    }

    .sc-note {
        display: flex;
        align-items: center;
        gap: 7px;
        margin: 10px 0 0;
        font-size: 13px;
        font-weight: 600;
    }

    .sc-note.ok {
        color: var(--ok);
    }

    .sc-note.bad {
        color: var(--bad);
    }

    .sc-note.muted {
        margin-top: 8px;
        font-weight: 500;
        color: var(--muted);
    }

    .sc-skel {
        height: 30px;
        margin-top: 8px;
        border-radius: 8px;
        background: linear-gradient(90deg, var(--soft), #e6ebf5, var(--soft));
        background-size: 200% 100%;
        animation: sc-skel 1.2s linear infinite;
    }

    @keyframes sc-skel {
        to {
            background-position: -200% 0;
        }
    }

    .sc-inline-err {
        margin: 6px 0 10px;
        padding: 10px 12px;
        border-radius: 10px;
        background: var(--bad-bg);
        color: var(--bad);
        font-size: 13px;
        font-weight: 600;
    }

    .sc-inline-err[hidden] {
        display: none;
    }

    /* error modal body */
    .sc-errtext {
        margin: 0 0 4px;
        font-size: 15px;
        line-height: 1.5;
        color: var(--ink);
    }

    .sc-errid {
        display: inline-block;
        margin: 8px 0 12px;
        padding: 4px 10px;
        border-radius: 8px;
        background: var(--soft);
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 14px;
        font-weight: 600;
        color: var(--ink);
    }

    /* toast */
    .sc-toast {
        position: fixed;
        left: 50%;
        bottom: 24px;
        z-index: 2000;
        display: flex;
        align-items: center;
        gap: 10px;
        max-width: 92vw;
        padding: 12px 18px;
        border-radius: 12px;
        background: #1b7f4b;
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 14px 34px rgba(0, 0, 0, .25);
        transform: translate(-50%, 20px);
        opacity: 0;
        visibility: hidden;
        transition: opacity .25s ease, transform .25s ease, visibility .25s;
    }

    .sc-toast.show {
        opacity: 1;
        visibility: visible;
        transform: translate(-50%, 0);
    }

    /* responsive */
    @media (max-width: 991px) {

        .sc-stats .sc-ses {
            flex-basis: 100%;
        }

        .sc-stats .sc-ses.is-hidden {
            flex-basis: auto;
        }
    }

    @media (max-width: 575px) {
        .sc-field {
            flex-direction: column;
        }

        .sc-input {
            height: 56px;
            font-size: 22px;
        }

        .sc-go {
            width: 100%;
            height: 52px;
        }

        .sc-head .sc-btn {
            width: 100%;
        }

        .sc-seat {
            grid-template-columns: 1fr;
        }

        .sc-mfoot {
            flex-direction: column-reverse;
        }
    }

    @media (prefers-reduced-motion: reduce) {

        .sc-modal.fade .modal-dialog,
        .sc-modal.show .sc-mic,
        .sc-live::before,
        .sc-skel,
        .sc-bar i,
        .sc-toast {
            animation: none;
            transition: none;
        }
    }
</style>
<style>
    /* cloak issuing: item cards inside the modal */
    .cc-items-h {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin: 4px 0 10px;
        padding-top: 12px;
        border-top: 1px solid var(--line);
    }

    .cc-count {
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--muted);
    }

    .cc-items {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(128px, 1fr));
        gap: 10px;
    }

    .cc-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        padding: 14px 10px 12px;
        border: 1px solid var(--line);
        border-radius: 14px;
        background: #fff;
        text-align: center;
        transition: border-color .2s, background .2s;
    }

    .cc-item.is-issued {
        border-color: #b9dfc9;
        background: var(--ok-bg);
    }

    .cc-ic {
        display: grid;
        place-items: center;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--brand-bg, #e8eefb);
        color: var(--brand, #1f4bb6);
        font-size: 18px;
    }

    .cc-item.is-issued .cc-ic {
        background: #fff;
        color: var(--ok);
    }

    .cc-name {
        display: block;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.2;
    }

    .cc-state {
        display: block;
        margin-top: 2px;
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
    }

    .cc-item.is-issued .cc-state {
        color: var(--ok);
    }

    .cc-item .sc-btn {
        width: 100%;
        justify-content: center;
        padding: 8px 10px;
        font-size: 13px;
    }

    .cc-done {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        padding: 8px 10px;
        border-radius: 10px;
        background: var(--ok);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
    }

    .cc-session {
        margin-bottom: 10px;
        padding: 10px 14px;
        border-radius: 12px;
        border: 1px solid var(--line);
    }

    /* student hero: avatar, big ID + Session chips (same size), programme */
    .cc-hero {
        --acc: #1f4bb6;
        --acc-bg: #eef2fb;
        margin-bottom: 14px;
        padding: 16px 16px 14px;
        border: 1px solid var(--line);
        border-top: 4px solid var(--acc);
        border-radius: 16px;
        background: linear-gradient(180deg, var(--acc-bg), #fff 78%);
    }

    .cc-hero.s1 {
        --acc: #d6336c;
        --acc-bg: #fdeaf2;
    }

    .cc-hero.s2 {
        --acc: #7048c9;
        --acc-bg: #f0ebfc;
    }

    .cc-hero.s3 {
        --acc: #1b9a5a;
        --acc-bg: #e5f6ec;
    }

    .cc-hero.s0 {
        --acc: #8a95a8;
        --acc-bg: #f4f6fb;
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
        background: var(--acc);
        color: #fff;
        text-transform: uppercase;
    }

    .cc-chip.ses::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #fff;
        opacity: .9;
    }

    .cc-hprog {
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px dashed rgba(23, 35, 61, .15);
    }

    .cc-hprog b {
        display: block;
        margin-top: 2px;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    .cc-prog {
        margin: 0 0 12px;
    }

    .cc-item {
        box-shadow: 0 1px 3px rgba(23, 35, 61, .06);
    }

    .cc-ic {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        font-size: 20px;
    }

    /* page shown at 90% (set --cc-zoom to 1 for 100%, 0.8 for 80% ...) */
    :root {
        --cc-zoom: 0.9;
    }

    .sc,
    .sc-modal .modal-dialog,
    .sc-toast {
        zoom: var(--cc-zoom);
    }

    .cc-session b {
        display: block;
        margin-top: 2px;
        font-size: 15px;
        font-weight: 700;
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
                        <div>
                            <h1>Cloak issuing</h1>
                            <p>Scan the student's QR code to see which clothing items to hand over.</p>
                        </div>
                    </header>

                    <!-- scan box -->
                    <section class="sc-card sc-scan">
                        <span class="sc-qr" aria-hidden="true"><i class="fas fa-qrcode"></i></span>
                        <h2>Scan QR for cloak issuing</h2>
                        <p>Click the box and scan the code, or type the student ID and press Enter.</p>
                        <form id="studentIDForm" autocomplete="off">
                            <div class="sc-field">
                                <input type="text" class="sc-input" id="studentID" name="studentID"
                                    placeholder="Enter or scan student ID" aria-label="Student ID" required>
                                <button type="submit" id="goBtn" class="sc-btn solid sc-go">
                                    <i class="fas fa-search" aria-hidden="true"></i> <span>Check student</span>
                                </button>
                            </div>
                        </form>
                    </section>

                </div> <!-- /sc -->
            </div>
        </div> <!-- content -->
    </div> <!-- content-wrapper -->
</div> <!-- wrapper -->

<!-- ✅ Student found + items -->
<div class="modal fade sc-modal ok" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="sc-mhead">
                <span class="sc-mic" aria-hidden="true"><i class="fas fa-check"></i></span>
                <div>
                    <h5 class="sc-mtitle" id="successModalLabel">Student found</h5>
                    <p class="sc-msub" id="successSub">Verified successfully</p>
                </div>
                <button type="button" class="sc-x" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body sc-mbody">
                <div id="studentInfo"></div>
                <div class="sc-inline-err" id="markErr" role="alert" hidden></div>
                <div id="itemsList"></div>
            </div>
            <div class="modal-footer sc-mfoot">
                <button type="button" class="sc-btn grow" data-bs-dismiss="modal">Done</button>
            </div>
        </div>
    </div>
</div>

<!-- ⚠ Warning (payment not completed) -->
<div class="modal fade sc-modal warn" id="warnModal" tabindex="-1" aria-labelledby="warnTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="sc-mhead">
                <span class="sc-mic" aria-hidden="true"><i class="fas fa-exclamation"></i></span>
                <div>
                    <h5 class="sc-mtitle" id="warnTitle">Payment not completed</h5>
                    <p class="sc-msub">Nothing was issued</p>
                </div>
                <button type="button" class="sc-x" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body sc-mbody">
                <p class="sc-errtext" id="warnText"></p>
                <span class="sc-errid" id="warnId" style="display:none;"></span>
            </div>
            <div class="modal-footer sc-mfoot">
                <button type="button" class="sc-btn" data-bs-dismiss="modal">Scan next student</button>
            </div>
        </div>
    </div>
</div>

<!-- ❌ Error (not registered / not found / server error) -->
<div class="modal fade sc-modal bad" id="errorModal" tabindex="-1" aria-labelledby="errorTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="sc-mhead">
                <span class="sc-mic" aria-hidden="true"><i class="fas fa-times"></i></span>
                <div>
                    <h5 class="sc-mtitle" id="errorTitle">Student not found</h5>
                    <p class="sc-msub">Nothing was changed</p>
                </div>
                <button type="button" class="sc-x" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body sc-mbody">
                <p class="sc-errtext" id="errorText">Please check the ID and try again.</p>
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
        let scanSeq = 0; // answers for an older scan are ignored
        let currentXhr = null;
        let shownStudent = ''; // student currently open in the modal

        // ---------- helpers ----------
        function esc(v) {
            return $('<div>').text(v ?? '').html();
        }

        function initials(name) {
            const w = String(name ?? '').trim().split(/\s+/).filter(Boolean);
            if (!w.length) return '?';
            return (w[0][0] + (w.length > 1 ? w[w.length - 1][0] : '')).toUpperCase();
        }

        function sessionLabel(v) {
            v = String(v ?? '').trim();
            if (!v || v.toLowerCase() === 'none') return '—';
            return v.replace(/_/g, ' ').toLowerCase().replace(/\b\w/g, function(c) {
                return c.toUpperCase();
            });
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

        // ---------- render ----------
        function sessionClass(v) {
            const m = String(v ?? '').match(/(\d+)\s*$/);
            const n = m ? parseInt(m[1], 10) : 0;
            return (n >= 1 && n <= 3) ? 's' + n : 's0';
        }

        function studentCard(d) {
            const ses = sessionLabel(d.session);
            const hasSes = ses !== '—';
            return `
                <div class="cc-hero ${hasSes ? sessionClass(d.session) : 's0'}">
                    <div class="cc-hero-top">
                        <span class="cc-av" aria-hidden="true">${esc(initials(d.student_name))}</span>
                        <div style="min-width:0">
                            <strong class="cc-hname">${esc(d.student_name)}</strong>
                            <div class="cc-chips">
                                <span class="cc-chip id" title="Student ID">${esc(d.student_id)}</span>
                                <span class="cc-chip ses" title="Session">${hasSes ? esc(ses) : 'No session'}</span>
                            </div>
                        </div>
                    </div>
                    <div class="cc-hprog">
                        <span class="sc-k">Programme</span>
                        <b>${esc(d.program)}</b>
                    </div>
                </div>`;
        }

        function itemsHtml(items, studentId) {
            const keys = ['cloak', 'slashes', 'hats'].filter(function(k) {
                return items && items[k] && items[k].assigned;
            });

            if (!keys.length) {
                return '<p class="sc-note muted"><i class="fas fa-info-circle" aria-hidden="true"></i> No clothing items are assigned to this student\'s programme.</p>';
            }

            const issued = keys.filter(function(k) {
                return items[k].collected;
            }).length;

            const pct = Math.round(issued / keys.length * 100);
            let html = '<div class="cc-items-h"><span class="sc-k" style="margin:0">Clothing items</span>' +
                '<span class="cc-count">' + issued + ' / ' + keys.length + ' issued</span></div>' +
                '<div class="sc-bar cc-prog" role="progressbar" aria-label="Items issued" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' + pct + '"><i style="width:' + pct + '%"></i></div>';
            html += '<div class="cc-items">';

            keys.forEach(function(k) {
                const meta = ITEMS[k];
                const done = !!items[k].collected;
                html += `
                    <div class="cc-item ${done ? 'is-issued' : ''}">
                        <span class="cc-ic" aria-hidden="true"><i class="fas ${meta.icon}"></i></span>
                        <div>
                            <span class="cc-name">${meta.name}</span>
                            <span class="cc-state">${done ? 'Issued' : 'Pending issue'}</span>
                        </div>
                        ${done
                            ? '<span class="cc-done"><i class="fas fa-check-circle" aria-hidden="true"></i> Issued</span>'
                            : `<button type="button" class="sc-btn ok markCollectBtn" data-item="${k}" data-student="${esc(studentId)}"><i class="fas fa-check" aria-hidden="true"></i> <span>Mark as issued</span></button>`}
                    </div>`;
            });

            html += '</div>';

            if (issued === keys.length) {
                html += '<p class="sc-note ok"><i class="fas fa-check-circle" aria-hidden="true"></i> All items issued</p>';
            } else {
                html += '<p class="sc-note bad"><i class="fas fa-exclamation-triangle" aria-hidden="true"></i> ' + (keys.length - issued) + ' item' + (keys.length - issued === 1 ? '' : 's') + ' still to issue</p>';
            }
            return html;
        }

        function renderStudent(res) {
            shownStudent = res.student_id;
            $('#studentInfo').html(studentCard(res));
            $('#itemsList').html(itemsHtml(res.items, res.student_id));
        }

        // ---------- problem modals ----------
        function showProblem(kind, title, text, id) {
            const p = kind === 'warn' ? 'warn' : 'error';
            $('#' + p + 'Title').text(title);
            $('#' + p + 'Text').text(text);
            $('#' + p + 'Id').text(id || '').toggle(!!id);
            $('#' + p + 'Modal').modal('show');
        }

        // ---------- look up ----------
        function lookup(studentID) {
            const mySeq = ++scanSeq;
            setBusy(true);

            currentXhr = $.ajax({
                url: 'checkStudentCloth.php',
                type: 'POST',
                dataType: 'json',
                cache: false,
                data: {
                    studentID: studentID
                },
                success: function(res) {
                    if (mySeq !== scanSeq) return;

                    if (res.status === 'found') {
                        $('#markErr').prop('hidden', true).text('');
                        renderStudent(res);
                        $('#successModal').modal('show');
                    } else if (res.status === 'not_registered') {
                        showProblem('bad', 'Student not registered', 'Please check the registered students table.', studentID);
                    } else if (res.status === 'not_paid') {
                        showProblem('warn', 'Payment not completed', 'This student is not in the payment records. Complete the payment before issuing clothing.', studentID);
                    } else {
                        showProblem('bad', 'Student not found', res.message || 'Please check the ID and try again.', studentID);
                    }
                },
                error: function(xhr, status) {
                    if (status === 'abort') return;
                    showProblem('bad', 'Server error', 'Could not reach the server. Please try again.');
                },
                complete: function() {
                    if (mySeq === scanSeq) setBusy(false);
                }
            });
        }

        $('#studentIDForm').on('submit', function(e) {
            e.preventDefault();
            const id = $studentID.val().trim();
            if (!id || busy) return;
            lookup(id);
        });

        // ---------- mark one item as issued ----------
        $(document).on('click', '.markCollectBtn', function() {
            const $btn = $(this);
            if ($btn.prop('disabled')) return;
            const studentID = String($btn.data('student'));
            const item = $btn.data('item');
            const label = $btn.html();
            const mySeq = scanSeq;

            $('#markErr').prop('hidden', true).text('');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> <span>Saving…</span>');

            function fail(msg) {
                $('#markErr').text(msg || 'Failed to mark as issued.').prop('hidden', false);
                $btn.prop('disabled', false).html(label);
            }

            $.ajax({
                url: 'markClothAttended.php',
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
                    toast(ITEMS[item].name + ' issued');
                    // pull the latest status so the modal shows what is really stored
                    $.ajax({
                        url: 'checkStudentCloth.php',
                        type: 'POST',
                        dataType: 'json',
                        cache: false,
                        data: {
                            studentID: studentID
                        },
                        success: function(r) {
                            if (mySeq !== scanSeq) return;
                            if (r.status === 'found') renderStudent(r);
                            else fail('Saved, but could not refresh the list.');
                        },
                        error: function() {
                            fail('Saved, but could not refresh the list.');
                        }
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
            shownStudent = '';
            $('#studentInfo, #itemsList').empty();
            $('#markErr').prop('hidden', true).text('');
            $('#studentIDForm')[0].reset();
            setBusy(false);
            $studentID.focus();
        }

        $('#successModal, #warnModal, #errorModal').on('hidden.bs.modal', resetScreen);

        $studentID.focus();
    });
</script>

</body>

</html>