<?php
// Champ de recherche par défaut
$search_field = "TitreVF";
// Label associé au champ de recherche, récupéré depuis le tableau des colonnes
$search_label = $columns[$search_field];
// Initialisation de la requête de recherche vide
$search_query = "";

// Dernière mise à jour
// Vérifie si le fichier 'update.txt' existe, sinon affiche '?'
$lastUpdate = file_exists('update.txt') ? file_get_contents('update.txt') : '?';
// Formate la chaîne de la dernière mise à jour avec la date récupérée
$last_update_label = sprintf($last_update, $lastUpdate);

// Préparation de la requête vers la base de données
// Comptage du nombre total d'enregistrements dans la table configurée
$total_record = $db->query("SELECT count(*) FROM " . $cfg->DB_TABLE)->fetchColumn();

// Récupération de la page actuelle depuis la requête GET, par défaut page 1
$page = isset($_GET['Page']) ? (int)$_GET['Page'] : 1;
// Calcul de l'offset pour la pagination (nombre d'enregistrements à sauter)
$offset = ($page - 1) * $nb_record_per_page;
// Activation de la pagination uniquement si elle est demandée et que le total dépasse la limite par page
$pagination = $paginate && $total_record > $nb_record_per_page;

// Si une requête de recherche a été soumise via POST
if (!empty($_POST['search_query'])) {
    // Sécurisation de la chaîne de recherche pour éviter les injections XSS
    $search_query = htmlspecialchars($_POST['search_query'], ENT_QUOTES, 'UTF-8');
    // Sécurisation du champ de recherche sélectionné
    $search_field = htmlspecialchars($_POST['search_field'], ENT_QUOTES, 'UTF-8');
    // Mise à jour du label du champ de recherche
    $search_label = $columns[$search_field];

    try {
        // Construction de la liste des colonnes à sélectionner dans la requête
        $columns_list = implode(", ", array_keys($columns));
        // Décodage des entités HTML pour la recherche en base
        $search_query_escaped = html_entity_decode($search_query, ENT_QUOTES, 'UTF-8');
        // Préparation de la requête SQL avec clause LIKE pour la recherche
        $stmt = $db->prepare("SELECT ID, $columns_list
        FROM {$cfg->DB_TABLE} 
        WHERE $search_field LIKE :search_query 
        ORDER BY TitreVF");
        // Liaison du paramètre de recherche avec les jokers pour la recherche partielle
        $stmt->bindValue(':search_query', "%$search_query_escaped%", PDO::PARAM_STR);
        // Exécution de la requête
        $stmt->execute();

        // Préparation d'une requête pour compter le nombre total de résultats correspondant à la recherche
        $count = $db->prepare("SELECT COUNT(*) 
        FROM {$cfg->DB_TABLE} 
        WHERE $search_field LIKE :search_query");
        // Liaison du paramètre de recherche
        $count->bindValue(':search_query', "%$search_query_escaped%", PDO::PARAM_STR);
        // Exécution de la requête de comptage
        $count->execute();
        // Récupération du nombre total de résultats
        $total_record = $count->fetchColumn();
        // Mise à jour du compteur local
        $count = $total_record;
        // Désactivation de la pagination lors d'une recherche
        $pagination = false;
    } catch (PDOException $e) {
        // En cas d'erreur SQL, on log l'erreur pour débogage
        error_log("Database error: " . $e->getMessage());
        // Initialisation des variables en cas d'erreur
        $count = 0;
        $response = [];
        $pagination = false;
    }
} else {
    // Si aucune recherche n'est effectuée, on prépare la liste des colonnes à récupérer
    $columns_list = implode(", ", array_keys($columns));
    // Construction de la requête SQL avec pagination si activée
    $query = "SELECT ID, $columns_list 
	FROM {$cfg->DB_TABLE} 
	" . ($paginate ? "ORDER BY TitreVF LIMIT :limit OFFSET :offset" : "ORDER BY TitreVF");
    // Préparation de la requête
    $stmt = $db->prepare($query);
    // Si la pagination est activée, on lie les paramètres limit et offset
    if ($paginate) {
        $stmt->bindValue(':limit', $nb_record_per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    }
    // Exécution de la requête
    $stmt->execute();
    // Le compteur est égal au nombre total d'enregistrements
    $count = $paginate ? $total_record : $total_record;
}

// Récupération de tous les résultats sous forme de tableau associatif
$response = $stmt->fetchAll();
// Formatage du label affichant le nombre de films affichés sur la page courante
$label_movie_count = sprintf($movie_count_paginate, $offset + 1, min($offset + $nb_record_per_page, $total_record), $total_record);

// Fonction de formatage des colonnes pour l'affichage
function column_format($field, $value)
{
    // Pour les champs 'Acteurs' et 'Realisateurs', on remplace les retours chariot par des virgules et tronque la chaîne à 120 caractères
    if (in_array($field, ['Acteurs', 'Realisateurs'])) {
        return htmlspecialchars(mb_substr(str_replace("\r", ", ", $value), 0, 120, "UTF-8")) . (strlen($value) > 80 ? '...' : '');
    }
    // Pour le champ 'Duree', on ajoute ' mn' après la valeur
    return $field == 'Duree' ? htmlspecialchars($value) . ' mn' : htmlspecialchars($value);
}
