<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="css/signup.css">
        
    <title>Create Account</title>
    <style>
        .container{
            animation: transitionIn-X 0.5s;
        }
    </style>
    <script src="js/multistep.js"></script>
</head>
<body>
<?php

//learn from w3schools.com
//Unset all the server side variables

session_start();

$_SESSION["user"]="";
$_SESSION["usertype"]="";

// Set the new timezone
date_default_timezone_set('America/Mexico_City');
$date = date('Y-m-d');

$_SESSION["date"]=$date;


//import database
include("connection.php");





if($_POST){

    $result= $database->query("select * from webuser");

    $fname=$_SESSION['personal']['fname'];
    $lname=$_SESSION['personal']['lname'];
    $name=$fname." ".$lname;
    $address=$_SESSION['personal']['address'];
    $nic=$_SESSION['personal']['nic'];
    $dob=$_SESSION['personal']['dob'];
    $email=$_POST['newemail'];
    $tele=$_POST['tele'];
    $newpassword=$_POST['newpassword'];
    $cpassword=$_POST['cpassword'];
    
    if ($newpassword==$cpassword){
        $sqlmain= "select * from webuser where email=?;";
        $stmt = $database->prepare($sqlmain);
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows==1){
            $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Already have an account for this Email address.</label>';
        }else{
            //TODO
            $database->query("insert into patient(pemail,pname,ppassword, paddress, pnic,pdob,ptel) values('$email','$name','$newpassword','$address','$nic','$dob','$tele');");
            $database->query("insert into webuser values('$email','p')");

            //print_r("insert into patient values($pid,'$email','$fname','$lname','$newpassword','$address','$nic','$dob','$tele');");
            $_SESSION["user"]=$email;
            $_SESSION["usertype"]="p";
            $_SESSION["username"]=$fname;

            header('Location: patient/index.php');
            $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;"></label>';
        }
        
    }else{
        $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password Conformation Error! Reconform Password</label>';
    }



    
}else{
    //header('location: signup.php');
    $error='<label for="promter" class="form-label"></label>';
}

?>


    <center>
    <div class="container">
        <ul id="progressbar">
            <li class="active">Paso 1</li>
            <li>Paso 2</li>
            <li>Paso 3</li>
        </ul>
        <form action="" method="POST">
            <!-- Paso 1 -->
            <fieldset>
                <h2 class="fs-title">Información Personal</h2>
                <h3 class="fs-subtitle">Introduce tus datos personales</h3>
                <input type="email" name="newemail" class="input-text" placeholder="Dirección de correo" required>
                <input type="tel" name="tele" class="input-text" placeholder="ejem: 5566779900" pattern="[0-9]{10}" required>
                <input type="button" name="next" class="next action-button" value="Siguiente">
            </fieldset>

            <!-- Paso 2 -->
            <fieldset>
                <h2 class="fs-title">Seguridad</h2>
                <h3 class="fs-subtitle">Crea una contraseña segura</h3>
                <input type="password" name="newpassword" class="input-text" placeholder="Nueva Contraseña" required>
                <input type="password" name="cpassword" class="input-text" placeholder="Confirmar Contraseña" required>
                <input type="button" name="previous" class="previous action-button" value="Anterior">
                <input type="button" name="next" class="next action-button" value="Siguiente">
            </fieldset>

            <!-- Paso 3 -->
            <fieldset>
                <h2 class="fs-title">Confirmación</h2>
                <h3 class="fs-subtitle">Revisa y confirma tus datos</h3>
                <p>Correo: <span id="email-review"></span></p>
                <p>Teléfono: <span id="tele-review"></span></p>
                <input type="button" name="previous" class="previous action-button" value="Anterior">
                <input type="submit" name="submit" class="submit action-button" value="Registrarse">
            </fieldset>
        </form>
    </div>
    </center>
</body>
</html>