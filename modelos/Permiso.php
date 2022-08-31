<?php 

require "../config/Conexion.php";
class Permiso{ 


	
public function __construct(){

}



//listar registros
public function listar(){
	$sql="SELECT * FROM permiso";
	return ejecutarConsulta($sql);
}
}

 ?>
