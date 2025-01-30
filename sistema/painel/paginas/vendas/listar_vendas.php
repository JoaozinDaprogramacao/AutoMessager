<?php 
$tabela = 'itens_venda';
require_once("../../../conexao.php");
@session_start();
$id_usuario = $_SESSION['id'];
$desconto = @$_POST['desconto'];
$troco = @$_POST['troco'];
$tipo_desconto = @$_POST['tipo_desconto'];
$frete = @$_POST['frete'];
$frete = str_replace(',', '.', $frete);

if($frete == ""){
	$frete = 0;
}

if($desconto == ""){
	$desconto = 0;
}

$total_troco = 0;
$total_trocoF = 0;

$total_v = 0;

//buscar o total da venda
$query = $pdo->query("SELECT * from $tabela where funcionario = '$id_usuario' and id_venda = '0' order by id asc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if($linhas > 0){
	for($i=0; $i<$linhas; $i++){	
		$total_das_vendas = $res[$i]['total'];
		$total_v += $total_das_vendas;
	}
}

if($tipo_desconto == '%'){
	if($desconto > 0 and $total_v > 0){
		$total_final = -($total_v * $desconto / 100);
	}else{
		$total_final = 0;
	}
	
}else{
	$total_final = -$desconto;
}

$total_final = $total_final + $frete;


