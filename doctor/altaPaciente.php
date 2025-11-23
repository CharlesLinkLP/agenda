<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/signup.css"> 
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
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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
        <table border="0">
            <tr>
                <td colspan="2">
                    <p class="header-text">Expediente</p>
                    <p class="sub-text">Agrega tus datos personales para continuar</p>
                </td>
            </tr>
            <form action="" method="POST" >
                <!-- Nombre completo -->
                <tr>
                    <td class="label-td" colspan="2">
                        <label class="form-label">Nombre completo: </label>
                    </td>
                </tr>
                <tr>
                    <td class="label-td">
                        <input type="text" name="apaterno" class="input-text" placeholder="Apellido Paterno" required>
                    </td>
                    <td class="label-td">
                        <input type="text" name="amaterno" class="input-text" placeholder="Apellido Materno" required>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <input type="text" name="nombres" class="input-text" placeholder="Nombre(s)" required>
                    </td>
                </tr>

                <!-- Fecha de nacimiento y edad -->
                <tr>
                    <td class="label-td">
                        <label class="form-label">Fecha de nacimiento: </label>
                        <input type="date" name="dob" class="input-text" required>
                    </td>
                    <td class="label-td">
                        <label class="form-label">Edad: </label>
                        <input type="number" name="edad" class="input-text" placeholder="Años cumplidos">
                    </td>
                </tr>

                <!-- Sexo y Estado Civil -->
                <tr>
                    <td class="label-td">
                        <label class="form-label">Sexo: </label>
                        <select name="sexo" class="input-text">
                            <option value="">Seleccione...</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </td>
                    <td class="label-td">
                        <label class="form-label">Estado Civil: </label>
                        <select name="estado_civil" class="input-text">
                            <option value="">Seleccione...</option>
                            <option value="soltero">Soltero(a)</option>
                            <option value="casado">Casado(a)</option>
                            <option value="union_libre">Unión Libre</option>
                            <option value="divorciado">Divorciado(a)</option>
                            <option value="viudo">Viudo(a)</option>
                        </select>
                    </td>
                </tr>

                <!-- Ocupación -->
                <tr>
                    <td class="label-td" colspan="2">
                        <label class="form-label">Ocupación: </label>
                        <input type="text" name="ocupacion" class="input-text" placeholder="Actividad principal">
                    </td>
                </tr>

                <!-- Domicilio -->
                <tr>
                    <td class="label-td" colspan="2">
                        <label class="form-label">Domicilio: </label>
                    </td>
                </tr>
                <tr>
                    <td class="label-td">
                        <input type="text" name="calle" class="input-text" placeholder="Calle">
                    </td>
                    <td class="label-td">
                        <input type="text" name="numero" class="input-text" placeholder="Número">
                    </td>
                </tr>
                <tr>
                    <td class="label-td">
                        <input type="text" name="colonia" class="input-text" placeholder="Colonia">
                    </td>
                    <td class="label-td">
                        <input type="text" name="cp" class="input-text" placeholder="Código Postal">
                    </td>
                </tr>
                <tr>
                    <td class="label-td">
                        <input type="text" name="ciudad" class="input-text" placeholder="Ciudad">
                    </td>
                    <td class="label-td">
                        <input type="text" name="estado" class="input-text" placeholder="Estado">
                    </td>
                </tr>

                <!-- Teléfonos -->
                <tr>
                    <td class="label-td">
                        <label class="form-label">Teléfono Celular: </label>
                        <input type="tel" name="telefono_cel" class="input-text" placeholder="Celular">
                    </td>
                    <td class="label-td">
                        <label class="form-label">Teléfono Fijo: </label>
                        <input type="tel" name="telefono_fijo" class="input-text" placeholder="Fijo">
                    </td>
                </tr>

                <!-- Email, CURP y NSS -->
                <tr>
                    <td class="label-td" colspan="2">
                        <label class="form-label">Correo electrónico: </label>
                        <input type="email" name="email" class="input-text" placeholder="correo@ejemplo.com">
                    </td>
                </tr>
                <tr>
                    <td class="label-td">
                        <label class="form-label">CURP: </label>
                        <input type="text" name="curp" class="input-text" placeholder="CURP" required>
                    </td>
                    <td class="label-td">
                        <label class="form-label">NSS: </label>
                        <input type="text" name="nss" class="input-text" placeholder="Número de Seguro Social">
                    </td>
                </tr>

                <!-- Tutor -->
                <tr>
                    <td class="label-td">
                        <label class="form-label">Nombre del tutor: </label>
                        <input type="text" name="tutor_nombre" class="input-text" placeholder="Nombre completo">
                    </td>
                    <td class="label-td">
                        <label class="form-label">Parentesco: </label>
                        <input type="text" name="tutor_parentesco" class="input-text" placeholder="Parentesco">
                    </td>
                </tr>

                <!-- Contacto de emergencia -->
                <tr>
                    <td class="label-td">
                        <label class="form-label">Contacto de emergencia: </label>
                        <input type="text" name="emergencia_nombre" class="input-text" placeholder="Nombre">
                    </td>
                    <td class="label-td">
                        <label class="form-label">Teléfono: </label>
                        <input type="tel" name="emergencia_telefono" class="input-text" placeholder="Teléfono" required>
                    </td>
                </tr>

                <!-- Botones -->
                <tr>
                    <td>
                        <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >
                    </td>
                    <td>
                        <input type="submit" value="Next" class="login-btn btn-primary btn">
                    </td>
                </tr>
            </form>
        </table>
    </div>
    </center>
</body>
</html>