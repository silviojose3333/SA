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
$imagens=mostrarMelhores();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
    <h1 class="titulo-principal">Melhor Avaliados</h1>

    

<style>
  .carousel {
    position: relative;
    padding: 0 80px; /* espaço para as setas fora da imagem */
  }

  .carousel-item {
    text-align: center;
  }

  .carousel-item img {
    height: 500px; /* 7 polegadas */
    width: 350px;  /* 5 polegadas */
    object-fit: cover;
    margin: 0 auto; /* centraliza a imagem */
  }

  .carousel-title {
    text-align: center;
    margin-top: 10px;
    font-weight: bold;
    font-size: 1.25rem;
  }

  /* Move setas para fora da imagem */
  .carousel-control-prev,
  .carousel-control-next {
    width: 60px;
    top: 50%;
    transform: translateY(-50%);
  }

  .carousel-control-prev {
    left: -60px;
  }

  .carousel-control-next {
    right: -60px;
  }
  .overlay2 {
  position: fixed;
  top: 0; left: 0;
  width: 100vw; height: 100vh;
  background: rgba(0, 0, 0, 0.85);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 20000;
  backdrop-filter: blur(8px);
  padding: 40px 15px;
}

.modal2 {
  background: #111;
  border: 3px solid #FFD700;
  padding: 40px;
  border-radius: 20px;
  box-shadow: 0 0 20px #FFD70099;
  max-width: 500px;
  width: 100%;
  color: #FFD700;
  text-align: center;
}

.modal2 h3 {
  margin-bottom: 20px;
  font-size: 1.8rem;
  text-shadow: 0 0 6px #FFD700;
}

.modal2 button {
  margin: 20px 10px 0;
  padding: 10px 22px;
  border: 2px solid #FFD700;
  border-radius: 10px;
  font-family: 'Georgia', serif;
  font-size: 1rem;
  font-weight: 600;
  color: #FFD700;
  background: transparent;
  transition: 0.3s ease;
  cursor: pointer;
}

.modal2 button:hover {
  background: #FFD700;
  color: #1a1a1a;
  box-shadow: 0 0 12px #FFD700;
}

.star-rating {
  direction: ltr;
  font-size: 2.2rem;
  display: flex;
  justify-content: center;
  margin: 20px 0;
  cursor: pointer;
  user-select: none;
}

.star {
  color: #333;
  padding: 0 6px;
  transition: color 0.3s ease;
}

.star:hover,
.star.filled {
  color: #ffd700;
  filter: drop-shadow(0 0 5px #ffd700);
}
</style>

<div id="meuCarrossel" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <?php foreach ($imagens as $index => $item): ?>
      <div class="carousel-item <?php if ($index === 0) echo 'active'; ?>">
        <a href="detales_obra.php?nome=<?= $item['id_serie'] ?>">
        <?php $id=$item['id_serie'];
                $temporadas=selecionarTemporada($id);
                $temporada_f=selecionarTemporada1($id);
                $episodio_f1=selecionarEpisodio1($temporada_f['id_temporada']);
                $nota = $episodio_f1['media_nota'] !== null ? number_format($episodio_f1['media_nota'], 1) : '0.0';
                $numero=$index+1;
                ?>
          <img src="<?= $item['imagem'] ?>" class="d-block" alt="<?=$numero?>-<?= $item['nome_serie'] ?>-<?= $nota?>">
        </a>
        <div class="carousel-title">
        <?=htmlspecialchars($numero)?>-<?= htmlspecialchars($item['nome_serie']) ?>-<?= htmlspecialchars($nota)?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <button class="carousel-control-prev" type="button" data-bs-target="#meuCarrossel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#meuCarrossel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>



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
<?php $modal="azul"?>
<?php if($id_perfil==2):?>
    <button class="avaliarSerie" onclick="abrirModal('<?= htmlspecialchars($serie['nome_serie']);?>','<?= htmlspecialchars($serie['id_serie']);?>','<?= htmlspecialchars($modal);?>')">Selecionar <?= htmlspecialchars($serie['nome_serie']) ?></button><br /><br />
<?php else:?>
    <button class="avaliarSerie" onclick="pedidoLogar()">Selecionar <?= htmlspecialchars($serie['nome_serie']) ?></button><br /><br />
<?php endif;?>

<?php endif; endforeach;?>

<div id="<?= htmlspecialchars($modal);?>" class="overlay2">
  <div class="modal2">
    <form method="POST" action="principal.php">
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
      <button type="button" onclick="fechar('<?= htmlspecialchars($modal);?>')">Cancelar</button>
    </form>
  </div>
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