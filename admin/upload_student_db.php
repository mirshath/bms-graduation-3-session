<?php
session_start();

// Only a logged-in admin may use this page
if (!isset($_SESSION['admin_id'])) {
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

/* =========================================================
   Helpers
   ========================================================= */
function up_h($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

/** Normalise a header cell so "Student ID", "student_id", "STUDENT-ID" all match. */
function up_norm_header($v): string
{
    return preg_replace('/[^a-z0-9]/', '', strtolower(trim((string)$v)));
}

/** Header aliases -> database column */
function up_header_map(): array
{
    return [
        'student_id'     => ['studentid', 'studentno', 'studentnumber', 'stuid', 'sid'],
        'name'           => ['name', 'studentname', 'fullname', 'nameinfull'],
        'dob'            => ['dob', 'dateofbirth', 'birthdate', 'birthday'],
        'given_email'    => ['givenemail', 'email', 'emailaddress', 'givenemailadd', 'givenemailaddress'],
        'mobile_no'      => ['mobileno', 'mobile', 'mobilenumber', 'phone', 'phoneno', 'contactno', 'telephone'],
        'payment_status' => ['paymentstatus', 'coursefeestatus', 'coursefeepaymentstatus', 'payment'],
        'status'         => ['status', 'registrationstatus'],
        'in_no'          => ['inno', 'invitationno', 'invitationnumber', 'in'],
        'active'         => ['active'],
    ];
}

/** Read a CSV file into an array of rows. */
function up_read_csv(string $path): array
{
    $raw = file_get_contents($path);
    if ($raw === false) {
        throw new Exception("Unable to read the uploaded file.");
    }
    // strip UTF-8 BOM, convert legacy encodings
    $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw);
    if (!mb_check_encoding($raw, 'UTF-8')) {
        $raw = mb_convert_encoding($raw, 'UTF-8', 'Windows-1252');
    }

    // detect delimiter from the first line
    $firstLine = strtok($raw, "\n");
    $delims = [',' => substr_count($firstLine, ','), ';' => substr_count($firstLine, ';'), "\t" => substr_count($firstLine, "\t")];
    arsort($delims);
    $delimiter = key($delims);

    $h = fopen('php://memory', 'r+');
    fwrite($h, $raw);
    rewind($h);

    $rows = [];
    while (($r = fgetcsv($h, 0, $delimiter, '"', '')) !== false) {
        $rows[] = $r;
    }
    fclose($h);
    return $rows;
}

/** Read the first sheet of an .xlsx file (no external library needed; uses ZipArchive). */
function up_read_xlsx(string $path): array
{
    if (!class_exists('ZipArchive')) {
        throw new Exception("The PHP 'zip' extension is not enabled, so .xlsx cannot be read. Enable extension=zip in php.ini or upload a CSV instead.");
    }
    $zip = new ZipArchive();
    if ($zip->open($path) !== true) {
        throw new Exception("The Excel file could not be opened. Make sure it is a valid .xlsx file.");
    }

    // shared strings
    $shared = [];
    $ssXml = $zip->getFromName('xl/sharedStrings.xml');
    if ($ssXml !== false) {
        $ss = @simplexml_load_string($ssXml);
        if ($ss) {
            foreach ($ss->xpath("//*[local-name()='si']") as $si) {
                $txt = '';
                foreach ($si->xpath(".//*[local-name()='t']") as $t) {
                    $txt .= (string)$t;
                }
                $shared[] = $txt;
            }
        }
    }

    // locate the first worksheet
    $sheetPath = 'xl/worksheets/sheet1.xml';
    $wb = @simplexml_load_string((string)$zip->getFromName('xl/workbook.xml'));
    $rels = @simplexml_load_string((string)$zip->getFromName('xl/_rels/workbook.xml.rels'));
    if ($wb && $rels) {
        $sheets = $wb->xpath("//*[local-name()='sheet']");
        if ($sheets) {
            $rid = (string)$sheets[0]->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
            foreach ($rels->xpath("//*[local-name()='Relationship']") as $rel) {
                if ((string)$rel['Id'] === $rid) {
                    $target = ltrim((string)$rel['Target'], '/');
                    $sheetPath = (strpos($target, 'xl/') === 0) ? $target : 'xl/' . $target;
                    break;
                }
            }
        }
    }

    $sheetXml = $zip->getFromName($sheetPath);
    $zip->close();
    if ($sheetXml === false) {
        throw new Exception("The first worksheet could not be found in the Excel file.");
    }
    $sheet = @simplexml_load_string($sheetXml);
    if (!$sheet) {
        throw new Exception("The worksheet could not be read.");
    }

    $rows = [];
    foreach ($sheet->xpath("//*[local-name()='row']") as $row) {
        $line = [];
        foreach ($row->xpath("./*[local-name()='c']") as $c) {
            // column index from the cell reference (A1, B1 ... AA1)
            preg_match('/^([A-Z]+)/', (string)$c['r'], $m);
            $col = 0;
            foreach (str_split($m[1] ?? 'A') as $ch) {
                $col = $col * 26 + (ord($ch) - 64);
            }
            $col--;

            $type = (string)$c['t'];
            $val = '';
            if ($type === 's') {
                $val = $shared[(int)$c->v] ?? '';
            } elseif ($type === 'inlineStr') {
                foreach ($c->xpath(".//*[local-name()='t']") as $t) {
                    $val .= (string)$t;
                }
            } else {
                $val = (string)$c->v;
            }
            $line[$col] = $val;
        }
        if ($line) {
            $max = max(array_keys($line));
            $full = [];
            for ($i = 0; $i <= $max; $i++) {
                $full[$i] = $line[$i] ?? '';
            }
            $rows[] = $full;
        } else {
            $rows[] = [];
        }
    }
    return $rows;
}

/** Convert many date styles (and Excel serial numbers) to Y-m-d, or null if invalid. */
function up_parse_date(string $v): ?string
{
    $v = trim($v);
    if ($v === '') return null;

    // Excel serial number (e.g. 36178)
    if (preg_match('/^\d+(\.\d+)?$/', $v) && (float)$v > 1 && (float)$v < 100000) {
        $d = new DateTime('1899-12-30');
        $d->modify('+' . (int)floor((float)$v) . ' days');
        return $d->format('Y-m-d');
    }

    $formats = ['Y-m-d', 'Y/m/d', 'd/m/Y', 'd-m-Y', 'd.m.Y', 'Ymd', 'd/m/y', 'd-M-Y', 'd M Y', 'Y-m-d H:i:s', 'd/m/Y H:i:s'];
    foreach ($formats as $f) {
        $d = DateTime::createFromFormat('!' . $f, $v);
        $err = DateTime::getLastErrors();
        if ($d && (!$err || ($err['warning_count'] == 0 && $err['error_count'] == 0))) {
            $year = (int)$d->format('Y');
            if ($year < 1900 || $year > (int)date('Y')) continue;
            return $d->format('Y-m-d');
        }
    }
    return null;
}

/** Stop spreadsheet formula injection in the exported CSV */
function up_csv_safe($v): string
{
    $v = (string)$v;
    return ($v !== '' && strpos('=+-@', $v[0]) !== false) ? "'" . $v : $v;
}

/* =========================================================
   Programs + sessions (from data_tables)
   ========================================================= */
$programs = [];   // programName => session
$loadError = false;
try {
    $res = mysqli_query($conn, "SELECT programName, session FROM data_tables WHERE programName IS NOT NULL AND programName <> '' ORDER BY programName ASC");
    if (!$res) throw new Exception(mysqli_error($conn));
    while ($r = mysqli_fetch_assoc($res)) {
        $programs[$r['programName']] = $r['session'] ?? '';
    }
} catch (Exception $e) {
    $loadError = true;
    error_log("Upload Student DB - load programs: " . $e->getMessage());
}

/* =========================================================
   Downloads: template + skipped rows
   ========================================================= */
if (isset($_GET['download'])) {
    $which = $_GET['download'];

    if ($which === 'template') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="old_students_template.csv"');
        echo "\xEF\xBB\xBF";
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Student ID', 'Name', 'DOB', 'Email', 'Mobile No', 'Payment Status', 'Status', 'IN No', 'Active'], ',', '"', '');
        fputcsv($out, ['123456', 'Example Student Name', '1999-01-19', 'student@example.com', '0771234567', 'paid', 'registered', '1', 'completed'], ',', '"', '');
        fclose($out);
        exit();
    }

    if ($which === 'skipped') {
        $data = $_SESSION['up_skipped'] ?? null;
        if (!$data || empty($data['rows'])) {
            header("Location: upload_student_db.php");
            exit();
        }
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="skipped_students_' . date('Ymd_His') . '.csv"');
        echo "\xEF\xBB\xBF";
        $out = fopen('php://output', 'w');
        fputcsv($out, ['File Row', 'Student ID', 'Name', 'DOB', 'Email', 'Mobile No', 'Payment Status', 'Status', 'IN No', 'Active', 'Program', 'Reason'], ',', '"', '');
        foreach ($data['rows'] as $s) {
            fputcsv($out, array_map('up_csv_safe', [
                $s['row'],
                $s['student_id'],
                $s['name'],
                $s['dob'],
                $s['email'],
                $s['mobile'],
                $s['payment_status'],
                $s['status'],
                $s['in_no'],
                $s['active'],
                $data['program'],
                $s['reason']
            ]), ',', '"', '');
        }
        fclose($out);
        exit();
    }
}

