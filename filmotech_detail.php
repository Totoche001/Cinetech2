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
function add_commas($string) {
	return str_replace("\r", ", ", $string);
}
function add_br($string) {
	return nl2br($string);
}
?>

<html>

<head>
	<?php include("include/head.php"); ?>
</head>

<!-- Header -->
<body class="text-gray-800">
	<!-- Header -->
	<div class="text-center py-4">
		<?php if ($show_title): ?>
			<div class="bg-gray-200 p-4 rounded shadow">
				<h1 class="text-4xl font-bold">
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>">
						<img src="img/logo.png" alt="Filmotech" class="h-28 mx-auto" />
					</a>
				</h1>
			</div>
		<?php else: ?>
			<div class="text-center">
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>">
					<img src="img/top.png" alt="Top Filmotech" class="h-28 mx-auto" />
				</a>
			</div>
		<?php endif; ?>
	</div>

	<!-- Main block -->

	<div class="container mx-auto">
		<?php include("include/time.php"); ?>
		<!-- Navigation bar -->
		<nav class="bg-gray-800 shadow">
			<div class="mx-auto px-4 sm:px-6 lg:px-8">
				<div class="relative flex items-center justify-between h-16">
					<!-- Logo and brand (aligned to the left) -->
					<div class="flex items-center">
                <a href="index.php" class="flex items-center">
                    <img src="img/favicon.png" alt="Filmotech" class="h-10 w-auto mr-2">
                    <span class="text-xl font-bold text-white">Filmotech</span>
                </a>
                <span class="text-gray-300 hover:text-white ml-6"><?php echo $navbar_detail_title; ?></span>
            </div>
					<!-- Mobile menu button -->
					<div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
						<button type="button"
							class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white"
							aria-controls="mobile-menu" aria-expanded="false">
							<span class="sr-only">Open main menu</span>
							<svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
								aria-hidden="true">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
							</svg>
						</button>
					</div>
				</div>
			</div>
		</nav>

		<!-- Main Content -->
		<div class="bg-gray-100 w-full px-4 py-6 mt-4">
			<div class="grid grid-cols-12 gap-6">
				<!-- Main Content -->
				<div class="col-span-8 bg-white p-6 rounded-lg shadow-md">
					<?php while ($data = $req->fetch()): ?>
						<!-- Movie Details Section -->
						<div class="flex items-start mb-6">
							<!-- Movie Poster -->
							<div class="w-1/2">
								<?php
								$filename = sprintf('%s/Filmotech_%05d.jpg', $cfg->POSTERS_DIRECTORY, $data['ID']);
								if (file_exists($filename)): ?>
									<a href="<?php echo $filename; ?>" target="_blank">
										<img src="<?php echo $filename; ?>" alt="Affiche" class="rounded-lg shadow-md hover:shadow-lg w-full h-auto">
									</a>
								<?php else: ?>
									<img src="img/0rien.jpg" alt="Affiche" class="rounded-lg shadow-md w-full h-auto">
								<?php endif; ?>
							</div>

							<!-- Movie Information -->
							<div class="w-1/2 ml-6">
								<h2 class="text-3xl font-bold text-gray-800 mb-2">
									<?php echo $data['TitreVF']; ?>
								</h2>
								<h3 class="text-xl text-gray-500 mb-4">
									VO : <?php echo $data['TitreVO']; ?>
								</h3>
								<p class="text-gray-600">
									<strong>Genre : </strong>
								<form action="index.php" method="post" class="inline">
									<input type="hidden" name="search_field" value="Genre">
									<input type="hidden" name="search_query" value="<?php echo htmlspecialchars($data['Genre']); ?>">
									<button type="submit" class="text-blue-500 hover:underline">
										<?php echo $data['Genre']; ?>
									</button>
								</form><br>
								<strong>Année : </strong>
								<form action="index.php" method="post" class="inline">
									<input type="hidden" name="search_field" value="Annee">
									<input type="hidden" name="search_query" value="<?php echo htmlspecialchars($data['Annee']); ?>">
									<button type="submit" class="text-blue-500 hover:underline">
										<?php echo $data['Annee']; ?>
									</button>
								</form><br>
								<strong>Durée : </strong><?php echo $data['Duree']; ?> mn<br>
								<strong>Pays : </strong>
								<form action="index.php" method="post" class="inline">
									<input type="hidden" name="search_field" value="Pays">
									<input type="hidden" name="search_query" value="<?php echo htmlspecialchars($data['Pays']); ?>">
									<button type="submit" class="text-blue-500 hover:underline">
										<?php echo $data['Pays']; ?>
									</button>
								</form>
								<span style="color: red; font-size: small;">attention pas encore de pagination pour ce genre de recherche</span>
								</p>
								<div class="mt-4">
									<img src="img/note<?php echo $data['Note']; ?>.png" alt="Note" class="inline-block">
								</div>
								<?php if (($data['BAChemin'] != "") && ($data['BAType'] == "URL")): ?>
									<a href="<?php echo $data['BAChemin']; ?>" class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white font-medium rounded-lg shadow-md hover:bg-blue-500">
										Voir la bande-annonce
									</a>
								<?php endif; ?>
							</div>
						</div>

						<!-- Additional Details Section -->
						<div class="bg-gray-50 p-6 rounded-lg shadow-md">
							<h4 class="text-2xl font-bold text-gray-700 mb-4">Détails Supplémentaires</h4>
							<span style="color: red; font-size: small;">des liens pour chaque acteur ou réalisateur ne fonctionne pas encore</span>
							<!-- Réalisateurs -->
							<!--<p class="mb-2">
								<strong>Réalisateurs : </strong>
								<php
								$realisateurs = explode(",", $data['Realisateurs']);
								foreach ($realisateurs as $index => $realisateur): ?>
							<form action="index.php" method="post" class="inline">
								<input type="hidden" name="search_field" value="Realisateurs">
								<input type="hidden" name="search_query" value="<php echo htmlspecialchars(trim($realisateur)); ?>">
								<button type="submit" class="text-blue-500 hover:underline">
									<php echo htmlspecialchars(trim($realisateur)); ?>
								</button>
							</form>
							<php if ($index < count($realisateurs) - 1): ?>
								<span>, </span>
							<php endif; ?>
						<php endforeach; ?>
						</p>-->
							<p><span class="text-info"><strong><?php echo ($field_labels['Realisateurs']); ?> : </strong></span>
								<span class="muted"><?php echo add_commas($data['Realisateurs']); ?></span>
							</p>

							<!-- Acteurs -->
							<!--<p class="mb-2">
							<strong><php echo ($field_labels['Acteurs']); ?></strong>
							<php
							$acteurs = explode(",", $data['Acteurs']);
							foreach ($acteurs as $index => $acteur): ?>
						<form action="index.php" method="post" class="inline">
							<input type="hidden" name="search_field" value="Acteurs">
							<input type="hidden" name="search_query" value="<php echo htmlspecialchars(trim($acteur)); ?>">
							<button type="submit" class="text-blue-500 hover:underline">
								<php echo htmlspecialchars(trim($acteur)); ?>
							</button>
						</form>
						<php if ($index < count($acteurs) - 1): ?>
							<span>, </span>
						<php endif; ?>
					<php endforeach; ?>
					</p>--><br>
							<p>
								<span class="text-info">
									<strong><?php echo add_br($field_labels['Acteurs']); ?> :</strong>
								</span>
								<?php
								$acteurs = add_commas($data['Acteurs']);
								$acteurs_display = (strlen($acteurs) > 200) ? htmlspecialchars(substr($acteurs, 0, 200)) : htmlspecialchars($acteurs);
								?>
								<span class="muted" id="acteur-text"><?php echo $acteurs_display; ?></span>
								<?php if (strlen($acteurs) > 200): ?>
									<span id="acteur-dots">...</span>
									<span id="acteur-more" style="display:none;"><?php echo htmlspecialchars(substr($acteurs, 200)); ?></span>
									<button onclick="toggleText('acteur')" id="acteur-btn" class="text-blue-500 hover:underline">Voir plus</button>
								<?php endif; ?>
							</p>

							<br>
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
									<button onclick="toggleText('synopsis')" id="synopsis-btn" class="text-blue-500 hover:underline">Voir plus</button>
								<?php endif; ?>
							</p>
							<br>
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
										<button onclick="toggleText('bonus')" id="bonus-btn" class="text-blue-500 hover:underline">Voir plus</button>
									<?php endif; ?>
								</p>
							<?php endif; ?>
							<br>
							<!-- Commentaires -->
							<?php if ($show_comments): ?>
								<p>
									<strong>Commentaires : </strong>
									<?php
									$comments = add_br($data['Commentaires']);
									$comments_display = (strlen($comments) > 400) ? substr($comments, 0, 400) : $comments;
									?>
									<span id="comments-text"><?php echo $comments_display; ?></span>
									<?php if (strlen($comments) > 400): ?>
										<span id="comments-dots">...</span>
										<span id="comments-more" style="display:none;"><?php echo substr($comments, 400); ?></span>
										<button onclick="toggleText('comments')" id="comments-btn" class="text-blue-500 hover:underline">Voir plus</button>
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
					<?php endwhile; ?>
				</div>

				<!-- Sidebar for Additional Links -->
				<div class="col-span-4">
					<!-- Additional Details -->
					<div class="bg-yellow-50 p-6 rounded-lg shadow-md mb-6">
						<h4 class="text-xl font-bold text-yellow-800 mb-4">Mes liens</h4>
						<ul class="list-disc list-inside text-yellow-700">
							<?php foreach ($favorites as $key => $value): ?>
								<li><a href="<?php echo $value; ?>" class="text-yellow-600 hover:underline"><?php echo $key; ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>


		<!-- Footer -->
		<footer class="bg-gray-800 text-gray-200 py-4">
			<div class="container mx-auto text-center">
				<p>&copy; <?php echo date("Y"); ?> - <?php echo ($copyright); ?></p>
				<p><a href="mailto:<?php echo ($mail_address); ?>" class="text-blue-400 hover:underline"><?php echo ($mail_label); ?></a></p>
				<p>Propulsé par <a href="http://www.filmotech.fr" target="_blank" class="text-blue-400 hover:underline">Filmotech</a></p>
			</div>
		</footer>

</body>

</html>