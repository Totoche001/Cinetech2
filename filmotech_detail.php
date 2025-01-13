<!DOCTYPE html>
<?php

/* FILMOTECH Website
	DETAIL page

	(c) 2013-2020 by Pascal PLUCHON
	https://www.filmotech.fr
*/

// Site parameters
require_once("include/params.inc.php");
require_once("include/config.inc.php");

// Select Movie ID
$id = isset($_GET['id']) ? $_GET['id'] : -1;
$req = $db->prepare('SELECT * FROM ' . $cfg->DB_TABLE . ' WHERE ID = :id');
$req->execute(['id' => $id]);

// Formatting functions
function add_commas($string)
{
	return str_replace("\r", ", ", $string);
}
function add_br($string)
{
	return nl2br($string);
}
?>

<html>

<head>
	<?php include("include/head.php"); ?>
	<!-- Bootstrap CSS -->
</head>

<!-- Header -->

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
							<a class="nav-link active" href="#"><?php echo ($navbar_detail_title); ?></a>
						</li>
					</ul>

				</div>
			</div>
		</nav>
	</div>

	<!-- Main Content -->
	<div class="container" style="background-color: #d3d3d3; padding: 20px; border-radius: 10px;">
		<div class="row">
			<div class="col-md-8">
				<!-- Main Content -->
				<div>
					<?php while ($data = $req->fetch()): ?>
						<!-- Movie Details Section -->
						<div class="row mb-4">
							<!-- Movie Poster -->
							<div class="col-md-4">
								<?php
								$filename = sprintf('%s/Filmotech_%05d.jpg', $cfg->POSTERS_DIRECTORY, $data['ID']);
								if (file_exists($filename)): ?>
									<a href="<?php echo $filename; ?>" target="_blank">
										<img src="<?php echo $filename; ?>" alt="Affiche" class="img-fluid rounded">
									</a>
								<?php else: ?>
									<img src="img/0rien.jpg" alt="Affiche" class="img-fluid rounded">
								<?php endif; ?>
							</div>

							<!-- Movie Information -->
							<div class="col-md-8">
								<h2 class="text-primary">
									<?php echo $data['TitreVF']; ?>
								</h2>
								<h3 class="text-secondary">
									VO : <?php echo $data['TitreVO']; ?>
								</h3>

								<strong>Genre : </strong>
								<form action="index.php" method="post" class="d-inline">
									<input type="hidden" name="search_field" value="Genre">
									<input type="hidden" name="search_query" value="<?php echo htmlspecialchars($data['Genre']); ?>">
									<button type="submit" class="btn btn-link text-decoration-none">
										<?php echo $data['Genre']; ?>
									</button>
								</form>
								<br>
								<strong>Année : </strong>
								<form action="index.php" method="post" class="d-inline">
									<input type="hidden" name="search_field" value="Annee">
									<input type="hidden" name="search_query" value="<?php echo htmlspecialchars($data['Annee']); ?>">
									<button type="submit" class="btn btn-link text-decoration-none">
										<?php echo $data['Annee']; ?>
									</button>
								</form>
								<br>
								<strong>Durée : </strong><?php echo $data['Duree']; ?> mn<br>
								<strong>Pays : </strong>
								<form action="index.php" method="post" class="d-inline">
									<input type="hidden" name="search_field" value="Pays">
									<input type="hidden" name="search_query" value="<?php echo htmlspecialchars($data['Pays']); ?>">
									<button type="submit" class="btn btn-link text-decoration-none">
										<?php echo $data['Pays']; ?>
									</button>
								</form>
								<span class="text-muted">Attention, pas encore de pagination pour ce genre de recherche</span>

								<div class="mb-3">
									<strong>Note : </strong>
									<img src="img/note<?php echo $data['Note']; ?>.png" alt="Note" class="img-fluid" style="border-radius: 5px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);">

								</div>
								<?php if (($data['BAChemin'] != "") && ($data['BAType'] == "URL")): ?>
									<a href="<?php echo $data['BAChemin']; ?>" class="btn btn-primary" style="border-radius: 5px;">
										Voir la bande-annonce
									</a>
								<?php endif; ?>
							</div>
						</div>

						<!-- Additional Details Section -->
						<div class="card mb-4">
							<div class="card-body">
								<h4 class="text-primary">Détails Supplémentaires</h4>
								<p class="mb-2"><span><strong><?php echo ($field_labels['Realisateurs']); ?> : </strong></span>
									<span><?php echo add_commas($data['Realisateurs']); ?></span>
								</p>

								<p class="mb-2">
									<span>
										<strong><?php echo add_br($field_labels['Acteurs']); ?> :</strong>
									</span>
									<?php
									$acteurs = add_commas($data['Acteurs']);
									$acteurs_display = (strlen($acteurs) > 200) ? htmlspecialchars(substr($acteurs, 0, 200)) : htmlspecialchars($acteurs);
									?>
									<span id="acteur-text"><?php echo $acteurs_display; ?></span>
									<?php if (strlen($acteurs) > 200): ?>
										<span id="acteur-dots">...</span>
										<span id="acteur-more" style="display:none;"><?php echo htmlspecialchars(substr($acteurs, 200)); ?></span>
										<button onclick="toggleText('acteur')" id="acteur-btn" class="btn btn-link text-decoration-none">Voir plus</button>
									<?php endif; ?>
								</p>

								<!-- Synopsis -->
								<p class="mb-2">
									<strong>Synopsis : </strong>
									<?php
									$synopsis = add_br($data['Synopsis']);
									$synopsis_display = (strlen($synopsis) > 400) ? substr($synopsis, 0, 400) : $synopsis;
									?>
									<span id="synopsis-text"><?php echo $synopsis_display; ?></span>
									<?php if (strlen($synopsis) > 400): ?>
										<span id="synopsis-dots">...</span>
										<span id="synopsis-more" style="display:none;"><?php echo substr($synopsis, 400); ?></span>
										<button onclick="toggleText('synopsis')" id="synopsis-btn" class="btn btn-link text-decoration-none">Voir plus</button>
									<?php endif; ?>
								</p>
								<!-- Bonus -->
								<?php if ($show_features): ?>
									<p class="mb-2">
										<strong>Bonus : </strong>
										<?php
										$bonus = add_br($data['Bonus']);
										$bonus_display = (strlen($bonus) > 200) ? substr($bonus, 0, 200) : $bonus;
										?>
										<span id="bonus-text"><?php echo $bonus_display; ?></span>
										<?php if (strlen($bonus) > 200): ?>
											<span id="bonus-dots">...</span>
											<span id="bonus-more" style="display:none;"><?php echo substr($bonus, 200); ?></span>
											<button onclick="toggleText('bonus')" id="bonus-btn" class="btn btn-link text-decoration-none">Voir plus</button>
										<?php endif; ?>
									</p>
								<?php endif; ?>
								<!-- Commentaires -->
								<?php if ($show_comments): ?>
									<p class="mb-2">
										<strong>Commentaires : </strong>
										<?php
										$comments = add_br($data['Commentaires']);
										$comments_display = (strlen($comments) > 400) ? substr($comments, 0, 400) : $comments;
										?>
										<span id="comments-text"><?php echo $comments_display; ?></span>
										<?php if (strlen($comments) > 400): ?>
											<span id="comments-dots">...</span>
											<span id="comments-more" style="display:none;"><?php echo substr($comments, 400); ?></span>
											<button onclick="toggleText('comments')" id="comments-btn" class="btn btn-link text-decoration-none">Voir plus</button>
										<?php endif; ?>
									</p>
								<?php endif; ?>

								<script>
									function toggleText(section) {
										var dots = document.getElementById(section + "-dots");
										var moreText = document.getElementById(section + "-more");
										var btnText = document.getElementById(section + "-btn");

										if (!dots || !moreText || !btnText) {
											console.error(`Élément introuvable pour la section : ${section}`);
											return;
										}

										if (dots.style.display === "none") {
											dots.style.display = "inline";
											btnText.innerHTML = "Voir plus";
											moreText.style.display = "none";
										} else {
											dots.style.display = "none";
											btnText.innerHTML = "Voir moins";
											moreText.style.display = "inline";
										}
									}
								</script>
							</div>
						</div>
						<?php if ($show_media_infos): ?>
							<div class="card mb-4">
								<div class="card-body">
									<h4 class="text-primary">Informations Médias</h4>
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

			<!-- Sidebar for Additional Links -->
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

	</div>
	<!-- Footer -->
	<?php include 'footer.php'; ?>

</body>


</html>