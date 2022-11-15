		<?php 

		class Clientes extends Controllers{
			public function __construct()
			{
				parent::__construct();
				session_start();
				session_regenerate_id(true);
				if(empty($_SESSION['login']))
				{
					header('Location: '.base_url().'/login');
				}
				getPermisos(3);
			}

			public function Clientes()
			{
				if(empty($_SESSION['permisosMod']['r'])){
					header("Location:".base_url().'/dashboard');
				}
				$data['page_tag'] = "Clientes";
				$data['page_title'] = "Clientes <small>Virtual store</small>";
				$data['page_name'] = "clientes";
				$data['page_functions_js'] = "functions_clientes.js";
				$this->views->getView($this,"clientes",$data);
			}


// FUNCIÓN PARA INSERTAR CLIENTES

			public function setCliente(){

				if($_POST){
					// dep($_POST);
					// exit();
					if(empty($_POST['txtNombre']) || empty($_POST['txtApellido']) || empty($_POST['txtTelefono'])  || empty($_POST['txtEmail'])){
						$arrResponse = array("status"=> false, "msg"=>"Datos incorrectos.");
					}else{

						$idUsuario = intval($_POST['idUsuario']);
						$strNombre = ucwords(strClean($_POST['txtNombre']));
						$strApellido = ucwords(strClean($_POST['txtApellido']));
						$intTelefono = intval(strClean($_POST['txtTelefono']));
						$strEmail = strtolower(strClean($_POST['txtEmail']));

						$strNit = strClean($_POST['txtNit']);
						$strNombreFiscal = strClean($_POST['txtNombreFiscal']);
						$strDirFiscal = strClean($_POST['txtDirFiscal']);
						$intTipoId = 7;
						$request_user="";

						if($idUsuario==0){
							$option = 1;
							$strPassword =  empty($_POST['txtPassword']) ? passGenerator() : $_POST['txtPassword'];
							$strPasswordEncript = hash("SHA256",$strPassword);
							if($_SESSION['permisosMod']['w']){
								$request_user = $this->model->insertCliente(
									$strNombre,
									$strApellido,
									$intTelefono, 
									$strEmail,
									$strPasswordEncript,
									$intTipoId,
									$strNit,
									$strNombreFiscal,
									$strDirFiscal
								);
							}

						}else{
							$option =2;
							$strPassword = empty($_POST['txtPassword']) ? "" : hash("sha256",$_POST['txtPassword']);
							if($_SESSION['permisosMod']['w']){
							$request_user = $this->model->updateCliente(
								$idUsuario,
								$strNombre,
								$strApellido,
								$intTelefono,
								$strEmail,
								$strPassword,
								$strNit,
								$strNombreFiscal,
								$strDirFiscal
							);
						}
						}	

						if($request_user > 0){
							if($option==1){
								$arrResponse = array('status' => true, 'msg' => '¡Datos guardados correctamente!');
								$nombreUsuario = $strNombre.' '.$strApellido;
								$dataUsuario = array('nombreUsuario' => $nombreUsuario,
									'email' => $strEmail,
									'password' => $strPassword,
									'asunto' => 'Bienvenido a tu tienda en línea');
								sendMailLocal($dataUsuario,'email_bienvenida');
							}else{
								$arrResponse = array('status' => true, 'msg' => '¡Datos actualizados correctamente!');
							}
						}else if($request_user == 'exist'){
							$arrResponse = array('status' => false, 'msg' => '¡Atención! El email ya existe, favor de ingresar otro.');
						} else{
							$arrResponse = array("status" => false, "msg" => "No es posible almacenar lo datos");
						}

					}
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
				}
				die();
			}


			// FUNCIÓN PARA EXTRAER TODOS LOS CLIENTES

			public function getClientes(){
				if($_SESSION['permisosMod']['r']){

					$arrData = $this ->model->selectClientes();
				// dep($arrData);
				// die();
					for ($i = 0 ; $i < count($arrData); $i++){
						$btnView='';
						$btnEdit=''; 
						$btnDelete='';
						if($_SESSION['permisosMod']['r']){
							$btnView = '<button class="btn btn-info btn-sm btnViewCliente" onClick="fntViewInfo('.$arrData[$i]['idpersona'].')" title="Ver Cliente"><i class="far fa-eye"></i></button>';
						}
						if($_SESSION['permisosMod']['u']){
							$btnEdit = '<button class="btn btn-primary btn-sm btnEditCliente" onClick="fntEditCliente(this,'.$arrData[$i]['idpersona'].')" title="Editar Cliente"><i class="fas fa-pencil-alt"></i></button>';
						}
						if($_SESSION['permisosMod']['d']){
							$btnDelete = '<button class="btn btn-danger btn-sm btnDelCliente" onClick="fntDelCliente('.$arrData[$i]['idpersona'].')" title="Eliminar Cliente"><i class="far fa-trash-alt"></i></button>';
						}

						$arrData[$i]['options'] = '<div class="text-center">'.$btnView.' ' .$btnEdit. ' '. $btnDelete. '</div>';
					}
					echo json_encode($arrData,JSON_UNESCAPED_UNICODE);

				}

				die();
			}

			// FUNCIÓN PARA EXTRAER LA INFORMACIÓN DE 1 SOLO CLIENTE
			public function getCliente ($idpersona){
				if($_SESSION['permisosMod']['r']){
					$idusuario = intval($idpersona);
					if($idusuario>0){
						$arrData = $this->model->selectCliente($idusuario);
					// dep($arrData);
					// exit;
						if(empty($arrData)){
							$arrResponse = array('status' => false, 'msg' =>'Datos no encontrados');
						}else{
							$arrResponse = array('status' => true, 'data' =>$arrData);
						}
						echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
					}
				}
				die();
			}

			//FUNCIÓN PARA ELIMINAR CLIENTE

			public function delCliente()
			{
				if($_POST){			
				if($_SESSION['permisosMod']['d']){		
					$intIdpersona = intval($_POST['idUsuario']);
					$requestDelete = $this->model->deleteCliente($intIdpersona);
					if($requestDelete)
					{
						$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el cliente');
					}else{
						$arrResponse = array('status' => false, 'msg' => 'Error al eliminar al cliente.');
					}
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);

				}
			}
				die();
			}

		}

	?>