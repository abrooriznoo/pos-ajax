<?php
require_once "../../Database/koneksi.php";

session_start();

$action = htmlspecialchars(filter_input(INPUT_POST, "action"));
$formData = $_POST["data"];
parse_str($formData, $formData);

$sanitizeData = [];

foreach ($formData as $key => $value) {
    $sanitizeData[$key] = htmlspecialchars(trim($value), ENT_QUOTES, "UTF-8");
}

switch ($action) {
    case 'create':
        $result = createData($sanitizeData);
        break;
    case 'login':
        $result = loginData($sanitizeData);
        break;
    // case 'detail':
    //     $result = detailData();
    //     break;
    // case 'update':
    //     $result = updateData();
    //     break;
    // case 'delete':
    //     $result = deleteData();
    //     break;
}

echo json_encode($result);
die();

function createData($sanitizeData)
{
    try {
        $sql = "INSERT INTO users (username, email, password, tgl_lahir, gender) VALUES (:username, :email, :password, :tanggal_lahir, :jenis_kelamin)";
        $stmt = db()->prepare($sql);
        $stmt->bindParam(":username", $sanitizeData["username"]);
        $stmt->bindParam(":email", $sanitizeData["email"]);
        $hashedPassword = password_hash($sanitizeData["password"], PASSWORD_BCRYPT);
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":tanggal_lahir", $sanitizeData["tanggal_lahir"]);
        $stmt->bindParam(":jenis_kelamin", $sanitizeData["jenis_kelamin"]);
        $stmt->execute();

        $rowCount = $stmt->rowCount();
        $stmt->closeCursor();

        if ($rowCount > 0) {
            return [
                "response" => 200,
                "status" => 1,
                "messages" => "User registered successfully",
            ];
        } else {
            return [
                "response" => 500,
                "status" => 0,
                "messages" => "Failed to register user",
            ];
        }
    } catch (PDOException $e) {
        return [
            "response" => 500,
            "status" => 0,
            "messages" => "Database error: " . $e->getMessage(),
        ];
    }
}

function loginData($sanitizeData)
{
    try {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = db()->prepare($sql);
        $stmt->bindParam(":email", $sanitizeData["email"]);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if ($user && password_verify($sanitizeData["password"], $user["password"])) {
            // Password benar, set session
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["email"] = $user["email"];

            return [
                "response" => 200,
                "status" => 1,
                "messages" => "Login successful",
            ];
        } else {
            return [
                "response" => 401,
                "status" => 0,
                "messages" => "Invalid email or password",
            ];
        }
    } catch (PDOException $e) {
        return [
            "response" => 500,
            "status" => 0,
            "messages" => "Database error: " . $e->getMessage(),
        ];
    }
}


