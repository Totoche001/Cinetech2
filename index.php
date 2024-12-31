<!DOCTYPE html>
<?php

/* FILMOTECH Website
	INDEX page

	(c) 2013-2020 by Pascal PLUCHON
	https://www.filmotech.fr
https://www.filmotech.fr/forum_archive/viewtopic.php?id=2928
		
*/

require_once("include/params.inc.php");
require_once("include/config.inc.php");

// Query parameters
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
	"Support" => "Support"
];

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
<html>

<head>
	<?php include("include/head.php"); ?>
</head>

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
			<div class="mx-auto px-4 sm:px-6 lg:px-7">
				<div class="relative flex items-center justify-between h-16">
					<!-- Logo and brand (aligned to the left) -->
					<div class="flex items-center">
						<a href="index.php" class="flex items-center">
							<img src="img/favicon.png" alt="Filmotech" class="h-10 w-auto mr-2">
							<span class="text-xl font-bold text-white">Filmotech</span>
						</a>
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

					<!-- Main navigation -->
					<div class="hidden sm:flex sm:space-x-4 sm:ml-6">
						<ul class="flex items-center space-x-4">
							<li><a href="#" class="text-gray-300 hover:text-white"><?php echo ($navbar_active_title); ?></a></li>
						</ul>
						<!-- Search bar -->
						<form class="flex items-center space-x-2" role="search" method="post">
							<input type="hidden" name="search_field" value="<?php echo ($search_field); ?>">
							<input type="text" class="bg-gray-700 text-gray-300 placeholder-gray-400 border border-gray-600 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
								placeholder="<?php echo ($navbar_search); ?>" name="search_query">
							<select name="search_field" class="bg-gray-700 text-gray-300 border border-gray-600 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
								<option value="TitreVF"><?php echo ($field_labels['TitreVF']); ?></option>
								<option value="TitreVO"><?php echo ($field_labels['TitreVO']); ?></option>
								<option value="Genre"><?php echo ($field_labels['Genre']); ?></option>
								<option value="Acteurs"><?php echo ($field_labels['Acteurs']); ?></option>
								<option value="Realisateurs"><?php echo ($field_labels['Realisateurs']); ?></option>
								<option value="Annee"><?php echo ($field_labels['Annee']); ?></option>
								<option value="Support"><?php echo ($field_labels['Support']); ?></option>
							</select>
							<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2">
								<?php echo ($navbar_go); ?>
							</button>
						</form>
					</div>
				</div>
			</div>
		</nav>

		<!-- Mobile Menu -->
		<div class="sm:hidden" id="mobile-menu">
			<div class="px-2 pt-2 pb-3 space-y-1">
				<ul class="flex flex-col items-center space-y-1">
					<li><a href="#" class="text-gray-300 hover:text-white"><?php echo ($navbar_active_title); ?></a></li>
				</ul>
				<!-- Search bar -->
				<form class="flex items-center space-x-2" role="search" method="post">
					<input type="hidden" name="search_field" value="<?php echo ($search_field); ?>">
					<input type="text" class="bg-gray-700 text-gray-300 placeholder-gray-400 border border-gray-600 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
						placeholder="<?php echo ($navbar_search); ?>" name="search_query">
					<select name="search_field" class="bg-gray-700 text-gray-300 border border-gray-600 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<option value="TitreVF"><?php echo ($field_labels['TitreVF']); ?></option>
						<option value="TitreVO"><?php echo ($field_labels['TitreVO']); ?></option>
						<option value="Genre"><?php echo ($field_labels['Genre']); ?></option>
						<option value="Acteurs"><?php echo ($field_labels['Acteurs']); ?></option>
						<option value="Realisateurs"><?php echo ($field_labels['Realisateurs']); ?></option>
						<option value="Annee"><?php echo ($field_labels['Annee']); ?></option>
						<option value="Support"><?php echo ($field_labels['Support']); ?></option>
					</select>
					<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2">
						<?php echo ($navbar_go); ?>
					</button>
				</form>
			</div>
		</div>

		<!-- Main block -->
		<div class="bg-gray-200 w-full px-4 mt-4">
			<div class="grid grid-cols-12 gap-4">
				<!-- Main Content -->
				<div class="col-span-8 bg-gray-200 p-6">
					<!-- Films au hasard -->
					<p class="font-bold text-lg text-gray-800 mb-4">Films au hasard:</p>
					<?php
					$data = $db->query('SELECT * FROM fmt_films ORDER BY RAND() LIMIT 1');
					$film = $data->fetch();
					if ($film):
					?>
						<a href="filmotech_detail.php?id=<?php echo $film["ID"]; ?>" class="text-blue-500 hover:underline" target="_blank" title="avec <?php echo $film["Acteurs"]; ?>">
							<?php echo $film["TitreVF"]; ?>; <?php echo $film["Genre"]; ?>; <?php echo $film["Annee"]; ?>
						</a>
					<?php else: ?>
						<p class="text-gray-600">Aucun film disponible.</p>
					<?php endif; ?>
					<div class="mt-6">
						<!-- Pagination (top) -->
						<?php if ($pagination): ?>
							<div class="flex justify-between items-center my-4">
								<!-- Bouton de page précédente -->
								<button
									onclick="location.href='?Page=<?php echo max(1, $page - 1); ?>'"
									class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 
        							<?php echo $page == 1 ? 'opacity-50 pointer-events-none cursor-not-allowed' : ''; ?>">
									← <?php echo $previous_page; ?>
								</button>

								<!-- Indicateur de page -->
								<span class="text-gray-700 font-medium">Page <?php echo $page; ?> / <?php echo ceil($total_record / $nb_record_per_page); ?></span>

								<!-- Bouton de page suivante -->
								<button
									onclick="location.href='?Page=<?php echo $page + 1; ?>'"
									class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 
        							<?php echo $page >= ceil($total_record / $nb_record_per_page) ? 'opacity-50 pointer-events-none cursor-not-allowed' : ''; ?>">
									<?php echo $next_page; ?> →
								</button>
							</div>
						<?php endif; ?>

					</div>
					<?php if ($count > 0): ?>
						<!-- Table des films -->
						<table class="w-full bg-gray-50 rounded-lg overflow-hidden">
							<thead>
								<tr class="border-b border-gray-300">
									<th class="px-4 py-2 text-left text-sm font-medium text-gray-600 uppercase"></th>
									<th class="px-4 py-2 text-left text-sm font-medium text-gray-600 uppercase">Titre VF</th>
									<th class="px-4 py-2 text-left text-sm font-medium text-gray-600 uppercase">Genre</th>
									<th class="px-4 py-2 text-left text-sm font-medium text-gray-600 uppercase">Support</th>
									<th class="px-4 py-2 text-left text-sm font-medium text-gray-600 uppercase">Année</th>
									<th class="px-4 py-2 text-left text-sm font-medium text-gray-600 uppercase">Durée</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($response as $index => $data): ?>
									<tr class="<?php echo $index % 2 == 0 ? 'bg-white' : 'bg-gray-100'; ?> hover:bg-gray-200">
										<td class="px-4 py-2">
											<?php
											if ($show_lent || $show_not_seen) {
												if ($show_lent && $show_not_seen) {
													if ($data['FilmVu'] == 'NON' && $data['PretEnCours'] == 'OUI') {
														echo '<img src="img/dot_green_orange.png" alt="Non vu et prêté">';
													} elseif ($data['FilmVu'] == 'NON') {
														echo '<img src="img/dot_green.png" alt="Non vu">';
													} elseif ($data['PretEnCours'] == 'OUI') {
														echo '<img src="img/dot_orange.png" alt="Prêté">';
													}
												}
											}
											?>
										</td>
										<td class="px-4 py-2"><a href="filmotech_detail.php?id=<?php echo $data['ID']; ?>" target="_blank" title="<?php echo $data['Synopsis']; ?>"><?php echo $data['TitreVF']; ?></a></td>
										<td class="px-4 py-2"><?php echo $data['Genre']; ?></td>
										<td class="px-4 py-2"><?php echo $data['Support']; ?></td>
										<td class="px-4 py-2"><?php echo $data['Annee']; ?></td>
										<td class="px-4 py-2"><?php echo $data['Duree']; ?> m</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else: ?>
						<table class="w-full bg-gray-50 rounded-lg overflow-hidden">
							<thead>
								<tr class="border-b border-gray-300">
									<th class="px-4 py-2 text-left text-sm font-medium text-gray-600 uppercase"></th>
									<th class="px-4 py-2 text-left text-sm font-medium text-gray-600 uppercase">Titre VF</th>
									<th class="px-4 py-2 text-left text-sm font-medium text-gray-600 uppercase">Genre</th>
									<th class="px-4 py-2 text-left text-sm font-medium text-gray-600 uppercase">Support</th>
									<th class="px-4 py-2 text-left text-sm font-medium text-gray-600 uppercase">Année</th>
									<th class="px-4 py-2 text-left text-sm font-medium text-gray-600 uppercase">duree</th>
								</tr>
							</thead>
							<tbody>
								<p class="text-gray-600 mt-4 bg-red-400 text-white p-2 rounded">Aucun film ne correspond à votre recherche.</p>
							</tbody>
						</table>

					<?php endif; ?>
					<!-- Pagination (bottom) -->
					<?php if ($pagination): ?>
						<div class="flex justify-between items-center my-4">
							<!-- Bouton de page précédente -->
							<button
								onclick="location.href='?Page=<?php echo max(1, $page - 1); ?>'"
								class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 
        <?php echo $page == 1 ? 'opacity-50 pointer-events-none cursor-not-allowed' : ''; ?>">
								← <?php echo $previous_page; ?>
							</button>

							<!-- Indicateur de page -->
							<span class="text-gray-700 font-medium">Page <?php echo $page; ?> / <?php echo ceil($total_record / $nb_record_per_page); ?></span>

							<!-- Bouton de page suivante -->
							<button
								onclick="location.href='?Page=<?php echo $page + 1; ?>'"
								class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 
        <?php echo $page >= ceil($total_record / $nb_record_per_page) ? 'opacity-50 pointer-events-none cursor-not-allowed' : ''; ?>">
								<?php echo $next_page; ?> →
							</button>
						</div>
					<?php endif; ?>
					<?php echo ('<p class="text-center"><small>' . $label_movie_count . '</small></p>'); ?>
					<?php if ($show_update_date && $count != 0) echo ('<p class="text-center"><small>' . $last_update_label . '</small></p>'); ?>
				</div>


				<!-- Right side -->
				<div class="col-span-4 space-y-4">
					<!-- Les 25 derniers films -->
					<div class="bg-yellow-100 p-4 border border-gray-300 rounded">
						<h4 class="font-bold text-lg">Les 25 derniers films</h4>
						<ul class="list-disc pl-5 space-y-1">
							<?php
							$latest = $db->query('SELECT * FROM fmt_films ORDER BY EntreeDate DESC LIMIT 25');
							while ($movie = $latest->fetch()): ?>
								<li><a href="filmotech_detail.php?id=<?php echo $movie['ID']; ?>" class="text-blue-500 hover:underline">
										<?php echo htmlspecialchars($movie['TitreVF']); ?>
									</a></li>
							<?php endwhile; ?>
						</ul>
					</div>

					<!-- Mes liens -->
					<div class="bg-green-100 p-4 border border-gray-300 rounded">
						<h4 class="font-bold text-lg">Mes liens</h4>
						<ul class="list-disc pl-5 space-y-1">
							<?php foreach ($favorites as $name => $link): ?>
								<li><a href="<?php echo $link; ?>" class="text-blue-500 hover:underline"><?php echo htmlspecialchars($name); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<!-- Footer -->
		<div class="bg-gray-100 p-4">
			<div class="grid grid-cols-3">
				<div class="text-gray-600"><?php echo ($copyright); ?></div>
				<div class="text-center text-gray-600"><a href="mailto:<?php echo ($mail_address); ?>">
						<?php echo ($mail_label); ?></a></div>
				<div class="text-right text-gray-600"><?php echo ($powered_by); ?>
					<a href="http://www.filmotech.fr" target="_blank" class="text-blue-600 hover:text-blue-900">Filmotech</a>
				</div>
			</div>
		</div>
</body>

</html>