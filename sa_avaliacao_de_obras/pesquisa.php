<?php
session_start();
require_once 'conexao.php';
require_once 'funcao.php';

// Definir perfil do usuário (ou perfil padrão)
if (isset($_SESSION['id_usuario'])) {
    $id_perfil = $_SESSION['perfil'];
} else {
    $id_perfil = 3; // Perfil padrão visitante
}

// Buscar nome do perfil
$sqlperfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtperfil = $pdo->prepare($sqlperfil);
$stmtperfil->bindParam(':id_perfil', $id_perfil, PDO::PARAM_INT);
$stmtperfil->execute();
$perfil = $stmtperfil->fetch(PDO::FETCH_ASSOC);
$nome_perfil = $perfil['nome_perfil'] ?? 'Visitante';

// Captura inputs com filtro e valores padrão
$pesquisa = $_POST['pesquisa'] ?? '';
$opc = $_POST['a'] ?? ' ';
$interesses = $_POST['generos'] ?? [];
$opcoes = [ 'Série', 'Filme', 'Livro'];
$selecionada = $_POST['tipo'] ?? '';

// Lista de gêneros (corrigi alguns erros de digitação)
$genero = [
    "Ação", "Animação", "Aventura", "Biografia ou Autobiografia", "Comédia", "Corrida", "Documentário", "Drama", "Esporte",
"Estratégia", "Fantasia", "Ficção Científica", "FPS", "Histórico", "Isekai", "Josei", "Literatura Clássica", "Mecha", "MMORPG", "Musical", "Não Ficção",
"Plataforma", "Policial ou Crime", "Puzzle", "RPG", "Romance", "Seinen", "Shōjo", "Shōnen", "Simulação", "Slice of Life", "Suspense ou Thriller",
"Terror ou Horror", "Terror Psicológico", "Terror de Sobrevivência"
];

// Buscar séries conforme critérios
$series = pesquisar($pesquisa, $selecionada, $interesses, $opc ?: 'Z');

// Permissões por perfil
$permissoes = [
    1 => ["função" => ["adicionar_obra.php", "relatorio.php", "adicionar_adm.php"]],
    2 => ["função" => ["relatorio.php"]],
    3 => ["função" => []],
];

// Opções disponíveis para menu
$opcoes_menu = $permissoes[$id_perfil] ?? ['funcao' => []];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pesquisa</title>
    <link rel="stylesheet" href="pesquisa.css" />
    <style>
        .star-rating {
            direction: rtl;
            unicode-bidi: bidi-override;
            font-size: 2rem;
            display: inline-flex;
            cursor: pointer;
        }
        .star {
            color: #ccc;
            transition: color 0.2s;
        }
        .star.filled {
            color: gold;
        }
        /* Estilo submenu para "Mais opções" */
        .submenu-wrapper {
            margin-top: 10px;
        }
        .submenu {
            display: none;
            margin-top: 10px;
            border: 1px solid #ffd700;
            padding: 15px;
            border-radius: 8px;
            max-height: 200px;
            overflow-y: auto;
            background:rgb(0, 0, 0);
        }
        .submenu ul {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }
        .submenu li {
            margin-bottom: 5px;
        }
    </style>
    <script>
        function toggleSubmenu(id) {
            const submenu = document.getElementById('submenu-' + id);
            if (submenu.style.display === 'block') {
                submenu.style.display = 'none';
            } else {
                submenu.style.display = 'block';
            }
        }

        function abrirModal(nome, id, modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = 'flex';
            document.getElementById('nomeEscolhido').textContent = nome;
            document.getElementById('inputNome').value = id;
        }

        function pedidoLogar() {
            alert('Você precisa estar logado para selecionar esta opção.');
        }

        function inicializarAvaliacao() {
            const estrelas = document.querySelectorAll('.star');
            const inputRating = document.getElementById('rating-value');

            estrelas.forEach(estrela => {
                estrela.addEventListener('click', function () {
                    const valor = parseInt(this.getAttribute('data-value'), 10);
                    atualizarEstrelas(valor);
                    salvarValor(valor);
                });
            });

            function atualizarEstrelas(valorSelecionado) {
                estrelas.forEach(estrela => {
                    if (parseInt(estrela.getAttribute('data-value'), 10) <= valorSelecionado) {
                        estrela.classList.add('filled');
                    } else {
                        estrela.classList.remove('filled');
                    }
                });
                inputRating.value = valorSelecionado + 1; // Ajuste se quiser que seja de 1 a 10
            }

            function salvarValor(valor) {
                // Aqui você pode adicionar código para salvar a avaliação imediatamente, se quiser.
            }
        }

        window.onload = inicializarAvaliacao;
    </script>
</head>
<body>
<nav>
    <ul class="menu">
        <?php foreach ($opcoes_menu['função'] as $arquivo): ?>
            <li><a href="<?= htmlspecialchars($arquivo) ?>"><?= ucfirst(str_replace("_", " ", basename($arquivo, ".php"))) ?></a></li>
        <?php endforeach; ?>
        <li><a href="principal.php">Novidades</a></li>
        <li class="dropdown">
            <a href="#"><?= htmlspecialchars($nome_perfil) ?></a>
            <ul class="dropdown-menu2">
                <?php if ($id_perfil != 3): ?>
                    <li><a href="alterar_cliente.php">Atualizar dados</a></li>
                <?php endif; ?>
                <li><a href="login.php">Trocar de conta</a></li>
                <li><a href="logout.php">Sair da conta</a></li>
            </ul>
        </li>
    </ul>