/* =========================================================
   Upload handling  (POST -> process -> redirect)
   ========================================================= */
if (empty($_SESSION['up_csrf'])) {
    $_SESSION['up_csrf'] = bin2hex(random_bytes(16));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fail = function (string $msg) {
        $_SESSION['up_result'] = ['error' => $msg];
        header("Location: upload_student_db.php");
        exit();
    };

    if (!hash_equals($_SESSION['up_csrf'], (string)($_POST['csrf'] ?? ''))) {
        $fail("Security token expired. Please try again.");
    }

    $program = trim((string)($_POST['program'] ?? ''));
    if ($program === '' || !array_key_exists($program, $programs)) {
        $fail("Please select a valid program.");
    }

    if (!isset($_FILES['student_file']) || $_FILES['student_file']['error'] !== UPLOAD_ERR_OK) {
        $fail("Please choose a CSV or Excel (.xlsx) file to upload.");
    }
    $file = $_FILES['student_file'];
    if ($file['size'] > 5 * 1024 * 1024) {
        $fail("File is too large (maximum 5 MB).");
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['csv', 'xlsx'], true)) {
        $fail("Only .csv or .xlsx files are allowed. (For an old .xls file, save it as .xlsx or .csv first.)");
    }

    try {
        $rows = ($ext === 'xlsx') ? up_read_xlsx($file['tmp_name']) : up_read_csv($file['tmp_name']);
    } catch (Exception $e) {
        $fail($e->getMessage());
    }

    // find the header row (first non-empty row)
    $headerIdx = null;
    foreach ($rows as $i => $r) {
        if (array_filter($r, fn($c) => trim((string)$c) !== '')) {
            $headerIdx = $i;
            break;
        }
    }
    if ($headerIdx === null) {
        $fail("The file is empty.");
    }

    // map header names -> column positions
    $colIndex = [];
    $aliases = up_header_map();
    foreach ($rows[$headerIdx] as $pos => $title) {
        $n = up_norm_header($title);
        foreach ($aliases as $field => $list) {
            if (in_array($n, $list, true) && !isset($colIndex[$field])) {
                $colIndex[$field] = $pos;
            }
        }
    }
    $missingCols = [];
    foreach (['student_id' => 'Student ID', 'name' => 'Name', 'dob' => 'DOB'] as $f => $label) {
        if (!isset($colIndex[$f])) $missingCols[] = $label;
    }
    if ($missingCols) {
        $fail("Required column(s) not found in the first row: " . implode(', ', $missingCols) . ". Download the template to see the expected headers.");
    }

    $dataRows = array_slice($rows, $headerIdx + 1, null, true);
    if (count($dataRows) > 10000) {
        $fail("Too many rows (maximum 10,000 per upload).");
    }

    // existing student IDs (case-insensitive, same as the DB collation)
    $existing = [];
    try {
        $res = mysqli_query($conn, "SELECT student_id FROM old_student_db");
        while ($r = mysqli_fetch_assoc($res)) {
            $existing[mb_strtolower(trim($r['student_id']))] = true;
        }
    } catch (Exception $e) {
        error_log("Upload Student DB - load existing: " . $e->getMessage());
        $fail("Could not check existing students. Nothing was uploaded.");
    }

    $get = function (array $r, string $field) use ($colIndex): string {
        return isset($colIndex[$field]) ? trim((string)($r[$colIndex[$field]] ?? '')) : '';
    };

    $inserted = 0;
    $skipped = [];
    $seenInFile = [];
    $totalRows = 0;
    $dupCount = 0;
    $invalidCount = 0;

    try {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO old_student_db (student_id, name, DOB, given_email, program, mobile_no, payment_status, status, in_no, active)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        mysqli_begin_transaction($conn);

        foreach ($dataRows as $idx => $r) {
            if (!array_filter($r, fn($c) => trim((string)$c) !== '')) continue; // blank line
            $totalRows++;
            $fileRow = $idx + 1;

            $sid     = $get($r, 'student_id');
            $name    = $get($r, 'name');
            $dobRaw  = $get($r, 'dob');
            $email   = $get($r, 'given_email');
            $mobRaw  = $get($r, 'mobile_no');
            $pay     = $get($r, 'payment_status');
            $status  = $get($r, 'status');
            $inRaw   = $get($r, 'in_no');
            $active  = $get($r, 'active');

            $rec = [
                'row' => $fileRow,
                'student_id' => $sid,
                'name' => $name,
                'dob' => $dobRaw,
                'email' => $email,
                'mobile' => $mobRaw,
                'payment_status' => $pay,
                'status' => $status,
                'in_no' => $inRaw,
                'active' => $active,
                'reason' => ''
            ];

            // ---- validation
            $reason = '';
            if ($sid === '' || mb_strlen($sid) > 255) {
                $reason = 'Student ID is empty or too long';
            } elseif ($name === '' || mb_strlen($name) > 255) {
                $reason = 'Name is empty or too long';
            }

            $dob = null;
            if ($reason === '') {
                $dob = up_parse_date($dobRaw);
                if ($dob === null) $reason = 'Invalid or missing DOB';
            }

            if ($reason === '' && $email !== '' && (mb_strlen($email) > 50 || !filter_var($email, FILTER_VALIDATE_EMAIL))) {
                $reason = 'Invalid email (or longer than 50 characters)';
            }

            // $mobile = null;
            // if ($reason === '' && $mobRaw !== '') {
            //     $digits = preg_replace('/\D/', '', $mobRaw);
            //     if (strlen($digits) === 11 && strpos($digits, '94') === 0) $digits = substr($digits, 2);
            //     if (strlen($digits) === 10 && $digits[0] === '0') $digits = substr($digits, 1);
            //     if ($digits === '' || strlen($digits) > 10 || (int)$digits > 2147483647) {
            //         $reason = 'Invalid mobile number';
            //     } else {
            //         $mobile = (int)$digits;
            //     }
            // }
            $mobile = null;

            if ($reason === '' && $mobRaw !== '') {

                // Keep digits only
                $digits = preg_replace('/\D/', '', $mobRaw);

                // Validate that something remains
                if ($digits === '') {
                    $reason = 'Invalid mobile number';
                }
                // Allow international numbers (including UAE/Dubai)
                elseif (strlen($digits) < 7 || strlen($digits) > 15) {
                    $reason = 'Invalid mobile number';
                } else {
                    $mobile = $digits;
                }
            }

            // IN No is free text (VARCHAR 255) - accept any value, like an Excel cell
            $inNo = null;
            if ($reason === '' && $inRaw !== '') {
                if (mb_strlen($inRaw) > 255) {
                    $reason = 'IN No is longer than 255 characters';
                } else {
                    $inNo = $inRaw;
                }
            }

            if ($reason === '' && (mb_strlen($pay) > 50 || mb_strlen($status) > 50)) {
                $reason = 'Payment status / status is too long';
            }

            // ---- duplicate checks
            $key = mb_strtolower($sid);
            if ($reason === '') {
                if (isset($existing[$key])) {
                    $reason = 'Duplicate: Student ID already exists in old_student_db';
                    $dupCount++;
                } elseif (isset($seenInFile[$key])) {
                    $reason = 'Duplicate: Student ID repeated in the file (first seen on row ' . $seenInFile[$key] . ')';
                    $dupCount++;
                }
            } else {
                $invalidCount++;
            }

            if ($reason !== '') {
                $rec['reason'] = $reason;
                $skipped[] = $rec;
                continue;
            }

            // ---- insert
            $payV    = $pay === '' ? null : strtolower($pay);
            $statusV = $status === '' ? null : strtolower($status);
            $activeV = $active === '' ? null : strtolower($active);
            $emailV  = $email === '' ? null : $email;

            try {
                mysqli_stmt_bind_param($stmt, "sssssissss", $sid, $name, $dob, $emailV, $program, $mobile, $payV, $statusV, $inNo, $activeV);
                mysqli_stmt_execute($stmt);
                $inserted++;
                $existing[$key] = true;
                $seenInFile[$key] = $fileRow;
            } catch (mysqli_sql_exception $e) {
                if ((int)$e->getCode() === 1062) {
                    $rec['reason'] = 'Duplicate: Student ID already exists in old_student_db';
                    $dupCount++;
                } else {
                    error_log("Upload Student DB - insert row $fileRow: " . $e->getMessage());
                    $rec['reason'] = 'Database error while saving this row';
                    $invalidCount++;
                }
                $skipped[] = $rec;
            }
        }

        mysqli_commit($conn);
        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        error_log("Upload Student DB - fatal: " . $e->getMessage());
        $fail("Upload failed and was rolled back. No students were added.");
    }

    $_SESSION['up_skipped'] = ['program' => $program, 'rows' => $skipped];
    $_SESSION['up_result'] = [
        'program'  => $program,
        'session'  => $programs[$program],
        'file'     => $file['name'],
        'total'    => $totalRows,
        'inserted' => $inserted,
        'dups'     => $dupCount,
        'invalid'  => $invalidCount,
    ];
    header("Location: upload_student_db.php");
    exit();
}

