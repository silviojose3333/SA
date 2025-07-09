
function inicializarAvaliacao() {
  const estrelas = document.querySelectorAll('.star');
  const inputRating = document.getElementById('rating-value');

  estrelas.forEach(estrela => {
    estrela.addEventListener('click', function () {
      const valor = this.getAttribute('data-value');
      atualizarEstrelas(valor);
      salvarValor(valor);
    });
  });

  function atualizarEstrelas(valorSelecionado) {
    estrelas.forEach(estrela => {
      const valorEstrela = estrela.getAttribute('data-value');
      estrela.classList.toggle('filled', valorEstrela <= valorSelecionado);
    });
  }

  function salvarValor(valor) {
    inputRating.value = valor;
  }
}


function toggleSubmenu(index) {
    const submenu = document.getElementById('submenu-' + index);
    if (submenu) {
        submenu.classList.toggle('show');
    }

}

function inicializarEstrelas(containerId, inputId) {
    const container = document.getElementById(containerId);
    const input = document.getElementById(inputId);
    const estrelas = container.querySelectorAll('.estrela');
    let notaSelecionada = 0;

    estrelas.forEach((estrela) => {
      const valor = parseInt(estrela.dataset.value);

      // Hover
      estrela.addEventListener('mouseenter', () => {
        estrelas.forEach(e => {
          e.classList.toggle('hover', parseInt(e.dataset.value) <= valor);
        });
      });

      estrela.addEventListener('mouseleave', () => {
        estrelas.forEach(e => e.classList.remove('hover'));
      });

      // Clique
      estrela.addEventListener('click', () => {
        notaSelecionada = valor;
        input.value = valor;

        estrelas.forEach(e => {
          e.classList.toggle('selecionada', parseInt(e.dataset.value) <= valor);
        });
      });
    });
  }

  // Fecha submenus ao clicar fora

function abrirModal(nome,serie,form) {
    document.getElementById(form).style.display = "flex";
    document.getElementById("nomeEscolhido").textContent = nome;
    document.getElementById("inputNome").value = serie;
    document.body.classList.add("modal-open");
  }
  function abrir(id) {
    document.getElementById(id).style.display = 'flex';
}
function fechar(id) {
    document.getElementById(id).style.display = 'none';
}
  function mostrarSenha(){
    var senha1=document.getElementById("Senha");
    var senha2=document.getElementById("Confirmar_senha");
    var tipo=senha1.type==="password"? "text":"password";
    senha1.type= tipo
    senha2.type= tipo
}
function pedidoLogar() {
    if (confirm("Logue na sua conta para avaliar essa obra.")) {
        // Redireciona se o usuário clicar em "OK"
        window.location.href = "login.php";
    } 
}
function confirmAction(usuario,episodio,serie) {
  console.log(usuario);
  console.log(episodio);
  console.log(serie);

  if (confirm("Você já avaliou a obra, se quiser mudar a nota por favor clique em 'ok' e clique para avaliar novamente.")){
      //  REDIRECIONA COM OS DADOS VIA GET
      window.location.href = "funcao.php?confirmado=1&usuario=" + encodeURIComponent(usuario) + "&episodio=" + encodeURIComponent(episodio) + "&serie=" + encodeURIComponent(serie);
  }
}

