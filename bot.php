<?php
require_once 'db.php';

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) exit;

// Telegram Bot Token
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

function sendMessage($chat_id, $text, $keyboard = null) {
    $data = ['chat_id' => $chat_id, 'text' => $text, 'reply_markup' => $keyboard ? json_encode($keyboard) : null];
    file_get_contents(API_URL . "sendMessage?" . http_build_query($data));
}

$chat_id = $update['message']['chat']['id'];
$user_id = $update['message']['from']['id'];
$text = trim($update['message']['text']);

// State tracking
$db = getDB();
$stmt = $db->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // Start registration
    $stmt = $db->prepare("INSERT INTO students (user_id) VALUES (?)");
    $stmt->execute([$user_id]);
    sendMessage($chat_id, "مرحبًا! الرجاء إرسال رقم الواتساب الخاص بك 📱");
} elseif (empty($user['whatsapp'])) {
    $stmt = $db->prepare("UPDATE students SET whatsapp = ? WHERE user_id = ?");
    $stmt->execute([$text, $user_id]);
    sendMessage($chat_id, "ما هي مرحلتك الدراسية؟ 🎓");
} elseif (empty($user['degree'])) {
    $stmt = $db->prepare("UPDATE students SET degree = ? WHERE user_id = ?");
    $stmt->execute([$text, $user_id]);
    sendMessage($chat_id, "ما رأيك بالدورات الأونلاين؟ 💬");
} elseif (empty($user['opinion'])) {
    $stmt = $db->prepare("UPDATE students SET opinion = ? WHERE user_id = ?");
    $stmt->execute([$text, $user_id]);
    sendMessage($chat_id, "ما الميزانية التي يمكنك دفعها للدورة؟ 💰");
} elseif (empty($user['budget'])) {
    $stmt = $db->prepare("UPDATE students SET budget = ? WHERE user_id = ?");
    $stmt->execute([$text, $user_id]);
    $keyboard = [
        "keyboard" => [
            [["text" => "دورة بايثون"], ["text" => "دورة تصميم جرافيك"]],
            [["text" => "دورة تسويق رقمي"]]
        ],
        "resize_keyboard" => true,
        "one_time_keyboard" => true
    ];
    sendMessage($chat_id, "اختر الدورة التي ترغب بها 👇", $keyboard);
} elseif (empty($user['selected_course'])) {
    $stmt = $db->prepare("UPDATE students SET selected_course = ? WHERE user_id = ?");
    $stmt->execute([$text, $user_id]);
    sendMessage($chat_id, "✅ شكراً لك! تم حفظ بياناتك. سنتواصل معك قريباً.");
} else {
    sendMessage($chat_id, "✅ بياناتك مسجلة مسبقاً. شكراً لتواصلك معنا!");
}
?>
