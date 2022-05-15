<?php

class  Media {

  public $imageInfo;
  public $fileName;
  public $fileType;
  public $fileTempPath;
  //Establecer destino para subir imagenes
  public $userPath = SITE_ROOT.DS.'..'.DS.'uploads/users';
  public $productPath = SITE_ROOT.DS.'..'.DS.'uploads/products';


  public $errors = array();
  public $upload_errors = array(
    0 => 'No hay error, se subio correctamente',
    1 => 'El archivo cargado excede el maximo permitido',
    2 => 'El archivo cargado excede la directiva MAX_FILE_SIZE que se especificó en el formulario HTML',
    3 => 'El archivo subido solo se cargó parcialmente',
    4 => 'Ningun archivo fue subido',
    6 => 'Falta una carpeta temporal',
    7 => 'No se pudo escribir el archivo en el disco.',
    8 => 'Una extensión de PHP detuvo la carga del archivo.'
  );
  public$upload_extensions = array(
   'gif',
   'jpg',
   'jpeg',
   'png',
  );
  public function file_ext($filename){
     $ext = strtolower(substr( $filename, strrpos( $filename, '.' ) + 1 ) );
     if(in_array($ext, $this->upload_extensions)){
       return true;
     }
   }

  public function upload($file)
  {
    if(!$file || empty($file) || !is_array($file)):
      $this->errors[] = "Ningún archivo subido.";
      return false;
    elseif($file['error'] != 0):
      $this->errors[] = $this->upload_errors[$file['error']];
      return false;
    elseif(!$this->file_ext($file['name'])):
      $this->errors[] = 'Formato de archivo incorrecto ';
      return false;
    else:
      $this->imageInfo = getimagesize($file['tmp_name']);
      $this->fileName  = basename($file['name']);
      $this->fileType  = $this->imageInfo['mime'];
      $this->fileTempPath = $file['tmp_name'];
     return true;
    endif;

  }

 public function process(){

    if(!empty($this->errors)):
      return false;
    elseif(empty($this->fileName) || empty($this->fileTempPath)):
      $this->errors[] = "La ubicación del archivo no esta disponible.";
      return false;
    elseif(!is_writable($this->productPath)):
      $this->errors[] = $this->productPath." Debe tener permisos de escritura!!!.";
      return false;
    elseif(file_exists($this->productPath."/".$this->fileName)):
      $this->errors[] = "El archivo {$this->fileName} ya existe.";
      return false;
    else:
     return true;
    endif;
 }
 /*--------------------------------------------------------------*/
 /* Función para Procesar archivo multimedia
 /*--------------------------------------------------------------*/
  public function process_media(){
    if(!empty($this->errors)){
        return false;
      }
    if(empty($this->fileName) || empty($this->fileTempPath)){
        $this->errors[] = "La ubicación del archivo no se encuenta disponible.";
        return false;
      }

    if(!is_writable($this->productPath)){
        $this->errors[] = $this->productPath." Debe tener permisos de escritura!!!.";
        return false;
      }

    if(file_exists($this->productPath."/".$this->fileName)){
      $this->errors[] = "El archivo {$this->fileName} ya existe.";
      return false;
    }

    if(move_uploaded_file($this->fileTempPath,$this->productPath.'/'.$this->fileName))
    {

      if($this->insert_media()){
        unset($this->fileTempPath);
        return true;
      }

    } else {

      $this->errors[] = "Error en la carga del archivo, posiblemente debido a permisos incorrectos en la carpeta de carga.";
      return false;
    }

  }
  /*--------------------------------------------------------------*/
  /* Función para Procesar imagen de usuario
  /*--------------------------------------------------------------*/
 public function process_user($id){

    if(!empty($this->errors)){
        return false;
      }
    if(empty($this->fileName) || empty($this->fileTempPath)){
        $this->errors[] = "La ubicación del archivo no estaba disponible.";
        return false;
      }
    if(!is_writable($this->userPath)){
        $this->errors[] = $this->userPath." Debe tener permisos de escritura";
        return false;
      }
    if(!$id){
      $this->errors[] = " ID de usuario ausente.";
      return false;
    }
    $ext = explode(".",$this->fileName);
    $new_name = randString(8).$id.'.' . end($ext);
    $this->fileName = $new_name;
    if($this->user_image_destroy($id))
    {
    if(move_uploaded_file($this->fileTempPath,$this->userPath.'/'.$this->fileName))
       {

         if($this->update_userImg($id)){
           unset($this->fileTempPath);
           return true;
         }

       } else {
         $this->errors[] = "Error en la carga del archivo, posiblemente debido a permisos incorrectos en la carpeta de carga.";
         return false;
       }
    }
 }
 /*--------------------------------------------------------------*/
 /* Función para Actualizar imagen de usuario
 /*--------------------------------------------------------------*/
  private function update_userImg($id){
     global $db;
      $sql = "UPDATE users SET";
      $sql .=" image='{$db->escape($this->fileName)}'";
      $sql .=" WHERE id='{$db->escape($id)}'";
      $result = $db->query($sql);
      return ($result && $db->affected_rows() === 1 ? true : false);

   }
 /*--------------------------------------------------------------*/
 /* Función para eliminar imagen antigua
 /*--------------------------------------------------------------*/
  public function user_image_destroy($id){
     $image = find_by_id('users',$id);
     if($image['image'] === 'no_image.jpg')
     {
       return true;
     } else {
       unlink($this->userPath.'/'.$image['image']);
       return true;
     }

   }
/*--------------------------------------------------------------*/
/* Función para insertar imagen multimedia
/*--------------------------------------------------------------*/
  private function insert_media(){

         global $db;
         $sql  = "INSERT INTO media ( file_name,file_type )";
         $sql .=" VALUES ";
         $sql .="(
                  '{$db->escape($this->fileName)}',
                  '{$db->escape($this->fileType)}'
                  )";
       return ($db->query($sql) ? true : false);

  }
/*--------------------------------------------------------------*/
/* Función para eliminar imágenes por id
/*--------------------------------------------------------------*/
   public function media_destroy($id,$file_name){
     $this->fileName = $file_name;
     if(empty($this->fileName)){
         $this->errors[] = "Falta el archivo de foto.";
         return false;
       }
     if(!$id){
       $this->errors[] = "ID de foto ausente.";
       return false;
     }
     if(delete_by_id('media',$id)){
         unlink($this->productPath.'/'.$this->fileName);
         return true;
     } else {
       $this->error[] = "Se ha producido un error en la eliminación de fotografías.";
       return false;
     }

   }



}


?>
