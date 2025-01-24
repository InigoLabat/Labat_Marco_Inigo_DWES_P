<?php
class JsonDataHandler{

    private $dataFilePath;

    public function __construct($dataFilePath){
        $this->dataFilePath=$dataFilePath;
    }

    public function readAll(){
        $json = file_get_contents($this->dataFilePath);
        $data = json_decode($json, true);
        return $data;
    }

    public function write($sensorActualizado) {
        $sensores = $this->readAll();

        foreach ($sensores as $indice => $elemento) {
            if ($elemento['id'] === $sensorActualizado['id']) {
                // Actualizamos los datos del sensor con los nuevos datos
                $sensores[$indice] = $sensorActualizado;
            }
        }
        
        if(file_put_contents($this->dataFilePath, json_encode($sensores, JSON_PRETTY_PRINT))){
            return true;
        }else{
            return false;
        }
        
    }
    
}

?>