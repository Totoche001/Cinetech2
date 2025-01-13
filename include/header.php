 <!-- Header -->
 <header class="text-center py-4">
        <?php if ($show_title): ?>
            <h1>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <img src="img/logo.png" alt="Filmotech" class="img-fluid" style="height: 7rem;" />
                </a>
            </h1>
        <?php else: ?>
            <div>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <img src="img/top.png" alt="Top Filmotech" class="img-fluid" style="height: 7rem;" />
                </a>
            </div>
        <?php endif; ?>
    </header>