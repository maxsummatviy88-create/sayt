<?php
// Налаштування Telegram
$telegramToken = '8854981330:AAE0zjnVDn6dS-xhA9bOp5jM4aMgmmSuDkI';
$chatId = '-5585856805';

// Налаштування Google Apps Script
$googleScriptUrl = 'https://script.google.com/macros/s/AKfycbxNkLjNi2jsJqW7VdtHIm0MqTMqlntP5mVOnKbjDYbxez-bkpHb0W6ooZWH9ZG6mUds/exec';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Отримання даних з форми
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $boat = isset($_POST['boat']) ? trim($_POST['boat']) : 'Не вказано';
    $date = isset($_POST['date']) ? trim($_POST['date']) : 'Не вказано';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // 1. ВІДПРАВКА В TELEGRAM
    $text = "<b>⚓ Нова заявка на оренду яхти!</b>\n\n";
    $text .= "<b>👤 Ім'я:</b> " . htmlspecialchars($name) . "\n";
    $text .= "<b>📞 Телефон:</b> " . htmlspecialchars($phone) . "\n";
    if (!empty($boat)) $text .= "<b>🛥 Яхта:</b> " . htmlspecialchars($boat) . "\n";
    if (!empty($date)) $text .= "<b>📅 Дата:</b> " . htmlspecialchars($date) . "\n";
    if (!empty($message)) $text .= "<b>💬 Коментар:</b> " . htmlspecialchars($message) . "\n";

    $telegramUrl = "https://api.telegram.org/bot{$telegramToken}/sendMessage";
    $telegramData = [
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];

    $chTelegram = curl_init($telegramUrl);
    curl_setopt($chTelegram, CURLOPT_POST, true);
    curl_setopt($chTelegram, CURLOPT_POSTFIELDS, http_build_query($telegramData));
    curl_setopt($chTelegram, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chTelegram, CURLOPT_SSL_VERIFYPEER, false);
    $telegramResult = curl_exec($chTelegram);
    curl_close($chTelegram);

    // 2. ВІДПРАВКА В GOOGLE ТАБЛИЦЮ
    $googleData = [
        'name' => $name,
        'phone' => $phone,
        'boat' => $boat,
        'date' => $date,
        'message' => $message
    ];

    $chGoogle = curl_init($googleScriptUrl);
    curl_setopt($chGoogle, CURLOPT_POST, true);
    curl_setopt($chGoogle, CURLOPT_POSTFIELDS, http_build_query($googleData));
    curl_setopt($chGoogle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chGoogle, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($chGoogle, CURLOPT_SSL_VERIFYPEER, false);
    $googleResult = curl_exec($chGoogle);
    curl_close($chGoogle);

    // Повертаємо відповідь для JS
    echo json_encode(['status' => 'success', 'message' => 'Заявку успішно відправлено!']);
    exit;
}
?>