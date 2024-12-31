<div class="container mx-auto text-center">
			<?php
			$jour = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
			$mois = ["", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
			$datefr = sprintf("%s %d %s %d", $jour[date("w")], date("d"), $mois[date("n")], date("Y"));
			echo "Nous sommes le " . $datefr;
			?>
			<div id="horloge">
				<a href="https://time.is/Belgium" id="time_is_link" rel="nofollow" class="text-red-600">Il est exactement :</a>
				<span id="Belgium_z706" class="text-red-300"></span>
				<script src="//widget.time.is/t.js"></script>
				<script>
					time_is_widget.init({
						Belgium_z706: {}
					});
				</script>
			</div>
		</div>