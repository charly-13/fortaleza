<?php 

class Categorias extends Controllers{
	public function __construct()
	{
		parent::__construct();
		session_start();
		session_regenerate_id(true);
		if(empty($_SESSION['login']))
		{
			header('Location: '.base_url().'/login');
		}
		getPermisos(6);
	}

	public function Categorias()
	{
		if(empty($_SESSION['permisosMod']['r'])){
			header("Location:".base_url().'/dashboard');
		}
		$data['page_tag'] = "Categorias";
		$data['page_title'] = "CATEGORIAS <small>Virtual store</small>";
		$data['page_name'] = "categorias";
		$data['page_functions_js'] = "functions_categorias.js";
		$this->views->getView($this,"categorias",$data);
	}

	public function setCategoria(){
		if($_POST){
			if(empty($_POST['txtNombre']) || empty($_POST['txtDescripcion']) || empty($_POST['listStatus'])){
				$arrResponse = array('status' => false, 'msg'=>'Datos incorrectos');
			}else{
				$intIdcategoria = intval($_POST['idCategoria']);
				$strCategoria = strClean($_POST['txtNombre']);
				$strDescripcion = strClean($_POST['txtDescripcion']);
				$intStatus = intval($_POST['listStatus']);
					//ALMACENAMOS LOS DATOS DE LA IMAGEN
				$foto = $_FILES['foto'];
				$nombre_foto = $foto['name'];
				$type = $foto['type'];
				$url_temp=$foto['tmp_name'];
				// $fecha = date('ymd');
				// $hora = date('Hms'); 
				$imgPortada = 'portada_categoria.png';
				if($nombre_foto != ''){
					$imgPortada = 'img_'.md5(date('d-m-Y H:m:s')).'.jpg';
				}
				if($intIdcategoria == 0){
					//CREAR
					$request_categoria = $this->model->insertCategoria($strCategoria,$strDescripcion,$imgPortada,$intStatus);
					$option = 1;	
				}else{
					//ACTUALIZAR
					$request_categoria = $this->model->updateCategoria($intIdcategoria,$strCategoria,$strDescripcion,$intStatus);
					$option = 2;
				}
				if($request_categoria > 1){
					if($option = 1){
						$arrResponse = array ('status'=>true,'msg'=>'Datos guardados correctamente');
						if($nombre_foto != ''){	uploadImage($foto,$imgPortada);	}
					}else{
						$arrResponse = array ('status'=>true,'msg'=>'Datos Actualizados correctamente');
					}

				}else if($request_categoria=='exist'){
					$arrResponse = array ('status'=>false,'msg'=>'¡Atención! La categoría ya existe.');
				} else{
					$arrResponse = array ('status'=>false,'msg'=>'No es posible almacenar los datos.');
				}

			}
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();

	}

}