<?php
$tabela = 'leads';
require '../../../../vendor/autoload.php';
require_once("../../../conexao.php");
require_once("modal/leads.php");

use PhpOffice\PhpSpreadsheet\IOFactory;

@session_start();
$id_usuario = @$_SESSION['id'];
$nincho = @$_POST['nincho-massa'];  // A variável nincho deve ser extraída corretamente

$arquivo = $_FILES['planilha']['tmp_name']; // O arquivo enviado

// Verificar se o arquivo foi carregado corretamente
if (!$arquivo) {
	echo "Erro: Nenhum arquivo foi enviado!";
	exit;
}

$erros = []; // Array para armazenar erros

try {
	$spreadsheet = IOFactory::load($arquivo);
	$sheet = $spreadsheet->getActiveSheet(); // Obtém a aba ativa da planilha

	// Itera sobre as linhas da planilha
	foreach ($sheet->getRowIterator() as $rowIndex => $row) {
		// Ignora a primeira linha (cabeçalho)
		if ($rowIndex == 1) continue;

		// Obtemos as células de cada coluna
		$colunas = [];
		foreach ($row->getCellIterator() as $cell) {
			$colunas[] = $cell->getValue();
		}

		// Verifica se as colunas possuem dados válidos
		if (count($colunas) < 3) {
			$erros[] = "Linha $rowIndex com dados incompletos.";
			continue;
		}

		// A linha contém as informações que você vai processar
		$nomeEmpresa = $colunas[0];
		$celular = $colunas[1];
		$endereco = $colunas[2];

		// Instanciando a classe Lead para processar os dados
		$lead = new Lead("", $nincho, $nomeEmpresa, $celular, "", "", "", "", "", "");

		// Processa o celular
		$celular = "55" . $celular;
		$celular = $lead->validarNumeroCelular($celular);

		// Se o celular for inválido, pula para o próximo
		if ($celular === "inválido") {
			$erros[] = "Celular inválido para o lead: $nomeEmpresa (Linha $rowIndex).";
			continue;
		}

		// Processa o endereço
		$endereco_traduzido = $lead->traduzEndereco($endereco);

		// Se o endereço for inválido, atribui "inválido" ao campo de endereço
		if (in_array("inválido", $endereco_traduzido)) {
			$endereco_traduzido = ["inválido", "inválido", "inválido", "inválido", "inválido", "inválido"]; // Define todos os valores como "inválido"
		}

		// Limita o valor do CEP a 10 caracteres para evitar o erro de truncamento
		$cep = substr($endereco_traduzido[5], 0, 10);  // Limita o valor do CEP a 10 caracteres

		// Agora, adiciona a lógica para inserir no banco de dados
		try {
			$query = $pdo->prepare("INSERT INTO $tabela (nincho, nome, celular, data_cad, endereco, usuario, numero, bairro, cidade, estado, cep) 
                VALUES (:nincho, :nome, :celular, curDate(), :endereco, :usuario, :numero, :bairro, :cidade, :estado, :cep)");

			// Bind de variáveis
			$query->bindValue(":nincho", $nincho);
			$query->bindValue(":nome", $nomeEmpresa);
			$query->bindValue(":celular", $celular);
			$query->bindValue(":endereco", $endereco_traduzido[0]);
			$query->bindValue(":numero", $endereco_traduzido[1]);
			$query->bindValue(":bairro", $endereco_traduzido[2]);
			$query->bindValue(":cidade", $endereco_traduzido[3]);
			$query->bindValue(":estado", $endereco_traduzido[4]);
			$query->bindValue(":cep", $cep); // Usando o CEP limitado a 10 caracteres
			$query->bindValue(":usuario", $id_usuario);

			// Executa a query
			if (!$query->execute()) {
				echo "";
			}
		} catch (Exception $e) {
			echo "";
		}
	}

	echo "Salvo com Sucesso";
} catch (Exception $e) {
	echo 'Erro ao ler o arquivo: ' . $e->getMessage();
}
