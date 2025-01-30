<?php
// Inicia o buffer de saída
ob_start();

// Ajuste do limite de memória para 2 GB e exibição de erros
ini_set('memory_limit', '2G'); // Ajuste de memória para grandes volumes de dados
ini_set('display_errors', 1); // Exibir erros
error_reporting(E_ALL); // Relatar todos os erros

require_once("../../../conexao.php");
require_once("../../../../sistema/Models/WhatsAppAPI/WhatsApp.php");

@session_start();
$id_usuario = @$_SESSION['id'];

$nincho = $_POST['nincho'];
$mensagem = $_POST['mensagem'];
$pagina = isset($_POST['pagina']) ? $_POST['pagina'] : 1; // Pega a página atual, ou 1 se não definida
$por_pagina = 50; // Número de leads a serem processados por vez

if (!$id_usuario) {
    echo "Erro: ID da sessão não encontrado.";
    exit();
}

// Busca o nome do usuário no banco de dados
$query = $pdo->prepare("SELECT nome FROM usuarios WHERE id = :id");
$query->bindValue(":id", $id_usuario);
$query->execute();

$resultado = $query->fetch(PDO::FETCH_ASSOC);

if (!$resultado) {
    echo "Erro: Usuário não encontrado.";
    exit();
}

$nome_usuario = $resultado['nome'];
$nome_sem_acentos = iconv('UTF-8', 'ASCII//TRANSLIT', $nome_usuario);
$nome_minusculo = strtolower($nome_sem_acentos);
$$nome_usuario = preg_replace('/[^a-z0-9]/', '', $nome_minusculo);

// Calcular o OFFSET para a consulta SQL
$offset = ($pagina - 1) * $por_pagina;

$query = $pdo->prepare("SELECT celular, prospectado FROM leads WHERE nincho = :nincho LIMIT :limit OFFSET :offset");
$query->bindValue(":nincho", $nincho);
$query->bindValue(":limit", $por_pagina, PDO::PARAM_INT);
$query->bindValue(":offset", $offset, PDO::PARAM_INT);
$query->execute();

// Obter os resultados
$resultados = $query->fetchAll(PDO::FETCH_ASSOC);

// Verifica se há leads para processar
if (empty($resultados)) {
    echo json_encode(['status' => 'success', 'message' => 'Enviado com Sucesso']);
    exit();
}

// Instancia a classe WhatsApp
$whatsapp = new WhatsApp();

$total = count($resultados); // Número de leads no lote
$enviados = 0; // Contador de enviados

foreach ($resultados as $index => $resultado) {
    $celular = $resultado['celular'];
    $prospectado = $resultado['prospectado'];

    // Verifica se o lead já foi prospectado
    if ($prospectado == 'Sim') {
        continue; // Pula esse lead se já foi prospectado
    }

    // Envia a mensagem
    $response = $whatsapp->enviaMensagem($nome_usuario, $mensagem, $celular);

    // Incrementa o contador de enviados
    $enviados++;

    // Calcula o progresso
    $progresso = round(($enviados / $total) * 100);

    // Envia o progresso para o frontend
    echo json_encode(['enviados' => $enviados, 'total' => $total, 'progresso' => $progresso]) . "\n";
    ob_flush();
    flush();

    // Marca o lead como prospectado
    $updateQuery = $pdo->prepare("UPDATE leads SET prospectado = 'Sim' WHERE celular = :celular");
    $updateQuery->bindValue(":celular", $celular);
    $updateQuery->execute();

    // Aguarda de 30 a 50 segundos antes de prosseguir para o próximo, exceto no último número
    if ($index < $total - 1) {
        $intervalo = rand(30, 50);
        sleep($intervalo);
    }
}

// Verifica se há mais leads a serem enviados (para a próxima página)
if (count($resultados) == $por_pagina) {
    $proxima_pagina = $pagina + 1;
    echo json_encode(['status' => 'success', 'message' => 'Envios parciais concluídos', 'proxima_pagina' => $proxima_pagina]) . "\n";
} else {
    echo json_encode(['status' => 'success', 'message' => 'Enviado com Sucesso']) . "\n";
}

// Limpa o buffer de saída e envia o conteúdo
ob_end_flush();
?>