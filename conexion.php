<?php
$servername = "localhost"; // insertarmos el nombre del servidor
$username = "root";  // insertamos el nombre que usaremos del servidor por default esta root 
$password = "";   //como sabemos que root no tiene contraseña dejamos en blanco esta casilla 
$dbname = "users"; // agregamos el nombre de la base de datos que usaremos 

 try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password); //se encarga de agregar la base de datos que tenemos para poder usarlo 
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //establece el modo de error en el modo "excepción", lo que significa que cuando se produce un error, se lanzará una excepción de PDO.
    echo "Conexión establecida correctamente"; //caso en el que se conecte a la base de datos nos aparecera este mensaje 
 } catch(PDOException $e) { //caso contraio no se conecte al servidor nos aparecera este mensaje 
    echo "Error al conectar a la base de datos: " . $e->getMessage();
 }
 function validar_datos($datos) { // definimos una funcion para poder validar datos , datos sera enviado como un array tambien solo podemos configurarlo para que pueda presentar valores tipo varchar 
    $errores = array(); //creara un array con el nombre de eroor

    if (empty($datos['nombre'])) { //condicionamos si el primer elemnto del array datos esta vacio nos dira que tenemos que poner este dato 
        $errores[] = 'El campo nombre es obligatorio';
    }

    if (empty($datos['apellido'])) {//condicionamos si el primer elemnto del array datos esta vacio nos dira que tenemos que poner este dato 
        $errores[] = 'El campo apellido es obligatorio';
    }

    if (empty($datos['email'])) { //condicionamos si el primer elemnto del array datos esta vacio nos dira que tenemos que poner este dato 
        $errores[] = 'El campo email es obligatorio';
    } else if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) { // luego verificara que la sintaxis de dirección de correo electrónico especificada en la RFC 822 es valida procedera 
        $errores[] = 'El campo email no es válido';
    }

    return $errores; // nos devolvera los errores que vea en forma de un array 
}
 function obtener_personas($conn) { //definimos una funcion para poder obtener los usuarios de una base de datos en este caso mi base de datos se llama personas 
    $sql = "SELECT * FROM personas"; //lo que hace es seleccionar la base de datos "personas"
    $stmt = $conn->query($sql);//definimos otra variable el cual usara un query es decir que llamara a esta base de datos 
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // lo usamos para recuperar los valores de mi base de datos 
}
function obtener_persona_por_id($conn, $id) { // definimos una funcion obtener personas por el id 
    $sql = "SELECT * FROM personas WHERE ID = :id"; // le decimos que queremos obtener los valores de acuerdo al id funciona como un diccionario 
    $stmt = $conn->prepare($sql);//consulta a la base de datos por los valores de este 
    $stmt->bindParam(':id', $id);// le decimos que queremos consultar los valores de acuerdo al id de este 
    $stmt->execute();//nos arrojara los valores de este
    return $stmt->fetch(PDO::FETCH_ASSOC);  // lo usamos para recuperar los valores de mi base de datos 
}
function insertar_persona($conn,$nombre,$apellido,$email) { //definimos una funcion para poder agregar o insertar un usuario en nuestra base de datos 
    $sql = "INSERT INTO personas (nombre, Apellido, Email) VALUES (:nombre, :apellido, :email)"; // le decimos que queremos insertar a un  usuario que tendra nombre , apellido ,email psdt: el id no es necesario ya que este es un primary key y que tambien esta en modo auto_increment 
    $stmt = $conn->prepare($sql);//se comunica con la base de datos 
    $stmt->bindParam(':nombre',$nombre); //le da los valores que esta en nombre 
    $stmt->bindParam(':apellido',$apellido);//le da los valores que esta en apellido
    $stmt->bindParam(':email',$email);//le da los valores que esta email
    return $stmt->execute(); //de acuerdo a esto le pasaremos los 3 valores 
}
function actualizar_Persona($pdo, $id, $nombre, $apellido, $email) { //definimos un modulo para actualiar persona
    try {
        // Validar los datos antes de actualizarlos en la base de datos
        if(empty($nombre) || empty($apellido) || empty($email)) { // verificamos que los espacios nombre , apellido , email no esten vacios 
            return false;
        }

        $sql = "UPDATE personas SET Nombre=?, Apellido=?, Email=? WHERE ID=?"; //le decimos que queremo actauliar el nombre , apellido , email
        $stmt= $pdo->prepare($sql);//nos comunicamos con la base de datos
        $stmt->execute([$nombre, $apellido, $email, $id]); //escribimos una array con tosos los datos que deben de pasarnos y tambien el id de la persona a la que modificaremos

        return true;
    } catch (PDOException $e) {
        echo "Error al actualizar los datos: " . $e->getMessage(); // caso que no se pueda comuncicar con el servidor y no nos actualice los valores nos saldra el siguiente mensaje 
        return false;
    }
}
function elimiar_Persona($pdo, $id) { //definimos una funcion eliminar personas
    try {
        $sql = "DELETE FROM personas WHERE ID=?";//le decimos a la bae de datos  que queremos borrar a una persona de acuerdo al id de este 
        $stmt= $pdo->prepare($sql);//nos comunicamos con la base de datos
        $stmt->execute([$id]);//le decimos que utilizaremos el id para poder ejecutarlo 

        return true;
    } catch (PDOException $e) {
        echo "Error al eliminar los datos: " . $e->getMessage(); // caso que no se pueda comuncicar con el servidor 
        return false;
    }
}

// Ejemplo de uso
// print_r(obtener_persona_por_id($conn,1));// mostrar un usuario por su id 

//print_r(obtener_personas($conn)); //mostrar los usuarios 
//print_r(validar_datos(obtener_personas($conn))) // validar la infromacion de un usuario 
//print_r(elimiar_Persona($conn,3));  //eliminar usuario de acuerdo a su id 
//echo insertar_persona($conn,'Ray Marcelo', 'Ibarra Huamancari', 'dasfqeqeadadq@gmail.com');
print_r(obtener_personas($conn));

// Cerrar conexión a la base de datos
?>