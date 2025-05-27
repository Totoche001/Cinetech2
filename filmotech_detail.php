<!DOCTYPE html>
<?php

/* FILMOTECH Website
	DETAIL page

	(c) 2013-2020 by Pascal PLUCHON
	https://www.filmotech.fr
*/

// Inclusion des paramètres et configurations du site
require_once("include/params.inc.php");      // Fichier contenant les paramètres généraux du site
require_once("include/config.inc.php");      // Fichier contenant la configuration de la base de données
require_once("include/search_column.php");    // Fichier contenant les fonctions de recherche

// Récupération et sécurisation de l'ID du film depuis l'URL
$id = isset($_GET['id']) ? $_GET['id'] : -1;  // Si aucun ID n'est fourni, on met -1 par défaut

// Préparation et exécution de la requête SQL pour récupérer les détails du film
$req = $db->prepare('SELECT * FROM ' . $cfg->DB_TABLE . ' WHERE ID = :id');
$req->execute(['id' => $id]);

// Fonctions de formatage du texte
// Fonction pour ajouter des virgules entre les éléments (utilisée pour les listes)
function add_commas($string)
{
	return str_replace("\r", ", ", $string);
}

// Fonction pour convertir les retours à la ligne en balises HTML <br>
function add_br($string)
{
	return nl2br($string);
}
?>
<html lang="fr">

<head>
	<?php include("include/head.php"); ?> <!-- Inclusion des métadonnées et liens CSS/JS -->
	<!-- Bootstrap CSS -->
</head>

<!-- En-tête de la page -->

