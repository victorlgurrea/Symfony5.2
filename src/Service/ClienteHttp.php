<?php

namespace App\Service;

use Exception;
use Symfony\Component\HttpClient\HttpClient;

class ClienteHttp {

    private $clienteHttp;

    public function __construct()
    {
        $this->clienteHttp = HttpClient::create();
    }

    public function obtenerCodigoUrl(string $url){

        $codigo = null;

        try {

            $respuesta = $this->clienteHttp->request("GET", $url);
            $codigo = $respuesta->getStatusCode();
        } catch(Exception $e) {
            $codigo = null;
        }

        return $codigo;

    }
}