$query = $pdo->query("SELECT * from $tabela where funcionario = '$id_usuario' and id_venda = '0' order by id asc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
echo '<div style="overflow:auto; max-height:200px; width:100%; scrollbar-width: thin;">';
if($linhas > 0){
	for($i=0; $i<$linhas; $i++){
	$id = $res[$i]['id'];
	$produto = $res[$i]['produto'];
	$valor = $res[$i]['valor'];
	$quantidade = $res[$i]['quantidade'];
	$total = $res[$i]['total'];
	


	$total_final += $total;
	$total_finalF = number_format($total_final, 2, ',', '.');
	$valorF = number_format($valor, 2, ',', '.');
	$totalF = number_format($total, 2, ',', '.');
	
	if($troco > 0){
		$total_troco = $troco - $total_final;
		$total_trocoF = number_format($total_troco, 2, ',', '.');
	}
	

	$query2 = $pdo->query("SELECT * from produtos where id = '$produto'");
	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
	$nome_produto = $res2[0]['nome'];
	$foto_produto = $res2[0]['foto'];
	$unidade = $res2[0]['unidade'];

	$query3 = $pdo->query("SELECT * FROM unidade_medida where id = '$unidade'");
	$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
	if(@count($res3) > 0){
		$nome_unidade = $res3[0]['nome'];
	}else{
		$nome_unidade = 'Sem Unidade';
	}

	$ocultar_quantidades = '';
	$sigla_unidade = '';
	if($nome_unidade == 'Quilogramas' or $nome_unidade == 'Quilo' or $nome_unidade == 'Quilograma' or $nome_unidade == 'KG'){
		$sigla_unidade = ' (KG)';
		$ocultar_quantidades = 'ocultar';
	}

	if($nome_unidade == 'Metros' or $nome_unidade == 'Metro' or $nome_unidade == 'M' or $nome_unidade == 'm'){
		$sigla_unidade = ' (m)';
		$ocultar_quantidades = 'ocultar';
	}

	if($nome_unidade == 'Litro' or $nome_unidade == 'Litros' or $nome_unidade == 'L'){
		$sigla_unidade = ' (L)';
		$ocultar_quantidades = 'ocultar';
	}

	//tratamento separa string
	$qt = explode(".", $quantidade);
	if($qt[1] > 0){
		$quantidadeF = $quantidade;		
	}else{
		$quantidadeF = $qt[0];
	}

	$nome_produtoF = mb_strimwidth($nome_produto, 0, 24, "..."); 

	echo '<div class="row">';
	echo '<div class="col-md-3" style="margin-right:3px">';
	echo '<img src="images/produtos/'.$foto_produto.'" width="45px">';
	echo '</div>';
	echo '<div class="col-md-9" style="margin-left:-15px; margin-top:3px">';
	echo '<span style="font-size:13px; margin-left: -15px">';
	echo '<span class="'.$ocultar_quantidades.'">'.$quantidadeF.'</span> '.$nome_produtoF.' ';
	echo '</span><br>';
	echo '<div style="font-size:12px; color:#570a03; margin-top:0px; margin-left:0px">
	<a class="'.$ocultar_quantidades.'" href="#" onclick="diminuir('.$id.', '.$quantidadeF.')"><big><i class="fa fa-minus-circle text-danger" ></i></big></a>
	'.$quantidadeF.' '.$sigla_unidade.'
	<a class="'.$ocultar_quantidades.'" href="#" onclick="aumentar('.$id.', '.$quantidadeF.')"><big><i class="fa fa-plus-circle text-success" ></i></big></a>
	Total Item: R$ '.$totalF.' ';


	echo '<div class="dropdown head-dpdn2" style="position:absolute; top:0px; right:10px">
		<a title="Remover Item" href="#" class="dropdown" data-bs-toggle="dropdown" aria-expanded="false"><big><i class="fa fa-times" style="color:#7d1107"></i></big></a>
		
	<div class="dropdown-menu" style="margin-left:-50px;margin-top:-35px; background: #fcecd6">
		<div>
		<div class="notification_desc2" style="background: #fcecd6  ">
		<p style="font-size:12px; padding-top:10px; padding-left:10px">Remover Item? <a href="#" onclick="excluirItem('.$id.')"><span class="text-danger">Sim</span></a></p>
		</div>
		</div>										
		</div>
	</div>';









	echo '</div>';
	echo '</div>';
	echo '</div>';
	
	}
}
echo '</div>';

$total_finalF = number_format($total_final, 2, ',', '.');
echo '<div align="right" style="margin-top:10px; font-size:14px; border-top:1px solid #8f8f8f;" >';
echo '<br>';
echo '<span style="margin-right:40px;">Itens: <b>('.$linhas.')</b></span>';
echo '<span>Subtotal: </span>';
echo '<span style="font-weight:bold"> R$ ';
echo $total_finalF;
echo '</span>';
if($troco > 0){
echo '<br><span>Troco: </span>';
echo '<span style="font-weight:bold"> R$ ';
echo $total_trocoF;
echo '</span>';
}
echo '</div>';


?>

<script type="text/javascript">
	var itens = "<?=$linhas?>";
	$('#valor_pago').val('<?=$total_final?>')
	$('#subtotal_venda').val('<?=$total_final?>')
	if(itens > 0){
		$("#btn_limpar").show();
		$("#btn_venda").show();
	}else{
		$("#btn_limpar").hide();
		$("#btn_venda").hide();
	}
	function excluirItem(id){
		 $.ajax({
        url: 'paginas/' + pag + "/excluir-item.php",
        method: 'POST',
        data: {id},
        dataType: "html",

        success:function(mensagem){
            if (mensagem.trim() == "Excluído com Sucesso") {           	
                listarVendas();
            } else {
                $('#mensagem-excluir').addClass('text-danger')
                $('#mensagem-excluir').text(mensagem)
            }
        }
    });
	}

	function diminuir(id, quantidade){
		 $.ajax({
        url: 'paginas/' + pag + "/diminuir.php",
        method: 'POST',
        data: {id, quantidade},
        dataType: "html",

        success:function(mensagem){

            if (mensagem.trim() == "Excluído com Sucesso") {           	
                listarVendas();
            } else {
                $('#mensagem-excluir').addClass('text-danger')
                $('#mensagem-excluir').text(mensagem)
            }
        }
    });
	}


	function aumentar(id, quantidade){
		 $.ajax({
        url: 'paginas/' + pag + "/aumentar.php",
        method: 'POST',
        data: {id, quantidade},
        dataType: "html",

        success:function(mensagem){
        	
            if (mensagem.trim() == "Excluído com Sucesso") {           	
                listarVendas();
            } else {
            	alert(mensagem)
                $('#mensagem-excluir').addClass('text-danger')
                $('#mensagem-excluir').text(mensagem)
            }
        }
    });
	}

	
</script>