<?php
session_start();
require_once 'conexao.php';
require_once "funcao.php";

if(isset($_SESSION['id_usuario'])){
    $id_perfil=$_SESSION['perfil'];
    $sqlperfil="SELECT nome_perfil FROM perfil WHERE id_perfil=:id_perfil";
    $stmtperfil=$pdo->prepare($sqlperfil);
    $stmtperfil->bindParam(':id_perfil',$id_perfil);
    $stmtperfil->execute();
    $perfil=$stmtperfil->fetch(PDO::FETCH_ASSOC);
    $nome_perfil=$perfil['nome_perfil'];
}else{
    $id_perfil=3;
    $sqlperfil="SELECT nome_perfil FROM perfil WHERE id_perfil=:id_perfil";
    $stmtperfil=$pdo->prepare($sqlperfil);
    $stmtperfil->bindParam(':id_perfil',$id_perfil);
    $stmtperfil->execute();
    $perfil=$stmtperfil->fetch(PDO::FETCH_ASSOC);
    $nome_perfil=$perfil['nome_perfil'];
}

$series=mostrarNovidades();
$permissoes=[
    1=>["função"=>["adicionar_obra.php","relatorio.php","adicionar_adm.php"]],
    2=>["função"=>["relatorio.php"]],
    3=>["função"=>["relatorio.php"]]
];
$opcoes_menu=$permissoes[$id_perfil];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id=$_POST['serie'];
    $serie=selecionarObra($id);
    $temporadas=selecionarTemporada($id);
    $temporada_f=selecionarTemporada1($id);
    $episodio_f1=selecionarEpisodio1($temporada_f['id_temporada']);
    $notas = $_POST['rating'] ?? 0;
    $avaliacaoRepetida = avaliar($_SESSION['id_usuario'],$episodio_f1['id_episodio'],$notas);
    if ($avaliacaoRepetida == TRUE) {
        echo "<script>
            window.onload = function() {
                confirmAction(" . json_encode($_SESSION['id_usuario']) . ", " . json_encode($_POST['nome']) . ", " . json_encode($id) . ");
            };
        </script>";
        $avaliacaoRepetida= FALSE;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Novidades</title>
    <link rel="stylesheet" href="principalll.css" />
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
                    </ul>
                </li>
            <?php endforeach;?>
            <form action="pesquisa.php" method="POST">
                <input type="text" name="pesquisa" class="pesquisa" id="pesquisa"  placeholder="Pesquisar" />
                <button type="submit">Pesquisar</button>
            </form>
            <li class="dropdown">
                <a href="#"><?php echo $nome_perfil;?></a>
                <ul class="dropdown-menu2">
                    <?php if($id_perfil!=3):?>
                        <li>
                            <a href="alterar_cliente.php">Atualizar dados</a>
                        </li>
                    <?php endif;?>
                    <li><a href="login.php">Trocar de conta</a></li>
                    <li><a href="logout.php">Sair da conta</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <h1 class="titulo-principal">Novidades</h1>

    <?php foreach($series as $serie): 
        if($serie['ativo']==1  ||  $id_perfil==1):?>

<form action="detales_obra.php" method="POST">
    <button type="submit" name="nome" value="<?=htmlspecialchars($serie["id_serie"])?>" style="all: unset; cursor: pointer;">
        <div class="card-serie">
            <div>
                <?php 
                $id=$serie['id_serie'];
                $temporadas=selecionarTemporada($id);
                $temporada_f=selecionarTemporada1($id);
                $episodio_f1=selecionarEpisodio1($temporada_f['id_temporada']);
                ?>
                <div class="mostrarSerie">
                    <img src="<?=htmlspecialchars($serie['imagem'])?>" alt="Imagem da série" />
                    <h2><?=htmlspecialchars($serie["nome_serie"])?></h2>
                    <h3><?=htmlspecialchars($serie["tipo"])?></h3>
                    <h4><?=htmlspecialchars($serie["genero"])?></h4>
                    <p><?=htmlspecialchars($serie["sinopse"])?></p>
                    <?php $nota = $episodio_f1['media_nota'] !== null ? number_format($episodio_f1['media_nota'], 1) : '0.0';?>
                    <p class="nota"><?=htmlspecialchars($nota)?>/10</p>
                    <input type="hidden" name="tipo_form" value="obra" />
                </div>
            </div>
        </div>
    </button>
</form>

<?php if($id_perfil==2):?>
    <button class="avaliarSerie" onclick="abrirModal('<?= htmlspecialchars($serie['nome_serie']);?>','<?= htmlspecialchars($serie['id_serie']);?>','meuModal')">Selecionar <?= htmlspecialchars($serie['nome_serie']) ?></button><br /><br />
<?php else:?>
    <button class="avaliarSerie" onclick="pedidoLogar()">Selecionar <?= htmlspecialchars($serie['nome_serie']) ?></button><br /><br />
<?php endif;?>

<?php endif; endforeach;?>

<div class="overlay" id="meuModal">
  <form class="modal" method="POST" action="principal.php">
    <h3>Confirmar seleção</h3>
    <p>Você selecionou: <span id="nomeEscolhido"></span></p>

    <input type="hidden" name="serie" id="inputNome" />
    <div class="star-rating" id="rating-container">
    <span class="star" data-value="0">&#9733;</span>
                <span class="star" data-value="1">&#9733;</span>
                <span class="star" data-value="2">&#9733;</span>
                <span class="star" data-value="3">&#9733;</span>
                <span class="star" data-value="4">&#9733;</span>
                <span class="star" data-value="5">&#9733;</span>
                <span class="star" data-value="6">&#9733;</span>
                <span class="star" data-value="7">&#9733;</span>
                <span class="star" data-value="8">&#9733;</span>
                <span class="star" data-value="9">&#9733;</span>
                <input type="hidden" id="rating-value" name="rating" value="0" />
    </div>
    
    <button type="submit">Enviar</button>
    <button type="button" onclick="fechar('meuModal')">Cancelar</button>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.star-rating').forEach(container => {
    const stars = container.querySelectorAll('.star');
    const input = container.querySelector('input[name="rating"]'); // <<< alterado aqui

    stars.forEach(star => {
      star.addEventListener('click', () => {
        const rating = parseInt(star.getAttribute('data-value')) + 1; // soma +1
        input.value = rating;

        // Limpa todas
        stars.forEach(s => s.classList.remove('filled'));

        // Adiciona "filled" até a clicada
        for (let i = 0; i < rating; i++) {
          stars[i].classList.add('filled');
        }
      });
    });
  });
});
</script>
</body>
</html>