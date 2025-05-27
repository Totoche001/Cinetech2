<!DOCTYPE html>
<html lang="fr">
<?php

/* FILMOTECH Website
	INDEX page

	(c) 2013-2025 by Pascal PLUCHON and Anthony Semal
	https://www.filmotech.fr
*/

// Inclusion des fichiers de configuration nécessaires
require_once("include/params.inc.php");    // Paramètres généraux du site
require_once("include/config.inc.php");    // Configuration de la base de données

// Inclusion des paramètres de recherche
require_once("include/search_column.php"); // Gestion des colonnes de recherche
?>

<head>
    <?php include("include/head.php"); // Inclusion des métadonnées HTML et CSS 
    ?>
</head>

<body>
    <?php include("include/header.php"); // Inclusion de l'en-tête du site 
    ?>
    <!-- Bloc principal -->
    <div class="container">
        <?php include("include/time2.php"); // Inclusion du composant d'affichage du temps 
        ?>
        <!-- Barre de navigation -->
        <?php include("include/menu.php"); // Inclusion du menu de navigation 
        ?>
    </div>
    <!-- Contenu Principal -->
    <div class="container" style="background-color: #d3d3d3; margin-top: 20px; padding-top: 5px;padding-bottom: 5px;margin-bottom: 20px;">
        <!-- Section des films au hasard -->
        <h5 class="mt-1">Films au hasard:</h5>
        <?php
        // Sélection aléatoire d'un film dans la base de données
        $data = $db->query('SELECT * FROM fmt_films ORDER BY RAND() LIMIT 1');
        $film = $data->fetch();
        if ($film):
        ?>
            <!-- Affichage du film sélectionné aléatoirement -->
            <div class="alert alert-info text-center" style="max-width: 50%; border-radius: 10px; padding: 15px;">
                <!-- Lien vers les détails du film -->
                <a href="filmotech_detail.php?id=<?php echo $film["ID"]; ?>" target="_blank" title="<?php echo $film["Synopsis"]; ?>" style="text-decoration: none; color: #007bff; font-weight: bold;">
                    <?php echo $film["TitreVF"]; ?>
                </a>
                <!-- Affichage des badges (genre et année) -->
                <div class="mt-2">
                    <span class="badge bg-secondary"><?php echo $film["Genre"]; ?></span>
                    <span class="badge bg-secondary"><?php echo $film["Annee"]; ?></span>
                </div>
                <!-- Section des acteurs avec bouton toggle -->
                <div class="mt-2">
                    <strong>Acteurs:</strong> <span id="actors" style="display:none;"><?php echo $film["Acteurs"]; ?></span>
                    <button class="badge bg-secondary" onclick="var actors = document.getElementById('actors'); actors.style.display = (actors.style.display === 'none') ? 'block' : 'none'; this.innerHTML = (actors.style.display === 'block') ? 'Cacher les acteurs' : 'Afficher les acteurs';"><?php echo (isset($film["Acteurs"]) && $film["Acteurs"] !== '') ? 'Afficher les acteurs' : ''; ?></button>
                </div>
            </div>
        <?php else: ?>
            <!-- Message si aucun film n'est trouvé -->
            <div class="alert alert-warning text-center" style="border-radius: 10px; padding: 15px;">Aucun film disponible.</div>
        <?php endif; ?>

        <?php if (!empty($search_query)): ?>
            <?php if ($count > 0): ?>
                <!-- Affichage des résultats de recherche -->
                <div class="alert alert-info">
                    Recherche de "<?php echo $search_query; ?>" dans <?php echo $search_label; ?>
                    (<?php echo $count; ?> résultat<?php echo $count > 1 ? 's' : ''; ?>)
                </div>
            <?php else: ?>
                <!-- Message si aucun résultat n'est trouvé -->
                <div class="alert alert-warning">
                    Aucun film ne correspond à votre recherche ("<?php echo $search_query; ?>" dans <?php echo $search_label; ?>)
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Système de pagination (en haut de page) -->
        <?php if ($pagination && $count > 0): ?>
            <div class="d-flex justify-content-between my-3">
                <!-- Bouton page précédente -->
                <button class="btn btn-secondary" onclick="location.href='?Page=<?php echo max(1, $page - 1); ?><?php echo !empty($search_query) ? '&search_query=' . urlencode($search_query) . '&search_field=' . urlencode($search_field) : ''; ?>'" <?php echo $page == 1 ? 'disabled' : ''; ?>>
                    ← <?php echo $previous_page; ?>
                </button>
                <!-- Indicateur de page actuelle -->
                <span><?php echo $page; ?> / <?php echo ceil($total_record / $nb_record_per_page); ?></span>
                <!-- Bouton page suivante -->
                <button class="btn btn-secondary" onclick="location.href='?Page=<?php echo $page + 1; ?><?php echo !empty($search_query) ? '&search_query=' . urlencode($search_query) . '&search_field=' . urlencode($search_field) : ''; ?>'" <?php echo $page >= ceil($total_record / $nb_record_per_page) ? 'disabled' : ''; ?>>
                    <?php echo $next_page; ?> →
                </button>
            </div>
        <?php endif; ?>
        <?php if ($count > 0): ?>
            <!-- Affichage du tableau uniquement si des films sont trouvés -->
            <div class="row">
                <div class="col-md-8">
                    <!-- Tableau triable avec Bootstrap -->
                    <table class="table table-striped" id="sortableTable">
                        <thead>
                            <tr>
                                <!-- En-têtes de colonnes avec fonctionnalité de tri -->
                                <th></th> <!-- Colonne pour les icônes de statut -->
                                <th onclick="sortTable(1)" style="cursor: pointer;">Titre VF <span id="arrow1"></span></th>
                                <th onclick="sortTable(2)" style="cursor: pointer;">Genre <span id="arrow2"></span></th>
                                <th onclick="sortTable(3)" style="cursor: pointer;">Support <span id="arrow3"></span></th>
                                <th onclick="sortTable(4)" style="cursor: pointer;">Année <span id="arrow4"></span></th>
                                <th onclick="sortTable(5, true)" style="cursor: pointer;">Durée <span id="arrow5"></span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($response as $index => $data): ?>
                                <!-- Boucle sur chaque film de la base de données -->
                                <tr>
                                    <td>
                                        <?php
                                        // Gestion des icônes de statut (vu/non vu et prêt)
                                        if ($show_lent || $show_not_seen) {
                                            if ($show_lent && $show_not_seen) {
                                                // Affichage des différentes icônes selon le statut du film
                                                if ($data['FilmVu'] == 'NON' && $data['PretEnCours'] == 'OUI') {
                                                    echo '<img src="img/dot_green_orange.png" alt="Non vu et prêté" class="img-fluid">';
                                                } elseif ($data['FilmVu'] == 'NON') {
                                                    echo '<img src="img/dot_green.png" alt="Non vu" class="img-fluid">';
                                                } elseif ($data['PretEnCours'] == 'OUI') {
                                                    echo '<img src="img/dot_orange.png" alt="Prêté" class="img-fluid">';
                                                }
                                            }
                                        }
                                        ?>
                                    </td>
                                    <!-- Lien vers la page de détails du film avec synopsis en infobulle -->
                                    <td><a href="filmotech_detail.php?id=<?php echo $data['ID']; ?>" target="_blank" title="<?php echo $data['Synopsis']; ?>"><?php echo $data['TitreVF']; ?></a></td>
                                    <!-- Informations générales du film -->
                                    <td><?php echo $data['Genre']; ?></td>
                                    <td><?php echo $data['Support']; ?></td>
                                    <td><?php echo $data['Annee']; ?></td>
                                    <td><?php echo $data['Duree']; ?> m</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <script>
                        function sortTable(n, isNumber = false) {
                            // Déclaration des variables nécessaires pour le tri
                            var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
                            table = document.getElementById("sortableTable"); // Récupère le tableau à trier
                            switching = true; // Indique si le tri est en cours
                            dir = "asc"; // Direction initiale du tri (ascendant)

                            // Réinitialise toutes les flèches de tri dans l'en-tête du tableau
                            for (let i = 1; i <= 5; i++) {
                                document.getElementById("arrow" + i).innerHTML = "";
                            }

                            while (switching) {
                                switching = false;
                                rows = table.rows; // Récupère toutes les lignes du tableau

                                // Parcourt toutes les lignes du tableau (sauf l'en-tête)
                                for (i = 1; i < (rows.length - 1); i++) {
                                    shouldSwitch = false;
                                    // Récupère les cellules à comparer
                                    x = rows[i].getElementsByTagName("TD")[n];
                                    y = rows[i + 1].getElementsByTagName("TD")[n];

                                    // Extrait le contenu textuel des cellules en ignorant le HTML
                                    let xContent = x.textContent || x.innerText;
                                    let yContent = y.textContent || y.innerText;

                                    if (isNumber) {
                                        // Pour les colonnes numériques (comme la durée)
                                        // Convertit les valeurs en nombres en retirant tout caractère non numérique
                                        var xValue = parseInt(xContent.replace(/[^\d]/g, ''));
                                        var yValue = parseInt(yContent.replace(/[^\d]/g, ''));

                                        // Compare les valeurs numériques selon la direction du tri
                                        if (dir == "asc") {
                                            if (xValue > yValue) {
                                                shouldSwitch = true;
                                                break;
                                            }
                                        } else if (dir == "desc") {
                                            if (xValue < yValue) {
                                                shouldSwitch = true;
                                                break;
                                            }
                                        }
                                    } else {
                                        // Pour les colonnes textuelles
                                        // Compare les chaînes de caractères en ignorant la casse
                                        if (dir == "asc") {
                                            if (xContent.toLowerCase() > yContent.toLowerCase()) {
                                                shouldSwitch = true;
                                                break;
                                            }
                                        } else if (dir == "desc") {
                                            if (xContent.toLowerCase() < yContent.toLowerCase()) {
                                                shouldSwitch = true;
                                                break;
                                            }
                                        }
                                    }
                                }

                                if (shouldSwitch) {
                                    // Échange les lignes si nécessaire
                                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                                    switching = true;
                                    switchcount++;
                                } else {
                                    // Si aucun échange n'a été fait et que le tri est ascendant,
                                    // inverse la direction du tri
                                    if (switchcount == 0 && dir == "asc") {
                                        dir = "desc";
                                        switching = true;
                                    }
                                }
                            }

                            // Met à jour la flèche de tri pour la colonne active
                            document.getElementById("arrow" + n).innerHTML = dir === "asc" ? " ↑" : " ↓";
                        }
                    </script>
                </div>
                <div class="col-md-4">
                    <!-- Section des 25 derniers films ajoutés à la collection -->
                    <div style="background-color: #f9e79f; padding: 10px; margin-bottom: 10px;">
                        <h4 style="background-color: #f9e79f">Les 25 derniers films</h4>
                        <?php
                        // Récupère les 25 films les plus récemment ajoutés, triés par date d'entrée décroissante
                        $latest = $db->query('SELECT * FROM fmt_films ORDER BY EntreeDate DESC LIMIT 25');
                        // Boucle sur chaque film pour les afficher
                        while ($movie = $latest->fetch()): ?>
                            <!-- Lien vers la page de détail du film avec son ID -->
                            <a href="filmotech_detail.php?id=<?php echo $movie['ID']; ?>" style="text-decoration: none;">
                                <?php echo htmlspecialchars($movie['TitreVF']); // Affiche le titre du film en sécurisant les caractères spéciaux 
                                ?>
                            </a><br>
                        <?php endwhile; ?>
                    </div>
                    <!-- Section des liens favoris personnalisés -->
                    <div style="background-color: #a2d5ab; padding: 10px; margin-bottom: 10px;">
                        <h4 style="background-color: #a2d5ab">Mes liens</h4>
                        <?php
                        // Boucle sur le tableau des favoris qui contient les noms et URLs
                        foreach ($favorites as $name => $link): ?>
                            <a href="<?php echo $link; ?>" style="text-decoration: none;">
                                <?php echo htmlspecialchars($name); // Affiche le nom du lien en sécurisant les caractères spéciaux 
                                ?>
                            </a><br>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Section de pagination en bas de page -->
            <?php if ($pagination): // Affiche la pagination uniquement si elle est activée 
            ?>
                <div class="text-center my-3">
                    <?php
                    // Affiche le nombre total de films
                    echo ('<p><small>' . $label_movie_count . '</small></p>');
                    // Affiche la date de dernière mise à jour si demandé et s'il y a des films
                    if ($show_update_date && $count != 0) echo ('<p><small>' . $last_update_label . '</small></p>');
                    ?>
                </div>
                <!-- Contrôles de navigation entre les pages -->
                <div class="d-flex justify-content-between" style="margin-top: 20px;">
                    <!-- Bouton page précédente - désactivé si on est sur la première page -->
                    <button class="btn btn-secondary" onclick="location.href='?Page=<?php echo max(1, $page - 1); ?><?php echo !empty($search_query) ? '&search_query=' . urlencode($search_query) . '&search_field=' . urlencode($search_field) : ''; ?>'" <?php echo $page == 1 ? 'disabled' : ''; ?>>
                        ← <?php echo $previous_page; ?>
                    </button>
                    <!-- Indicateur de la page courante -->
                    <span>Page <?php echo $page; ?> / <?php echo ceil($total_record / $nb_record_per_page); ?></span>
                    <!-- Bouton page suivante - désactivé si on est sur la dernière page -->
                    <button class="btn btn-secondary" onclick="location.href='?Page=<?php echo $page + 1; ?><?php echo !empty($search_query) ? '&search_query=' . urlencode($search_query) . '&search_field=' . urlencode($search_field) : ''; ?>'" <?php echo $page >= ceil($total_record / $nb_record_per_page) ? 'disabled' : ''; ?>>
                        <?php echo $next_page; ?> →
                    </button>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Section des statistiques de la collection de films -->
    <div class="container" style="background-color: #e8f4f8; padding: 20px; margin-bottom: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h4 class="text-center mb-4">Statistiques de la collection</h4>
        <div class="row">
            <?php
            try {
                // Requête pour obtenir les statistiques globales des films vus et non vus
                // Calcule le total des films, le nombre de films vus, non vus et le pourcentage de films vus
                $stats_vu = $db->query("SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN FilmVu = 'OUI' THEN 1 ELSE 0 END) as vus,
                    SUM(CASE WHEN FilmVu = 'NON' THEN 1 ELSE 0 END) as non_vus,
                    ROUND((SUM(CASE WHEN FilmVu = 'OUI' THEN 1 ELSE 0 END) * 100.0 / COUNT(*)), 2) as pourcentage_vus
                    FROM fmt_films")->fetch(PDO::FETCH_ASSOC);

                // Requête préparée pour obtenir les statistiques par type de support
                // Compte le nombre de films pour chaque type de support, en excluant les supports NULL
                $stats_support_query = $db->prepare("SELECT Support, COUNT(*) as count 
                    FROM fmt_films 
                    WHERE Support IS NOT NULL 
                    GROUP BY Support 
                    ORDER BY count DESC");
                $stats_support_query->execute();
                $stats_support = $stats_support_query->fetchAll(PDO::FETCH_ASSOC);

                // Requête pour obtenir les statistiques détaillées des notes
                // Calcule la moyenne, le nombre de films par catégorie de note (excellent, bon, moyen, mauvais)
                // Ne prend en compte que les films vus et ayant une note
                $stats_notes = $db->query("SELECT 
                    ROUND(AVG(Note), 2) as moyenne,
                    SUM(CASE WHEN Note >= 8 THEN 1 ELSE 0 END) as excellent,
                    SUM(CASE WHEN Note >= 6 AND Note < 8 THEN 1 ELSE 0 END) as bon,
                    SUM(CASE WHEN Note >= 4 AND Note < 6 THEN 1 ELSE 0 END) as moyen,
                    SUM(CASE WHEN Note < 4 THEN 1 ELSE 0 END) as mauvais,
                    MIN(Note) as note_min,
                    MAX(Note) as note_max,
                    COUNT(Note) as total_notes
                    FROM fmt_films 
                    WHERE FilmVu = 'OUI' AND Note IS NOT NULL")->fetch(PDO::FETCH_ASSOC);

                // Requête pour obtenir les statistiques des notes par année
                // Groupe les résultats par année et calcule les moyennes et répartitions des notes
                $stats_notes_annees = $db->query("SELECT 
                    Annee as annee,
                    ROUND(AVG(Note), 2) as moyenne,
                    SUM(CASE WHEN Note >= 8 THEN 1 ELSE 0 END) as excellent,
                    SUM(CASE WHEN Note >= 6 AND Note < 8 THEN 1 ELSE 0 END) as bon,
                    SUM(CASE WHEN Note >= 4 AND Note < 6 THEN 1 ELSE 0 END) as moyen,
                    SUM(CASE WHEN Note < 4 AND Note IS NOT NULL THEN 1 ELSE 0 END) as mauvais,
                    COUNT(CASE WHEN Note IS NOT NULL AND Note != 0 THEN 1 END) as total_notes,
                    COUNT(*) as total_films_vus
                    FROM fmt_films 
                    WHERE Note IS NOT NULL AND FilmVu = 'OUI' AND Annee IS NOT NULL
                    GROUP BY Annee
                    ORDER BY Annee DESC")->fetchAll(PDO::FETCH_ASSOC);

                // Requête pour obtenir les statistiques temporelles
                // Récupère la date du plus ancien film, du plus récent, et le nombre d'années couvertes
                $stats_dates = $db->query("SELECT 
                    MIN(EntreeDate) as plus_ancien,
                    MAX(EntreeDate) as plus_recent,
                    COUNT(DISTINCT YEAR(EntreeDate)) as nombre_annees
                    FROM fmt_films")->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // En cas d'erreur de base de données, on initialise les variables avec des valeurs par défaut
                error_log("Erreur de base de données: " . $e->getMessage());
                $stats_vu = ['total' => 0, 'vus' => 0, 'non_vus' => 0, 'pourcentage_vus' => 0];
                $stats_support = [];
                $stats_notes = ['moyenne' => 0, 'excellent' => 0, 'bon' => 0, 'moyen' => 0, 'mauvais' => 0, 'note_min' => 0, 'note_max' => 0, 'total_notes' => 0];
                $stats_dates = ['plus_ancien' => null, 'plus_recent' => null, 'nombre_annees' => 0];
                $stats_notes_annees = [];
            }
            ?>
            <!-- Affichage des statistiques de visionnage -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Visionnage</h5>
                        <p class="card-text">Total des films: <strong><?php echo number_format($stats_vu['total'], 0, ',', ' '); ?></strong></p>
                        <p class="card-text">Films vus: <strong><?php echo number_format($stats_vu['vus'], 0, ',', ' '); ?></strong> (<?php echo $stats_vu['pourcentage_vus']; ?>%)</p>
                        <p class="card-text">Films à voir: <strong><?php echo number_format($stats_vu['non_vus'], 0, ',', ' '); ?></strong></p>
                        <p class="card-text">Collection débutée le: <strong><?php echo date('d/m/Y', strtotime($stats_dates['plus_ancien'])); ?></strong></p>
                    </div>
                </div>
            </div>

            <!-- Affichage des statistiques des notes -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Notes</h5>
                        <p class="card-text">Moyenne générale: <strong><?php echo $stats_notes['moyenne']; ?>/10</strong></p>
                        <p class="card-text">Excellents (8-10): <strong><?php echo $stats_notes['excellent']; ?></strong> (<?php echo round(($stats_notes['excellent'] / $stats_notes['total_notes']) * 100, 1); ?>%)</p>
                        <p class="card-text">Bons (6-7): <strong><?php echo $stats_notes['bon']; ?></strong> (<?php echo round(($stats_notes['bon'] / $stats_notes['total_notes']) * 100, 1); ?>%)</p>
                        <p class="card-text">Moyens (4-5): <strong><?php echo $stats_notes['moyen']; ?></strong> (<?php echo round(($stats_notes['moyen'] / $stats_notes['total_notes']) * 100, 1); ?>%)</p>
                        <p class="card-text">Mauvais (0-3): <strong><?php echo $stats_notes['mauvais']; ?></strong> (<?php echo round(($stats_notes['mauvais'] / $stats_notes['total_notes']) * 100, 1); ?>%)</p>

                        <!-- Bouton pour afficher/masquer les statistiques détaillées par année -->
                        <button class="btn btn-sm btn-outline-primary mt-3" type="button" data-bs-toggle="collapse" data-bs-target="#statsParAnnee" aria-expanded="false">
                            Voir les statistiques par année
                        </button>

                        <!-- Section dépliable des statistiques par année -->
                        <div class="collapse mt-3" id="statsParAnnee">
                            <h6>Statistiques par année</h6>
                            <?php foreach ($stats_notes_annees as $stat): ?>
                                <div class="border-bottom py-2">
                                    <strong><?php echo $stat['annee']; ?></strong> - Moyenne: <?php echo $stat['moyenne']; ?>/10<br>
                                    <strong>Total films vus: <?php echo $stat['total_films_vus']; ?></strong> (dont <?php echo $stat['total_notes']; ?> notés)<br>
                                    <small>
                                        Excellents: <?php echo $stat['excellent']; ?> (<?php echo round(($stat['excellent'] / $stat['total_notes']) * 100, 1); ?>%)<br>
                                        Bons: <?php echo $stat['bon']; ?> (<?php echo round(($stat['bon'] / $stat['total_notes']) * 100, 1); ?>%)<br>
                                        Moyens: <?php echo $stat['moyen']; ?> (<?php echo round(($stat['moyen'] / $stat['total_notes']) * 100, 1); ?>%)<br>
                                        Mauvais: <?php echo $stat['mauvais']; ?> (<?php echo round(($stat['mauvais'] / $stat['total_notes']) * 100, 1); ?>%)
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Inclusion du pied de page -->
    <?php include('include/footer.php'); ?>

    <!-- Chargement du JavaScript de Bootstrap -->
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>