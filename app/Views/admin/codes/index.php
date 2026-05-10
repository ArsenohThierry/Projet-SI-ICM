<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Codes</title>
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
    role_user: "<?php echo esc($sessionUser['role_user'] ?? ''); ?>",
    gold: <?php echo session()->get('user_option') === 'gold' ? 'true' : 'false'; ?>
  };
</script>

<div id="pageContent">
    <div class="page-header">
        <div class="header-title">
            <div class="logo-icon" style="background: var(--primary);">
                <i class="fa-solid fa-ticket" style="color:white;"></i>
            </div>
            <div>
                <h2>Codes de credit</h2>
                <p class="text-muted">Creation et suivi des codes.</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="/admin/codes/new" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nouveau code
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
                        <th>Code</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($codes)): ?>
                    <?php foreach ($codes as $c): ?>
                    <tr>
                        <td><?php echo esc($c['valeur']); ?></td>
                        <td><?php echo esc($c['montant']); ?></td>
                        <td>
                            <?php echo $c['status'] === 'used' ? 'Utilise' : 'Disponible'; ?>
                        </td>
                        <td class="table-actions">
                            <form method="post" action="/admin/codes/<?php echo $c['id']; ?>/delete" onsubmit="return confirm('Supprimer ce code ?');">
                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding:1rem;">Aucun code.</td>
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
  NF_LAYOUT.inject('admin-codes', 'Administration', 'Codes');
  const mc = document.getElementById('mainContent');
  const content = document.getElementById('pageContent');
  if (mc && content) {
    mc.appendChild(content);
  }
</script>

</body>
</html>
