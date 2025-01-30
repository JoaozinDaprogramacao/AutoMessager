<?php
require '../../../vendor/autoload.php'; // Certifique-se de ter o PhpSpreadsheet instalado via Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font;

// Criação da planilha
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Cabeçalhos das colunas
$headers = ['Nome', 'Celular', 'Data Cadastro', 'Endereço', 'Bairro', 'Cidade', 'Estado', 'CEP'];

// Adicionar cabeçalhos na primeira linha
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', strtoupper($header));
    $col++;
}

// Estilizar cabeçalhos
$sheet->getStyle('A1:H1')->getFont()->setBold(true)->setItalic(true)->setColor(new Color(Color::COLOR_BLUE));
$sheet->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('FFD6EAF8'); // Cor azul clara

// Consultar o banco de dados
@session_start();
require_once("../verificar.php");
$mostrar_registros = @$_SESSION['registros'];
$id_usuario = @$_SESSION['id'];
require_once("../../conexao.php");

if ($mostrar_registros == 'Não') {
    $query = $pdo->query("SELECT * FROM leads WHERE usuario = '$id_usuario' ORDER BY id DESC");
} else {
    $query = $pdo->query("SELECT * FROM leads ORDER BY id DESC");
}
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);

if ($linhas > 0) {
    $rowNum = 2; // Começa na segunda linha
    foreach ($res as $row) {
        $data_cad = implode('/', array_reverse(explode('-', $row['data_cad']))); // Formatar a data

        $sheet->setCellValue("A$rowNum", $row['nome']);
        $sheet->setCellValue("B$rowNum", $row['celular']);
        $sheet->setCellValue("C$rowNum", $data_cad);
        $sheet->setCellValue("D$rowNum", $row['endereco']);
        $sheet->setCellValue("E$rowNum", $row['bairro']);
        $sheet->setCellValue("F$rowNum", $row['cidade']);
        $sheet->setCellValue("G$rowNum", $row['estado']);
        $sheet->setCellValue("H$rowNum", $row['cep']);
        $rowNum++;
    }
}

// Ajustar largura das colunas
foreach (range('A', 'H') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Exportar para o formato .xlsx
$arquivo = "rel-leads.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $arquivo . '"');
header('Cache-Control: max-age=0');

// Salvar o arquivo e enviar para download
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
