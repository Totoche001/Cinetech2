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
                            <a class="nav-link active" href="#"><?php echo ($navbar_active_title); ?></a>
                        </li>
                    </ul>
                    <!-- Search bar -->
                    <form class="d-flex ms-auto" role="search" method="post">
                        <input type="hidden" name="search_field" value="<?php echo ($search_field); ?>">
                        <input class="form-control me-2" type="text" placeholder="<?php echo ($navbar_search); ?>" name="search_query" aria-label="Search">
                        <select class="form-select me-2" name="search_field">
                            <option value="TitreVF"><?php echo ($field_labels['TitreVF']); ?></option>
                            <option value="TitreVO"><?php echo ($field_labels['TitreVO']); ?></option>
                            <option value="Genre"><?php echo ($field_labels['Genre']); ?></option>
                            <option value="Acteurs"><?php echo ($field_labels['Acteurs']); ?></option>
                            <option value="Realisateurs"><?php echo ($field_labels['Realisateurs']); ?></option>
                            <option value="Annee"><?php echo ($field_labels['Annee']); ?></option>
                            <option value="Support"><?php echo ($field_labels['Support']); ?></option>
                        </select>
                        <button class="btn btn-outline-success" type="submit" style="min-width: 120px;">
                            <?php echo ($navbar_go); ?>
                        </button>
                    </form>
                </div>
            </div>
        </nav>