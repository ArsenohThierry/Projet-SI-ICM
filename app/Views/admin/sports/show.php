<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail activite</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
</head>
<body>

<?php $sessionUser = $user ?? []; ?>
<?php $sportData = isset($sport) && is_array($sport) ? $sport : []; ?>
<script>
  window.NF_USER = {
    prenom: "<?php echo esc($sessionUser['prenom'] ?? ''); ?>",
    nom: "<?php echo esc($sessionUser['nom'] ?? ''); ?>",
    email: "<?php echo esc($sessionUser['email'] ?? ''); ?>",
    role_user: "<?php echo esc($sessionUser['role_user'] ?? ''); ?>",
    gold: <?php echo session()->get('user_option') === 'gold' ? 'true' : 'false'; ?>
  };
</script>

<div id="pageContent">
    <div class="page-header">
        <div class="header-title">
            <div class="logo-icon" style="background: var(--primary);">
                <i class="fa-solid fa-dumbbell" style="color:white;"></i>
            </div>
            <div>
                <h2><?php echo esc($sportData['nom'] ?? 'Activite'); ?></h2>
                <p class="text-muted">Details de l'activite.</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="/admin/sports" class="btn btn-outline">Retour</a>
            <a href="/admin/sports/<?php echo (int) ($sportData['id'] ?? 0); ?>/edit" class="btn btn-primary">Modifier</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="info-row">
                <div class="info-label">Nom</div>
                <div class="info-value"><?php echo esc($sportData['nom'] ?? ''); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Objectif</div>
                <div class="info-value"><?php echo esc($sportData['libelle'] ?? ''); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Âge minimum</div>
                <div class="info-value"><?php echo esc($sportData['age_min'] ?? ''); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Âge maximum</div>
                <div class="info-value"><?php echo esc($sportData['age_max'] ?? ''); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Calories / minute</div>
                <div class="info-value"><?php echo esc($sportData['calories_brulees'] ?? ''); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Durée recommandée</div>
                <div class="info-value"><?php echo esc($sportData['duree_recommandee'] ?? ''); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Genre</div>
                <div class="info-value"><?php echo esc($sportData['genre'] ?? 'Tous'); ?></div>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/app.js"></script>
<script src="/assets/js/layout.js"></script>
<script>
  NF_LAYOUT.inject('admin-sports', 'Administration', 'Activites');
  const adminSportsMainContent = document.getElementById('mainContent');
  const adminSportsPageContent = document.getElementById('pageContent');
  if (adminSportsMainContent && adminSportsPageContent) {
    adminSportsMainContent.appendChild(adminSportsPageContent);
  }
</script>

</body>
</html>
