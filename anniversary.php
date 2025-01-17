
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include 'include/head.php'; ?>
    <style>
        .anniversary-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a1a, #4a4a4a);
            padding: 50px 0;
        }
        .celebration-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        .confetti {
            animation: confetti-fall 3s linear infinite;
        }
        @keyframes confetti-fall {
            0% { transform: translateY(-100px) rotate(0deg); }
            100% { transform: translateY(100vh) rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="anniversary-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="celebration-card">
                        <h1 class="display-4 mb-4">🎉 Joyeux Anniversaire Cinetech! 🎉</h1>
                        <h2 class="text-muted mb-4">10 An de Cinéma et de Passion</h2>
                        <div class="mb-4">
                            <i class="fas fa-film fa-4x text-primary mb-3"></i>
                            <p class="lead">
                                Depuis un an, nous partageons ensemble notre amour du cinéma. 
                                Merci à tous nos fidèles utilisateurs pour cette belle aventure!
                            </p>
                        </div>
                        <div class="anniversary-stats mt-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h3 class="card-title">365</h3>
                                            <p class="card-text">Jours de cinéma</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h3 class="card-title">1000+</h3>
                                            <p class="card-text">Films partagés</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h3 class="card-title">10000+</h3>
                                            <p class="card-text">Membres</p>
                                        </div>
                                    </div>
                                </div>
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
        function createConfetti() {
            const colors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff'];
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.position = 'fixed';
            confetti.style.left = Math.random() * 100 + 'vw';
            confetti.style.width = '10px';
            confetti.style.height = '10px';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            document.body.appendChild(confetti);

            setTimeout(() => {
                confetti.remove();
            }, 3000);
        }

        setInterval(createConfetti, 300);
    </script>
</body>
</html>
