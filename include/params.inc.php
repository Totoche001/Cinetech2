<?php
// Site parameters

//Column and search field
$columns = [
    "TitreVF" => "Titre VF",
    "TitreVO" => "Titre VO",
    "Genre" => "Genre",
    "Acteurs" => "Acteurs",
    "Realisateurs" => "Realisateurs",
    "Annee" => "Année",
    "Duree" => "Durée",
    "Commentaires" => "Commentaires",
    "Bonus" => "Bonus",
    "Reference" => "Référence",
    "Note" => "Note",
    "EntreeDate" => "EntreeDate",
    "FilmVu" => "FilmVu",
    "BAChemin" => "BAChemin",
    "BAType" => "BAType",
    "Support" => "Support",
    "Synopsis" => "Synopsis",
	"PretEnCours" => "PretEnCours",
	"Pays" => "Pays"
];

// --- Header ---
$window_title = "Filmotech";
$show_title = false;
$title_label = "Filmotech";

// --- Navbar ---
$navbar_title = "Filmotech";
$navbar_active_title = "Liste des films";
$navbar_search_by = "Recherche par";
$navbar_search = "Recherche";
$navbar_go = "Rechercher !";

// --- Database labels ---
$field_labels = [
	"TitreVF" => "Titre",
	"TitreVO" => "Titre original",
	"Genre" => "Genre",
	"Acteurs" => "Acteurs",
	"Realisateurs" => "Réalisateurs",
	"Commentaires" => "Commentaires",
	"Bonus" => "Bonus",
	"Reference" => "Référence",
	"Duree" => "Durée",
	"Annee" => "Année",
	"Synopsis" => "Synopsis",
	"Support" => "Support",
	"NombreSupport" => "Nbre supports",
	"Langues" => "Langues",
	"SousTitres" => "Sous-titres",
	"Audio" => "Audio",
	"Zone" => "Zone",
	"Edition" => "Edition",
	"Pays" => "Pays"
];

// --- Movie list ---
$second_column = "Genre";
$show_lent = true;
$show_not_seen = true;
$movie_not_seen = "Film non visionné";
$movie_lent = "Prêts en cours";
$movie_not_seen_and_lent = "Film prêté et non visionné";

$paginate = true;
$nb_record_per_page = 50;
$next_page = "Suivant";
$previous_page = "Précédent";

$no_result = "Aucun résultat pour";
$result_for_search = "Résultat de la recherche";
$contains = "contient";

// --- Movie detail ---
$navbar_detail_title = "Détail d'un film";
$show_comments = true;
$show_features = true;
$show_trailer = "Voir la bande annonce";
$show_media = "Voir le film";

// --- Sidebar ---
// Latest addition (List page)
$show_latest = true;
$max_latest = 25;
$latest_label = sprintf("Les %d derniers films", $max_latest);

// Favorites
$show_favorites_index = true;
$show_favorites_detail = true;
$favorites_label = "Mes liens";
$favorites = array(
	"Allocine" => "https://www.allocine.fr",
	"IMDB" => "https://www.imdb.com"
);

// Personal code #1
$show_custom_1_index = false;
$show_custom_1_detail = false;
$custom_label_1 = "Code perso 1";
$custom_code_1 = "";

// Personal code #2
$show_custom_2_index = false;
$show_custom_2_detail = false;
$custom_label_2 = "Code perso 2";
$custom_code_2 = "";

// Movie details (Detail page)
$show_media_infos = true;
$media_informations = "Informations support";

// --- Footer ---
$powered_by = "Propulsé par";

// --- Year Website started
$startYear = 2016;
$currentYear = date("Y");
$copyright = "©   $startYear à $currentYear";
$yearsSinceStart = $currentYear - $startYear;
//$copyright = "©   2016 à " . date("Y");

$mail_label = "your name";
$mail_address = "your mail address";

// --- Footer ---
$show_update_date = true;
$movie_count = "%d film(s)";
$movie_count_paginate = "[%d-%d]  -  %d film(s)";
$last_update = "Dernière mise à jour le " . date('d/m/Y H:i:s.', getlastmod());
?>
