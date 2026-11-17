<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MobAccess</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="manifest" href="manifest.json">
    <link rel="icon" href="images/icon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="images/icon.svg">
    <meta name="theme-color" content="#6450ff">

    <style>
        .nav-link{
            color: #fff;
            text-decoration: none;
            font-size: 15px;
            margin-right: 20px;
            opacity: .85;
            transition: .2s;
        }
        .nav-link:hover{
            opacity: 1;
            color: #9b8cff;
        }
        .btn-instalar{
            background: #6450ff;
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: .2s;
        }
        .btn-instalar:hover{
            background: #755fff;
        }

        .sobre{
            padding: 80px 8%;
            background: #0a1030;
            color: #e8e8f0;
            text-align: center;
        }
        .sobre h2{
            font-size: 32px;
            margin-bottom: 20px;
            color: #fff;
        }
        .sobre h2 span{
            color: #9b8cff;
        }
        .sobre p{
            max-width: 700px;
            margin: 0 auto 15px;
            color: #c9c9d8;
            line-height: 1.7;
            font-size: 16px;
        }

        .modal-instalar-overlay{
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.6);
            z-index: 999;
            align-items: center;
            justify-content: center;
        }
        .modal-instalar{
            background: #10173a;
            color: #fff;
            border-radius: 16px;
            padding: 30px;
            max-width: 380px;
            width: 90%;
            text-align: center;
            border: 1px solid rgba(255,255,255,.08);
        }
        .modal-instalar h3{
            margin-bottom: 12px;
        }
        .modal-instalar p{
            color: #c9c9d8;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .modal-instalar button{
            background: #6450ff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
        }
    </style>
</head>

<script>

document.addEventListener('click', () => {

    const audio =
    document.getElementById('scooterSound');

    if(audio){
        audio.volume = 0.10;
        audio.play().catch(() => {});
    }

},{once:true});

document.addEventListener('mousemove',(e)=>{

    const hero =
    document.querySelector('.hero');

    const x =
    (e.clientX/window.innerWidth-0.5)*25;

    const y =
    (e.clientY/window.innerHeight-0.5)*15;

    hero.style.backgroundPosition =
    `${50+x*0.3}% ${50+y*0.3}%`;

});

</script>

<body>

  <header class="navbar">

      <div class="logo">
          MobAccess
      </div>

      <nav>

          <a href="#sobre" class="nav-link">
              Sobre nós
          </a>

          <a href="#contato" class="nav-link">
              Contato
          </a>

          <a href="login.php" class="btn-primary">
              🚀 Começar
          </a>

          <a href="#scooters" class="btn-secondary">
              🛵 Ver Scooters
          </a>

          <button id="btnInstalarApp" class="btn-instalar" style="display:none;">
              📲 Baixar App
          </button>

      </nav>

  </header>

<section class="hero">

    
  <div class="hero-content">

      <h1>
          Mobilidade Inteligente
      </h1>

      <p>
          Alugue scooters facilmente,
          com segurança, praticidade e conforto.
      </p>
      
      <div class="stats">

          <div>
              <h2>500+</h2>
              <span>Usuários</span>
          </div>

          <div>
              <h2>50+</h2>
              <span>Scooters</span>
          </div>

          <div>
              <h2>24h</h2>
              <span>Suporte</span>
          </div>

      </div>

  </div>


    <!-- TRÁFEGO DE SCOOTERS -->

<div class="traffic">

    <img src="images/scooter_city.png"
         alt="Scooter City"
         class="scooter city">

    <img src="images/scooter_pro.png"
         alt="Scooter Pro"
         class="scooter pro">

    <img src="images/scooter_max.png"
         alt="Scooter Max"
         class="scooter max">

</div>

<audio id="scooterSound" loop>
    <source src="audio/scooter.mp3" type="audio/mpeg">
</audio>

</section>

