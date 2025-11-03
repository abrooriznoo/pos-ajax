<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once $_SESSION["dir_root"] . "\public\crud\crudSales.php";
// require_once $_SESSION["dir_root"] . "\public\crud\crudPurchase.php"; // contoh module lain

// Bisa juga buat array mapping module ke class handler-nya
$moduleHandlers = [
    'sales' => new crudSales(),
    // 'purchase' => new crudPurchase(), // misal ada module purchase
];

// Baca input JSON
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($data['module'], $data['action'])) {
        $module = $data['module'];
        $action = $data['action'];

        if (array_key_exists($module, $moduleHandlers)) {
            $handler = $moduleHandlers[$module];

            // Dinamis panggil method sesuai action
            // Contoh: action = 'save' panggil method save, dsb
            if (method_exists($handler, $action)) {
                $result = $handler->$action($data);
                echo json_encode($result);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => "Method action '$action' tidak ditemukan di module '$module'"
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => "Module '$module' tidak tersedia"
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => "Module atau action tidak disediakan"
        ]);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $data = $_GET;  // <-- Ambil data dari query string GET

    if (isset($data['module'], $data['action'])) {
        $module = $data['module'];
        $action = $data['action'];

        if (array_key_exists($module, $moduleHandlers)) {
            $handler = $moduleHandlers[$module];

            if (method_exists($handler, $action)) {
                $result = $handler->$action($data);
                echo json_encode($result);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => "Method action '$action' tidak ditemukan di module '$module'"
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => "Module '$module' tidak tersedia"
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => "Module atau action tidak disediakan"
        ]);
    }
}

?>