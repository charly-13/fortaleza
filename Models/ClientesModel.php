<?php 

class ClientesModel extends Mysql{
	private $intIdUsuario;
	private $strNombre;
	private $strApellido; 
	private $intTelefono;
	private $strEmail;
	private $strPassword;
	private $intTipoId;
	private $strNit;
	private $strNomFiscal;
	private $strDirFiscal;

	public function __construct() 
	{
		parent::__construct();
	}

	public function insertCliente(string $nombre, string $apellido, int $telefono, string $email, string $password, int $tipoid, string $nit, string $nombrefical, string $DirFiscal){
		//DATOS PERSONALES
		$this->strNombre = $nombre;
		$this->strApellido = $apellido;
		$this->intTelefono = $telefono;
		$this->strEmail = $email;
		$this->strPassword = $password;
		$this->intTipoId = $tipoid;
        //DATOS FISCALES
		$this->strNit = $nit;
		$this->strNomFiscal = $nombrefical;
		$this->strDirFiscal = $DirFiscal;
		$return = 0; 
		$sql = "SELECT * FROM persona WHERE email_user 	= '{$this->strEmail}'";
		$request = $this->select_all($sql);
		if(empty($request)){
			$query_insert = "INSERT INTO persona (nombres,apellidos,telefono,email_user,password,rolid,nit,nombrefiscal,direccionfiscal) VALUES(?,?,?,?,?,?,?,?,?)";
			$arrData = array(
				$this->strNombre, 
				$this->strApellido,
				$this->intTelefono,		 	
				$this->strEmail,
				$this->strPassword, 
				$this->intTipoId,
				$this->strNit,
				$this->strNomFiscal,
				$this->strDirFiscal 
			);
			$request_insert= $this->insert($query_insert,$arrData);
			$return = $request_insert;
		}else{
			$return = "exist";
		}
		return $return;

	}

    // FUNCIÓN PARA EXTRAER TODOS LOS CLIENTES
	public function selectClientes(){
		$sql = "SELECT idpersona,nombres,apellidos,telefono,email_user,status FROM persona WHERE rolid=7 AND status != 0";
		$request = $this->select_all($sql);
		return $request;
	}
	//FUNCIÓN PATRA EXTRAER LA INFORMACIÓN DE 1 CLIENTE
	public function selectCliente(int $idpersona){
		$this->intIdUsuario = $idpersona;
		$sql ="SELECT idpersona,nombres,apellidos,telefono,email_user,nit,nombrefiscal,direccionfiscal,status,DATE_Format(datecreated,'%d-%m-%Y') AS fechaRegistro FROM persona WHERE idpersona = $this->intIdUsuario";
		$request = $this->select($sql);
		return $request;
	}

	//FUNCIÓN PARA EDITAR CLIENTE 
	public function updateCliente(int $idusuario, string $nombre, string $apellido, int $telefono, string $email, string $password, string $nit, string $nomFiscal, string $dirFiscal){
		$this->intIdUsuario = $idusuario;
		$this->strNombre = $nombre;
		$this->strApellido = $apellido;
		$this->intTelefono = $telefono;
		$this->strEmail = $email;
		$this->strPassword = $password;
		$this->strNit = $nit;
		$this->strNomFiscal = $nomFiscal;
		$this->strDirFiscal = $dirFiscal;

		$sql = "SELECT * FROM persona WHERE (email_user='{$this->strEmail}' AND idpersona != $this->intIdUsuario)";
		$request = $this->select_all($sql);
		if(empty($request)){
			if($this->strPassword != ''){
				$sql ="UPDATE persona SET nombres = ?, apellidos = ?, telefono = ?, email_user = ?, password = ?, nit = ?, nombrefiscal = ?, direccionfiscal = ? WHERE idpersona = $this->intIdUsuario";
				$arrData = array(
					$this->strNombre,
					$this->strApellido,
					$this->intTelefono,
					$this->strEmail,
					$this->strPassword,
					$this->strNit,
					$this->strNomFiscal,
					$this->strDirFiscal
				);
			}else{
				$sql ="UPDATE persona SET nombres = ?, apellidos = ?, telefono = ?, email_user = ?, nit = ?, nombrefiscal = ?, direccionfiscal = ? WHERE idpersona = $this->intIdUsuario";
				$arrData = array(
					$this->strNombre,
					$this->strApellido,
					$this->intTelefono,
					$this->strEmail,
					$this->strNit,
					$this->strNomFiscal,
					$this->strDirFiscal
				);
			}
			$request = $this->Update($sql,$arrData);
		}else{
			$request = "exist";
		}
		return $request;

	}

		//FUNCIÓN PARA ELIMINAR CLIENTE
	public function deleteCliente(int $intIdpersona)
	{
		$this->intIdUsuario = $intIdpersona;
		$sql = "UPDATE persona SET status = ? WHERE idpersona = $this->intIdUsuario ";
		$arrData = array(0);
		$request = $this->update($sql,$arrData);
		return $request;
	}
}



?>