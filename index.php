<?php

/* if (10>5){
    echo "es verdadero";
}else {
    echo "es falso";
}
$resultado = (5>10) ? 'verdadero' : 'falso'; */

//CRUD = Create(Crear) - Read (Leer) - Update (Actualizar) - Delete(Borrar)
//$_FILES -- para trabajar con archivos

/* echo "<pre>";
var_dump($_FILES);
echo "</pre>"; */

$txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
$txtNombre = (isset($_POST['txtNombre'])) ? $_POST['txtNombre'] : "";
$txtApellidoPat = (isset($_POST['txtApellidoPat'])) ? $_POST['txtApellidoPat'] : "";
$txtApellidoMat = (isset($_POST['txtApellidoMat'])) ? $_POST['txtApellidoMat'] : "";
$txtCorreo = (isset($_POST['txtCorreo'])) ? $_POST['txtCorreo'] : "";
$txtFoto = (isset($_FILES['txtFoto']["name"])) ? $_FILES['txtFoto']["name"] : "";

$accion=(isset($_POST['accion'])) ? $_POST['accion'] : "";//btnAgregar

require_once('../conexion/conexion.php');

switch($accion){

    case "btnAgregar":
        $sentencia = $pdo->prepare("INSERT INTO empleados(nombre,apellido_pat,apellido_mat,correo,foto)
        VALUES (:Nombre,:ApellidoPat,:ApellidoMat,:Correo,:Foto) ");    //pdo es un objeto instanciado

        $sentencia->bindParam('Nombre', $txtNombre);
        $sentencia->bindParam('ApellidoPat', $txtApellidoPat);
        $sentencia->bindParam('ApellidoMat', $txtApellidoMat);
        $sentencia->bindParam('Correo', $txtCorreo);
        //Asignar un nombre único a la foto que se envíe
        $time = new DateTime(); //clase DateTime por defecto en php
        $nombreArchivo= ($txtFoto!="") ? $time->getTimestamp()."_".$txtFoto : "imagen.png" ;
        $tmpFoto= $_FILES['txtFoto']["tmp_name"]; //accediendo a la dirección del archivo foto temporal

        if($txtFoto!=""){
            move_uploaded_file($tmpFoto,"../imagenes/".$nombreArchivo);
        }

        $sentencia->bindParam('Foto', $nombreArchivo);

        $sentencia->execute();

        header('Location:index.php');

        break;
    case "btnActualizar":
        
        //Actuliza todos los campos menos el campo foto
        $sentencia = $pdo->prepare(" UPDATE empleados set 
        nombre=:Nombre,
        apellido_pat=:ApellidoPat,
        apellido_mat=:ApellidoMat,
        correo=:Correo
         WHERE ID=:ID");

        $sentencia->bindParam('Nombre', $txtNombre);
        $sentencia->bindParam('ApellidoPat', $txtApellidoPat);
        $sentencia->bindParam('ApellidoMat', $txtApellidoMat);
        $sentencia->bindParam('Correo', $txtCorreo);
        $sentencia->bindParam('ID', $txtID);

        $sentencia->execute();


        //Asignar un nombre único a la foto que se envíe
        $time = new DateTime(); //clase DateTime por defecto en php
        $nombreArchivo= ($txtFoto!="") ? $time->getTimestamp()."_".$txtFoto : "imagen.png" ;
        $tmpFoto= $_FILES['txtFoto']["tmp_name"]; //accediendo a la dirección del archivo foto temporal

        if($txtFoto!=""){
            move_uploaded_file($tmpFoto,"../imagenes/".$nombreArchivo);//Muevo el archivo de temporal a la carpeta imagenes(permanente)
            
            $sentencia=$pdo->prepare("SELECT foto FROM empleados WHERE ID=:ID");
            $sentencia->bindParam(':ID',$txtID);
            $sentencia->execute();
            $fotoEmpleado=$sentencia->fetch(PDO::FETCH_LAZY);
            //var_dump($fotoEmpleado);
    
            if(isset($fotoEmpleado["foto"])){
                if(file_exists("../imagenes/".$fotoEmpleado["foto"])){
                    unlink("../imagenes/".$fotoEmpleado["foto"]);
                }
            }//Eliminación de archivo si existe en el servidor archivos  


            $sentencia = $pdo->prepare("UPDATE empleados set
            foto=:Foto
            where 
            ID=:ID"); 

            $sentencia->bindParam(':Foto',$nombreArchivo);
            $sentencia->bindParam(':ID',$txtID);
            $sentencia->execute();//Actualizando el nombre de la foto más actual

           
            


        }

       
        
        header('Location:index.php');

        break;

    case "btnBorrar":
        
        $sentencia=$pdo->prepare("SELECT foto FROM empleados WHERE ID=:ID");
        $sentencia->bindParam(':ID',$txtID);
        $sentencia->execute();
        $fotoEmpleado=$sentencia->fetch(PDO::FETCH_LAZY);
        //var_dump($fotoEmpleado);

        if(isset($fotoEmpleado["foto"])){
            if(file_exists("../imagenes/".$fotoEmpleado["foto"])){
                unlink("../imagenes/".$fotoEmpleado["foto"]);
            }
        }//Eliminación de archivo si existe en el servidor archivos
        
        //Eliminamos el registro de la BD
        $sentencia = $pdo->prepare("DELETE FROM empleados where ID=:ID");
        $sentencia->bindParam('ID', $txtID);
        $sentencia->execute(); 

       header('Location:index.php');
       
       
        break;

    case "btnCancelar":

        header('Location:index.php');
        break; 
}

$sentencia = $pdo->prepare("SELECT * FROM empleados"); //lista todos los registros de la tabla empleados
$sentencia->execute();
$listaEmpleados = $sentencia->fetchAll(PDO::FETCH_ASSOC);

/*  echo "<pre>";
var_dump($listaEmpleados);
echo "</pre>";   */
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de la empresa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    
</head>
<body>

    <div class="container">
        <form action="" method="post" enctype="multipart/form-data">
            <label for="">ID:</label>
            <input type="text" name="txtID" value="<?php echo $txtID;?>" placeholder="" id="txtID" require>
            <br>

            <label for="">Nombre:</label>
            <input type="text" name="txtNombre" value="<?php echo $txtNombre;?>" placeholder="" id="txtNombre" require>
            <br>

            <label for="">Apellido Paterno:</label>
            <input type="text" name="txtApellidoPat" value="<?php echo $txtApellidoPat;?>"  placeholder="" id="txtApellidoPat" require>
            <br>

            <label for="">Apellido Materno:</label>
            <input type="text" name="txtApellidoMat" value="<?php echo $txtApellidoMat;?>" placeholder="" id="txtApellidoMat" require>
            <br>

            <label for="">Correo:</label>
            <input type="text" name="txtCorreo" value="<?php echo $txtCorreo;?>" placeholder="" id="txtCorreo" require>
            <br>

            <label for="">Foto:</label>
            <input type="file" name="txtFoto" value="" placeholder="" id="txtFoto" require>
            <br>


            <button type="submit" name="accion" value="btnAgregar">Agregar</button>
            <button type="submit" name="accion" value="btnActualizar">Actualizar</button>
            <button type="submit" name="accion" value="btnBorrar">Borrar</button>
            <button type="submit" name="accion" value="btnCancelar">Cancelar</button>

        </form>

        <div class="row">
            <table class="table">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nombre completo</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

            <?php
            foreach($listaEmpleados as $empleado){ ?>
            <tr>
                <td> <img class="img-thumbnail" width="100px" src="../imagenes/<?php echo $empleado['foto'];?> " >        </td>
                <td> <?php echo $empleado['nombre']." ". $empleado['apellido_pat']." ". $empleado['apellido_mat'] ;?> </td>
                <td><?php echo $empleado['correo'];?> </td>
                <td>
                    <form action="" method="post">   
                        <input type="hidden" name="txtID" value="<?php echo $empleado['ID'];?>">
                        <input type="hidden" name="txtNombre" value="<?php echo $empleado['nombre'];?>">
                        <input type="hidden" name="txtApellidoPat" value="<?php echo $empleado['apellido_pat'];?>">
                        <input type="hidden" name="txtApellidoMat" value="<?php echo $empleado['apellido_mat'];?>">
                        <input type="hidden" name="txtCorreo" value="<?php echo $empleado['correo'];?>">
                        <input type="hidden" name="txtFoto" value="<?php echo $empleado['foto'];?>"> 
                        <input type="submit" value="Seleccionar" name="accion">
                        <button value="btnBorrar" type="submit" name="accion">Borrar</button>
                    </form>
                </td>
                
                
            </tr>
            <?php } ?>

            </table>

        </div>   
    </div>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</html>