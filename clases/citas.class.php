<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class citas extends conexion {

    private $table = "citas";
    private $CitaId = "";
    private $PacienteId = "";
    private $Fecha = "";
    private $HoraInicio = "";
    private $HoraFin = "";
    private $Estado = "";
    private $telefono = "";
    private $Motivo = "0000-00-00";

    public function listaCitas($pagina = 1){
        $inicio  = 0 ;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) +1 ;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT * FROM " . $this->table . " INNER JOIN pacientes ON pacientes.PacienteId = citas.PacienteId ORDER BY CitaId DESC limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    public function listaCitasByPaciente($idPaciente){
        $query = "SELECT * FROM " . $this->table . " INNER JOIN pacientes ON pacientes.PacienteId = citas.PacienteId WHERE citas.PacienteId = ". $idPaciente ." ORDER BY citas.CitaId DESC";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    public function obtenerCita($id){
        $query = "SELECT * FROM " . $this->table . " WHERE CitaId = '$id'";
        return parent::obtenerDatos($query);

    }

    public function post($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);

        if(!isset($datos['idPacient'])){
            return $_respuestas->error_400();
        }else{
            $this->PacienteId = $datos['idPacient'];
            if(isset($datos['Fecha'])) { $this->Fecha = $datos['Fecha']; }
            if(isset($datos['HoraInicio'])) { $this->HoraInicio = $datos['HoraInicio']; }
            if(isset($datos['HoraFIn'])) { $this->HoraFIn = $datos['HoraFIn']; }
            if(isset($datos['Motivo'])) { $this->Motivo = $datos['Motivo']; }
            $resp = $this->insertarCita();
            if($resp){
                $respuesta = $_respuestas->response;
                $respuesta["result"] = array(
                    "PacienteId" => $resp
                );
                return $respuesta;
            }else{
                return $_respuestas->error_500();
            }
        }
    }

    private function insertarCita(){
        $query = "INSERT INTO " . $this->table . " (PacienteId, Fecha, HoraInicio, HoraFIn, Estado, Motivo)
        values
        ('" . $this->PacienteId . "','" . $this->Fecha . "','" . $this->HoraInicio ."','" . $this->HoraFIn . "', 'Confirmada', '"  . $this->Motivo . "')"; 
        $resp = parent::nonQueryId($query);
        if($resp){
             return $resp;
        }else{
            return 0;
        }
    }

    public function put($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);
        echo $datos['CitaId'];

        if(!isset($datos['CitaId'])){
            return $_respuestas->error_400();
        }else{
            $this->CitaId = $datos['CitaId'];
            if(isset($datos['Fecha'])) { $this->Fecha = $datos['Fecha']; }
            if(isset($datos['HoraInicio'])) { $this->HoraInicio = $datos['HoraInicio']; }
            if(isset($datos['HoraFIn'])) { $this->HoraFIn = $datos['HoraFIn']; }
            if(isset($datos['Motivo'])) { $this->Motivo = $datos['Motivo']; }

            $resp = $this->modificarCita();
            if($resp){
                $respuesta = $_respuestas->response;
                $respuesta["result"] = array(
                    "CitaId" => $this->CitaId
                );
                return $respuesta;
            }else{
                return $_respuestas->error_500();
            }
        }
    }

    private function modificarCita(){
        $query = "UPDATE " . $this->table . " SET Fecha = '" . $this->Fecha . "',HoraInicio = '" . $this->HoraInicio . "', HoraFIn = '" . $this->HoraFIn . "', Motivo = '" . $this->Motivo . "' WHERE CitaId = '" . $this->CitaId . "'"; 
        $resp = parent::nonQuery($query);
        if($resp >= 1){
             return $resp;
        }else{
            return 0;
        }
    }

    public function delete($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);

        if(!isset($datos['idCita'])){
            return $_respuestas->error_400();
        }else{
            $this->CitaId = $datos['idCita'];
            $resp = $this->eliminarCita();
            if($resp){
                $respuesta = $_respuestas->response;
                $respuesta["result"] = array(
                    "idCita" => $this->CitaId
                );
                return $respuesta;
            }else{
                return $_respuestas->error_500();
            }
        }
    }

    private function eliminarCita(){
        $query = "DELETE FROM " . $this->table . " WHERE CitaId = '" . $this->CitaId . "'";
        $resp = parent::nonQuery($query);
        if($resp >= 1 ){
            return $resp;
        }else{
            return 0;
        }
    }


    private function buscarToken(){
        $query = "SELECT  TokenId,UsuarioId,Estado from usuarios_token WHERE Token = '" . $this->token . "' AND Estado = 'Activo'";
        $resp = parent::obtenerDatos($query);
        if($resp){
            return $resp;
        }else{
            return 0;
        }
    }


    private function actualizarToken($tokenid){
        $date = date("Y-m-d H:i");
        $query = "UPDATE usuarios_token SET Fecha = '$date' WHERE TokenId = '$tokenid' ";
        $resp = parent::nonQuery($query);
        if($resp >= 1){
            return $resp;
        }else{
            return 0;
        }
    }



}





?>