<?php
require_once "conexao.php";
require_once "funcao.php";
//  VERIFICA SE O ID FOI ENVIADO

$opcoes = ['Anime', 'Serie', 'Filme', 'Livro','Jogo'];
//  SÃO AS OPÇÕES DE GENÊROS DE OBRAS PARA FAZER AS CHECKBOXES
$genero=["ação","animação","aventura","biografia ou autobiografia","comedia","corrida","documentário","drama","esporte",
"estratégia","fantasia","ficcao cientifica","fps","historico","isekai","josei","literatura clássica","mecha","mmorpg","uusical","nao ficcao",
"plataforma","policial ou crime","puzzle","rpg","romance","seinen","shōjo","shōnen","simulação","slice of life","suspense ou thriller",
"terror ou horror","terro psicológico","terror de sobrevivencia"];
if (!isset($_GET['id'])) {
    die("ID da série não informado.");
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
if(isset($_FILES['imagem_obra']) && $_FILES['imagem_obra']['error'] === UPLOAD_ERR_OK){
    $arquivo=$_FILES['imagem_obra'];
    $pasta="img/";
    if($arquivo['error']){
        die('azul');
    }
    $nome_arquivo=$arquivo['name'];
    $novo_nomeArquivo=uniqid();
    
    $extensao=strtolower(pathinfo($nome_arquivo,PATHINFO_EXTENSION));
    if($extensao!="jpg" && $extensao!="png"){
        die('vermelho');
    }
    $path=$pasta.$novo_nomeArquivo.".".$extensao;
    $deu_certo=move_uploaded_file($arquivo["tmp_name"],$path);
    if($deu_certo){
        echo "<p>Arquivo enviado com sucesso</p>";
    }else{
        die('verde');
    }
}

    if (!empty($_POST['genero'])) {
        $opcoesSelecionadas = $_POST['genero']; //  ISSO É UM ARRAY

        //  JUNTA TODOS OS VALORES EM UMA STRING SEPARADA POR VÍRGULAS
        $stringFinal = implode(', ', $opcoesSelecionadas);

        echo "Opções selecionadas: " . htmlspecialchars($stringFinal);
    } else {
    echo "Nenhuma opção foi selecionada.";
    }
}
$id = intval($_GET['id']);

// CONSULTA PARA OBTER OS DADOS DA SÉRIE
$sql = "SELECT * FROM serie WHERE id_serie = $id";
$result = $pdo->query($sql);



$serie = $result->fetch(PDO::FETCH_ASSOC);

// SE O FORMULÁRIO FOI ENVIADO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome_serie'];
    $imagem = $_POST['imagem'];
    $genero = $_POST['genero'];
    $tipo = $_POST['tipo'];
    $sinopse = $_POST['sinopse'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    $sql = "
        UPDATE serie SET 
            nome_serie = :nome,
            imagem = :imagem,
            genero = :genero,
            tipo = :tipo,
            sinopse = :sinopse,
            ativo = :ativo
        WHERE id_serie = :id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    
    $stmt->bindParam(":genero",$stringFinal);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':sinopse', $sinopse);
    $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    if($_FILES['imagem_obra']['error'] === UPLOAD_ERR_OK){
    $stmt->bindParam(":imagem",$path);
    }else{
        $stmt->bindParam(":imagem",$serie['imagem']);
    }
    if ($stmt->execute()) {
        echo "<p>Série atualizada com sucesso!</p>";
        header('Location: principal.php');
        exit();
    } else {
        echo "<p>Erro ao atualizar a série.</p>";
    }
}



?>

<!--FORMULÁRIO DE EDIÇÃO-->
<link rel="stylesheet" href="editarr_serie.css">
<script src="script.js"></script>
<h2>Editar Série</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Nome:</label><br>
    <input type="text" name="nome_serie" value="<?= htmlspecialchars($serie['nome_serie']) ?>"><br><br>

    <label></label><br>
    <img src= "<?=htmlspecialchars($serie['imagem'])?>" width='200'><br><br>


   

    
    <label for="imagem_obra">Trocar a imagem da série:</label><br>
        <!--USANDO UM INPUT TYPE FILE PARA ISSO-->
        <input type="file"    name="imagem_obra" id="imagem_obra"><br><br>

        <select name="tipo" id="tipo">
        <!--FAZ O SELECT ATRAVÉS DE UM FOREACH ELE PEGA A VARIAVEL OPÇÃO COM O NOME DE TODOS OS TIPOS DE OBRAS E MOSTRA ATRAZÉS DO PHP-->
        <?php foreach ($opcoes as $opcao): ?>
            <option value="<?php echo $opcao; ?>"><?php echo $opcao; ?></option>
        <?php endforeach; ?>
        </select>

        <!--UM CAMPO PARA COLOCAR O GÊNERO DA OBRA-->
        <div class="submenu-wrapper">
            <!--ATRAVÉS DA FUNÇÃO TOGGLESUBMENU FAZ UM SUB-MENU AO CLICAR NO BOTAO-->
            <button class="botaoSubmenu" type="button" onclick="toggleSubmenu(1)">Mais opções</button>

            
            <!--SUB-MENU COM CHECKBOXES (CRIADO COM FOREACH)-->
            <div class="submenu" id="submenu-1">
           <?php  $selecionados = array_map('trim', explode(',', $serie['genero']));?>


                <?php foreach ($genero as $valor): ?>
                    <label>
                        <input class="submenuCheckbox" type="checkbox" name="genero[]" value="<?php echo $valor; ?>" 
                            <?php echo in_array($valor, $selecionados) ? 'checked' : ''; ?>
                        >
                        <?php echo $valor; ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        

        

    <label>Sinopse:</label><br>
    <textarea name="sinopse" rows="4" cols="50"><?= htmlspecialchars($serie['sinopse']) ?></textarea><br><br>

    <label>Ativo:</label>
    <input type="checkbox" name="ativo" <?= $serie['ativo'] ? 'checked' : '' ?>><br><br>

    <input type="submit" value="Salvar Alterações">
</form>