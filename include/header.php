 <!-- En-tête de la page -->
 <header class="text-center py-4">
     <?php if ($show_title): ?>
         <!-- Si la variable $show_title est vraie, afficher le titre principal -->
         <h1>
             <a href="<?php echo $_SERVER['PHP_SELF']; ?>">
                 <!-- Logo principal cliquable qui redirige vers la page actuelle -->
                 <img src="img/logo.png" alt="Filmotech" class="img-fluid" style="height: 7rem;" />
             </a>
         </h1>
     <?php else: ?>
         <!-- Sinon, afficher une image alternative sans titre -->
         <div>
             <a href="<?php echo $_SERVER['PHP_SELF']; ?>">
                 <!-- Image alternative cliquable qui redirige vers la page actuelle -->
                 <img src="img/top.png" alt="Top Filmotech" class="img-fluid" style="height: 7rem;" />
             </a>
         </div>
     <?php endif; ?>
 </header>