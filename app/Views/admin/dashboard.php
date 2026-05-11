<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
    <link rel="icon" type="image/png" href="/assets/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
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

<?php
$totalUsers = $totalUsers ?? 0;
$totalGold = $totalGold ?? 0;
$totalRevenu = $totalRevenu ?? 0;
$regimesActifs = $regimesActifs ?? 0;

$dataObjectifs = $dataObjectifs ?? [];
$dataRegimes = $dataRegimes ?? [];
$dataInscriptions = $dataInscriptions ?? [];

$objectifs = $objectifs ?? [];
$regimes = $regimes ?? [];
$tableauCroise = $tableauCroise ?? [];
$listeUtilisateurs = $listeUtilisateurs ?? [];

$matrix = [];
foreach ($tableauCroise as $row) {
    $regimeId = (int) ($row['regime_id'] ?? 0);
    $objectifId = (int) ($row['objectif_id'] ?? 0);
    $total = (int) ($row['total'] ?? 0);
    if (!isset($matrix[$regimeId])) {
        $matrix[$regimeId] = [];
    }
    $matrix[$regimeId][$objectifId] = $total;
}
?>

<div id="pageContent" class="page-content">
    <div class="page-header">
        <div class="header-title">
            <div class="logo-icon" style="background: var(--primary);">
                <i class="fa-solid fa-chart-line" style="color:white;"></i>
            </div>
            <div>
                <h2>Dashboard admin</h2>
                <p class="text-muted">Vue d'ensemble des statistiques.</p>
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card green">
            <div class="stat-icon green"><i class="fa-solid fa-users"></i></div>
            <div class="stat-value"><?php echo esc((string) $totalUsers); ?></div>
            <div class="stat-label">Utilisateurs</div>
        </div>
        <div class="stat-card gold">
            <div class="stat-icon gold"><i class="fa-solid fa-crown"></i></div>
            <div class="stat-value"><?php echo esc((string) $totalGold); ?></div>
            <div class="stat-label">Utilisateurs Gold</div>
        </div>
        <div class="stat-card info">
            <div class="stat-icon info"><i class="fa-solid fa-coins"></i></div>
            <div class="stat-value"><?php echo esc(number_format((float) $totalRevenu, 2, '.', ' ')); ?> Ar</div>
            <div class="stat-label">Revenus totals</div>
        </div>
        <div class="stat-card danger">
            <div class="stat-icon danger"><i class="fa-solid fa-bowl-food"></i></div>
            <div class="stat-value"><?php echo esc((string) $regimesActifs); ?></div>
            <div class="stat-label">Regimes actifs</div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="card">
            <div class="card-header">
                <h3>Repartition des objectifs</h3>
            </div>
            <div class="card-body" style="display:flex;justify-content:center;">
                <div style="width:400px;height:400px;">
                    <canvas id="chartObjectifs"></canvas>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3>Regimes les plus populaires</h3>
            </div>
            <div class="card-body">
                <canvas id="chartRegimes" height="180"></canvas>
            </div>
        </div>
    </div>

    <div class="card" style="margin-top:1.5rem;">
        <div class="card-header">
            <h3>Tableau croise — Regime x Objectif</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Regime</th>
                            <?php foreach ($objectifs as $obj): ?>
                                <th><?php echo esc((string) ($obj['libelle'] ?? '')); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($regimes)): ?>
                            <?php foreach ($regimes as $regime): ?>
                                <?php $regimeId = (int) ($regime['id'] ?? 0); ?>
                                <tr>
                                    <td><?php echo esc((string) ($regime['nom'] ?? '')); ?></td>
                                    <?php foreach ($objectifs as $obj): ?>
                                        <?php $objId = (int) ($obj['id'] ?? 0); ?>
                                        <td><?php echo esc((string) ($matrix[$regimeId][$objId] ?? 0)); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?php echo 1 + count($objectifs); ?>" style="text-align:center; padding:1rem;">Aucune donnee.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card" style="margin-top:1.5rem;">
        <div class="card-header">
            <h3>Liste des utilisateurs</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>IMC</th>
                            <th>Objectif actuel</th>
                            <th>Regime actuel</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($listeUtilisateurs)): ?>
                            <?php foreach ($listeUtilisateurs as $row): ?>
                                <?php
                                    $nom = trim((string) (($row['prenom'] ?? '') . ' ' . ($row['nom'] ?? '')));
                                    $nom = $nom !== '' ? $nom : ($row['username'] ?? '');
                                ?>
                                <tr>
                                    <td><?php echo esc($nom); ?></td>
                                    <td><?php echo esc((string) ($row['imc'] ?? '')); ?></td>
                                    <td><?php echo esc((string) ($row['objectif_libelle'] ?? '')); ?></td>
                                    <td><?php echo esc((string) ($row['regime_nom'] ?? '')); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding:1rem;">Aucun utilisateur.</td>
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
  NF_LAYOUT.inject('admin-dashboard', 'Administration', 'Dashboard');
  const mc = document.getElementById('mainContent');
  const content = document.getElementById('pageContent');
  if (mc && content) {
    mc.appendChild(content);
  }

  const labelsObjectifs = <?php echo json_encode(array_keys($dataObjectifs)); ?>;
  const valuesObjectifs = <?php echo json_encode(array_values($dataObjectifs)); ?>;

  const labelsRegimes = <?php echo json_encode(array_keys($dataRegimes)); ?>;
  const valuesRegimes = <?php echo json_encode(array_values($dataRegimes)); ?>;

  const chartObjectifs = document.getElementById('chartObjectifs');
  if (chartObjectifs) {
        new Chart(chartObjectifs.getContext('2d'), {
      type: 'pie',
      data: {
        labels: labelsObjectifs,
        datasets: [{
          data: valuesObjectifs,
          backgroundColor: ['#10B981', '#F59E0B', '#3B82F6']
        }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
    });
  }

  const chartRegimes = document.getElementById('chartRegimes');
  if (chartRegimes) {
    new Chart(chartRegimes.getContext('2d'), {
      type: 'bar',
      data: {
        labels: labelsRegimes,
        datasets: [{
          data: valuesRegimes,
          backgroundColor: '#10B981'
        }]
      },
      options: {
        plugins: {
          legend: { display: false }
        }
      }
    });
  }

  
</script>

</body>
</html>
