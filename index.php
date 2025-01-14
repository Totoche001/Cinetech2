<!DOCTYPE html>
<html lang="fr">
<?php

/* FILMOTECH Website
	INDEX page

	(c) 2013-2025 by Pascal PLUCHON and Anthony Semal
	https://www.filmotech.fr
*/

require_once("include/params.inc.php");
require_once("include/config.inc.php");

// Query parameters
require_once("include/search_column.php");
?>
<head>
    <?php include("include/head.php"); ?>
</head>

<body>
   <?php include("include/header.php"); ?>
    <!-- Main block -->
    <div class="container">
        <?php include("include/time.php"); ?>
        <!-- Navigation bar -->
        <?php include("include/menu.php"); ?>
    </div>
    <!-- Main Content -->
    <div class="container" style="background-color: #d3d3d3; margin-top: 20px; padding-top: 5px;padding-bottom: 5px;margin-bottom: 20px;">
        <!-- Films au hasard -->
        <h5 class="mt-1">Films au hasard:</h5>
        <?php
        $data = $db->query('SELECT * FROM fmt_films ORDER BY RAND() LIMIT 1');
        $film = $data->fetch();
        if ($film):
        ?>
            <div class="alert alert-info text-center" style="max-width: 50%; border-radius: 10px; padding: 15px;">
                <a href="filmotech_detail.php?id=<?php echo htmlspecialchars($film["ID"]); ?>" target="_blank" title="<?php echo htmlspecialchars($film["Synopsis"]); ?>" style="text-decoration: none; color: #007bff; font-weight: bold;">
                    <?php echo htmlspecialchars($film["TitreVF"]); ?>
                </a>
                <div class="mt-2">
                    <span class="badge bg-secondary"><?php echo htmlspecialchars($film["Genre"]); ?></span>
                    <span class="badge bg-secondary"><?php echo htmlspecialchars($film["Annee"]); ?></span>
                </div>
                <div class="mt-2">
                    <strong>Acteurs:</strong> <span id="actors" style="display:none;"><?php echo htmlspecialchars($film["Acteurs"]); ?></span>
                    <button class="badge bg-secondary" onclick="var actors = document.getElementById('actors'); actors.style.display = (actors.style.display === 'none') ? 'block' : 'none'; this.innerHTML = (actors.style.display === 'block') ? 'Cacher les acteurs' : 'Afficher les acteurs';"><?php echo (isset($film["Acteurs"]) && $film["Acteurs"] !== '') ? 'Afficher les acteurs' : ''; ?></button>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center" style="border-radius: 10px; padding: 15px;">Aucun film disponible.</div>
        <?php endif; ?>

        <?php if (!empty($search_query)): ?>
            <?php if ($count > 0): ?>
                <div class="alert alert-info">
                    Recherche de "<?php echo htmlspecialchars($search_query); ?>" dans <?php echo htmlspecialchars($search_label); ?>
                    (<?php echo $count; ?> résultat<?php echo $count > 1 ? 's' : ''; ?>)
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    Aucun film ne correspond à votre recherche ("<?php echo htmlspecialchars($search_query); ?>" dans <?php echo htmlspecialchars($search_label); ?>)
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Pagination (top) -->
        <?php if ($pagination && $count > 0): ?>
            <div class="d-flex justify-content-between my-3">
                <button class="btn btn-secondary" onclick="location.href='?Page=<?php echo max(1, $page - 1); ?><?php echo !empty($search_query) ? '&search_query=' . urlencode($search_query) . '&search_field=' . urlencode($search_field) : ''; ?>'" <?php echo $page == 1 ? 'disabled' : ''; ?>>
                    ← <?php echo $previous_page; ?>
                </button>
                <span><?php echo $page; ?> / <?php echo ceil($total_record / $nb_record_per_page); ?></span>
                <button class="btn btn-secondary" onclick="location.href='?Page=<?php echo $page + 1; ?><?php echo !empty($search_query) ? '&search_query=' . urlencode($search_query) . '&search_field=' . urlencode($search_field) : ''; ?>'" <?php echo $page >= ceil($total_record / $nb_record_per_page) ? 'disabled' : ''; ?>>
                    <?php echo $next_page; ?> →
                </button>
            </div>
        <?php endif; ?>

        <?php if ($count > 0): ?>
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-striped" id="sortableTable">
                        <thead>
                            <tr>
                                <th></th>
                                <th onclick="sortTable(1)" style="cursor: pointer;">Titre VF <span id="arrow1"></span></th>
                                <th onclick="sortTable(2)" style="cursor: pointer;">Genre <span id="arrow2"></span></th>
                                <th onclick="sortTable(3)" style="cursor: pointer;">Support <span id="arrow3"></span></th>
                                <th onclick="sortTable(4)" style="cursor: pointer;">Année <span id="arrow4"></span></th>
                                <th onclick="sortTable(5, true)" style="cursor: pointer;">Durée <span id="arrow5"></span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($response as $index => $data): ?>
                                <tr>
                                    <td>
                                        <?php
                                        if ($show_lent || $show_not_seen) {
                                            if ($show_lent && $show_not_seen) {
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
                                    <td><a href="filmotech_detail.php?id=<?php echo $data['ID']; ?>" target="_blank" title="<?php echo $data['Synopsis']; ?>"><?php echo $data['TitreVF']; ?></a></td>
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
                        var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
                        table = document.getElementById("sortableTable");
                        switching = true;
                        dir = "asc";
                        
                        // Reset all arrows
                        for(let i = 1; i <= 5; i++) {
                            document.getElementById("arrow" + i).innerHTML = "";
                        }
                        
                        while (switching) {
                            switching = false;
                            rows = table.rows;
                            
                            for (i = 1; i < (rows.length - 1); i++) {
                                shouldSwitch = false;
                                x = rows[i].getElementsByTagName("TD")[n];
                                y = rows[i + 1].getElementsByTagName("TD")[n];
                                
                                // Get text content for titles (removing HTML)
                                let xContent = x.textContent || x.innerText;
                                let yContent = y.textContent || y.innerText;
                                
                                if (isNumber) {
                                    var xValue = parseInt(xContent.replace(/[^\d]/g, ''));
                                    var yValue = parseInt(yContent.replace(/[^\d]/g, ''));
                                    
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
                                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                                switching = true;
                                switchcount++;
                            } else {
                                if (switchcount == 0 && dir == "asc") {
                                    dir = "desc";
                                    switching = true;
                                }
                            }
                        }
                        
                        // Update arrow for current column
                        document.getElementById("arrow" + n).innerHTML = dir === "asc" ? " ↑" : " ↓";
                    }
                    </script>
                </div>
                <div class="col-md-4">
                    <!-- Les 25 derniers films -->
                    <div style="background-color: #f9e79f; padding: 10px; margin-bottom: 10px;">
                        <h4 style="background-color: #f9e79f">Les 25 derniers films</h4>
                        <?php
                        $latest = $db->query('SELECT * FROM fmt_films ORDER BY EntreeDate DESC LIMIT 25');
                        while ($movie = $latest->fetch()): ?>
                            <a href="filmotech_detail.php?id=<?php echo $movie['ID']; ?>" style="text-decoration: none;">
                                <?php echo htmlspecialchars($movie['TitreVF']); ?>
                            </a><br>
                        <?php endwhile; ?>
                    </div>
                    <!-- Mes liens -->
                    <div style="background-color: #a2d5ab; padding: 10px; margin-bottom: 10px;">
                        <h4 style="background-color: #a2d5ab">Mes liens</h4>
                        <?php foreach ($favorites as $name => $link): ?>
                            <a href="<?php echo $link; ?>" style="text-decoration: none;">
                                <?php echo htmlspecialchars($name); ?>
                            </a><br>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Pagination (bottom) -->
            <?php if ($pagination): ?>
                <div class="text-center my-3">
                    <?php echo( '<p><small>' . $label_movie_count . '</small></p>' ); ?>
                    <?php if ($show_update_date && $count!=0) echo( '<p><small>' . $last_update_label . '</small></p>' ); ?>
                </div>
                <div class="d-flex justify-content-between" style="margin-top: 20px;">
                    <button class="btn btn-secondary" onclick="location.href='?Page=<?php echo max(1, $page - 1); ?><?php echo !empty($search_query) ? '&search_query=' . urlencode($search_query) . '&search_field=' . urlencode($search_field) : ''; ?>'" <?php echo $page == 1 ? 'disabled' : ''; ?>>
                        ← <?php echo $previous_page; ?>
                    </button>
                    <span>Page <?php echo $page; ?> / <?php echo ceil($total_record / $nb_record_per_page); ?></span>
                    <button class="btn btn-secondary" onclick="location.href='?Page=<?php echo $page + 1; ?><?php echo !empty($search_query) ? '&search_query=' . urlencode($search_query) . '&search_field=' . urlencode($search_field) : ''; ?>'" <?php echo $page >= ceil($total_record / $nb_record_per_page) ? 'disabled' : ''; ?>>
                        <?php echo $next_page; ?> →
                    </button>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

<!-- Footer -->
<?php include ('include/footer.php'); ?>

<script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>