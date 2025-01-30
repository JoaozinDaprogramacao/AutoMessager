<?php 
$tabela = 'produtos';
require_once("../../../conexao.php");

$nome = $_POST['nome'];
$categoria = $_POST['categoria'];
$fornecedor = $_POST['fornecedor'];
$obs = $_POST['obs'];
$valor_compra = $_POST['valor_compra'];
$valor_compra = str_replace('.', '', $valor_compra);
$valor_compra = str_replace(',', '.', $valor_compra);
$valor_venda = $_POST['valor_venda'];
$valor_venda = str_replace('.', '', $valor_venda);
$valor_venda = str_replace(',', '.', $valor_venda);
$estoque_minimo = $_POST['estoque_minimo'];
$estoque_minimo = str_replace('.', '', $estoque_minimo);
$estoque_minimo = str_replace(',', '.', $estoque_minimo);
$estoque = $_POST['estoque'];
$estoque = str_replace('.', '', $estoque);
$estoque = str_replace(',', '.', $estoque);
$unidade = $_POST['unidade'];
$id = $_POST['id'];
$tem_estoque = $_POST['tem_estoque'];

$valor_sem_promocao = $_POST['valor_sem_promocao'];
$valor_sem_promocao = str_replace('.', '', $valor_sem_promocao);
$valor_sem_promocao = str_replace(',', '.', $valor_sem_promocao);

if($valor_sem_promocao > 0){
	if($valor_sem_promocao <= $valor_venda){
		echo 'O valor sem promoção tem que ser maior que o valor de venda do produto!';
		exit();
	}
}


if($estoque == ""){
	$estoque = 0;
}

if($valor_compra == ""){
	$valor_compra = 0;
}

if($categoria == ""){
	$categoria = 0;
}

if($unidade == ""){
	$unidade = 0;
}

if($fornecedor == ""){
	$fornecedor = 0;
}

if($tem_estoque == ""){
	$tem_estoque = 0;
}

//validacao
$query = $pdo->query("SELECT * from $tabela where nome = '$nome'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id_reg = @$res[0]['id'];
if(@count($res) > 0 and $id != $id_reg){
	echo 'Nome já Cadastrado!';
	exit();
}


//validar troca da foto
$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
	$foto = $res[0]['foto'];
}else{
	$foto = 'sem-foto.png';
}


//SCRIPT PARA SUBIR FOTO NO SERVIDOR
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['foto']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../images/produtos/' .$nome_img;

$imagem_temp = @$_FILES['foto']['tmp_name']; 

if(@$_FILES['foto']['name'] != ""){
	$ext = pathinfo($nome_img, PATHINFO_EXTENSION);   
	if($ext == 'png' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'PNG' or $ext == 'JPG' or $ext == 'JPEG' or $ext == 'GIF' or $ext == 'webp' or $ext == 'WEBP'){ 
	
			//EXCLUO A FOTO ANTERIOR
			if($foto != "sem-foto.png"){
				@unlink('../../images/produtos/'.$foto);
			}

			$foto = $nome_img;
		
		move_uploaded_file($imagem_temp, $caminho);
	}else{
		echo 'Extensão de Imagem não permitida!';
		exit();
	}
}


if($id == ""){
$query = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, categoria = :categoria, obs = :obs, valor_compra = :valor_compra, valor_venda = :valor_venda, estoque = :estoque, estoque_minimo = :estoque_minimo, foto = :foto, tem_estoque = :tem_estoque, unidade = :unidade, fornecedor = :fornecedor, vendas = 0, ativo = 'Sim', valor_sem_promocao = :valor_sem_promocao ");
	
}else{
$query = $pdo->prepare("UPDATE $tabela SET nome = :nome, categoria = :categoria, obs = :obs, valor_compra = :valor_compra, valor_venda = :valor_venda, estoque = :estoque, estoque = :estoque, estoque_minimo = :estoque_minimo, foto = :foto, tem_estoque = :tem_estoque, unidade = :unidade, fornecedor = :fornecedor, valor_sem_promocao = :valor_sem_promocao where id = '$id'");
}
$query->bindValue(":nome", "$nome");
$query->bindValue(":categoria", "$categoria");
$query->bindValue(":obs", "$obs");
$query->bindValue(":valor_compra", "$valor_compra");
$query->bindValue(":valor_venda", "$valor_venda");
$query->bindValue(":estoque", "$estoque");
$query->bindValue(":tem_estoque", "$tem_estoque");
$query->bindValue(":estoque_minimo", "$estoque_minimo");
$query->bindValue(":foto", "$foto");
$query->bindValue(":unidade", "$unidade");
$query->bindValue(":fornecedor", "$fornecedor");
$query->bindValue(":valor_sem_promocao", "$valor_sem_promocao");
$query->execute();

echo 'Salvo com Sucesso';


 ?>
