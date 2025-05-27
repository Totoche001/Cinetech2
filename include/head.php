<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
// Vérifie si un paramètre 'id' est présent dans l'URL
if (isset($_GET['id'])) {
    // Convertit la valeur de 'id' en entier pour éviter les injections SQL
    $film_id = intval($_GET['id']);
    // Exécute une requête pour récupérer les informations du film correspondant à l'ID
    $data = $db->query('SELECT * FROM fmt_films WHERE ID = ' . $film_id);
    // Récupère la première ligne de résultat de la requête
    $film = $data->fetch();
    // Affiche la balise <title> avec le titre de la fenêtre suivi du titre du film en version française
    // htmlspecialchars est utilisé pour éviter les problèmes de sécurité liés aux caractères spéciaux
    echo '<title>' . (isset($window_title) ? $window_title . ' - ' . htmlspecialchars($film["TitreVF"]) : htmlspecialchars($film["TitreVF"])) . '</title>';
} else {
    // Si aucun 'id' n'est fourni, affiche simplement le titre de la fenêtre
    echo '<title>' . htmlspecialchars($window_title) . '</title>';
}
?>
<!-- Inclusion du fichier CSS principal pour le style -->
<link rel="stylesheet" href="css/style.css">
<!-- Inclusion de la feuille de style Bootstrap pour la mise en page responsive -->
<link rel="stylesheet" href="css/bootstrap.min.css">
<!-- Définition de l'icône favicon du site -->
<link rel="icon" type="image/png" href="img/favicon.png" />