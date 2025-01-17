<div class="container d-flex justify-content-center">
    <div class="card shadow-sm">
        <div class="card-body text-center">
            <h6>
                <?php
                $jour = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
                $mois = ["", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
                $datefr = sprintf("%s %d %s %d", $jour[date("w")], date("d"), $mois[date("n")], date("Y"));
                echo 'Nous sommes le ' . $datefr;
                ?>
            </h6>
            <div id="horloge" class="card shadow-sm">
                <div class="card-body text-center">
                    <p class="text-danger text-decoration-none small d-inline">Il est exactement : </p>
                    <input type="text" id="clock" class="text-danger display-10 small border-0 bg-transparent d-inline" readonly>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Définir le fuseau horaire
date_default_timezone_set('Europe/Brussels'); // Remplacez par le fuseau horaire souhaité
$heure = (int) date('H');
$minute = (int) date('i');
$seconde = (int) date('s');
?>

<script>
    let heure = <?php echo $heure; ?>;
    let minute = <?php echo $minute; ?>;
    let seconde = <?php echo $seconde; ?>;

    function clock() {
        // Incrémente les secondes
        seconde++;
        if (seconde === 60) {
            seconde = 0;
            minute++;
            if (minute === 60) {
                minute = 0;
                heure++;
                if (heure === 24) {
                    heure = 0;
                }
            }
        }

        // Formate l'heure
        const formattedTime = [
            heure.toString().padStart(2, '0'),
            minute.toString().padStart(2, '0'),
            seconde.toString().padStart(2, '0'),
        ].join(':');

        // Met à jour le champ d'affichage de l'heure
        document.getElementById('clock').value = formattedTime;
    }

    // Met à jour l'horloge toutes les secondes
    setInterval(clock, 1000);

    // Initialiser l'horloge immédiatement
    clock();
</script>