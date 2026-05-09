<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Activites</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
</head>
<body>

<?php $sessionUser = $user ?? []; ?>
<?php $sportsList = isset($sports) && is_array($sports) ? $sports : []; ?>
<?php $errorMessage = session()->getFlashdata('error'); ?>
<?php $successMessage = session()->getFlashdata('success'); ?>
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
                <h2>Activites sportives</h2>
                <p class="text-muted">Gestion des activites sportives disponibles.</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="/admin/sports/new" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nouvelle activite
            </a>
        </div>
    </div>

    <?php if (!empty($errorMessage)): ?>
      <div class="toast error" style="position:relative; margin-bottom:1rem;">
        <i class="fa-solid fa-times-circle toast-icon"></i>
        <span><?php echo esc((string) $errorMessage); ?></span>
      </div>
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
      <div class="toast success" style="position:relative; margin-bottom:1rem;">
        <i class="fa-solid fa-check-circle toast-icon"></i>
        <span><?php echo esc((string) $successMessage); ?></span>
      </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body" style="padding:0;">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Objectif</th>
                            <th>Âge min</th>
                            <th>Calories / min</th>
                            <th>Durée</th>
                            <th>Genre</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($sportsList)): ?>
                        <?php foreach ($sportsList as $s): ?>
                        <?php
                            $sportId = (int) ($s['sport_id'] ?? 0);
                            $sportNom = (string) ($s['nom'] ?? '');
                            $sportObjectif = (string) ($s['libelle'] ?? '');
                            $sportAgeMin = (string) ($s['age_min'] ?? '');
                            $sportCalories = (string) ($s['calories_brulees'] ?? '');
                            $sportDuree = (string) ($s['duree_recommandee'] ?? '');
                            $sportGenre = (string) (($s['genre'] ?? 'Tous') ?: 'Tous');
                        ?>
                        <tr>
                            <td><?php echo esc($sportNom); ?></td>
                            <td><?php echo esc($sportObjectif); ?></td>
                            <td><?php echo esc($sportAgeMin); ?></td>
                            <td><?php echo esc($sportCalories); ?></td>
                            <td><?php echo esc($sportDuree); ?></td>
                            <td><?php echo esc($sportGenre); ?></td>
                            <td class="table-actions">
                                <a href="/admin/sports/<?php echo $sportId; ?>" class="btn btn-outline btn-sm">Voir</a>
                                <a href="/admin/sports/<?php echo $sportId; ?>/edit" class="btn btn-outline btn-sm">Modifier</a>
                                <form method="post" action="/admin/sports/<?php echo $sportId; ?>/delete" onsubmit="return confirm('Supprimer cette activite ?');">
                                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:1rem;">Aucune activite.</td>
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
  const adminSportsMainContent = document.getElementById('mainContent');
  const adminSportsPageContent = document.getElementById('pageContent');
  if (adminSportsMainContent && adminSportsPageContent) {
    adminSportsMainContent.appendChild(adminSportsPageContent);
  }
</script>

</body>
</html>
