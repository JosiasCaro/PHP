<?php
abstract class Conectar {

    private $con;

    public function conectar() {

        try {
            $this->con = new PDO("mysql:dbname=algo;host=localhost", "root", "");
        } catch(PDOException $e) {

            die("error :" .$e);
        }
        return $this->con;
    }

    public function setNames() {

        return $this->con->query("SET NAMES 'utf8'");
    }

}

class Datos extends conectar {

    private $bd;

    public function __construct() {

        $this->bd = self::conectar();
        self::setNames();
    }

    public function getDatos($sql) {

        $datos = $this->bd->prepare($sql);
        
        //Ejecuta la consulta $sql
        $datos->execute();
        
        //Retorna todos los registros que provengan de la consulta $sql
        return $datos->fetchAll();

        //cierro la conexion
        $this->bd=null;
    }

    public function getDato($sql) {

        $datos = $this->bd->prepare($sql);
        
        //Ejecuta la consulta $sql
        $datos->execute();
        
        //Retorna el registro que provengan de la consulta $sql
        return $datos->fetch();

        //cierro la conexion
        $this->bd=null;
    }

    public function setDato($sql) {
        $datos = $this->bd->prepare($sql);
        //Ejecuta la consulta $sql
        $datos->execute();
        //Deja ver el ultimo insert si $sql tiene un insert
        //return $this->bd->lastInsertId();

    }
}