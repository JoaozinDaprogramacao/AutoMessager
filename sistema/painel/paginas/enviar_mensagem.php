<?php
require_once("verificar.php");
$pag = 'enviar_mensagem';

//verificar se ele tem a permissão de estar nessa página
if (@$leads == 'ocultar') {
	echo "<script>window.location='index'</script>";
	exit();
}
?>
<div class="justify-content-between">
	<div class="left-content mt-2 mb-3">
		<a class="btn ripple btn-primary text-white" onclick="inserir()" type="button"><i class="fe fe-plus me-2"></i> Enviar Mensagem</a>
	</div>
</div>

<style>
	#progress-bar {
		width: 0%;
		height: 25px;
		background-color: #4caf50;
		color: white;
		text-align: center;
		line-height: 25px;
		border-radius: 0px !important;
		position: relative;
		overflow: hidden;
		transition: width 0.3s ease-in-out;
	}

	#progress-bar::after {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		height: 100%;
		width: 100%;
		background: linear-gradient(90deg, rgba(255, 255, 255, 0.3) 25%, rgba(255, 255, 255, 0) 50%, rgba(255, 255, 255, 0.3) 75%);
		animation: loading 1s infinite linear;
	}

	@keyframes loading {
		from {
			transform: translateX(-100%);
		}

		to {
			transform: translateX(100%);
		}
	}
</style>


<!-- Modal Perfil -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_inserir"></span></h4>
				<button id="btn-fechar" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-3 mb-2">

							<label>Nincho</label>
							<select class="form-select" name="nincho" id="nincho" style="width:100%">
								<option value="0">Escolher Nincho</option>
								<?php
								$query = $pdo->query("SELECT * from ninchos order by id asc");
								$res = $query->fetchAll(PDO::FETCH_ASSOC);
								$linhas = @count($res);
								if ($linhas > 0) {
									for ($i = 0; $i < $linhas; $i++) { ?>
										<option value="<?php echo $res[$i]['id'] ?>"><?php echo $res[$i]['nome'] ?></option>
								<?php }
								} ?>
							</select>
						</div>
						<div class="col-md-3 mb-2">
							<label for="mensagem">Mensagem</label>
							<textarea class="form-control" placeholder="Escreva o script de vendas" name="mensagem" id="mensagem-zap" rows="5" cols="10"></textarea>
						</div>
					</div>

				</div>

				<input type="hidden" class="form-control" id="id" name="id">

				<br>
				<small>
					<div id="mensagem" align="center"></div>
					<div id="progress-container" style="width: 100%; background-color: #ddd; border-radius: 0px; overflow: hidden;">
						<div id="progress-bar">0%</div>
					</div>
				</small>
				<div class="modal-footer">
					<button type="submit" id="btn_salvar" class="btn btn-primary">Salvar</button>
				</div>
			</form>
		</div>
	</div>
</div>


<script type="text/javascript">
	var pag = "<?= $pag ?>"
</script>
<script src="js/ajax-mensagem.js"></script>
<script src="js/ajax-massa.js"></script>


<script type="text/javascript">
	function buscarNin(id) {
		$('#nin').val(id);
		listar(id)
	}
</script>

