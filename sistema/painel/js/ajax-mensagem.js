$(document).ready(function () {

});

function listar(p1, p2, p3, p4, p5, p6) {
    $.ajax({
        url: 'paginas/' + pag + "/listar.php",
        method: 'POST',
        data: { p1, p2, p3, p4, p5, p6 },
        dataType: "html",

        success: function (result) {
            $("#listar").html(result);
            $('#mensagem-excluir').text('');
        }
    });
}

function inserir() {
    $('#mensagem').text('');
    $('#titulo_inserir').text('Enviar Mensagem');
    $('#modalForm').modal('show');
    limparCampos();
}

function limparCampos() {
    $('#nincho').val('');
    $('#mensagem-zap').val('');
    $('#mensagem').val('');
    $('#progress-bar').css('width', '0%').text('');
}

$("#form").submit(function (event) {
    event.preventDefault();

    var formData = new FormData(this);
    var pagina = 1; // Começa da página 1

    $('#mensagem').text('Enviando mensagens...');
    $('#btn_salvar').hide();

    function enviarMensagens() {
        formData.append('pagina', pagina);

        $.ajax({
            url: 'paginas/' + pag + "/enviar.php",
            type: 'POST',
            data: formData,
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                xhr.onprogress = function () {
                    var data = xhr.responseText.split('\n');
                    var last = data[data.length - 2]; // Penúltima linha da resposta
                    try {
                        var progress = JSON.parse(last);

                        // Verifica se é a mensagem final
                        if (progress.status === 'success') {
                            $('#mensagem').text(progress.message);
                            $('#progress-bar').css('width', '100%').text('100%').css('animation', 'none');

                            // Simula o clique no botão para fechar
                            $('#btn-fechar').trigger('click');

                            $('#btn_salvar').show();
                        } else if (progress.progresso !== undefined) {
                            // Atualiza a barra de progresso
                            var percentual = progress.progresso;
                            $('#progress-bar').css('width', percentual + '%').text(percentual + '%');
                        }

                        // Se houver uma próxima página, continua o envio
                        if (progress.proxima_pagina) {
                            pagina = progress.proxima_pagina; // Atualiza a página
                            enviarMensagens(); // Envia a próxima página
                        }
                    } catch (e) {
                        console.error('Erro ao processar progresso:', e);
                    }
                };
                return xhr;
            },
            success: function () {
                $('#btn_salvar').show();
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    }

    enviarMensagens(); // Inicia o envio das mensagens
});


function excluir(id) {
    $('#mensagem-excluir').text('Excluindo...')

    $.ajax({
        url: 'paginas/' + pag + "/excluir.php",
        method: 'POST',
        data: { id },
        dataType: "html",

        success: function (mensagem) {
            if (mensagem.trim() == "Excluído com Sucesso") {
                listar();
                limparCampos()
            } else {
                $('#mensagem-excluir').addClass('text-danger')
                $('#mensagem-excluir').text(mensagem)
            }
        }
    });
}




function excluirMultiplos(id) {
    $('#mensagem-excluir').text('Excluindo...')

    $.ajax({
        url: 'paginas/' + pag + "/excluir.php",
        method: 'POST',
        data: { id },
        dataType: "html",

        success: function (mensagem) {
            if (mensagem.trim() == "Excluído com Sucesso") {
                //listar();
                limparCampos()
            } else {
                $('#mensagem-excluir').addClass('text-danger')
                $('#mensagem-excluir').text(mensagem)
            }
        }
    });
}



function ativar(id, acao) {
    $.ajax({
        url: 'paginas/' + pag + "/mudar-status.php",
        method: 'POST',
        data: { id, acao },
        dataType: "html",

        success: function (mensagem) {
            if (mensagem.trim() == "Alterado com Sucesso") {
                listar();
            } else {
                $('#mensagem-excluir').addClass('text-danger')
                $('#mensagem-excluir').text(mensagem)
            }
        }
    });
}



function mascara_moeda(valor) {
    var valorAlterado = $('#' + valor).val();
    valorAlterado = valorAlterado.replace(/\D/g, ""); // Remove todos os não dígitos
    valorAlterado = valorAlterado.replace(/(\d+)(\d{2})$/, "$1,$2"); // Adiciona a parte de centavos
    valorAlterado = valorAlterado.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1."); // Adiciona pontos a cada três dígitos
    valorAlterado = valorAlterado;
    $('#' + valor).val(valorAlterado);
}


function mascara_decimal(valor) {
    var valorAlterado = $('#' + valor).val();
    valorAlterado = valorAlterado.replace(/\D/g, ""); // Remove todos os não dígitos
    valorAlterado = valorAlterado.replace(/(\d+)(\d{1})$/, "$1,$2"); // Adiciona a parte de centavos
    valorAlterado = valorAlterado.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1."); // Adiciona pontos a cada três dígitos
    valorAlterado = valorAlterado;
    $('#' + valor).val(valorAlterado);
}
