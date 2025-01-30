<?php 
$tabela = 'produtos';
require_once("../../../conexao.php");

$cat = @$_POST['p1'];

if($cat == ""){
	$filtrar = "";
}else{
	$filtrar = " where categoria = '$cat'";
}

$query = $pdo->query("SELECT * from $tabela $filtrar order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if($linhas > 0){
echo <<<HTML

	<table class="table table-bordered text-nowrap border-bottom dt-responsive" id="tabela">
	<thead> 
	<tr> 
	<th align="center" width="5%" class="text-center">Selecionar</th>
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
	$ativo = $res[$i]['ativo'];
	$valor_sem_promocao = $res[$i]['valor_sem_promocao'];

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


	if($ativo == 'Sim'){
	$icone = 'fa-check-square';
	$titulo_link = 'Desativar Usuário';
	$acao = 'Não';
	$classe_ativo = '';
	}else{
		$icone = 'fa-square-o';
		$titulo_link = 'Ativar Usuário';
		$acao = 'Sim';
		$classe_ativo = '#c4c4c4';
	}
	


echo <<<HTML
<tr style="">
<td align="center">
<div class="custom-checkbox custom-control">
<input type="checkbox" class="custom-control-input" id="seletor-{$id}" onchange="selecionar('{$id}')">
<label for="seletor-{$id}" class="custom-control-label mt-1 text-dark"></label>
</div>
</td>
<td style="color:{$classe_ativo}">{$nome}</td>
<td style="color:{$classe_ativo}">{$nome_cat}</td>
<td style="color:{$classe_ativo}">R$ {$valor_vendaF}</td>
<td  style="color:{$classe_estoque}">{$estoqueF} {$estoque_minimoF}</td>
<td style="color:{$classe_ativo}">{$nome_unidade}</td>
<td style="color:{$classe_ativo}" class="esc"><img src="images/produtos/{$foto}" width="25px"></td>

<td>
	<big><a class="btn btn-info btn-sm" href="#" onclick="editar('{$id}','{$nome}','{$categoria}', '{$fornecedor}', '{$obs}','{$valor_compra}', '{$valor_venda}', '{$tem_estoque}', '{$estoque}','{$estoque_minimo}', '{$foto}', '{$unidade}', '{$valor_sem_promocao}')" title="Editar Dados"><i class="fa fa-edit "></i></a></big>

	<div class="dropdown" style="display: inline-block;">                      
                        <a class="btn btn-danger btn-sm" href="#" aria-expanded="false" aria-haspopup="true" data-bs-toggle="dropdown" class="dropdown"><i class="fa fa-trash "></i> </a>
                        <div  class="dropdown-menu tx-13">
                        <div style="width: 240px; padding:15px 5px 0 10px;" class="dropdown-item-text">
                        <p>Confirmar Exclusão? <a href="#" onclick="excluir('{$id}')"><span class="text-danger">Sim</span></a></p>
                        </div>
                        </div>
                        </div>


<big><a class="btn btn-primary btn-sm" href="#" onclick="mostrar('{$nome}','{$nome_cat}', '{$nome_fornecedor}', '{$obs}','{$valor_compraF}', '{$valor_vendaF}', '{$estoqueF}', '{$nome_unidade}', '{$foto}')" title="Mostrar Dados"><i class="fa fa-info-circle "></i></a></big>



<a class="btn btn-danger btn-sm" href="#" onclick="saida('{$id}','{$nome}', '{$estoque}')" title="Saída de Produto"><i class="fa fa-sign-out"></i></a>

	<big><a class="btn btn-success btn-sm" href="#" onclick="entrada('{$id}','{$nome}', '{$estoque}')" title="Entrada de Produto"><i class="fa fa-sign-in"></i></a></big>

<big><a class="btn btn-success btn-sm" href="#" onclick="ativar('{$id}', '{$acao}')" title="{$titulo_link}"><i class="fa {$icone} "></i></a></big>

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
	function editar(id, nome, categoria, fornecedor, obs, valor_compra, valor_venda, tem_estoque, estoque, estoque_minimo, foto, unidade, valor_sem_promocao ){

		$('#mensagem').text('');
    	$('#titulo_inserir').text('Editar Registro');

    	$('#id').val(id);
    	$('#nome').val(nome); 
    	$('#categoria').val(categoria).change();
    	$('#fornecedor').val(fornecedor).change();
    	$('#obs').val(obs);  
    	$('#tem_estoque').val(tem_estoque).change();

    	$('#valor_compra').val(valor_compra);
    	$('#valor_venda').val(valor_venda);  
    	$('#estoque').val(estoque);
    	$('#estoque_minimo').val(estoque_minimo);
    	$('#unidade').val(unidade).change();

    	$('#valor_sem_promocao').val(valor_sem_promocao);

    	mascara_moeda('valor_compra'); 
    	mascara_moeda('valor_venda'); 
    	mascara_moeda('valor_sem_promocao'); 

    	mascara_decimal('estoque'); 
    	mascara_decimal('estoque_minimo'); 
    	
    	$('#foto').val('');
    	$('#target').attr("src", "images/produtos/" + foto);
    

    	$('#modalForm').modal('show');
	}


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




	function limparCampos(){
		$('#id').val('');
    	$('#nome').val(''); 
    	$('#categoria').val('0');
    	$('#fornecedor').val('0');
    	$('#tem_estoque').val('Sim');  
    	$('#valor_compra').val('');
    	$('#valor_venda').val('');  
    	$('#estoque').val('');
    	$('#estoque_minimo').val('');
    	$('#unidade').val('0');
    	$('#foto').val('');
    	$('#obs').val('');


    	$('#target').attr("src", "images/produtos/sem-foto.png");
    	
    	$('#ids').val('');
    	$('#btn-deletar').hide();	
	}

	function selecionar(id){

		var ids = $('#ids').val();

		if($('#seletor-'+id).is(":checked") == true){
			var novo_id = ids + id + '-';
			$('#ids').val(novo_id);
		}else{
			var retirar = ids.replace(id + '-', '');
			$('#ids').val(retirar);
		}

		var ids_final = $('#ids').val();
		if(ids_final == ""){
			$('#btn-deletar').hide();
		}else{
			$('#btn-deletar').show();
		}
	}

	function deletarSel(){
		var ids = $('#ids').val();
		var id = ids.split("-");
		
		for(i=0; i<id.length-1; i++){
			excluirMultiplos(id[i]);			
		}

		setTimeout(() => {
		  	listar();	
		}, 1000);

		limparCampos();
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