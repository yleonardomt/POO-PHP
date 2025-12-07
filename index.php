<?php
session_start();

// Archivo para guardar mensajes
$archivo = "mensajes.txt";

// Asignar un usuario aleatorio (Persona 1 o Persona 2) si no existe
if(!isset($_SESSION['usuario'])) {
    $_SESSION['usuario'] = "Persona " . rand(1,2);
}

// Guardar nuevo mensaje enviado por formulario (AJAX o normal)
if(isset($_POST['mensaje']) && !empty($_POST['mensaje'])) {
    $msg = date('H:i:s') . " - " . $_SESSION['usuario'] . ": " . htmlspecialchars($_POST['mensaje']) . "\n";
    file_put_contents($archivo, $msg, FILE_APPEND);
    exit; // Para AJAX
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Chat 2 personas en tiempo real</title>
    <style>
        body { font-family: Arial; margin: 50px; }
        #chat { border: 1px solid #ccc; height: 300px; overflow-y: scroll; padding: 10px; background: #f9f9f9; }
        input { width: 80%; padding: 5px; }
        button { padding: 5px 10px; }
    </style>
</head>
<body>
    <h1>Chat en tiempo real</h1>
    <p>Tu usuario: <b><?php echo $_SESSION['usuario']; ?></b></p>
    <div id="chat"></div>
    <form id="formulario">
        <input type="text" id="mensaje" autocomplete="off" placeholder="Escribe tu mensaje">
        <button type="submit">Enviar</button>
    </form>

    <script>
        // Función para actualizar mensajes
        function actualizarChat() {
            fetch('mensajes.txt')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('chat').innerText = data;
                    document.getElementById('chat').scrollTop = document.getElementById('chat').scrollHeight;
                });
        }

        // Envía mensaje por AJAX sin recargar
        document.getElementById('formulario').addEventListener('submit', function(e){
            e.preventDefault();
            let mensaje = document.getElementById('mensaje').value;
            if(mensaje.trim() === '') return;

            fetch('', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'mensaje=' + encodeURIComponent(mensaje)
            }).then(() => {
                document.getElementById('mensaje').value = '';
                actualizarChat();
            });
        });

        // Actualiza chat cada segundo
        setInterval(actualizarChat, 1000);
        actualizarChat();
    </script>
</body>
</html>
