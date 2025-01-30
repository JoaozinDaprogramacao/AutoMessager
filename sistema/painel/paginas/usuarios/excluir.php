<?php
$tabela = 'usuarios';
require_once("../../../conexao.php");
require_once("../../../../sistema/Models/WhatsAppAPI/WhatsApp.php");


$id = $_POST['id'];

$query = $pdo->query("SELECT nome FROM $tabela where id = '$id'");
$nome = $query->fetch(PDO::FETCH_ASSOC);

$whatsapp = new WhatsApp();

$nome_usuario = $nome['nome'];
$nome_sem_acentos = iconv('UTF-8', 'ASCII//TRANSLIT', $nome_usuario);
$nome_minusculo = strtolower($nome_sem_acentos);
$$nome_usuario = preg_replace('/[^a-z0-9]/', '', $nome_minusculo);

$whatsapp->deletarSessao($nome_usuario);

$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$foto = @$res[0]['foto'];

if ($foto != "sem-foto.jpg") {
	@unlink('../../images/perfil/' . $foto);
}

$pdo->query("DELETE FROM $tabela WHERE id = '$id' ");
echo 'Excluído com Sucesso';