<script type="text/javascript">
	$("#form-arquivos").submit(function() {
		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: 'paginas/' + pag + "/arquivos.php",
			type: 'POST',
			data: formData,

			success: function(mensagem) {
				$('#mensagem-arquivo').text('');
				$('#mensagem-arquivo').removeClass()
				if (mensagem.trim() == "Inserido com Sucesso") {
					//$('#btn-fechar-arquivos').click();
					$('#nome-arq').val('');
					$('#arquivo_conta').val('');
					$('#target-arquivos').attr('src', 'images/arquivos/sem-foto.png');
					listarArquivos();
				} else {
					$('#mensagem-arquivo').addClass('text-danger')
					$('#mensagem-arquivo').text(mensagem)
				}

			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});
</script>

<script type="text/javascript">
	function listarArquivos() {
		var id = $('#id-arquivo').val();
		$.ajax({
			url: 'paginas/' + pag + "/listar-arquivos.php",
			method: 'POST',
			data: {
				id
			},
			dataType: "text",

			success: function(result) {
				$("#listar-arquivos").html(result);
			}
		});
	}
</script>




<script type="text/javascript">
	function carregarImgArquivos() {
		var target = document.getElementById('target-arquivos');
		var file = document.querySelector("#arquivo_conta").files[0];

		var arquivo = file['name'];
		resultado = arquivo.split(".", 2);

		if (resultado[1] === 'pdf') {
			$('#target-arquivos').attr('src', "images/pdf.png");
			return;
		}

		if (resultado[1] === 'rar' || resultado[1] === 'zip') {
			$('#target-arquivos').attr('src', "images/rar.png");
			return;
		}

		if (resultado[1] === 'doc' || resultado[1] === 'docx' || resultado[1] === 'txt') {
			$('#target-arquivos').attr('src', "images/word.png");
			return;
		}


		if (resultado[1] === 'xlsx' || resultado[1] === 'xlsm' || resultado[1] === 'xls') {
			$('#target-arquivos').attr('src', "images/excel.png");
			return;
		}


		if (resultado[1] === 'xml') {
			$('#target-arquivos').attr('src', "images/xml.png");
			return;
		}



		var reader = new FileReader();

		reader.onloadend = function() {
			target.src = reader.result;
		};

		if (file) {
			reader.readAsDataURL(file);

		} else {
			target.src = "";
		}
	}
</script>


<script type="text/javascript">
	function totalizar() {
		valor = $('#valor-baixar').val();
		desconto = $('#valor-desconto').val();
		juros = $('#valor-juros').val();
		multa = $('#valor-multa').val();
		taxa = $('#valor-taxa').val();

		valor = valor.replace(",", ".");
		desconto = desconto.replace(",", ".");
		juros = juros.replace(",", ".");
		multa = multa.replace(",", ".");
		taxa = taxa.replace(",", ".");

		if (valor == "") {
			valor = 0;
		}

		if (desconto == "") {
			desconto = 0;
		}

		if (juros == "") {
			juros = 0;
		}

		if (multa == "") {
			multa = 0;
		}

		if (taxa == "") {
			taxa = 0;
		}

		subtotal = parseFloat(valor) + parseFloat(juros) + parseFloat(taxa) + parseFloat(multa) - parseFloat(desconto);


		console.log(subtotal)

		$('#subtotal').val(subtotal);

	}

	function calcularTaxa() {
		pgto = $('#saida-baixar').val();
		valor = $('#valor-baixar').val();
		$.ajax({
			url: 'paginas/receber/calcular_taxa.php',
			method: 'POST',
			data: {
				valor,
				pgto
			},
			dataType: "html",

			success: function(result) {
				$('#valor-taxa').val(result);
				totalizar();
			}
		});


	}
</script>




<script type="text/javascript">
	$("#form-baixar").submit(function() {
		event.preventDefault();
		var formData = new FormData(this);

		var id_conta = $('#id_contas').val();

		$.ajax({
			url: 'paginas/receber/baixar.php',
			type: 'POST',
			data: formData,

			success: function(mensagem) {

				$('#mensagem-baixar').text('');
				$('#mensagem-baixar').removeClass()
				if (mensagem.trim() == "Baixado com Sucesso") {
					$('#btn-fechar-baixar').click();
					listarDebitos(id_conta);

				} else {
					$('#mensagem-baixar').addClass('text-danger')
					$('#mensagem-baixar').text(mensagem)
				}

			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});
</script>





<script>
	function limpa_formulário_cep() {
		//Limpa valores do formulário de cep.
		document.getElementById('endereco').value = ("");
		document.getElementById('bairro').value = ("");
		document.getElementById('cidade').value = ("");
		document.getElementById('estado').value = ("");
		//document.getElementById('ibge').value=("");
	}

	function meu_callback(conteudo) {
		if (!("erro" in conteudo)) {
			//Atualiza os campos com os valores.
			document.getElementById('endereco').value = (conteudo.logradouro);
			document.getElementById('bairro').value = (conteudo.bairro);
			document.getElementById('cidade').value = (conteudo.localidade);
			document.getElementById('estado').value = (conteudo.uf);
			//document.getElementById('ibge').value=(conteudo.ibge);
		} //end if.
		else {
			//CEP não Encontrado.
			limpa_formulário_cep();
			alert("CEP não encontrado.");
		}
	}

	function pesquisacep(valor) {

		//Nova variável "cep" somente com dígitos.
		var cep = valor.replace(/\D/g, '');

		//Verifica se campo cep possui valor informado.
		if (cep != "") {

			//Expressão regular para validar o CEP.
			var validacep = /^[0-9]{8}$/;

			//Valida o formato do CEP.
			if (validacep.test(cep)) {

				//Preenche os campos com "..." enquanto consulta webservice.
				document.getElementById('endereco').value = "...";
				document.getElementById('bairro').value = "...";
				document.getElementById('cidade').value = "...";
				document.getElementById('estado').value = "...";
				//document.getElementById('ibge').value="...";

				//Cria um elemento javascript.
				var script = document.createElement('script');

				//Sincroniza com o callback.
				script.src = 'https://viacep.com.br/ws/' + cep + '/json/?callback=meu_callback';

				//Insere script no documento e carrega o conteúdo.
				document.body.appendChild(script);

			} //end if.
			else {
				//cep é inválido.
				limpa_formulário_cep();
				alert("Formato de CEP inválido.");
			}
		} //end if.
		else {
			//cep sem valor, limpa formulário.
			limpa_formulário_cep();
		}
	};
</script>



<script type="text/javascript">
	function buscar() {
		var ina = $("#ina").val();
		listar(ina);
	}
</script>