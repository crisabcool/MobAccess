<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MobAccess</title>

    <link rel="stylesheet" href="css/style.css">
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

          <a href="login.php" class="btn-primary">
              🚀 Começar
          </a>

          <a href="#scooters" class="btn-secondary">
              🛵 Ver Scooters
          </a>

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

        <div>

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


</body>

</html>
