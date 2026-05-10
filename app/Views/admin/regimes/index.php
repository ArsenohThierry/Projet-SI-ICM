<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Regimes</title>
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
        <h2>Regimes alimentaires</h2>
        <p class="text-muted">Gestion des regimes et de leurs proportions.</p>
      </div>
    </div>
    <div class="header-actions">
      <a href="/admin/regimes/new" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Nouveau regime
      </a>
    </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="toast error" style="position:relative; margin-bottom:1rem;">
        <i class="fa-solid fa-times-circle toast-icon"></i>
        <span><?php echo esc(session()->getFlashdata('error')); ?></span>
      </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="toast success" style="position:relative; margin-bottom:1rem;">
        <i class="fa-solid fa-check-circle toast-icon"></i>
        <span><?php echo esc(session()->getFlashdata('success')); ?></span>
      </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
          <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Viande %</th>
                        <th>Volaille %</th>
                        <th>Poisson %</th>
                        <th>Prix / jour</th>
                        <th>Variation / jour</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($regimes)): ?>
                    <?php foreach ($regimes as $r): ?>
                    <tr>
                        <td><?php echo esc($r['nom']); ?></td>
                        <td><?php echo esc($r['pourcentage_viande']); ?></td>
                        <td><?php echo esc($r['pourcentage_volaille']); ?></td>
                        <td><?php echo esc($r['pourcentage_poisson']); ?></td>
                        <td><?php echo esc($r['montant']); ?></td>
                        <td><?php echo esc($r['variation_poids']); ?></td>
                        <td class="table-actions">
                            <a href="/admin/regimes/<?php echo $r['id']; ?>" class="btn btn-outline btn-sm">Voir</a>
                            <a href="/admin/regimes/<?php echo $r['id']; ?>/edit" class="btn btn-outline btn-sm">Modifier</a>
                            <form method="post" action="/admin/regimes/<?php echo $r['id']; ?>/delete" onsubmit="return confirm('Supprimer ce regime ?');">
                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center; padding:1rem;">Aucun regime.</td>
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
  NF_LAYOUT.inject('admin-regimes', 'Administration', 'Regimes');
  const mc = document.getElementById('mainContent');
  const content = document.getElementById('pageContent');
  if (mc && content) {
    mc.appendChild(content);
  }
</script>

</body>
</html>
