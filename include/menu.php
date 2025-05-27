<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <!-- Logo et nom de la marque, lien vers la page d'accueil -->
        <a class="navbar-brand" href="index.php">
            <img src="img/favicon1.jpg" alt="Filmotech">
            <span class="ms-2">Filmotech</span>
        </a>
        <!-- Bouton pour afficher ou masquer le menu en mode responsive (petits écrans) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Contenu du menu qui peut être replié en mode responsive -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <!-- Élément actif du menu affichant le titre dynamique -->
                <li class="nav-item">
                    <a class="nav-link active" href="#"><?php echo ($navbar_active_title); ?></a>
                </li>
            </ul>
            <?php if (!str_contains($_SERVER['PHP_SELF'], 'filmotech_detail.php')): ?>
                <!-- Barre de recherche affichée uniquement si on n'est pas sur la page filmotech_detail.php -->
                <form class="d-flex ms-auto" role="search" method="post">
                    <!-- Champ caché pour conserver la valeur du champ de recherche sélectionné -->
                    <input type="hidden" name="search_field" value="<?php echo htmlspecialchars($search_field, ENT_QUOTES, 'UTF-8'); ?>">
                    <!-- Champ texte pour saisir la requête de recherche -->
                    <input class="form-control me-2" type="text" placeholder="<?php echo htmlspecialchars($navbar_search, ENT_QUOTES, 'UTF-8'); ?>" name="search_query" aria-label="Search" maxlength="100">
                    <!-- Liste déroulante pour choisir le champ sur lequel effectuer la recherche -->
                    <select class="form-select me-2" name="search_field">
                        <option value="TitreVF"><?php echo htmlspecialchars($field_labels['TitreVF'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <option value="TitreVO"><?php echo htmlspecialchars($field_labels['TitreVO'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <option value="Genre"><?php echo htmlspecialchars($field_labels['Genre'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <option value="Acteurs"><?php echo htmlspecialchars($field_labels['Acteurs'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <option value="Realisateurs"><?php echo htmlspecialchars($field_labels['Realisateurs'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <option value="Annee"><?php echo htmlspecialchars($field_labels['Annee'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <option value="Support"><?php echo htmlspecialchars($field_labels['Support'], ENT_QUOTES, 'UTF-8'); ?></option>
                    </select>
                    <!-- Bouton pour lancer la recherche -->
                    <button class="btn btn-outline-success" type="submit" style="min-width: 120px;">
                        <?php echo htmlspecialchars($navbar_go, ENT_QUOTES, 'UTF-8'); ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</nav>