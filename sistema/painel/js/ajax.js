$(document).ready(function() { 
    $('#listar').text("Carregando Dados...");   
    listar();    
} );

function listar(p1, p2, p3, p4, p5, p6){
    $.ajax({
        url: 'paginas/' + pag + "/listar.php",
        method: 'POST',
        data: {p1, p2, p3, p4, p5, p6},
        dataType: "html",

        success:function(result){
            $("#listar").html(result);
            $('#mensagem-excluir').text('');
        }
    });
}

function inserir(){    
    $('#mensagem').text('');
    $('#titulo_inserir').text('Inserir Registro');
    $('#modalForm').modal('show');
    limparCampos();
}


$("#form").submit(function () {

    event.preventDefault();
    var formData = new FormData(this);

    $('#mensagem').text('Salvando...')
    $('#btn_salvar').hide();

    $.ajax({
        url: 'paginas/' + pag + "/salvar.php",
        type: 'POST',
        data: formData,

        success: function (mensagem) {
            $('#mensagem').text('');
            $('#mensagem').removeClass()
            if (mensagem.trim() == "Salvo com Sucesso") {

                $('#btn-fechar').click();
                listar();

                $('#mensagem').text('')          

            } else {

                $('#mensagem').addClass('text-danger')
                $('#mensagem').text(mensagem)
            }

            $('#btn_salvar').show();

        },

        cache: false,
        contentType: false,
        processData: false,

    });

});



function inserir_massa(){    
    $('#mensagem_massa').text('');
    $('#titulo_inserir_massa').text('Inserir Registro em Massa');
    $('#modalForm-massa').modal('show');
    limparCampos();
}


$("#form_massa").submit(function () {

    event.preventDefault();
    var formData = new FormData(this);

    $('#mensagem_massa').text('Salvando...')
    $('#btn_salvar_massa').hide();

    $.ajax({
        url: 'paginas/' + pag + "/salvar-massa.php",
        type: 'POST',
        data: formData,

        success: function (mensagem) {
            $('#mensagem_massa').text('');
            $('#mensagem_massa').removeClass()
            if (mensagem.trim() == "Salvo com Sucesso") {

                $('#btn-fechar-massa').click();
                listar();

                $('#mensagem_massa').text('')          

            } else {

                $('#mensagem_massa').addClass('text-danger')
                $('#mensagem_massa').text(mensagem)
            }

            $('#btn_salvar_massa').show();

        },

        cache: false,
        contentType: false,
        processData: false,

    });

});


function excluir(id){   
    $('#mensagem-excluir').text('Excluindo...')
    
    $.ajax({
        url: 'paginas/' + pag + "/excluir.php",
        method: 'POST',
        data: {id},
        dataType: "html",

        success:function(mensagem){
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




function excluirMultiplos(id){   
    $('#mensagem-excluir').text('Excluindo...')
    
    $.ajax({
        url: 'paginas/' + pag + "/excluir.php",
        method: 'POST',
        data: {id},
        dataType: "html",

        success:function(mensagem){
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



function ativar(id, acao){  
    $.ajax({
        url: 'paginas/' + pag + "/mudar-status.php",
        method: 'POST',
        data: {id, acao},
        dataType: "html",

        success:function(mensagem){
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
  var valorAlterado = $('#'+valor).val();
  valorAlterado = valorAlterado.replace(/\D/g, ""); // Remove todos os não dígitos
  valorAlterado = valorAlterado.replace(/(\d+)(\d{2})$/, "$1,$2"); // Adiciona a parte de centavos
  valorAlterado = valorAlterado.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1."); // Adiciona pontos a cada três dígitos
  valorAlterado = valorAlterado;
  $('#'+valor).val(valorAlterado);
}


function mascara_decimal(valor) {
  var valorAlterado = $('#'+valor).val();
  valorAlterado = valorAlterado.replace(/\D/g, ""); // Remove todos os não dígitos
  valorAlterado = valorAlterado.replace(/(\d+)(\d{1})$/, "$1,$2"); // Adiciona a parte de centavos
  valorAlterado = valorAlterado.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1."); // Adiciona pontos a cada três dígitos
  valorAlterado = valorAlterado;
  $('#'+valor).val(valorAlterado);
}