// one-time result message
$result = $_SESSION['up_result'] ?? null;
unset($_SESSION['up_result']);
$skippedRows = ($result && empty($result['error'])) ? ($_SESSION['up_skipped']['rows'] ?? []) : [];

include("includes/header.php");
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

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

    .up-menu {
        display: none;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
        padding: 9px 16px;
        border: 1px solid #c4ccd9;
        border-radius: 12px;
        background: #fff;
        color: #17233d;
        font: 600 14px 'Public Sans', system-ui, sans-serif;
        cursor: pointer;
    }

    @media (max-width: 991.98px) {
        .up-menu {
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

    .up {
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
        --bad-bg: #fdeceb;
        font-family: 'Public Sans', system-ui, sans-serif;
        color: var(--ink);
        max-width: 1100px;
        margin-inline: auto;
        padding: 8px 0 36px;
        line-height: 1.5;
    }

    .up *,
    .up *::before,
    .up *::after {
        box-sizing: border-box;
    }

    .up h1 {
        margin: 0;
        font-size: 26px;
        font-weight: 700;
    }

    .up-sub {
        margin: 4px 0 22px;
        color: var(--muted);
        font-size: 14px;
    }

    .up-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 16px;
        padding: 22px;
        margin-bottom: 18px;
    }

    .up-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 16px;
    }

    .up-field label {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .up-field select,
    .up-field input[type=text],
    .up-field input[type=file] {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #c4ccd9;
        border-radius: 10px;
        background: #fff;
        font: inherit;
        font-size: 14px;
        color: var(--ink);
    }

    .up-field input[readonly] {
        background: var(--soft);
        font-weight: 600;
    }

    .up-field select:focus,
    .up-field input:focus {
        outline: none;
        border-color: var(--brand);
        box-shadow: 0 0 0 3px var(--brand-bg);
    }

    .up-full {
        grid-column: 1 / -1;
    }

    .up-hint {
        margin-top: 6px;
        font-size: 12.5px;
        color: var(--muted);
    }

    .up-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid var(--line);
    }

    .up-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border: 1px solid transparent;
        border-radius: 10px;
        font: 600 14px 'Public Sans', system-ui, sans-serif;
        cursor: pointer;
        text-decoration: none !important;
    }

    .up-btn.primary {
        background: var(--brand);
        color: #fff;
    }

    .up-btn.primary:hover {
        background: var(--brand-d);
    }

    .up-btn.primary:disabled {
        opacity: .6;
        cursor: not-allowed;
    }

    .up-btn.ghost {
        background: #fff;
        border-color: #c4ccd9;
        color: var(--ink);
    }

    .up-btn.ghost:hover {
        background: var(--soft);
    }

    .up-btn.warn {
        background: var(--warn);
        color: #fff;
    }

    .up-alert {
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 18px;
        font-size: 14px;
        font-weight: 500;
    }

    .up-alert.bad {
        background: var(--bad-bg);
        color: var(--bad);
    }

    .up-kpis {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin: 14px 0;
    }

    .up-kpi {
        padding: 14px 16px;
        border-radius: 12px;
        background: var(--soft);
    }

    .up-kpi span {
        display: block;
        font-size: 12px;
        color: var(--muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .up-kpi strong {
        font-size: 26px;
    }

    .up-kpi.ok {
        background: var(--ok-bg);
    }

    .up-kpi.ok strong {
        color: var(--ok);
    }

    .up-kpi.warn {
        background: var(--warn-bg);
    }

    .up-kpi.warn strong {
        color: var(--warn);
    }

    .up-tablewrap {
        overflow-x: auto;
        border: 1px solid var(--line);
        border-radius: 12px;
        margin-top: 12px;
    }

    .up table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .up th,
    .up td {
        padding: 8px 12px;
        text-align: left;
        border-bottom: 1px solid var(--line);
        white-space: nowrap;
    }

    .up th {
        background: var(--soft);
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: var(--muted);
    }

    .up td.reason {
        white-space: normal;
        color: var(--bad);
        font-weight: 500;
    }

    .up code {
        background: var(--soft);
        padding: 1px 6px;
        border-radius: 6px;
        font-size: 12.5px;
    }

    /* Select2 to match the rest of the page */
    .up .select2-container {
        width: 100% !important;
    }

    .up .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #c4ccd9;
        border-radius: 10px;
        background: #fff;
        outline: 0;
    }

    .up .select2-container--default.select2-container--focus .select2-selection--single,
    .up .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(31, 75, 182, .14);
    }

    .up .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px;
        padding-left: 12px;
        padding-right: 44px;
        font-size: 14px;
        font-weight: 500;
        color: var(--ink);
    }

    .up .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #7b879b;
    }

    .up .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 6px;
    }

    .up .select2-container--default .select2-selection--single .select2-selection__clear {
        margin-right: 8px;
        color: #7b879b;
        font-size: 18px;
    }

    .select2-dropdown {
        border-color: #c4ccd9 !important;
        border-radius: 10px !important;
        overflow: hidden;
        font-family: 'Public Sans', system-ui, sans-serif;
        font-size: 14px;
        box-shadow: 0 12px 30px rgba(23, 35, 61, .14);
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: #e8eefb !important;
        color: #173a91 !important;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background: #f4f6fb !important;
        font-weight: 600;
    }

    @media (max-width: 767px) {

        .up-grid,
        .up-kpis {
            grid-template-columns: 1fr 1fr;
        }

        .up-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div id="wrapper">
    <?php include("nav.php"); ?>
    <div id="sidebarOverlay"></div>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include("includes/topnav.php"); ?>

            <div class="container-fluid mt-4">
                <button type="button" id="sidebarToggleMobile" class="up-menu" aria-label="Open menu">
                    <i class="fas fa-bars" aria-hidden="true"></i> <span>Menu</span>
                </button>

                <div class="up">
                    <h1>Upload Old Students</h1>
                    <p class="up-sub">Choose the program, then upload a CSV or Excel file. Students whose Student ID already exists are skipped, and every skipped row can be exported.</p>

                    <?php if ($loadError): ?>
                        <div class="up-alert bad">Error loading programs. Please try again later.</div>
                    <?php endif; ?>

                    <?php if ($result && !empty($result['error'])): ?>
                        <div class="up-alert bad"><i class="fas fa-exclamation-circle"></i> <?php echo up_h($result['error']); ?></div>
                    <?php endif; ?>

                    <?php if ($result && empty($result['error'])): ?>
                        <section class="up-card">
                            <strong><i class="fas fa-file-import"></i> Upload finished</strong>
                            <div class="up-sub" style="margin:2px 0 0">
                                <?php echo up_h($result['file']); ?> &middot; <?php echo up_h($result['program']); ?> (<?php echo up_h($result['session']); ?>)
                            </div>
                            <div class="up-kpis">
                                <div class="up-kpi"><span>Rows in file</span><strong><?php echo (int)$result['total']; ?></strong></div>
                                <div class="up-kpi ok"><span>Uploaded</span><strong><?php echo (int)$result['inserted']; ?></strong></div>
                                <div class="up-kpi warn"><span>Duplicates skipped</span><strong><?php echo (int)$result['dups']; ?></strong></div>
                                <div class="up-kpi warn"><span>Invalid rows skipped</span><strong><?php echo (int)$result['invalid']; ?></strong></div>
                            </div>

                            <?php if ($skippedRows): ?>
                                <a class="up-btn warn" href="upload_student_db.php?download=skipped">
                                    <i class="fas fa-download"></i> Export <?php echo count($skippedRows); ?> missing row(s) (CSV)
                                </a>
                                <div class="up-tablewrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>File row</th>
                                                <th>Student ID</th>
                                                <th>Name</th>
                                                <th>Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (array_slice($skippedRows, 0, 50) as $s): ?>
                                                <tr>
                                                    <td><?php echo (int)$s['row']; ?></td>
                                                    <td><?php echo up_h($s['student_id']); ?></td>
                                                    <td><?php echo up_h($s['name']); ?></td>
                                                    <td class="reason"><?php echo up_h($s['reason']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php if (count($skippedRows) > 50): ?>
                                    <div class="up-hint">Showing the first 50. The exported CSV contains all <?php echo count($skippedRows); ?>.</div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="up-hint" style="color:var(--ok)"><i class="fas fa-check-circle"></i> No rows were skipped.</div>
                            <?php endif; ?>
                        </section>
                    <?php endif; ?>

                    <form class="up-card" method="post" enctype="multipart/form-data" id="uploadForm" autocomplete="off">
                        <input type="hidden" name="csrf" value="<?php echo up_h($_SESSION['up_csrf']); ?>">

                        <div class="up-grid">
                            <div class="up-field">
                                <label for="program">Program</label>
                                <select name="program" id="program" required>
                                    <option value=""></option>
                                    <?php foreach ($programs as $p => $s): ?>
                                        <option value="<?php echo up_h($p); ?>"><?php echo up_h($p); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="up-field">
                                <label for="session">Session</label>
                                <input type="text" id="session" readonly placeholder="Select a program first">
                            </div>

                            <div class="up-field up-full">
                                <label for="student_file">CSV or Excel file</label>
                                <input type="file" name="student_file" id="student_file" accept=".csv,.xlsx" required>
                                <div class="up-hint">
                                    Columns (first row): <code>Student ID</code> <code>Name</code> <code>DOB</code> are required;
                                    <code>Email</code> <code>Mobile No</code> <code>Payment Status</code> <code>Status</code> <code>IN No</code> <code>Active</code> are optional.
                                    Max 5 MB. Format: .csv or .xlsx.
                                </div>
                            </div>
                        </div>

                        <div class="up-actions">
                            <button type="submit" class="up-btn primary" id="uploadBtn"><i class="fas fa-upload"></i> Upload students</button>
                            <a class="up-btn ghost" href="upload_student_db.php?download=template"><i class="fas fa-file-csv"></i> Download template</a>
                            <a class="up-btn ghost" href="oldStudentsDB.php"><i class="fas fa-arrow-left"></i> Old students list</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        document.body.classList.add('at-page');

        // mobile sidebar
        document.getElementById('sidebarToggleMobile').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-open');
        });
        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            document.body.classList.remove('sidebar-open');
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') document.body.classList.remove('sidebar-open');
        });

        // program -> session (loaded from data_tables)
        var sessions = <?php echo json_encode($programs, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

        function loadSelect2(cb) {
            if (window.jQuery && jQuery.fn.select2) return cb();
            var css = document.createElement('link');
            css.rel = 'stylesheet';
            css.href = 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css';
            document.head.appendChild(css);
            var js = document.createElement('script');
            js.src = 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js';
            js.onload = cb;
            js.onerror = cb; // falls back to the normal select
            document.head.appendChild(js);
        }

        function init() {
            var $program = jQuery('#program');
            var $session = jQuery('#session');

            if (jQuery.fn.select2) {
                $program.select2({
                    placeholder: '-- Select program --',
                    allowClear: true,
                    width: '100%'
                });
            }

            // Select2 fires jQuery events, so listen with jQuery
            $program.on('change', function() {
                var v = jQuery(this).val();
                $session.val(v && sessions[v] ? sessions[v] : '');
            });

            // basic checks + double-click guard
            jQuery('#uploadForm').on('submit', function(e) {
                if (!$program.val()) {
                    e.preventDefault();
                    alert('Please select a program.');
                    return;
                }
                var f = document.getElementById('student_file').files[0];
                if (f && !/\.(csv|xlsx)$/i.test(f.name)) {
                    e.preventDefault();
                    alert('Only .csv or .xlsx files are allowed.');
                    return;
                }
                jQuery('#uploadBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Uploading...');
            });
        }

        function ready() {
            if (!window.jQuery) {
                setTimeout(ready, 50);
                return;
            }
            loadSelect2(init);
        }
        ready();
    })();
</script>

</body>

</html>