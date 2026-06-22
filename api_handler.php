<?php
/**
 * API Handler - Improved Security
 * Managing Gemini and D-ID API calls with proper error handling
 */

header('Content-Type: application/json');

// Load configuration
require_once 'config.php';
include 'db.php';

// Only allow requests from same origin
$allowed_origins = ALLOWED_ORIGINS;
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: " . $allowed_origins[0]);
}

header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type");

/**
 * Send HTTP Request Helper
 */
function sendRequest($url, $method = 'GET', $headers = [], $data = null, $timeout = 30) {
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            throw new Exception("cURL Error: $error");
        }
        
        return ['status' => $httpCode, 'body' => $response];
    } catch (Exception $e) {
        error_log("Request Error: " . $e->getMessage());
        return ['status' => 0, 'body' => '', 'error' => $e->getMessage()];
    }
}

$action = $_POST['action'] ?? '';

// --- 1. Generate Product Description with Gemini ---
if ($action === 'gemini') {
    try {
        if (empty(GEMINI_API_KEY)) {
            throw new Exception('Gemini API Key not configured');
        }
        
        $pName = cleanInput($_POST['name'] ?? 'محصول');
        $pDesc = cleanInput($_POST['desc'] ?? '');
        
        if (empty($pName)) {
            throw new Exception('Product name is required');
        }
        
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . GEMINI_API_KEY;
        
        $prompt = "تو فروشنده بوتیک هستی. محصول: '$pName' ($pDesc). یک جمله بسیار کوتاه (زیر 8 کلمه)، جذاب و عامیانه فارسی برای توضیح این محصول بنویس.";
        
        $data = json_encode([
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ]);
        
        $headers = ["Content-Type: application/json"];
        
        $res = sendRequest($url, 'POST', $headers, $data);
        
        if ($res['status'] === 200) {
            echo $res['body'];
        } else {
            throw new Exception('Gemini API Error: ' . $res['status']);
        }
    } catch (Exception $e) {
        error_log("Gemini Request Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// --- 2. Create Video Talk with D-ID ---
if ($action === 'did_create_talk') {
    try {
        $text = cleanInput($_POST['text'] ?? '');
        $imgUrl = cleanInput($_POST['img_url'] ?? '');
        
        if (empty($text) || empty($imgUrl)) {
            throw new Exception('Text and image URL are required');
        }
        
        $success = false;
        $finalResponse = null;
        
        // Loop through D-ID API keys
        foreach ($DID_API_KEYS as $key) {
            if (empty($key)) continue;
            
            try {
                $url = "https://api.d-id.com/talks";
                $headers = [
                    "Authorization: Basic " . base64_encode($key),
                    "Content-Type: application/json"
                ];
                
                $body = json_encode([
                    "source_url" => $imgUrl,
                    "script" => [
                        "type" => "text",
                        "input" => $text,
                        "provider" => [
                            "type" => "microsoft",
                            "voice_id" => "fa-IR-FaridNeural"
                        ]
                    ],
                    "config" => [
                        "fluent" => true,
                        "pad_audio" => "0.0",
                        "stitch" => true
                    ]
                ]);
                
                $result = sendRequest($url, 'POST', $headers, $body, 60);
                
                // Check response status
                if ($result['status'] == 201 || $result['status'] == 200) {
                    echo $result['body'];
                    $success = true;
                    break;
                } elseif ($result['status'] == 402 || $result['status'] == 401 || $result['status'] == 403) {
                    // Key quota exhausted, try next key
                    continue;
                } else {
                    // Other technical error
                    $finalResponse = json_encode([
                        'error' => 'Technical Error',
                        'status' => $result['status']
                    ]);
                    break;
                }
            } catch (Exception $e) {
                error_log("D-ID Key Error: " . $e->getMessage());
                continue;
            }
        }
        
        // If no key worked, notify admin
        if (!$success) {
            notifyAdminOfAPIFailure($conn);
            
            if ($finalResponse) {
                echo $finalResponse;
            } else {
                http_response_code(503);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'تمام کلیدهای API منقضی شده‌اند. مدیر را مطلع کردیم.'
                ]);
            }
        }
    } catch (Exception $e) {
        error_log("D-ID Create Talk Error: " . $e->getMessage());
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// --- 3. Get Video Status ---
if ($action === 'did_get_talk') {
    try {
        $talkId = cleanInput($_POST['talk_id'] ?? '');
        
        if (empty($talkId)) {
            throw new Exception('Talk ID is required');
        }
        
        // Try each API key
        foreach ($DID_API_KEYS as $key) {
            if (empty($key)) continue;
            
            try {
                $url = "https://api.d-id.com/talks/" . urlencode($talkId);
                $headers = [
                    "Authorization: Basic " . base64_encode($key),
                    "Content-Type: application/json"
                ];
                
                $result = sendRequest($url, 'GET', $headers);
                
                if ($result['status'] == 200) {
                    echo $result['body'];
                    exit;
                }
            } catch (Exception $e) {
                error_log("D-ID Get Talk Error: " . $e->getMessage());
                continue;
            }
        }
        
        http_response_code(404);
        echo json_encode(['error' => 'Could not retrieve video']);
    } catch (Exception $e) {
        error_log("D-ID Get Talk Error: " . $e->getMessage());
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

/**
 * Notify Admin of API Key Failure
 */
function notifyAdminOfAPIFailure($conn) {
    try {
        $sysPhone = "0000000000";
        
        // Get or create system user
        $result = dbSelect(
            "SELECT id FROM users WHERE phone = ?",
            [$sysPhone],
            's'
        );
        
        if (empty($result)) {
            $hashed = password_hash('secure_system_password', PASSWORD_BCRYPT);
            $insert = dbExecute(
                "INSERT INTO users (name, phone, email, password, is_verified) VALUES (?, ?, ?, ?, 1)",
                ['System Alert', $sysPhone, 'sys@admin.com', $hashed],
                'ssss'
            );
            $sysId = $insert['last_id'];
        } else {
            $sysId = $result[0]['id'];
        }
        
        // Check if we've already sent a recent alert
        $recentMsg = dbSelect(
            "SELECT created_at FROM messages WHERE user_id = ? AND sender = 'user' ORDER BY id DESC LIMIT 1",
            [$sysId],
            'i'
        );
        
        $shouldInsert = true;
        if (!empty($recentMsg)) {
            $lastTime = strtotime($recentMsg[0]['created_at']);
            // Only send alert every hour
            if (time() - $lastTime < 3600) {
                $shouldInsert = false;
            }
        }
        
        if ($shouldInsert) {
            $msg = "⚠️ هشدار: تمام کلیدهای D-ID منقضی شده‌اند یا اعتبار ندارند. لطفاً کلیدهای جدید را اضافه کنید.";
            dbExecute(
                "INSERT INTO messages (user_id, sender, message, is_read, created_at) VALUES (?, 'user', ?, 0, NOW())",
                [$sysId, $msg],
                'is'
            );
        }
    } catch (Exception $e) {
        error_log("Admin Notification Error: " . $e->getMessage());
    }
}

?>
