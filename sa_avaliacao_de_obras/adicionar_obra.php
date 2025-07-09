<?php
//  SÃO AS OPÇÕES DE TIPOS DE OBRAS PARA O SELECT
$opcoes = [ 'Série', 'Filme', 'Livro'];
//  SÃO AS OPÇÕES DE GENEROS DE OBRAS PARA FAZER AS CHECKBOXES
$genero=["Ação", "Animação", "Aventura", "Biografia ou Autobiografia", "Comédia", "Corrida", "Documentário", "Drama", "Esporte",
"Estratégia", "Fantasia", "Ficção Científica", "FPS", "Histórico", "Isekai", "Josei", "Literatura Clássica", "Mecha", "MMORPG", "Musical", "Não Ficção",
"Plataforma", "Policial ou Crime", "Puzzle", "RPG", "Romance", "Seinen", "Shōjo", "Shōnen", "Simulação", "Slice of Life", "Suspense ou Thriller",
"Terror ou Horror", "Terror Psicológico", "Terror de Sobrevivência"];

session_start();
require_once 'conexao.php';
if(isset($_SESSION['id_usuario'])==1){
    $id_perfil=$_SESSION['perfil'];
    $sqlperfil="SELECT nome_perfil FROM perfil WHERE id_perfil=:id_perfil";
    $stmtperfil=$pdo->prepare($sqlperfil);
    $stmtperfil->bindParam(':id_perfil',$id_perfil);
    $stmtperfil->execute();
    $perfil=$stmtperfil->fetch(PDO::FETCH_ASSOC);
    $nome_perfil=$perfil['nome_perfil'];
  }else{
    echo "<script>alert('Acesso negado!'); window.location.href='principal.php';</script>";
    exit();
  }
  $permissoes=[
    1=>["função"=>["relatorio.php","adicionar_adm.php"]],
    
    2=>["função"=>["relatorio.php"]],
  
    3=>["função"=>[]]
  
    
  ];
  //    OBTENDO AS OPÇÕES DISPONÍVEIS PARA O PERFIL LOGADO
  $opcoes_menu=$permissoes[$id_perfil];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de cliente</title>
    <link rel="stylesheet" href="adicionarr_obra.css">
    <script src="script.js"></script>
    

</head>
<body>
<nav>
        <ul class="menu">
            <?php foreach($opcoes_menu as $categoria=>$arquivos):?>
                <li class="dropdown">
                    <a href="#"><?=$categoria?></a>
                    <ul class="dropdown-menu">
                        <?php foreach($arquivos as $arquivo):?>
                            <li>
                                <a href="<?=$arquivo?>"><?=ucfirst(str_replace("_"," ",basename($arquivo,".php")))?></a>
                            </li>
                            <?php endforeach;?>
                            <li>
                                <a href="principal.php">Novidades</a>
                            </li>
                    </ul>

                </li>

                <?php endforeach;?>
                <form action="pesquisa.php" method="POST">
                <input type="text" name="pesquisa" class="pesquisa" id="pesquisa"  placeholder="Pesquisar" >
                    <button type="submit">Pesquisar</button>
                </form>
                <li class="dropdown">
                    <a href="#"><?php echo $nome_perfil;?></a>
                    <ul class="dropdown-menu2">
                    <?php if($id_perfil!=3):?>
                        <li>
                            <a href="alterar_cliente.php">atualizar dados</a>
                        </li>
                      <?php endif;?>
                        <li>
                            <a href="login.php">Trocar de conta</a>
                        </li>
                        <li>
                            <a href="logout.php">Sair da conta</a>
                        </li>
                    </ul>
                </li>
        </ul>
    </nav>
    <h2>Adicionar Obra</h2>
    <form class="adicionarObra" enctype="multipart/form-data" action="processar_add_obra.php" method="POST">
        <!-- UM CAMPO PARA COLOCAR O NOME DA OBRA-->
        <label for="nome_obra">Nome da obra:</label>
        <input type="text" id="nome_obra" name="nome_obra" required>

        <!--UM CAMPO PARA COLOCAR O TIPO DA OBRA-->
        <select name="tipo" id="tipo">
        <!--FAZ O SELECT ATRAVÉS DE UM FOREACH ELE PEGA A VARIAVEL OPÇÃO COM O NOME DE TODOS OS TIPOS DE OBRAS E MOSTRA ATRAVÉS DO PHP-->
        <?php foreach ($opcoes as $opcao): ?>
            <option value="<?php echo $opcao; ?>"><?php echo $opcao; ?></option>
        <?php endforeach; ?>
        </select>

        <!--UM CAMPO PARA COLOCAR O GÊNERO DA OBRA-->
        <div class="submenu-wrapper">
            <!--ATRAVÉS DA FUNÇÃO TOGGLESUBMENU FAZ UM SUB-MENU AO CLICAR NO BOTAO-->
            <button class="botaoSubmenu" type="button"   onclick="toggleSubmenu(1)">Mais opçoes</button>


            <!-- SUB-MENU COM CHECKBOXES (CRIADO COM FOREACH) -->
            <div class="submenu" id="submenu-1">
                <!--USA UM FOREACH PARA FAZER OS CAMPOS DE CHECKBOX-->
                <?php foreach ($genero as $valor): ?>
                    <label>
                        <input class="submenuCheckbox" type="checkbox" name="genero[]" value="<?php echo $valor; ?>">
                        <?php echo $valor; ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!--UM CAMPO PARA COLOCAR A SINOPSE DA OBRA-->
        <label for="sinopse">Sinopse:</label>
        <!--ATRAZES DE UMA TEXTAREA-->
        <textarea name="sinopse" id="sinopse" rows="5" cols="40"></textarea><br><br>


        <!--UM CAMPO PARA COLOCAR UMA IMAGEM PARA A OBRA-->
        <label for="imagem_obra">Selecione uma imagem para a obra:</label><br>
        <!--USANDO UM INPUT TYPE FILE PARA ISSO-->
        <input type="file" name="imagem_obra" id="imagem_obra"><br><br>
        

        <button class="submitAddObra" type="submit">Adicionar Obra</button>
</form>
</body>
</html>