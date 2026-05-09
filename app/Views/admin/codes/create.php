<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau code</title>
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
        <h2>Creer un code</h2>
        <p class="text-muted">Definir le code et le montant.</p>
      </div>
    </div>
    <div class="header-actions">
      <a href="/admin/codes" class="btn btn-outline">Retour</a>
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
        <form method="post" action="/admin/codes" class="form-stack">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Code</label>
              <input class="form-control" name="valeur" value="<?php echo esc(old('valeur')); ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Montant</label>
              <input type="number" step="0.01" class="form-control" name="montant" value="<?php echo esc(old('montant')); ?>" required>
            </div>
          </div>
          <div class="form-actions">
            <a href="/admin/codes" class="btn btn-outline">Annuler</a>
            <button class="btn btn-primary" type="submit">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
</div>

<script src="/assets/js/app.js"></script>
<script src="/assets/js/layout.js"></script>
<script>
  NF_LAYOUT.inject('admin-codes', 'Administration', 'Codes');
  const mc = document.getElementById('mainContent');
  const content = document.getElementById('pageContent');
  if (mc && content) {
    mc.appendChild(content);
  }
</script>

</body>
</html>
