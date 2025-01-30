<?php 
$tabela = 'produtos';
require_once("../../../conexao.php");

$query = $pdo->query("SELECT * from $tabela where estoque < estoque_minimo order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if($linhas > 0){
echo <<<HTML

	<table class="table table-bordered text-nowrap border-bottom dt-responsive" id="tabela">
	<thead> 
	<tr> 
	
	<th>Nome</th>	
	<th>Categoria</th>	
	<th>Valor Venda</th>	
	<th>Estoque</th>
	<th>Unidade</th>		
	<th>Foto</th>	
	<th>Ações</th>
	</tr> 
	</thead> 
	<tbody>	
HTML;

for($i=0; $i<$linhas; $i++){
	$id = $res[$i]['id'];
	$nome = $res[$i]['nome'];
	$categoria = $res[$i]['categoria'];
	$obs = $res[$i]['obs'];
	$valor_compra = $res[$i]['valor_compra'];
	$valor_venda = $res[$i]['valor_venda'];
	$tem_estoque = $res[$i]['tem_estoque'];
	$estoque = $res[$i]['estoque'];
	$unidade = $res[$i]['unidade'];
	$fornecedor = $res[$i]['fornecedor'];
	$estoque_minimo = $res[$i]['estoque_minimo'];
	

	$foto = $res[$i]['foto'];

	$dataF = implode('/', array_reverse(@explode('-', $data)));
	$valorF = @number_format($valor_compra, 2, ',', '.');
	$valor_compraF = @number_format($valor_compra, 2, ',', '.');
	$valor_vendaF = @number_format($valor_venda, 2, ',', '.');


	$query2 = $pdo->query("SELECT * FROM categorias where id = '$categoria'");
	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
	if(@count($res2) > 0){
		$nome_cat = $res2[0]['nome'];
	}else{
		$nome_cat = 'Sem Categoria';
	}


	$query3 = $pdo->query("SELECT * FROM unidade_medida where id = '$unidade'");
	$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
	if(@count($res3) > 0){
		$nome_unidade = $res3[0]['nome'];
	}else{
		$nome_unidade = 'Sem Unidade';
	}



	if($estoque == ""){
		$estoque == 'Não tem estoque';
	}


	//tratamento separa string
	$est = explode(".", $estoque);
	if($est[1] > 0){
		$estoqueF = $estoque;		
	}else{
		$estoqueF = $est[0];
	}

	//tratamento separa string
	$est = explode(".", $estoque_minimo);
	if($est[1] > 0){
		$estoque_minimoF = $estoque_minimo;		
	}else{
		$estoque_minimoF = $est[0];
	}


	if($tem_estoque == 'Sim'){
		if($estoque < $estoque_minimo){
			$classe_estoque = 'red';
			$estoque_minimoF = ' / <span style="color:green">('.$estoque_minimoF.')</span>';
		}else{
			$classe_estoque = '';
			$estoque_minimoF = '';
		}
	}




	$query4 = $pdo->query("SELECT * FROM fornecedores where id = '$nome'");
	$res4 = $query4->fetchAll(PDO::FETCH_ASSOC);
	if(@count($res4) > 0){
		$nome_fornecedor = $res4[0]['nome'];
	}else{
		$nome_fornecedor = 'Sem Fornecedor';
	}


	


echo <<<HTML
<tr style="">

<td>{$nome}</td>
<td>{$nome_cat}</td>
<td>R$ {$valor_vendaF}</td>
<td style="color:{$classe_estoque}">{$estoqueF} {$estoque_minimoF}</td>
<td>{$nome_unidade}</td>
<td class="esc"><img src="images/produtos/{$foto}" width="25px"></td>

<td>


<big><a class="btn btn-primary btn-sm" href="#" onclick="mostrar('{$nome}','{$nome_cat}', '{$nome_fornecedor}', '{$obs}','{$valor_compraF}', '{$valor_vendaF}', '{$estoqueF}', '{$nome_unidade}', '{$foto}')" title="Mostrar Dados"><i class="fa fa-info-circle "></i></a></big>


<a class="btn btn-danger btn-sm" href="#" onclick="saida('{$id}','{$nome}', '{$estoque}')" title="Saída de Produto"><i class="fa fa-sign-out"></i></a>

	<big><a class="btn btn-success btn-sm" href="#" onclick="entrada('{$id}','{$nome}', '{$estoque}')" title="Entrada de Produto"><i class="fa fa-sign-in"></i></a></big>



</td>
</tr>
HTML;

}

}else{
	echo 'Não possui nenhum cadastro!';
}


echo <<<HTML
</tbody>
<small><div align="center" id="mensagem-excluir"></div></small>
</table>

<br>		
			<p align="right" style="margin-top: -10px">
				<span style="margin-right: 10px">Total Itens  <span > {$linhas} </span></span>
				
			</p>

HTML;
?>



<script type="text/javascript">
	$(document).ready( function () {		
    $('#tabela').DataTable({
    	"language" : {
            //"url" : '//cdn.datatables.net/plug-ins/1.13.2/i18n/pt-BR.json'
        },
        "ordering": false,
		"stateSave": true
    });
} );
</script>

<script type="text/javascript">
	
	function mostrar(nome, categoria, fornecedor, obs, valor_compra, valor_venda, estoque, unidade, foto){
		    	
    	$('#titulo_dados').text(nome);
    	$('#categoria_dados').text(categoria);
    	$('#fornecedor_dados').text(fornecedor);
    	$('#obs_dados').text(obs);
    	$('#valor_compra_dados').text(valor_compra);
    	$('#valor_venda_dados').text(valor_venda);
    	$('#estoque_dados').text(estoque);
    	$('#unidade_dados').text(unidade);

    	$('#foto_dados').attr("src", "images/produtos/" + foto);
    	

    	$('#modalDados').modal('show');
	}
	

	</script>




<script type="text/javascript">
	function saida(id, nome, estoque){

		$('#nome_saida').text(nome);
		$('#estoque_saida').val(estoque);
		$('#id_saida').val(id);		

		$('#quantidade_saida').val('');
		$('#motivo_saida').val('');

		$('#modalSaida').modal('show');
	}
</script>


<script type="text/javascript">
	function entrada(id, nome, estoque){

		$('#nome_entrada').text(nome);
		$('#estoque_entrada').val(estoque);
		$('#id_entrada').val(id);

		$('#quantidade_entrada').val('');
		$('#motivo_entrada').val('');		

		$('#modalEntrada').modal('show');
	}
</script>