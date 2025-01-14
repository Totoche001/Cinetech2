<?php
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
$pagination = $paginate && $total_record > $nb_record_per_page;

if (!empty($_POST['search_query'])) {
    $search_query = htmlspecialchars($_POST['search_query']);
    $search_field = htmlspecialchars($_POST['search_field']);
    $search_label = $columns[$search_field];

    try {
        $columns_list = implode(", ", array_keys($columns));
        $stmt = $db->prepare("SELECT ID, $columns_list
        FROM {$cfg->DB_TABLE} 
        WHERE $search_field LIKE :search_query 
        ORDER BY TitreVF LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $nb_record_per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':search_query', "%$search_query%", PDO::PARAM_STR);
        $stmt->execute();

        $count = $db->prepare("SELECT COUNT(*) 
        FROM {$cfg->DB_TABLE} 
        WHERE $search_field LIKE :search_query");
        $count->bindValue(':search_query', "%$search_query%", PDO::PARAM_STR);
        $count->execute();
        $total_record = $count->fetchColumn();
        $count = $total_record;
        $pagination = $total_record > $nb_record_per_page;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $count = 0;
        $response = [];
        $pagination = false;
    }
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
    $count = $paginate ? $total_record : $total_record;
}
$response = $stmt->fetchAll();

$label_movie_count = sprintf($movie_count_paginate, $offset + 1, min($offset + $nb_record_per_page, $total_record), $total_record);

function column_format($field, $value)
{
    if (in_array($field, ['Acteurs', 'Realisateurs'])) {
        return htmlspecialchars(mb_substr(str_replace("\r", ", ", $value), 0, 120, "UTF-8")) . (strlen($value) > 80 ? '...' : '');
    }
    return $field == 'Duree' ? htmlspecialchars($value) . ' mn' : htmlspecialchars($value);
}