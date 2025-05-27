<div class="container d-flex justify-content-center">
    <div class="card shadow-sm">
        <div class="card-body text-center">
            <h6>
                <?php
                // Tableau des jours de la semaine en français
                $jour = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
                // Tableau des mois de l'année en français (index 0 vide pour correspondre à date("n"))
                $mois = ["", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
                // Formatage de la date complète en français : jour semaine, jour du mois, mois, année
                $datefr = sprintf("%s %d %s %d", $jour[date("w")], date("d"), $mois[date("n")], date("Y"));
                // Affichage de la date formatée
                echo 'Nous sommes le ' . $datefr;
                ?>
            </h6>
            <div id="horloge" class="card shadow-sm">
                <div class="card-body text-center">
                    <p class="text-danger text-decoration-none small d-inline">Il est exactement : </p>
                    <!-- Champ texte en lecture seule pour afficher l'heure -->
                    <input type="text" id="clock" class="text-danger display-10 small border-0 bg-transparent d-inline" readonly>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Définir le fuseau horaire à Bruxelles (Europe/Brussels)
date_default_timezone_set('Europe/Brussels'); // Remplacez par le fuseau horaire souhaité
// Récupération de l'heure actuelle en heures, minutes et secondes sous forme d'entiers
$heure = (int) date('H');
$minute = (int) date('i');
$seconde = (int) date('s');
?>

<script>
    // Initialisation des variables JavaScript avec les valeurs PHP récupérées
    let heure = <?php echo $heure; ?>;
    let minute = <?php echo $minute; ?>;
    let seconde = <?php echo $seconde; ?>;

    // Fonction qui met à jour l'heure affichée chaque seconde
    function clock() {
        // Incrémente les secondes
        seconde++;
        // Si les secondes atteignent 60, on remet à zéro et on incrémente les minutes
        if (seconde === 60) {
            seconde = 0;
            minute++;
            // Si les minutes atteignent 60, on remet à zéro et on incrémente les heures
            if (minute === 60) {
                minute = 0;
                heure++;
                // Si les heures atteignent 24, on remet à zéro (nouvelle journée)
                if (heure === 24) {
                    heure = 0;
                }
            }
        }

        // Formatage de l'heure au format HH:MM:SS avec des zéros devant si nécessaire
        const formattedTime = [
            heure.toString().padStart(2, '0'),
            minute.toString().padStart(2, '0'),
            seconde.toString().padStart(2, '0'),
        ].join(':');

        // Mise à jour du champ texte avec l'heure formatée
        document.getElementById('clock').value = formattedTime;
    }

    // Appelle la fonction clock toutes les 1000 millisecondes (1 seconde) pour mettre à jour l'heure
    setInterval(clock, 1000);

    // Appelle immédiatement la fonction clock pour afficher l'heure sans délai au chargement
    clock();
</script>