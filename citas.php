<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/citas.class.php';

$_respuestas = new respuestas;
$citas = new citas;


if($_SERVER['REQUEST_METHOD'] == "GET"){

    if(isset($_GET["page"])){
        $pagina = $_GET["page"];
        $citas = $citas->listaCitas($pagina);
        header("Content-Type: application/json");
        echo json_encode($citas);
        http_response_code(200);
    }else if(isset($_GET['id'])){
        $citaid = $_GET['id'];
        $datosCitas = $citas->obtenerCita($citaid);
        header("Content-Type: application/json");
        echo json_encode($datosCitas);
        http_response_code(200);
    }else if(isset($_GET['idPacient'])){
        $idPaciente = $_GET["idPacient"];
        $citas = $citas->listaCitasByPaciente($idPaciente);
        header("Content-Type: application/json");
        echo json_encode($citas);
        http_response_code(200);
    }

}else if($_SERVER['REQUEST_METHOD'] == "POST"){
    //recibimos los datos enviados
    $postBody = file_get_contents("php://input");
    //enviamos los datos al manejador
    $datosArray = $citas->post($postBody);
    //delvovemos una respuesta 
     header('Content-Type: application/json');
     if(isset($datosArray["result"]["error_id"])){
         $responseCode = $datosArray["result"]["error_id"];
         http_response_code($responseCode);
     }else{
         http_response_code(200);
     }
     echo json_encode($datosArray);
    
}else if($_SERVER['REQUEST_METHOD'] == "PUT"){
      //recibimos los datos enviados
      $postBody = file_get_contents("php://input");
      //enviamos datos al manejador
      $datosArray = $citas->put($postBody);
        //delvovemos una respuesta 
     header('Content-Type: application/json');
     if(isset($datosArray["result"]["error_id"])){
         $responseCode = $datosArray["result"]["error_id"];
         http_response_code($responseCode);
     }else{
         http_response_code(200);
     }
     echo json_encode($datosArray);

}else if($_SERVER['REQUEST_METHOD'] == "DELETE"){

        $headers = getallheaders();
        var_dump($headers);
        if(isset($headers["idCita"])){
            //recibimos los datos enviados por el header
            $send = [
                "idCita" =>$headers["idCita"]
            ];
            $postBody = json_encode($send);
        }else{
            //recibimos los datos enviados
            $postBody = file_get_contents("php://input");
        }
        
        //enviamos datos al manejador
        $datosArray = $citas->delete($postBody);
        //delvovemos una respuesta 
        header('Content-Type: application/json');
        if(isset($datosArray["result"]["error_id"])){
            $responseCode = $datosArray["result"]["error_id"];
            http_response_code($responseCode);
        }else{
            http_response_code(200);
        }
        echo json_encode($datosArray);
       

}else{
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}


?>