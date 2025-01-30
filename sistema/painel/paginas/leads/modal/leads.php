<?php

class Lead
{
    private $id;
    private $nincho;
    private $nome;
    private $celular;
    private $endereco;
    private $numero;
    private $bairro;
    private $cidade;
    private $estado;
    private $cep;

    public function __construct(
        $id,
        $nincho,
        $nome,
        $celular,
        $endereco,
        $numero,
        $bairro,
        $cidade,
        $estado,
        $cep
    ) {
        $this->id = $id;
        $this->nincho = $nincho;
        $this->nome = $nome;
        $this->celular = $celular;
        $this->endereco = $endereco;
        $this->numero = $numero;
        $this->bairro = $bairro;
        $this->cidade = $cidade;
        $this->estado = $estado;
        $this->cep = $cep;
    }

    public function construtorByString($linha)
    {
        $colunas = explode(";", $linha);

        if (count($colunas) < 3) {
            throw new Exception("Linha de entrada inválida.");
        }

        $nomeEmpresa = trim($colunas[0]);
        $celular = trim($colunas[1]);
        $endereco = trim($colunas[2]);

        $celular = "55" . $celular;
        $celular = $this->validarNumeroCelular($celular);

        $this->nome = $nomeEmpresa;
        $this->celular = $celular;

        $endereco_traduzido = $this->traduzEndereco($endereco);

        if (count($endereco_traduzido) !== 5) {
            throw new Exception("Formato de endereço inválido.");
        }

        [$this->endereco, $this->numero, $this->bairro, $this->cidade, $this->estado, $this->cep] = $endereco_traduzido;
    }

    public function validarNumeroCelular($numero)
    {
        $numero = preg_replace('/\D/', '', $numero);

        if (!ctype_digit($numero) || strlen($numero) < 12) {
            return "inválido";
        }

        $codigoPais = substr($numero, 0, 2);
        $ddd = substr($numero, 2, 2);
        $numeroCelular = substr($numero, 4);

        if (strlen($numeroCelular) == 9 && $numeroCelular[0] == '9') {
            $numeroCelular = substr($numeroCelular, 1);
        }

        if (strlen($numeroCelular) != 8) {
            return "inválido";
        }

        return $codigoPais . $ddd . $numeroCelular;
    }

    public function traduzEndereco($enderecoCompleto)
    {
        if (!strpos($enderecoCompleto, '-') || !strpos($enderecoCompleto, ',')) {
            return ["inválido", "inválido", "inválido", "inválido", "inválido"];
        }

        list($ruaENumero, $resto) = explode('-', $enderecoCompleto, 2);
        $ruaENumero = trim($ruaENumero);
        $resto = trim($resto);

        if (!strpos($ruaENumero, ',') || !strpos($resto, ',')) {
            return ["inválido", "inválido", "inválido", "inválido", "inválido"];
        }

        list($rua, $numero) = explode(',', $ruaENumero, 2);
        $rua = trim($rua);
        $numero = trim($numero);

        list($bairro, $cidadeEstadoCep) = explode(',', $resto, 2);
        $bairro = trim($bairro);
        $cidadeEstadoCep = trim($cidadeEstadoCep);

        if (!strpos($cidadeEstadoCep, ' - ') || !strpos($cidadeEstadoCep, ',')) {
            return ["inválido", "inválido", "inválido", "inválido", "inválido"];
        }

        list($cidade, $estadoCep) = explode(' - ', $cidadeEstadoCep, 2);
        $cidade = trim($cidade);

        list($estado, $cep) = explode(',', $estadoCep, 2);
        $estado = trim($estado);
        $cep = trim($cep);

        return [$rua, $numero, $bairro, $cidade, $estado, $cep];
    }

    public function getNincho()
    {
        return $this->nincho;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function getCelular()
    {
        return $this->celular;
    }

    public function getEndereco()
    {
        return $this->endereco;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function getBairro()
    {
        return $this->bairro;
    }

    public function getCidade()
    {
        return $this->cidade;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getCep()
    {
        return $this->cep;
    }

    public function getId()
    {
        return $this->id;
    }
}
