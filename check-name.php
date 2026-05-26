<?php
header('Content-Type: application/json');

// ---- ডাটাবেজ কানেকশন ফাইল ইনক্লুড করুন ----
// আপনার প্রজেক্টের কানেকশন ফাইলের পাথ ঠিক করুন (যেমন: config.php বা db.php)
include 'common/config.php'; 

// getSetting ফাংশনটি যদি অলরেডি অন্য কোথাও ডিফাইন করা না থাকে, তবে এখানে ডিফাইন করে নেওয়া হলো
if (!function_exists('getSetting')) {
    function getSetting($conn, $name) {
        $name = $conn->real_escape_string($name);
        $result = $conn->query("SELECT value FROM settings WHERE name='$name'");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['value'];
        }
        return '';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $requestData = json_decode($json, true);
    $uid = isset($requestData['id']) ? trim($requestData['id']) : '';

    if (empty($uid)) {
        echo json_encode(['error' => true, 'message' => 'UID দিন']);
        exit;
    }

    // ---- ডাটাবেজ থেকে API Key লোড করা হচ্ছে ----
    $apiKey = getSetting($conn, 'name_checking_api_key');

    // যদি ডাটাবেজে কোনো কি সেট করা না থাকে
    if (empty($apiKey)) {
        echo json_encode(['error' => true, 'message' => 'API Key কনফিগার করা নেই!']);
        exit;
    }

    $url = "https://nanobd.shop/ffnamecheck/api.php?uid=" . urlencode($uid) . "&key=" . $apiKey;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200 && $response !== false) {
        $data = json_decode($response, true);
        if (isset($data['status']) && $data['status'] == 'success') {
            echo json_encode([
                'error' => false, 
                // আগের কারেকশন অনুযায়ী ডেটা যদি nested থাকে (data.nickname বা data.username) তা হ্যান্ডেল করবে
                'nickname' => $data['data']['username'] ?? ($data['data']['nickname'] ?? 'নাম নেই')
            ]);
        } else {
            echo json_encode(['error' => true, 'message' => $data['message'] ?? 'ভুল UID']);
        }
    } else {
        echo json_encode(['error' => true, 'message' => 'Server Error or Invalid API Key']);
    }
    exit;
}
