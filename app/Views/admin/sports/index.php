<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Activites</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
    <link rel="icon" type="image/png" href="/assets/logo.png">
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

<div id="pageContent" class="page-content">
    <div class="page-header">
    <div class="header-title">
      <div class="logo-icon" style="background: var(--primary);">
        <i class="fa-solid fa-dumbbell" style="color:white;"></i>
      </div>
      <div>
        <h2>Activites sportives</h2>
        <p class="text-muted">Gestion des activites disponibles.</p>
      </div>
    </div>
    <div class="header-actions">
      <a href="/admin/sports/new" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Nouvelle activite
      </a>
    </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="toast error" style="position:relative; margin-bottom:1rem;">
        <i class="fa-solid fa-times-circle toast-icon"></i>
        <span><?php echo esc((string) session()->getFlashdata('error')); ?></span>
      </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="toast success" style="position:relative; margin-bottom:1rem;">
        <i class="fa-solid fa-check-circle toast-icon"></i>
        <span><?php echo esc((string) session()->getFlashdata('success')); ?></span>
      </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
          <table class="table">
                <thead>
                    <tr>
                      <th>Nom</th>
                      <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($sports)): ?>
                    <?php foreach ($sports as $s): ?>
                    <tr>
                        <td><?php echo esc((string) ($s['nom'] ?? '')); ?></td>
                        <td class="table-actions">
                            <a href="/admin/sports/<?php echo $s['id']; ?>" class="btn btn-outline btn-sm">Voir</a>
                            <a href="/admin/sports/<?php echo $s['id']; ?>/edit" class="btn btn-outline btn-sm">Modifier</a>
                            <form method="post" action="/admin/sports/<?php echo $s['id']; ?>/delete" onsubmit="return confirm('Supprimer cette activite ?');">
                              <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" style="text-align:center; padding:1rem;">Aucune activite.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
                </table>
              </div>
        </div>
    </div>
</div>

<script src="/assets/js/app.js"></script>
<script src="/assets/js/layout.js"></script>
<script>
  NF_LAYOUT.inject('admin-sports', 'Administration', 'Activites');
  const mc = document.getElementById('mainContent');
  const content = document.getElementById('pageContent');
  if (mc && content) {
    mc.appendChild(content);
  }
</script>

</body>
</html>
