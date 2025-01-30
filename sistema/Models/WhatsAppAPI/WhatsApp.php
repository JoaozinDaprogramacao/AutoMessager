<?php

class WhatsApp
{

    private $API_KEY;
    private $URL;
    private $id;

    const SESSION = "session";
    const CLIENT = "client";

    public function __construct()
    {
        $this->API_KEY = "inovasite";
        $this->URL = "http://localhost:3000";
    }

    public function criarSessao($id)
    {
        // Inicializa a sessão cURL
        $ch = curl_init();

        // Corrigindo a URL
        $url = rtrim($this->URL, '/') . '/' . self::SESSION . "/start/$id";  // Garante a barra correta entre a base e o caminho

        // Configurações cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);  // Define o método GET
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-api-key: ' . $this->API_KEY  // Define o cabeçalho como JSON
        ]);

        // Executa a requisição
        $response = curl_exec($ch);

        // Verifica se ocorreu algum erro na requisição
        if (curl_errno($ch)) {
            echo 'Erro cURL: ' . curl_error($ch);
        }


        // Fecha a sessão cURL
        curl_close($ch);
    }

    public function deletarSessao($id)
    {
        // Inicializa a sessão cURL
        $ch = curl_init();

        // Corrigindo a URL
        $url = rtrim($this->URL, '/') . '/' . self::SESSION . "/terminate/$id";  // Garante a barra correta entre a base e o caminho

        // Configurações cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);  // Define o método GET
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-api-key: ' . $this->API_KEY  // Define o cabeçalho como JSON
        ]);

        // Executa a requisição
        $response = curl_exec($ch);

        // Verifica se ocorreu algum erro na requisição
        if (curl_errno($ch)) {
            echo 'Erro cURL: ' . curl_error($ch);
        }

        // Fecha a sessão cURL
        curl_close($ch);
    }

    public function getQrCode($id)
    {
        $ch = curl_init();
        $url = rtrim($this->URL, '/') . '/' . self::SESSION . "/qr/$id" . '/' . "image";

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-api-key: ' . $this->API_KEY
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return 'Erro cURL: ' . curl_error($ch);
        }

        curl_close($ch);

        // Converte a imagem binária para Base64
        $base64Image = 'data:image/png;base64,' . base64_encode($response);

        return $base64Image; // Retorna a imagem codificada em Base64
    }
    public function enviaMensagem($id, $mensagem, $contato)
    {
        // Inicializa a sessão cURL
        $ch = curl_init();

        // Corrigindo a URL
        $url = rtrim($this->URL, '/') . '/' . self::CLIENT . "/sendMessage/$id";

        // Dados a serem enviados no corpo da requisição
        $data = [
            "chatId" => $contato . "@c.us",
            "contentType" => "string",
            "content" => $mensagem
        ];

        // Configurações cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); // Define o método como POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Adiciona o corpo JSON
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-api-key: ' . $this->API_KEY, // Adiciona a chave da API
            'Content-Type: application/json', // Define o tipo do conteúdo como JSON
        ]);

        // Executa a requisição
        $response = curl_exec($ch);

        // Verifica se ocorreu algum erro na requisição
        if (curl_errno($ch)) {
            echo 'Erro cURL: ' . curl_error($ch);
        }

        // Fecha a sessão cURL
        curl_close($ch);

        return $response; // Retorna a resposta da requisição
    }
}
