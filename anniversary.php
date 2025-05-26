<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include 'include/head.php'; ?>
    <style>
        .anniversary-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a1a, #4a4a4a);
            padding: 50px 0;
            position: relative;
            overflow: hidden;
        }
        .celebration-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
            text-align: center;
            transform: translateY(50px);
            opacity: 0;
            animation: fadeInUp 1s forwards;
        }
        .confetti {
            animation: confetti-spread 3s linear infinite;
        }
        .balloon {
            animation: balloon-float 15s ease-in-out infinite;
        }
        @keyframes confetti-spread {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(var(--tx), var(--ty)) rotate(360deg); }
        }
        @keyframes balloon-float {
            0% { transform: translate(0, 0); }
            25% { transform: translate(20px, -50px); }
            50% { transform: translate(-20px, -100px); }
            75% { transform: translate(10px, -50px); }
            100% { transform: translate(0, 0); }
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .stat-card {
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: scale(1.05);
        }
        .movie-reel {
            animation: spin 10s linear infinite;
        }
        @keyframes spin {
            100% { transform: rotate(360deg); }
        }
        .highlight-number {
            font-size: 3rem;
            font-weight: bold;
            background: linear-gradient(45deg, #ff4081, #7c4dff);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: numberPulse 2s infinite;
        }
        @keyframes numberPulse {
            50% { transform: scale(1.1); }
        }
        .info-cinema {
            padding: 20px;
            margin: 20px 0;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .info-cinema:hover {
            background: rgba(255, 64, 129, 0.1);
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <div class="anniversary-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="celebration-card">
                        <h1 class="display-4 mb-4">🎬 Joyeux Anniversaire Cinetech! 🎉</h1>
                        <h2 class="text-muted mb-4">10 Ans de Magie Cinématographique</h2>
                        <div class="mb-4">
                            <i class="fas fa-film fa-4x text-primary mb-3 movie-reel"></i>
                            <p class="lead">
                                Depuis une décennie, nous célébrons ensemble la magie du 7ème art. 
                                Une aventure extraordinaire remplie d'émotions, de découvertes et de partages!
                            </p>
                        </div>
                        <div class="anniversary-stats mt-5">
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <div class="card bg-light stat-card">
                                        <div class="card-body">
                                            <h3 class="highlight-number">3650</h3>
                                            <p class="card-text">Jours de cinéma</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light stat-card">
                                        <div class="card-body">
                                            <h3 class="highlight-number">20k+</h3>
                                            <p class="card-text">Films partagés</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5">
                            <h3 class="mb-4">Le Cinéma en Chiffres</h3>
                            <div class="info-cinema">
                                <h4>Le Saviez-vous?</h4>
                                <p>Le premier film de l'histoire fut projeté en 1895 par les frères Lumière</p>
                            </div>
                            <div class="info-cinema">
                                <h4>Record du Box-office</h4>
                                <p>Avatar reste le film le plus rentable de l'histoire avec plus de 2,8 milliards de dollars de recettes</p>
                            </div>
                            <div class="info-cinema">
                                <h4>Les Oscars</h4>
                                <p>La cérémonie des Oscars existe depuis 1929, récompensant l'excellence cinématographique</p>
                            </div>
                        </div>
                        <div class="mt-5">
                            <a href="index.php" class="btn btn-primary btn-lg">Retour à l'accueil</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function createConfettiExplosion(x, y) {
            const colors = ['#ff4081', '#7c4dff', '#ffeb3b', '#00bcd4', '#76ff03'];
            for (let i = 0; i < 30; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.position = 'fixed';
                confetti.style.left = x + 'px';
                confetti.style.top = y + 'px';
                confetti.style.width = '10px';
                confetti.style.height = '10px';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.borderRadius = '50%';
                confetti.style.zIndex = '1000';
                
                const angle = Math.random() * Math.PI * 2;
                const velocity = 100 + Math.random() * 200;
                const tx = Math.cos(angle) * velocity;
                const ty = Math.sin(angle) * velocity;
                
                confetti.style.setProperty('--tx', `${tx}px`);
                confetti.style.setProperty('--ty', `${ty}px`);
                
                document.body.appendChild(confetti);

                setTimeout(() => confetti.remove(), 3000);
            }
        }

        function createBalloon() {
            const balloon = document.createElement('div');
            balloon.className = 'balloon';
            balloon.style.position = 'fixed';
            balloon.style.left = Math.random() * (window.innerWidth - 50) + 'px';
            balloon.style.bottom = '-50px';
            balloon.style.fontSize = '50px';
            balloon.textContent = '🎈';
            balloon.style.zIndex = '999';
            
            // Animation du ballon qui flotte
            balloon.animate([
                { bottom: '-50px' },
                { bottom: window.innerHeight + 'px' }
            ], {
                duration: 15000,
                iterations: Infinity,
                direction: 'alternate',
                easing: 'ease-in-out'
            });
            
            document.body.appendChild(balloon);
        }

        // Limiter le nombre de ballons à 5
        let balloonCount = 0;
        const maxBalloons = 5;

        function manageBalloons() {
            if (balloonCount < maxBalloons) {
                createBalloon();
                balloonCount++;
            }
        }

        // Créer des explosions de confetti aléatoires
        setInterval(() => {
            const x = Math.random() * window.innerWidth;
            const y = Math.random() * window.innerHeight;
            createConfettiExplosion(x, y);
        }, 2000);

        // Créer des ballons avec un intervalle plus long
        setInterval(manageBalloons, 5000);
        // Animation des info-cinema au scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });

        document.querySelectorAll('.info-cinema').forEach((item) => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            observer.observe(item);
        });
    </script>
</body>
</html>