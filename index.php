<!DOCTYPE html>
<?php

/* FILMOTECH Website
	INDEX page

	(c) 2013-2020 by Pascal PLUCHON
	https://www.filmotech.fr
https://www.filmotech.fr/forum_archive/viewtopic.php?id=2928
	Rajouter une colonne pour la recherche : ligne 37
	Modifier une colonne ("preparing database request" et "Movielist"): ligne 70,85,91,217,252
*/

require_once("include/params.inc.php");
require_once("include/config.inc.php");

// Query parameters


$search_field = "TitreVF";
$search_label = $columns[$search_field];
$search_query = "";

// Last update
$lastUpdate = file_exists('update.txt') ? file_get_contents('update.txt') : '?';
$last_update_label = sprintf($last_update, $lastUpdate);

// Preparing database request
// Count number of records
$total_record = $db->query("SELECT count(*) FROM " . $cfg->DB_TABLE)->fetchColumn();

$page = isset($_GET['Page']) ? (int)$_GET['Page'] : 1;
$offset = ($page - 1) * $nb_record_per_page;
$pagination = $paginate;

if (!empty($_POST['search_query'])) {
    $search_query = $_POST['search_query'];
    $search_field = $_POST['search_field'];
    $search_label = $columns[$search_field];

    $columns_list = implode(", ", array_keys($columns));
    $stmt = $db->prepare("SELECT ID, $columns_list
	FROM {$cfg->DB_TABLE} 
	WHERE $search_field LIKE :search_query 
	ORDER BY TitreVF");
    $stmt->execute([':search_query' => "%$search_query%"]);

    $count = $db->prepare("SELECT COUNT(*) 
	FROM {$cfg->DB_TABLE} 
	WHERE $search_field LIKE :search_query");
    $count->execute([':search_query' => "%$search_query%"]);
    $count = $count->fetchColumn();
    $pagination = false;
} else {
    $columns_list = implode(", ", array_keys($columns));
    $query = "SELECT ID, $columns_list 
	FROM {$cfg->DB_TABLE} 
	" . ($paginate ? "ORDER BY TitreVF LIMIT :limit OFFSET :offset" : "ORDER BY TitreVF");
    $stmt = $db->prepare($query);
    if ($paginate) {
        $stmt->bindValue(':limit', $nb_record_per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    }
    $stmt->execute();
    $count = $paginate ? 1 : $total_record;
}
$response = $stmt->fetchAll();

$label_movie_count = $pagination ? sprintf($movie_count_paginate, $offset + 1, min($offset + $nb_record_per_page, $total_record), $total_record) : sprintf($movie_count, $count);

function column_format($field, $value)
{
    if (in_array($field, ['Acteurs', 'Realisateurs'])) {
        return htmlspecialchars(mb_substr(str_replace("\r", ", ", $value), 0, 120, "UTF-8")) . (strlen($value) > 80 ? '...' : '');
    }
    return $field == 'Duree' ? htmlspecialchars($value) . ' mn' : htmlspecialchars($value);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include("include/head.php"); ?>
</head>

<body>
   <?php include("include/header.php"); ?>
    <!-- Main block -->
    <div class="container">
        <?php include("include/time.php"); ?>
        <!-- Navigation bar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <img src="img/favicon.png" alt="Filmotech" style="height: 30px;">
                    <span class="ms-2">Filmotech</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" href="#"><?php echo ($navbar_active_title); ?></a>
                        </li>
                    </ul>
                    <!-- Search bar -->
                    <form class="d-flex ms-auto" role="search" method="post">
                        <input type="hidden" name="search_field" value="<?php echo ($search_field); ?>">
                        <input class="form-control me-2" type="text" placeholder="<?php echo ($navbar_search); ?>" name="search_query" aria-label="Search">
                        <select class="form-select me-2" name="search_field">
                            <option value="TitreVF"><?php echo ($field_labels['TitreVF']); ?></option>
                            <option value="TitreVO"><?php echo ($field_labels['TitreVO']); ?></option>
                            <option value="Genre"><?php echo ($field_labels['Genre']); ?></option>
                            <option value="Acteurs"><?php echo ($field_labels['Acteurs']); ?></option>
                            <option value="Realisateurs"><?php echo ($field_labels['Realisateurs']); ?></option>
                            <option value="Annee"><?php echo ($field_labels['Annee']); ?></option>
                            <option value="Support"><?php echo ($field_labels['Support']); ?></option>
                        </select>
                        <button class="btn btn-outline-success" type="submit" style="min-width: 120px;">
                            <?php echo ($navbar_go); ?>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </div>
    <!-- Main Content -->
    <div class="container" style="background-color: #d3d3d3">
        <!-- Films au hasard -->
        <h5 class="mt-4">Films au hasard:</h5>
        <?php
        $data = $db->query('SELECT * FROM fmt_films ORDER BY RAND() LIMIT 1');
        $film = $data->fetch();
        if ($film):
        ?>
            <div class="alert alert-info text-center" style="max-width: 100%; border-radius: 10px; padding: 15px;">
                <a href="filmotech_detail.php?id=<?php echo htmlspecialchars($film["ID"]); ?>" target="_blank" title="avec <?php echo htmlspecialchars($film["Acteurs"]); ?>" style="text-decoration: none; color: #007bff; font-weight: bold;">
                    <?php echo htmlspecialchars($film["TitreVF"]); ?>
                </a>
                <div class="mt-2">
                    <span class="badge bg-secondary"><?php echo htmlspecialchars($film["Genre"]); ?></span>
                    <span class="badge bg-secondary"><?php echo htmlspecialchars($film["Annee"]); ?></span>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center" style="border-radius: 10px; padding: 15px;">Aucun film disponible.</div>
        <?php endif; ?>

        <!-- Pagination (top) -->
        <?php if ($pagination): ?>
            <div class="d-flex justify-content-between my-3">
                <button class="btn btn-secondary" onclick="location.href='?Page=<?php echo max(1, $page - 1); ?>'" <?php echo $page == 1 ? 'disabled' : ''; ?>>
                    ← <?php echo $previous_page; ?>
                </button>
                <span><?php echo $page; ?> / <?php echo ceil($total_record / $nb_record_per_page); ?></span>
                <button class="btn btn-secondary" onclick="location.href='?Page=<?php echo $page + 1; ?>'" <?php echo $page >= ceil($total_record / $nb_record_per_page) ? 'disabled' : ''; ?>>
                    <?php echo $next_page; ?> →
                </button>
            </div>
        <?php endif; ?>

        <?php if ($count > 0): ?>
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Titre VF</th>
                                <th>Genre</th>
                                <th>Support</th>
                                <th>Année</th>
                                <th>Durée</th>
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
                <div class="d-flex justify-content-between my-3">
                    <button class="btn btn-secondary" onclick="location.href='?Page=<?php echo max(1, $page - 1); ?>'" <?php echo $page == 1 ? 'disabled' : ''; ?>>
                        ← <?php echo $previous_page; ?>
                    </button>
                    <span>Page <?php echo $page; ?> / <?php echo ceil($total_record / $nb_record_per_page); ?></span>
                    <button class="btn btn-secondary" onclick="location.href='?Page=<?php echo $page + 1; ?>'" <?php echo $page >= ceil($total_record / $nb_record_per_page) ? 'disabled' : ''; ?>>
                        <?php echo $next_page; ?> →
                    </button>
                </div>
            <?php endif; ?>
    </div>
<?php else: ?>
    <div class="alert alert-warning">Aucun film ne correspond à votre recherche.</div>
<?php endif; ?>



<!-- Footer -->
<?php include ('include/footer.php'); ?>

<script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>