<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar senha</title>
    <link rel="stylesheet" href="enviar_email.css">
</head>
<body>
    <h2>Recuperar senha</h2>
    <form action="verificar_email.php" method="POST">
    <label for="email">Digite o seu e-mail cadastrado:</label>
    <input type="email" id="email" name="email" required>
    <button type="submit">Enviar codigo de verificação para o e-mail</button>


    </form>
</body>
</html>