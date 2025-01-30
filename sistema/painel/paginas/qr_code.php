<?php
require_once("verificar.php");
$pag = 'qr_code';

//verificar se ele tem a permissão de estar nessa página
if (@$qr_code == 'ocultar') {
	echo "<script>window.location='index'</script>";
	exit();
}
?>
<script>
	var pag = "<?php echo $pag; ?>"; // Passa a variável PHP para o JavaScript

	function pegarQrCode() {
		console.log(pag); // Exibe a variável 'pag' no console para depuração
		$.ajax({
			url: 'paginas/' + pag + "/get.php", // Usa a variável 'pag'
			type: 'GET',

			success: function(mensagem) {
				console.log('Resposta do servidor:', mensagem); // Log da resposta para depuração
				if (mensagem.trim().startsWith("data:image")) {
					$('#qrCodeImage').attr('src', mensagem.trim()); // Atribui a imagem Base64 ao src
					$('#qrCodeModal').modal('show'); // Mostra o modal
				} else {
					$('#mensagem').addClass('text-danger');
					$('#mensagem').text("Erro ao carregar o QR Code.");
				}
			},


			cache: false,
			contentType: false,
			processData: false,
		});
	}
</script>


<!-- Botão para abrir a ação -->
<div class="justify-content-between">
	<div class="left-content mt-2 mb-3">
		<a class="btn ripple btn-primary text-white" onclick="pegarQrCode()" type="button">
			<i class="fe fe-plus me-2"></i> Adicionar <?php echo ucfirst($pag); ?>
		</a>
	</div>
</div>

<!-- Modal para exibir o QR Code -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="qrCodeModalLabel">QR Code</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body text-center">
				<img id="qrCodeImage" src="" alt="QR Code" style="max-width: 100%; height: auto;">
			</div>
		</div>
	</div>
</div>