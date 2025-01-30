<?php 
$tabela = 'itens_venda';
require_once("../../../conexao.php");

@session_start();
$id_usuario = $_SESSION['id'];

$quantidade = $_POST['quantidade'];
$quantidade = str_replace('.', '', $quantidade);
$quantidade = str_replace(',', '.', $quantidade);
$id_produto = $_POST['id_produto'];

$query = $pdo->query("SELECT * from produtos where id = '$id_produto'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$estoque = $res[0]['estoque'];
$valor = $res[0]['valor_venda'];
$tem_estoque = $res[0]['tem_estoque'];
$vendas = $res[0]['vendas'];
$unidade = $res[0]['unidade'];

$query3 = $pdo->query("SELECT * FROM unidade_medida where id = '$unidade'");
	$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
	if(@count($res3) > 0){
		$nome_unidade = $res3[0]['nome'];
	}else{
		$nome_unidade = 'Sem Unidade';
	}


if($valor <= 0){
	echo 'O valor do produto tem que ser maior que zero';
	exit();
}

if($quantidade > $estoque and $tem_estoque == 'Sim'){
	echo 'A quantidade de produtos não pode ser maior que a quantidade em estoque, por enquanto você tem '.$estoque.' itens deste produto no estoque!';
	exit();
}

$total = $quantidade * $valor;

$pdo->query("INSERT INTO itens_venda SET produto = '$id_produto', valor = '$valor', quantidade = '$quantidade', total = '$total', id_venda = '0', funcionario = '$id_usuario'");

echo 'Inserido com Sucesso';

if($tem_estoque == 'Sim'){
	$novo_estoque = $estoque - $quantidade;

	if($nome_unidade == 'Quilogramas' or $nome_unidade == 'Quilo' or $nome_unidade == 'Quilograma' or $nome_unidade == 'KG' or $nome_unidade == 'Metros' or $nome_unidade == 'Metro' or $nome_unidade == 'M' or $nome_unidade == 'm' or $nome_unidade == 'Litro' or $nome_unidade == 'Litros' or $nome_unidade == 'L'){
		$vendas = $vendas + 1;
	}else{
		$vendas = $vendas + $quantidade;
	}

	
	//adicionar os produtos na tabela produtos
	$pdo->query("UPDATE produtos SET estoque = '$novo_estoque', vendas = '$vendas' WHERE id = '$id_produto'"); 
}


?>