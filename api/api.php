<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

date_default_timezone_set('Asia/Jakarta');

$host     = 'localhost';
$db_name  = 'bemtvsoe_absensi';
$username = 'bemtvsoe_konek';
$password = 'Masuk123@';
$SECRET_KEY = 'KODEPANJANG';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Koneksi database gagal']);
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
    $input = json_decode(file_get_contents('php://input'), true);
    $user = trim($input['username'] ?? '');
    $pass = $input['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$user]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($account && password_verify($pass, $account['password'])) {
        $_SESSION['login'] = true;
        $_SESSION['username'] = $account['username'];
        echo json_encode(['status' => 'success', 'message' => 'Login berhasil']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Username atau password salah']);
    }
    exit;
}

if ($action === 'logout') {
    session_destroy();
    echo json_encode(['status' => 'success', 'message' => 'Logout berhasil']);
    exit;
}

if ($action === 'check_session') {
    if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
        echo json_encode(['status' => 'logged_in']);
    } else {
        echo json_encode(['status' => 'logged_out']);
    }
    exit;
}

if ($action === 'list_images') {
    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }
    $dir = '../image/';
    $images = [];
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..') {
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'])) {
                        $images[] = $file;
                    }
                }
            }
            closedir($dh);
        }
    }
    echo json_encode(['status' => 'success', 'images' => $images]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'scan') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['key']) || $input['key'] !== $SECRET_KEY || !isset($input['nama']) || !isset($input['kelas'])) {
        echo json_encode(['status' => 'invalid', 'message' => 'QR Code ditolak! Bukan QR Code resmi.']);
        exit;
    }

    $nama      = trim($input['nama']);
    $kelas     = trim($input['kelas']);
    $tanggal   = date('Y-m-d');
    $jam_masuk = date('H:i:s');
    $status    = 'Hadir';

    $stmtCheck = $pdo->prepare("SELECT jam_masuk FROM absensi WHERE nama = ? AND kelas = ? AND tanggal = ?");
    $stmtCheck->execute([$nama, $kelas, $tanggal]);
    $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        echo json_encode(['status' => 'warning', 'message' => "Halo $nama, kamu sudah absen hari ini pada pukul " . $existing['jam_masuk']]);
        exit;
    }

    $stmtInsert = $pdo->prepare("INSERT INTO absensi (nama, kelas, tanggal, jam_masuk, status) VALUES (?, ?, ?, ?, ?)");
    if ($stmtInsert->execute([$nama, $kelas, $tanggal, $jam_masuk, $status])) {
        echo json_encode(['status' => 'success', 'message' => "Absensi Berhasil! Selamat datang, $nama ($kelas) - Pukul $jam_masuk"]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan ke database']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    $tanggal = isset($_GET['tanggal']) && !empty($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
    $stmt = $pdo->prepare("SELECT id, nama, kelas, tanggal, jam_masuk, status FROM absensi WHERE tanggal = ? ORDER BY id DESC");
    $stmt->execute([$tanggal]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'tanggal' => $tanggal,
        'data' => $data
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? 0;

    $stmt = $pdo->prepare("DELETE FROM absensi WHERE id = ?");
    if ($stmt->execute([$id])) {
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add_manual') {
    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }
    $input = json_decode(file_get_contents('php://input'), true);
    $nama = trim($input['nama'] ?? '');
    $kelas = trim($input['kelas'] ?? '');
    $tanggal = $input['tanggal'] ?? date('Y-m-d');
    $jam = date('H:i:s');
    $status = $input['status'] ?? 'Hadir';

    if (empty($nama) || empty($kelas)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama dan kelas wajib diisi']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO absensi (nama, kelas, tanggal, jam_masuk, status) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$nama, $kelas, $tanggal, $jam, $status])) {
        echo json_encode(['status' => 'success', 'message' => 'Absen manual berhasil ditambahkan']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambah data']);
    }
    exit;
}
?>