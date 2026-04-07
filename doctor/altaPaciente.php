<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/signup.css"> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/multistep.js"></script>
    <title>Creacion de expediente</title>
    
</head>
<body>
<?php

session_start();
// Verificar si el doctor está logueado
if(!isset($_SESSION["user"]) || !isset($_SESSION["usertype"]) || $_SESSION["usertype"]!="d"){
    header('Location: ../login.php');
    exit();
}

// Guardar el email del doctor
$doctorEmail = $_SESSION["user"];


// Set the new timezone
date_default_timezone_set('America/Mexico_City');
$date = date('Y-m-d');

$_SESSION["date"]=$date;


require_once("../connection.php");
if($_POST){
    error_log("Datos recibidos: " . print_r($_POST, true)); // Registro de depuración
    // Obtener los datos del formulario
    $apaterno = $_POST['apaterno'];
    $amaterno = $_POST['amaterno'];
    $nombres = $_POST['nombres'];
    $dob = $_POST['dob'];
    $edad = $_POST['edad'];
    $sexo = $_POST['sexo'];
    $estado_civil = $_POST['estado_civil'];
    $ocupacion = $_POST['ocupacion'];
    $calle = $_POST['calle'];
    $numero = $_POST['numero'];
    $colonia = $_POST['colonia'];
    $cp = $_POST['cp'];
    $ciudad = $_POST['ciudad'];
    $estado = $_POST['estado'];
    $telefono_cel = $_POST['telefono_cel'];
    $telefono_fijo = $_POST['telefono_fijo'];
    $email = $_POST['email'];
    $curp = $_POST['curp'];
    $nss = $_POST['nss'];
    $tutor_nombre = $_POST['tutor_nombre'];
    $tutor_parentesco = $_POST['tutor_parentesco'];
    $emergencia_nombre = $_POST['emergencia_nombre'];
    $emergencia_telefono = $_POST['emergencia_telefono'];

    // Verificar si el email ya existe
    $sqlcheck = "SELECT * FROM patient WHERE email=?";
    $stmt = $database->prepare($sqlcheck);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){
        $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Ya existe una cuenta con este correo electrónico.</label>';
    } else {
        // Preparar la consulta SQL
        $sqlinsert = "INSERT INTO patient (
            apaterno, amaterno, nombres, dob, edad, sexo, estado_civil,
            ocupacion, calle, numero, colonia, cp, ciudad, estado,
            telefono_cel, telefono_fijo, email, curp, nss,
            tutor_nombre, tutor_parentesco, emergencia_nombre, emergencia_telefono
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Preparar y ejecutar la consulta
        $stmt = $database->prepare($sqlinsert);
        $stmt->bind_param("ssssissssssssssssssssss",
            $apaterno, $amaterno, $nombres, $dob, $edad, $sexo, $estado_civil,
            $ocupacion, $calle, $numero, $colonia, $cp, $ciudad, $estado,
            $telefono_cel, $telefono_fijo, $email, $curp, $nss,
            $tutor_nombre, $tutor_parentesco, $emergencia_nombre, $emergencia_telefono
        );

        if($stmt->execute()){
            // Insertar en webuser
            $sqlwebuser = "INSERT INTO webuser (email, usertype) VALUES (?, 'p')";
            $stmt = $database->prepare($sqlwebuser);
            $stmt->bind_param("s", $email);
            $stmt->execute();


// Guardar datos en sesión para que generar_pdf.php los use
            $_SESSION['pdf_patient'] = [
                'apaterno'=>$apaterno,'amaterno'=>$amaterno,'nombres'=>$nombres,
                'dob'=>$dob,'edad'=>$edad,'sexo'=>$sexo,'estado_civil'=>$estado_civil,
                'ocupacion'=>$ocupacion,'calle'=>$calle,'numero'=>$numero,'colonia'=>$colonia,
                'cp'=>$cp,'ciudad'=>$ciudad,'estado'=>$estado,'telefono_cel'=>$telefono_cel,
                'telefono_fijo'=>$telefono_fijo,'email'=>$email,'curp'=>$curp,'nss'=>$nss,
                'tutor_nombre'=>$tutor_nombre,'tutor_parentesco'=>$tutor_parentesco,
                'emergencia_nombre'=>$emergencia_nombre,'emergencia_telefono'=>$emergencia_telefono
            ];

            // Redirigir al generador de PDF (script independiente, sin salida previa)
            header('Location: /agenda/tcpdf/generar_pdf.php');
            exit();
        } else {
            $error = 'Error al guardar los datos. Intente nuevamente.';
        }
    }
}
?>

    <center>
    <div class="container">
        <ul id="progressbar">
            <li class="active">Datos Personales</li>
            <li>Datos Adicionales</li>
        </ul>
        <form id="msform" action="" method="POST">
            <!-- Paso 1: Datos Personales -->
            <fieldset>
                <h2 class="fs-title">Datos Personales</h2>
                <h3 class="fs-subtitle">Introduce tus datos personales</h3>
                <table border="0">
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="nombres" class="form-label">Nombre(s):</label>
                            <input type="text" name="nombres" class="input-text" required>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td">
                            <label for="apaterno" class="form-label">Apellido Paterno:</label>
                            <input type="text" name="apaterno" class="input-text" required>
                        </td>
                        <td class="label-td">
                            <label for="amaterno" class="form-label">Apellido Materno:</label>
                            <input type="text" name="amaterno" class="input-text" required>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td">
                            <label for="dob" class="form-label">Fecha de Nacimiento:</label>
                            <input type="date" name="dob" class="input-text" required>
                        </td>
                        <td class="label-td">
                            <label for="edad" class="form-label">Edad:</label>
                            <input type="number" name="edad" class="input-text" required>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td">
                            <label for="sexo" class="form-label">Sexo:</label>
                            <select name="sexo" class="input-text" required>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                        </td>
                        <td class="label-td">
                            <label for="estado_civil" class="form-label">Estado Civil:</label>
                            <select name="estado_civil" class="input-text" required>
                                <option value="Soltero">Soltero</option>
                                <option value="Casado">Casado</option>
                                <option value="Divorciado">Divorciado</option>
                                <option value="Viudo">Viudo</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="ocupacion" class="form-label">Ocupación:</label>
                            <input type="text" name="ocupacion" class="input-text" required>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="calle" class="form-label">Calle:</label>
                            <input type="text" name="calle" class="input-text" required>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td">
                            <label for="numero" class="form-label">Número:</label>
                            <input type="text" name="numero" class="input-text" required>
                        </td>
                        <td class="label-td">
                            <label for="colonia" class="form-label">Colonia:</label>
                            <input type="text" name="colonia" class="input-text" required>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td">
                            <label for="cp" class="form-label">Código Postal:</label>
                            <input type="text" name="cp" class="input-text" required>
                        </td>
                        <td class="label-td">
                            <label for="ciudad" class="form-label">Ciudad:</label>
                            <input type="text" name="ciudad" class="input-text" required>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="estado" class="form-label">Estado:</label>
                            <input type="text" name="estado" class="input-text" required>
                        </td>
                    </tr>
                </table>
                <input type="button" name="next" class="next action-button" value="Siguiente">
            </fieldset>

            <!-- Paso 2: Datos Adicionales -->
            <fieldset>
                <h2 class="fs-title">Datos Adicionales</h2>
                <h3 class="fs-subtitle">Introduce los datos restantes</h3>
                <table border="0">
                    <tr>
                        <td class="label-td">
                            <label for="telefono_cel" class="form-label">Teléfono Celular:</label>
                            <input type="text" name="telefono_cel" class="input-text" required>
                        </td>
                        <td class="label-td">
                            <label for="telefono_fijo" class="form-label">Teléfono Fijo:</label>
                            <input type="text" name="telefono_fijo" class="input-text">
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="email" class="form-label">Correo Electrónico:</label>
                            <input type="email" name="email" class="input-text" required>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td">
                            <label for="curp" class="form-label">CURP:</label>
                            <input type="text" name="curp" class="input-text" required>
                        </td>
                        <td class="label-td">
                            <label for="nss" class="form-label">NSS:</label>
                            <input type="text" name="nss" class="input-text" required>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="tutor_nombre" class="form-label">Nombre del Tutor:</label>
                            <input type="text" name="tutor_nombre" class="input-text">
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td">
                            <label for="tutor_parentesco" class="form-label">Parentesco del Tutor:</label>
                            <input type="text" name="tutor_parentesco" class="input-text">
                        </td>
                        <td class="label-td">
                            <label for="emergencia_nombre" class="form-label">Contacto de Emergencia:</label>
                            <input type="text" name="emergencia_nombre" class="input-text">
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="emergencia_telefono" class="form-label">Teléfono de Emergencia:</label>
                            <input type="text" name="emergencia_telefono" class="input-text">
                        </td>
                    </tr>
                </table>
                <input type="button" name="previous" class="previous action-button" value="Anterior">
                <input type="submit" name="submit" class="submit action-button" value="Guardar">
            </fieldset>
        </form>
    </div>
    </center>
</body>
</html>