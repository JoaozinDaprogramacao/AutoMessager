<?php
require_once("../../../conexao.php");
require_once("../../../../sistema/Models/WhatsAppAPI/WhatsApp.php");

@session_start();
$id_usuario = @$_SESSION['id'];

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

// Instancia a classe WhatsApp
$whatsapp = new WhatsApp();

// Obtém o QR Code usando o nome do usuário
$response = $whatsapp->getQrCode($nome_usuario);

// Retorna o QR Code (URL ou Base64) como resposta
echo $response;
