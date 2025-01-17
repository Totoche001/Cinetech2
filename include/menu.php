<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="img/favicon1.jpg" alt="Filmotech">
            <span class="ms-2">Filmotech</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" href="#"><?php echo ($navbar_active_title); ?></a>
                </li>
            </ul>
            <!-- Search bar -->
            <form class="d-flex ms-auto" role="search" method="post">
                <input type="hidden" name="search_field" value="<?php echo htmlspecialchars($search_field, ENT_QUOTES, 'UTF-8'); ?>">
                <input class="form-control me-2" type="text" placeholder="<?php echo htmlspecialchars($navbar_search, ENT_QUOTES, 'UTF-8'); ?>" name="search_query" aria-label="Search" maxlength="100">
                <select class="form-select me-2" name="search_field">
                    <option value="TitreVF"><?php echo htmlspecialchars($field_labels['TitreVF'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <option value="TitreVO"><?php echo htmlspecialchars($field_labels['TitreVO'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <option value="Genre"><?php echo htmlspecialchars($field_labels['Genre'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <option value="Acteurs"><?php echo htmlspecialchars($field_labels['Acteurs'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <option value="Realisateurs"><?php echo htmlspecialchars($field_labels['Realisateurs'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <option value="Annee"><?php echo htmlspecialchars($field_labels['Annee'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <option value="Support"><?php echo htmlspecialchars($field_labels['Support'], ENT_QUOTES, 'UTF-8'); ?></option>
                </select>
                <button class="btn btn-outline-success" type="submit" style="min-width: 120px;">
                    <?php echo htmlspecialchars($navbar_go, ENT_QUOTES, 'UTF-8'); ?>
                </button>
            </form>
        </div>
    </div>
</nav>