<body>
	<?php include("include/header.php"); ?> <!-- Inclusion de l'en-tête du site -->

	<!-- Bloc principal -->

	<div class="container">
		<?php include("include/time.php"); ?> <!-- Inclusion de l'affichage de l'heure -->

		<!-- Barre de navigation -->
		<?php include("include/menu.php"); ?> <!-- Inclusion du menu de navigation -->
	</div>
	<!-- Section du contenu principal -->
	<div class="container" style="background-color: #d3d3d3; margin-top: 20px; padding-top: 5px;padding-bottom: 5px;margin-bottom: 20px;">
		<div class="row">
			<div class="col-md-8">
				<!-- Section principale du contenu -->
				<div>
					<?php while ($data = $req->fetch()): ?>
						<!-- Section des détails du film -->
						<div class="row mb-4">
							<!-- Affichage de l'affiche du film -->
							<div class="col-md-4">
								<?php
								// Construction du chemin de l'image de l'affiche
								$filename = sprintf('%s/Filmotech_%05d.jpg', $cfg->POSTERS_DIRECTORY, $data['ID']);
								// Vérification si l'affiche existe
								if (file_exists($filename)): ?>
									<!-- Affichage de l'affiche avec lien pour agrandissement -->
									<a href="<?php echo $filename; ?>" target="_blank">
										<img src="<?php echo $filename; ?>" alt="Affiche" class="img-fluid rounded" style="width: 100%; height: auto;">
									</a>
								<?php else: ?>
									<!-- Affichage d'une image par défaut si l'affiche n'existe pas -->
									<img src="img/0rien.jpg" alt="Affiche" class="img-fluid rounded" style="width: 100%; height: auto;">
								<?php endif; ?>
							</div>

							<!-- Section des informations textuelles du film -->
							<div class="col-md-8">
								<!-- Titre en version française -->
								<h2 class="text-primary">
									<?php echo $data['TitreVF']; ?>
								</h2>
								<!-- Titre en version originale -->
								<h3 class="text-secondary">
									VO : <?php echo $data['TitreVO']; ?>
								</h3>

								<!-- Genre avec formulaire de recherche intégré -->
								<strong>Genre : </strong>
								<form action="index.php" method="post" class="d-inline">
									<input type="hidden" name="search_field" value="Genre">
									<input type="hidden" name="search_query" value="<?php echo htmlspecialchars($data['Genre']); ?>">
									<button type="submit" class="btn btn-link text-decoration-none">
										<?php echo $data['Genre']; ?>
									</button>
								</form>
								<br>
								<!-- Année avec formulaire de recherche intégré -->
								<strong>Année : </strong>
								<form action="index.php" method="post" class="d-inline">
									<input type="hidden" name="search_field" value="Annee">
									<input type="hidden" name="search_query" value="<?php echo htmlspecialchars($data['Annee']); ?>">
									<button type="submit" class="btn btn-link text-decoration-none">
										<?php echo $data['Annee']; ?>
									</button>
								</form>
								<br>
								<!-- Durée du film -->
								<strong>Durée : </strong><?php echo $data['Duree']; ?> mn<br>
								<!-- Pays avec formulaire de recherche intégré -->
								<strong>Pays : </strong>
								<form action="index.php" method="post" class="d-inline">
									<input type="hidden" name="search_field" value="Pays">
									<input type="hidden" name="search_query" value="<?php echo htmlspecialchars($data['Pays']); ?>">
									<button type="submit" class="btn btn-link text-decoration-none">
										<?php echo $data['Pays']; ?>
									</button>
								</form>

								<!-- Affichage de la note avec une image -->
								<div class="mb-3">
									<strong>Note : </strong>
									<img src="img/note<?php echo $data['Note']; ?>.png" alt="Note" class="img-fluid" style="border-radius: 5px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);">
								</div>

								<!-- Lien vers la bande-annonce si disponible -->
								<?php if (($data['BAChemin'] != "") && ($data['BAType'] == "URL")): ?>
									<a href="<?php echo $data['BAChemin']; ?>" class="btn btn-primary" style="border-radius: 5px;">
										Voir la bande-annonce
									</a>
								<?php endif; ?>
							</div>
						</div>

						<!-- Section des détails supplémentaires -->
						<div class="card mb-4">
							<div class="card-body">
								<h4 class="text-primary">Détails Supplémentaires</h4>
								<!-- Affichage des réalisateurs avec gestion des virgules -->
								<p class="mb-2"><span><strong><?php echo ($field_labels['Realisateurs']); ?> : </strong></span>
									<span><?php echo add_commas($data['Realisateurs']); ?></span>
								</p>

								<!-- Affichage des acteurs avec système "voir plus" si le texte dépasse 200 caractères -->
								<p class="mb-2">
									<span>
										<strong><?php echo add_br($field_labels['Acteurs']); ?> :</strong>
									</span>
									<?php
									// Préparation du texte des acteurs avec gestion des virgules
									$acteurs = add_commas($data['Acteurs']);
									// Limite l'affichage initial à 200 caractères si le texte est plus long
									$acteurs_display = (strlen($acteurs) > 200) ? htmlspecialchars(substr($acteurs, 0, 200)) : htmlspecialchars($acteurs);
									?>
									<span id="acteur-text"><?php echo $acteurs_display; ?></span>
									<?php if (strlen($acteurs) > 200): ?>
										<!-- Éléments pour le système "voir plus/moins" -->
										<span id="acteur-dots">...</span>
										<span id="acteur-more" style="display:none;"><?php echo htmlspecialchars(substr($acteurs, 200)); ?></span>
										<button onclick="toggleText('acteur')" id="acteur-btn" class="btn btn-link text-decoration-none">Voir plus</button>
									<?php endif; ?>
								</p>

								<!-- Affichage du synopsis avec système "voir plus" si le texte dépasse 400 caractères -->
								<p class="mb-2">
									<strong>Synopsis : </strong>
									<?php
									// Préparation du synopsis avec gestion des retours à la ligne
									$synopsis = add_br($data['Synopsis']);
									// Limite l'affichage initial à 400 caractères si le texte est plus long
									$synopsis_display = (strlen($synopsis) > 400) ? substr($synopsis, 0, 400) : $synopsis;
									?>
									<span id="synopsis-text"><?php echo $synopsis_display; ?></span>
									<?php if (strlen($synopsis) > 400): ?>
										<!-- Éléments pour le système "voir plus/moins" -->
										<span id="synopsis-dots">...</span>
										<span id="synopsis-more" style="display:none;"><?php echo substr($synopsis, 400); ?></span>
										<button onclick="toggleText('synopsis')" id="synopsis-btn" class="btn btn-link text-decoration-none">Voir plus</button>
									<?php endif; ?>
								</p>

								<!-- Section des bonus, affichée uniquement si $show_features est vrai -->
								<?php if ($show_features): ?>
									<p class="mb-2">
										<strong>Bonus : </strong>
										<?php
										// Préparation du texte des bonus avec gestion des retours à la ligne
										$bonus = add_br($data['Bonus']);
										// Limite l'affichage initial à 200 caractères si le texte est plus long
										$bonus_display = (strlen($bonus) > 200) ? substr($bonus, 0, 200) : $bonus;
										?>
										<span id="bonus-text"><?php echo $bonus_display; ?></span>
										<?php if (strlen($bonus) > 200): ?>
											<!-- Éléments pour le système "voir plus/moins" -->
											<span id="bonus-dots">...</span>
											<span id="bonus-more" style="display:none;"><?php echo substr($bonus, 200); ?></span>
											<button onclick="toggleText('bonus')" id="bonus-btn" class="btn btn-link text-decoration-none">Voir plus</button>
										<?php endif; ?>
									</p>
								<?php endif; ?>

								<!-- Section des commentaires, affichée uniquement si $show_comments est vrai -->
								<?php if ($show_comments): ?>
									<p class="mb-2">
										<strong>Commentaires : </strong>
										<?php
										// Préparation des commentaires avec gestion des retours à la ligne
										$comments = add_br($data['Commentaires']);
										// Limite l'affichage initial à 400 caractères si le texte est plus long
										$comments_display = (strlen($comments) > 400) ? substr($comments, 0, 400) : $comments;
										?>
										<span id="comments-text"><?php echo $comments_display; ?></span>
										<?php if (strlen($comments) > 400): ?>
											<!-- Éléments pour le système "voir plus/moins" -->
											<span id="comments-dots">...</span>
											<span id="comments-more" style="display:none;"><?php echo substr($comments, 400); ?></span>
											<button onclick="toggleText('comments')" id="comments-btn" class="btn btn-link text-decoration-none">Voir plus</button>
										<?php endif; ?>
									</p>
								<?php endif; ?>

								<!-- Script JavaScript pour gérer l'affichage du texte "voir plus/voir moins" -->
								<script>
									// Fonction pour basculer l'affichage du texte complet/réduit
									// Paramètre section : identifiant de la section à modifier (synopsis, bonus, commentaires)
									function toggleText(section) {
										// Récupération des éléments DOM nécessaires
										var dots = document.getElementById(section + "-dots"); // Les points de suspension
										var moreText = document.getElementById(section + "-more"); // Le texte supplémentaire
										var btnText = document.getElementById(section + "-btn"); // Le bouton voir plus/moins

										// Vérification de l'existence des éléments DOM
										if (!dots || !moreText || !btnText) {
											console.error(`Élément introuvable pour la section : ${section}`);
											return;
										}

										// Logique de basculement de l'affichage
										if (dots.style.display === "none") {
											// Si les points sont cachés, on revient à l'état initial (texte réduit)
											dots.style.display = "inline"; // Affiche les points
											btnText.innerHTML = "Voir plus"; // Change le texte du bouton
											moreText.style.display = "none"; // Cache le texte supplémentaire
										} else {
											// Si les points sont visibles, on affiche le texte complet
											dots.style.display = "none"; // Cache les points
											btnText.innerHTML = "Voir moins"; // Change le texte du bouton
											moreText.style.display = "inline"; // Affiche le texte supplémentaire
										}
									}
								</script>
							</div>
						</div>
						<?php if ($show_media_infos): ?>
							<!-- Section des informations sur le média -->
							<div class="card mb-4">
								<div class="card-body">
									<h4 class="text-primary">Informations Médias</h4>
									<!-- Affichage des différentes caractéristiques du média -->
									<p class="mb-2"><strong><?php echo ($field_labels['Reference']); ?> : </strong><?php echo $data['Reference']; ?></p>
									<p class="mb-2"><strong><?php echo ($field_labels['Support']); ?> : </strong><?php echo $data['Support']; ?></p>
									<p class="mb-2"><strong><?php echo ($field_labels['Edition']); ?> : </strong><?php echo $data['Edition']; ?></p>
									<p class="mb-2"><strong><?php echo ($field_labels['Zone']); ?> : </strong><?php echo $data['Zone']; ?></p>
									<p class="mb-2"><strong><?php echo ($field_labels['Langues']); ?> : </strong><?php echo $data['Langues']; ?></p>
									<p class="mb-2"><strong><?php echo ($field_labels['SousTitres']); ?> : </strong><?php echo $data['SousTitres']; ?></p>
									<p class="mb-2"><strong><?php echo ($field_labels['Audio']); ?> : </strong><?php echo $data['Audio']; ?></p>
								</div>
							</div>
						<?php endif; ?>
					<?php endwhile; ?>
				</div>
			</div>

			<!-- Barre latérale pour les liens additionnels -->
			<div class="col-md-4">
				<!-- Section affichant les 25 films les plus récemment ajoutés -->
				<div style="background-color: #f9e79f; padding: 10px; margin-bottom: 10px;">
					<h4 style="background-color: #f9e79f">Les 25 derniers films</h4>
					<?php
					// Requête SQL pour récupérer les 25 derniers films, triés par date d'entrée décroissante
					$latest = $db->query('SELECT * FROM fmt_films ORDER BY EntreeDate DESC LIMIT 25');
					// Boucle pour afficher chaque film avec un lien vers sa page de détails
					while ($movie = $latest->fetch()): ?>
						<a href="filmotech_detail.php?id=<?php echo $movie['ID']; ?>" style="text-decoration: none;">
							<?php echo htmlspecialchars($movie['TitreVF']); // Affichage sécurisé du titre en français 
							?>
						</a><br>
					<?php endwhile; ?>
				</div>
				<!-- Section des liens favoris personnalisés -->
				<div style="background-color: #a2d5ab; padding: 10px; margin-bottom: 10px;">
					<h4 style="background-color: #a2d5ab">Mes liens</h4>
					<?php
					// Boucle pour afficher chaque lien favori avec son nom
					foreach ($favorites as $name => $link): ?>
						<a href="<?php echo $link; ?>" style="text-decoration: none;">
							<?php echo htmlspecialchars($name); // Affichage sécurisé du nom du lien 
							?>
						</a><br>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

	</div>
	<!-- Inclusion du pied de page -->
	<?php include 'include/footer.php'; ?>
	<!-- Chargement du fichier JavaScript de Bootstrap -->
	<script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>