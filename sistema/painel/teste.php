<?php
// Configuração da API do Google Maps
$apiKey = 'AIzaSyDK4lzjKMHPdR0r60shMQyCmprH-M_R9vE'; // Substitua pela sua chave de API
$url = "https://places.googleapis.com/v1/places:searchText";

// Corpo da requisição em JSON
$requestBody = json_encode([
    "textQuery" => "Nutricionistas em Janaúba"
]);

// Inicializa o cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    "X-Goog-Api-Key: $apiKey",
    "X-Goog-FieldMask: places.displayName,places.formattedAddress,places.priceLevel"
]);

// Executa a requisição
$response = curl_exec($ch);

// Verifica erros na requisição
if (curl_errno($ch)) {
    echo "Erro na requisição: " . curl_error($ch);
    exit;
}

// Fecha o cURL
curl_close($ch);

// Converte o JSON de resposta em array associativo
$data = json_decode($response, true);

// Exibe os resultados
if (isset($data['places'])) {
    echo "<h1>Resultados da busca por Nutricionistas em Janaúba</h1>";
    echo "<ul>";
    foreach ($data['places'] as $place) {
        echo "<li>";
        echo "<strong>Nome:</strong> " . htmlspecialchars($place['displayName']) . "<br>";
        echo "<strong>Endereço:</strong> " . htmlspecialchars($place['formattedAddress']) . "<br>";
        echo "<strong>Preço:</strong> " . htmlspecialchars($place['priceLevel'] ?? 'N/A') . "<br>";
        echo "<hr>";
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Nenhum resultado encontrado ou erro na consulta.</p>";
    if (isset($data['error']['message'])) {
        echo "<p>Erro: " . htmlspecialchars($data['error']['message']) . "</p>";
    }
}
?>
