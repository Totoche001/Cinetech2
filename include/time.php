<div class="container d-flex justify-content-center">
	<div class="card shadow-sm">
		<div class="card-body text-center">
			<h6>
				<?php
				$jour = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
				$mois = ["", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
				$datefr = sprintf("%s %d %s %d", $jour[date("w")], date("d"), $mois[date("n")], date("Y"));
				echo 'Nous sommes le ' . $datefr;
				?>
				<div id="horloge" class="card shadow-sm">
					<div class="card-body">
						<a href="https://time.is/Belgium" id="time_is_link" rel="nofollow" class="text-danger text-decoration-none small">Il est exactement :</a>
						<span id="Belgium_z706" class="display-10 text-danger"></span>
						<script src="//widget.time.is/t.js"></script>
						<script>
							time_is_widget.init({
								Belgium_z706: {}
							});
						</script>
					</div>
				</div>
			</h6>
		</div>
	</div>
</div>