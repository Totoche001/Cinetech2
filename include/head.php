<?php //include("include/head.php"); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    if (isset($_GET['id'])) {
        $film_id = intval($_GET['id']);
        $data = $db->query('SELECT * FROM fmt_films WHERE ID = ' . $film_id);
        $film = $data->fetch();
        echo '<title>' . (isset($window_title) ? $window_title . ' - ' . htmlspecialchars($film["TitreVF"]) : htmlspecialchars($film["TitreVF"]) ) . '</title>';
    } else {
        echo '<title>' . htmlspecialchars($window_title) . '</title>';
    }
    ?>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="img/favicon.png" />