<?php

require_once __DIR__ . '/../../utils/JsonDataHandler.php';

class TermostatoController{

    // public $datadataPathPath = __DIR__ . '/../../data/dispositivos.json';

    public function index(){
        echo "Hola desde el método GET";
    }

    public function update($json){   
        $handler = new JsonDataHandler(__DIR__ . '/../../data/dispositivos.json');
        $dispositivos = $handler->readAll();

        $mensaje = '';

        if($json['temperatura_objetivo'] < 16 || $json['temperatura_objetivo'] > 22) {
            $mensaje = 'La temperatura no está en el rango admitido.';
            $this->sendResponse($mensaje);
            return;
        }

        foreach($dispositivos as $dispositivo) {
            if ($json['id'] == $dispositivo['id']) {
                if ($json['temperatura_objetivo'] > $dispositivo['temperatura_actual']) {
                    $dispositivo['temperatura_objetivo'] = $json['temperatura_objetivo'];
                    $dispositivo['modo'] = 'calor';
                    $mensaje = 'Modo cambiado a calor';
                } elseif ($json['temperatura_objetivo'] < $dispositivo['temperatura_actual']) {
                    $dispositivo['temperatura_objetivo'] = $json['temperatura_objetivo'];
                    $dispositivo['modo'] = 'frio';
                    $mensaje = 'Modo cambiado a frio'; 
                } elseif ($json['temperatura_objetivo'] = $dispositivo['temperatura_actual']) {
                    $dispositivo['temperatura_objetivo'] = $json['temperatura_objetivo'];
                    $dispositivo['modo'] = 'ventilar';
                    $mensaje = 'Modo cambiado a ventilación';
                }
                $handler->write($dispositivo);
            }
        }
        $this->sendResponse($mensaje);
    }

    private function sendResponse($mensaje) {
        http_response_code(200);
        $json = json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        echo $json;
    }
}

?>