<section class="sobre" id="sobre">

    <h2>Sobre <span>nós</span></h2>

    <p>
        O MobAccess nasceu para resolver um problema simples: deslocamentos curtos
        ainda são lentos, caros ou pouco práticos. Criamos uma plataforma de scooters
        elétricas compartilhadas pensada para levar você do ponto A ao ponto B
        de forma rápida, acessível e sustentável.
    </p>

    <p>
        Da reserva pelo mapa em tempo real ao pagamento e liberação por QR Code,
        cada etapa foi desenhada para ser simples — porque mobilidade urbana
        não devia ser complicada.
    </p>

</section>

<section class="scooters" id="scooters">

    <h2>Nossos Scooters</h2>

    <div class="cards">

        <div class="card">

            <img src="images/scooter_city.png">

            <h3>Modelo City</h3>

            <p>Ideal para trajetos urbanos</p>

            <span>R$ 5,00 / hora</span>

            <button>Alugar</button>

        </div>

        <div class="card">

            <img src="images/scooter_pro.png">

            <h3>Modelo Pro</h3>

            <p>Mais rápido e confortável</p>

            <span>R$ 8,00 / hora</span>

            <button>Alugar</button>

        </div>

        <div class="card">

            <img src="images/scooter_max.png">

            <h3>Modelo Max</h3>

            <p>Alta autonomia</p>

            <span>R$ 10,00 / hora</span>

            <button>Alugar</button>

        </div>

    </div>

</section>

<footer class="footer">

    <div class="footer-container">

        <div>

            <h3>MobAccess</h3>

            <p>
                Mobilidade inteligente para todos.
            </p>

            <p>
                
            © 2026 MobAccess.
             Todos os direitos reservados.

             Desenvolvido por
             Cristiano Aparecido de Macedo.

            </p>

        </div>

        <div id="contato">

            <h4>Contato</h4>

            <p>
                📱 (11) 91871-9020
            </p>

            <p>
                ✉ mobaccess@gmail.com
            </p>

            <p>
                🌐 @mobaccess
            </p>

        </div>

        
          <div>

              <h4>Redes Sociais</h4>

              <div class="social-links">

                  <a href="#">
                      📷 Instagram
                  </a>

                  <a href="#">
                      📘 Facebook
                  </a>

                  <a href="#">
                      💼 LinkedIn
                  </a>

                  <a href="#">
                      ▶ YouTube
                  </a>

                  <a href="#">
                      💬 WhatsApp
                  </a>

              </div>

          </div>


</footer>

<!-- Modal de instalação (fallback iOS / navegadores sem prompt nativo) -->
<div class="modal-instalar-overlay" id="modalInstalar">
    <div class="modal-instalar">
        <h3>📲 Instalar o MobAccess</h3>
        <p>
            No iPhone: toque no ícone de compartilhar (□↑) e escolha
            <strong>"Adicionar à Tela de Início"</strong>.<br><br>
            No Android/Chrome: toque no menu (⋮) e escolha
            <strong>"Instalar app"</strong> ou <strong>"Adicionar à tela inicial"</strong>.
        </p>
        <button onclick="document.getElementById('modalInstalar').style.display='none';">Entendi</button>
    </div>
</div>

<script>
// Registra o Service Worker (necessário para o PWA funcionar)
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('service-worker.js').catch(() => {});
}

// Lógica do botão "Baixar App"
let deferredPrompt;
const btnInstalar = document.getElementById('btnInstalarApp');

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    btnInstalar.style.display = 'inline-block';
});

btnInstalar.addEventListener('click', async () => {
    if (deferredPrompt) {
        deferredPrompt.prompt();
        await deferredPrompt.userChoice;
        deferredPrompt = null;
    } else {
        document.getElementById('modalInstalar').style.display = 'flex';
    }
});

// Em navegadores que não disparam beforeinstallprompt (ex: Safari/iOS),
// mostra o botão sempre, e ao clicar abre o modal com instruções manuais
setTimeout(() => {
    if (!deferredPrompt) {
        btnInstalar.style.display = 'inline-block';
    }
}, 1500);
</script>

</body>

</html>