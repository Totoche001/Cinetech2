<!-- Footer -->
<div class="container">
	<footer class="bg-light text-center py-3" style="width: 100%;">
		<!-- Affichage du copyright à gauche avec une marge -->
		<div style="float: left; margin: 10px;"><?php echo ($copyright); ?></div>
		<?php
		// Vérifie si le nombre d'années depuis le début est un multiple de 5 et supérieur à 0
		if ($yearsSinceStart > 0 && $yearsSinceStart % 5 == 0) {
			// Conteneur en ligne pour l'icône et le texte d'anniversaire
			echo '<div class="d-inline-block">';
			// Lien vers la page anniversaire, ouverture dans un nouvel onglet
			echo '<a href="anniversary.php" target="_blank" style="text-decoration: none; color: inherit;">';
			// Icône SVG d'un cadeau avec animation de rebond
			echo '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-gift animate__animated animate__bounce" viewBox="0 0 16 16">
				<path d="M3 2.5a2.5 2.5 0 0 1 5 0 2.5 2.5 0 0 1 5 0v.006c0 .07 0 .27-.038.494H15a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 14.5V7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h2.038A2.968 2.968 0 0 1 3 2.506V2.5zm1.068.5H7v-.5a1.5 1.5 0 1 0-3 0c0 .085.002.274.045.43a.522.522 0 0 0 .023.07zM9 3h2.932a.56.56 0 0 0 .023-.07c.043-.156.045-.345.045-.43a1.5 1.5 0 0 0-3 0V3zM1 4v2h6V4H1zm8 0v2h6V4H9zm5 3H9v8h4.5a.5.5 0 0 0 .5-.5V7zm-7 8V7H2v7.5a.5.5 0 0 0 .5.5H7z"/>
			</svg>';

			// Texte d'anniversaire centré sous l'icône
			echo '<div class="mt-2 text-center">';
			echo '<small class="text-muted">' . $yearsSinceStart . ' ans !,<br> Joyeux anniversaire</small>';
			echo '</div>';
			echo '</a>';
			echo '</div>';

			// Inclusion de la feuille de style animate.css pour l'animation
			echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">';
			// Style personnalisé pour définir la durée et la répétition de l'animation
			echo '<style>
				.animate__animated.animate__bounce {
					--animate-duration: 2s;
					animation-iteration-count: infinite;
				}
			</style>';
		}
		?>
		<!-- Conteneur central pour un futur lien mail (commenté) -->
		<div style="text-align: center; display: inline-block; margin: 0 10px;">
			<!--
			<a href="mailto:<?php // echo ($mail_address); 
							?>">
				<?php // echo ($mail_label); 
				?>
			</a>
			-->
		</div>
		<!-- Groupe de boutons pour les liens vers les réseaux sociaux et email -->
		<div class="btn-group">
			<!-- Lien LinkedIn avec icône SVG -->
			<a href="https://www.linkedin.com/in/anthony-semal/" target="_blank" class="btn btn-outline-secondary">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-linkedin" viewBox="0 0 16 16">
					<path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-2.4-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z" />
				</svg>
				<span class="visually-hidden">LinkedIn</span>
			</a>
			<!-- Lien GitHub avec icône SVG -->
			<a href="https://github.com/Totoche001/Cinetech2" target="_blank" class="btn btn-outline-secondary">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-github" viewBox="0 0 16 16">
					<path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.012 8.012 0 0 0 16 8c0-4.42-3.58-8-8-8z" />
				</svg>
				<span class="visually-hidden">GitHub</span>
			</a>
			<!-- Lien Discord avec icône SVG -->
			<a href="https://discord.com/" target="_blank" class="btn btn-outline-secondary">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-discord" viewBox="0 0 16 16">
					<path d="M13.545 2.907a13.227 13.227 0 0 0-3.257-1.011.05.05 0 0 0-.052.025c-.141.25-.297.577-.406.833a12.19 12.19 0 0 0-3.658 0 8.258 8.258 0 0 0-.412-.833.051.051 0 0 0-.052-.025c-1.125.194-2.22.534-3.257 1.011a.041.041 0 0 0-.021.018C.356 6.024-.213 9.047.066 12.032c.001.014.01.028.021.037a13.276 13.276 0 0 0 3.995 2.02.05.05 0 0 0 .056-.019c.308-.42.582-.863.818-1.329a.05.05 0 0 0-.01-.059.051.051 0 0 0-.018-.011 8.875 8.875 0 0 1-1.248-.595.05.05 0 0 1-.02-.066.051.051 0 0 1 .015-.019c.084-.063.168-.129.248-.195a.05.05 0 0 1 .051-.007c2.619 1.196 5.454 1.196 8.041 0a.052.052 0 0 1 .053.007c.08.066.164.132.248.195a.051.051 0 0 1-.004.085 8.254 8.254 0 0 1-1.249.594.05.05 0 0 0-.03.03.052.052 0 0 0 .003.041c.24.465.515.909.817 1.329a.05.05 0 0 0 .056.019 13.235 13.235 0 0 0 4.001-2.02.049.049 0 0 0 .021-.037c.334-3.451-.559-6.449-2.366-9.106a.034.034 0 0 0-.02-.019Zm-8.198 7.307c-.789 0-1.438-.724-1.438-1.612 0-.889.637-1.613 1.438-1.613.807 0 1.45.73 1.438 1.613 0 .888-.637 1.612-1.438 1.612Zm5.316 0c-.788 0-1.438-.724-1.438-1.612 0-.889.637-1.613 1.438-1.613.807 0 1.451.73 1.438 1.613 0 .888-.631 1.612-1.438 1.612Z" />
				</svg>
				<span class="visually-hidden">Discord</span>
			</a>
			<!-- Lien mail avec icône enveloppe -->
			<a href="mailto:<?php echo ($mail_address); ?>" class="btn btn-outline-secondary">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
					<path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z" />
				</svg>
				<span class="visually-hidden">Email</span>
			</a>
		</div>
		<!-- Affichage à droite du texte "powered by" avec lien vers Filmotech -->
		<div style="float: right; margin: 10px;"><?php echo ($powered_by); ?>
			<a href="http://www.filmotech.fr" target="_blank">Filmotech</a>
		</div>
	</footer>
</div>