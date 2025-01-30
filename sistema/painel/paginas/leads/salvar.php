<?php
$tabela = 'leads';
require_once("../../../conexao.php");

@session_start();
$id_usuario = @$_SESSION['id'];


$nome = @$_POST['nome'];
$celular = @$_POST['celular'];
$endereco = @$_POST['endereco'];
$numero = @$_POST['numero'];
$bairro = @$_POST['bairro'];
$cidade = @$_POST['cidade'];
$estado = @$_POST['estado'];
$cep = @$_POST['cep'];
$id = @$_POST['id'];
$nincho = @$_POST['nincho'];

$complemento = @$_POST['complemento'];


function validarNumeroCelular($numero)
{
	// Remove todos os caracteres que não são dígitos
	$numero = preg_replace('/\D/', '', $numero);

	// Verifica se o número contém apenas dígitos
	if (!ctype_digit($numero)) {
		return "inválido";
	}

	// Verifica se o número possui o código de país e DDD (mínimo de 12 dígitos)
	if (strlen($numero) < 12) {
		return "inválido";
	}

	// Extrai o código de país e DDD
	$codigoPais = substr($numero, 0, 2); // Os dois primeiros dígitos
	$ddd = substr($numero, 2, 2);       // Dois dígitos após o código do país
	$numeroCelular = substr($numero, 4); // Restante do número

	// Verifica se o número tem pelo menos 8 dígitos restantes
	if (strlen($numeroCelular) < 8) {
		return "inválido";
	}

	// Se o número tiver 9 dígitos, remove o primeiro dígito
	if (strlen($numeroCelular) > 8) {
		$numeroCelular = substr($numeroCelular, 1);
	}

	// Verifica novamente se o número ajustado tem exatamente 8 dígitos
	if (strlen($numeroCelular) != 8) {
		return "inválido";
	}

	// Retorna o número validado no formato correto
	return $codigoPais . $ddd . $numeroCelular;
}


$celular = validarNumeroCelular($celular);


//validacao celular
if ($celular != "") {
	$query = $pdo->query("SELECT * from $tabela where celular = '$celular'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$id_reg = @$res[0]['id'];
	if (@count($res) > 0 and $id != $id_reg) {
		echo 'celular já Cadastrado!';
		exit();
	}
}

if ($id == "") {
	$query = $pdo->prepare("INSERT INTO $tabela SET nincho = :nincho, nome = :nome, celular = :celular, data_cad = curDate(), endereco = :endereco, usuario = '$id_usuario', numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado, cep = :cep, complemento = :complemento");
} else {
	$query = $pdo->prepare("UPDATE $tabela SET nincho = :nincho, nome = :nome, celular = :celular, endereco = :endereco, usuario = '$id_usuario', numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado, cep = :cep, complemento = :complemento where id = '$id'");
}

$query->bindValue(":nincho", "$nincho");
$query->bindValue(":nome", "$nome");
$query->bindValue(":celular", "$celular");
$query->bindValue(":endereco", "$endereco");
$query->bindValue(":numero", "$numero");
$query->bindValue(":bairro", "$bairro");
$query->bindValue(":cidade", "$cidade");
$query->bindValue(":estado", "$estado");
$query->bindValue(":cep", "$cep");
$query->bindValue(":complemento", "$complemento");

$query->execute();


echo 'Salvo com Sucesso';
