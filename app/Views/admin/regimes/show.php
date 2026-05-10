<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail regime</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
</head>
<body>

<?php $sessionUser = $user ?? []; ?>
<script>
  window.NF_USER = {
    prenom: "<?php echo esc($sessionUser['prenom'] ?? ''); ?>",
    nom: "<?php echo esc($sessionUser['nom'] ?? ''); ?>",
    username: "<?php echo esc($sessionUser['username'] ?? session()->get('username') ?? ''); ?>",
    email: "<?php echo esc($sessionUser['email'] ?? ''); ?>",
    objectif_choisi: "<?php echo esc($sessionUser['objectif_choisi'] ?? ''); ?>",
    regime_choisi: "<?php echo esc($sessionUser['regime_choisi'] ?? ''); ?>",
    sport_choisi: "<?php echo esc($sessionUser['sport_choisi'] ?? ''); ?>",
    role_user: "<?php echo esc($sessionUser['role_user'] ?? ''); ?>",
    gold: <?php echo session()->get('user_option') === 'gold' ? 'true' : 'false'; ?>
  };
</script>

<div id="pageContent">
    <div class="page-header">
        <div class="header-title">
            <div class="logo-icon" style="background: var(--primary);">
                <i class="fa-solid fa-bowl-food" style="color:white;"></i>
            </div>
            <div>
                <h2><?php echo esc($regime['nom']); ?></h2>
                <p class="text-muted">Details du regime.</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="/admin/regimes" class="btn btn-outline">Retour</a>
            <a href="/admin/regimes/<?php echo $regime['id']; ?>/edit" class="btn btn-primary">Modifier</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="info-row">
                <div class="info-label">% Viande</div>
                <div class="info-value"><?php echo esc($regime['pourcentage_viande']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">% Volaille</div>
                <div class="info-value"><?php echo esc($regime['pourcentage_volaille']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">% Poisson</div>
                <div class="info-value"><?php echo esc($regime['pourcentage_poisson']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Prix / jour</div>
                <div class="info-value"><?php echo esc($regime['montant']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Variation poids / jour</div>
                <div class="info-value"><?php echo esc($regime['variation_poids']); ?></div>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/app.js"></script>
<script src="/assets/js/layout.js"></script>
<script>
  NF_LAYOUT.inject('admin-regimes', 'Administration', 'Regimes');
  const mc = document.getElementById('mainContent');
  const content = document.getElementById('pageContent');
  if (mc && content) {
    mc.appendChild(content);
  }
</script>

</body>
</html>
