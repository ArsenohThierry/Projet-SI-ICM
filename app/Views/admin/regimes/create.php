<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau regime</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
</head>
<body>

<?php $sessionUser = $user ?? []; ?>
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
                <i class="fa-solid fa-plus" style="color:white;"></i>
            </div>
            <div>
                <h2>Ajouter un regime</h2>
                <p class="text-muted">Definir les proportions et le prix.</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="/admin/regimes" class="btn btn-outline">Retour</a>
        </div>
    </div>

    <?php $errors = session()->getFlashdata('errors') ?? []; ?>
    <?php if (!empty($errors)): ?>
      <div class="toast warning" style="position:relative; margin-bottom:1rem;">
        <i class="fa-solid fa-exclamation-triangle toast-icon"></i>
        <span><?php echo esc(implode(' | ', $errors)); ?></span>
      </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3>Informations principales</h3>
        </div>
        <div class="card-body">
            <form method="post" action="/admin/regimes" class="form-stack">
                <div class="form-group">
                    <label class="form-label">Nom</label>
                    <input class="form-control" name="nom" value="<?php echo esc(old('nom')); ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">% Viande</label>
                        <input type="number" class="form-control" name="pourcentage_viande" value="<?php echo esc(old('pourcentage_viande')); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">% Volaille</label>
                        <input type="number" class="form-control" name="pourcentage_volaille" value="<?php echo esc(old('pourcentage_volaille')); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">% Poisson</label>
                        <input type="number" class="form-control" name="pourcentage_poisson" value="<?php echo esc(old('pourcentage_poisson')); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Prix / jour</label>
                        <input type="number" class="form-control" name="montant" value="<?php echo esc(old('montant')); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Variation poids / jour</label>
                        <input type="number" class="form-control" name="variation_poids" value="<?php echo esc(old('variation_poids')); ?>" required>
                    </div>
                </div>
                <div class="form-actions">
                    <a href="/admin/regimes" class="btn btn-outline">Annuler</a>
                    <button class="btn btn-primary" type="submit">Enregistrer</button>
                </div>
            </form>
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