</nav>

<form action="pesquisa.php" method="POST">
    <input
        type="text"
        name="pesquisa"
        class="pesquisa"
        id="pesquisa"
        placeholder="Pesquisa"
        value="<?= htmlspecialchars($pesquisa) ?>"
    />
    <br />
    <label><input name="a" type="radio" value="Z" <?= ($opc == 'Z') ? 'checked' : '' ?> /> A-Z</label>
    <label><input name="a" type="radio" value="A" <?= ($opc == 'A') ? 'checked' : '' ?> /> Melhor avaliado</label>
    <label><input name="a" type="radio" value="R" <?= ($opc == 'R') ? 'checked' : '' ?> /> Mais Recente</label>
    <br />

    <div class="submenu-wrapper">
        <button type="button" onclick="toggleSubmenu(1)">Mais opções</button>
        <div class="submenu" id="submenu-1">
            <ul>
            <?php foreach ($genero as $valor):
                $checked = in_array($valor, $interesses) ? 'checked' : '';
            ?>
                <li>
                    <label>
                        <input type="checkbox" name="generos[]" value="<?= htmlspecialchars($valor) ?>" <?= $checked ?> />
                        <?= htmlspecialchars(ucfirst($valor)) ?>
                    </label>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <select name="tipo" id="tipo" style="margin-top: 10px;">
        <option value="" <?= empty($selecionada) ? 'selected' : '' ?>>-- Todos os tipos --</option>
        <?php foreach ($opcoes as $opcao): ?>
            <option value="<?= htmlspecialchars($opcao) ?>" <?= ($opcao === $selecionada) ? 'selected' : '' ?>>
                <?= htmlspecialchars($opcao) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <br /><br />
    <input type="submit" value="Executar" />
</form>

<?php foreach ($series as $serie):
    if ($serie['ativo'] == 1 || $id_perfil == 1):
        $temporada_f = selecionarTemporada1($serie['id_serie']);
        $episodio_f1 = selecionarEpisodio1($temporada_f['id_temporada'] ?? null);
        $nota = isset($episodio_f1['media_nota']) ? number_format($episodio_f1['media_nota'], 1) : '0.0';
?>
<form action="detales_obra.php" method="POST">
    <button
        type="submit"
        name="nome"
        value="<?= htmlspecialchars($serie["id_serie"]) ?>"
        style="all: unset; cursor: pointer;"
    >
        <div
            style="
                border: none;
                padding: 20px;
                margin: 10px;
                display: flex;
                gap: 20px;
                align-items: center;
                background-color:#000000 60%;
            "
        >
            <div class="mostrarSerie">
                <img src="<?= htmlspecialchars($serie['imagem']) ?>" width="200" alt="<?= htmlspecialchars($serie["nome_serie"]) ?>" />
                <br /><br />
                <h1><?= htmlspecialchars($serie["nome_serie"]) ?></h1>
                <h2><?= htmlspecialchars($serie["tipo"]) ?></h2>
                <h3><?= htmlspecialchars($serie["genero"]) ?></h3>
                <p><?= htmlspecialchars($serie["sinopse"]) ?></p>
                <p><?= $nota ?>/10</p>
                <input type="hidden" name="tipo_form" value="obra" />
            </div>
        </div>
    </button>
</form>

<?php if ($id_perfil == 2): ?>
    <button
        class="avaliarSerie"
        onclick="abrirModal('<?= htmlspecialchars($serie['nome_serie']); ?>', '<?= htmlspecialchars($serie['id_serie']); ?>', 'meuModal')"
    >
        Selecionar <?= htmlspecialchars($serie['nome_serie']) ?>
    </button>
    <br /><br />
<?php else: ?>
    <button class="avaliarSerie" onclick="pedidoLogar()">
        Selecionar <?= htmlspecialchars($serie['nome_serie']) ?>
    </button>
    <br /><br />
<?php endif; ?>

<?php
    endif;
endforeach;
?>

<div class="overlay" id="meuModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background: rgba(0,0,0,0.7); justify-content: center; align-items: center;">
    <form class="modal" method="POST" action="principal.php" style="background:#fff; padding: 20px; border-radius: 8px; min-width: 300px;">
        <h3>Confirmar seleção</h3>
        <p>Você selecionou: <span id="nomeEscolhido"></span></p>
        <input type="hidden" name="serie" id="inputNome" />
        <div class="star-rating" id="rating-container">
            <?php
            // Gerar estrelas de 10 a 1 (ajustei para 10 estrelas)
            for ($i = 9; $i >= 0; $i--) {
                echo '<span class="star" data-value="' . $i . '">&#9733;</span>';
            }
            ?>
        </div>
        <input type="hidden" id="rating-value" name="rating" value="0" />
        <br />
        <button type="submit">Enviar</button>
        <button type="button" onclick="document.getElementById('meuModal').style.display='none';" style="margin-left: 10px;">Cancelar</button>
    </form>
</div>

</body>
</html>