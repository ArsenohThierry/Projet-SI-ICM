<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NutriFit — Tableau de bord</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/all.min.css">
  <link rel="icon" href="/assets/logo.png">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
  <style>
    .activity-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 0;
      border-bottom: 1px solid var(--border);
    }

    .activity-item:last-child {
      border-bottom: none;
    }

    .activity-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      flex-shrink: 0;
    }

    .activity-time {
      font-size: 0.78rem;
      color: var(--text-muted);
      margin-left: auto;
      white-space: nowrap;
    }

    .activity-text {
      font-size: 0.88rem;
      color: var(--text-secondary);
    }

    .activity-text strong {
      color: var(--text-primary);
      font-weight: 600;
    }

    .weight-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 9px 0;
      border-bottom: 1px solid var(--border);
      font-size: 0.88rem;
    }

    .weight-row:last-child {
      border-bottom: none;
    }

    .weight-date {
      color: var(--text-muted);
    }

    .weight-val {
      font-weight: 700;
      font-family: 'Sora', sans-serif;
    }

    .weight-diff {
      font-size: 0.78rem;
      font-weight: 600;
    }

    .weight-diff.up {
      color: var(--danger);
    }

    .weight-diff.down {
      color: var(--primary-dark);
    }

    .regime-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px;
      border-radius: var(--radius);
      background: var(--bg);
      border: 1px solid var(--border);
      margin-bottom: 0.6rem;
      transition: var(--transition);
    }

    .regime-item:hover {
      border-color: var(--primary);
    }

    .regime-icon {
      width: 38px;
      height: 38px;
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1rem;
      flex-shrink: 0;
    }

    .regime-name {
      font-size: 0.88rem;
      font-weight: 600;
    }

    .regime-cal {
      font-size: 0.78rem;
      color: var(--text-muted);
    }

    .regime-progress {
      margin-top: 4px;
      width: 100%;
    }

    .imc-scale {
      position: relative;
      height: 12px;
      border-radius: 99px;
      background: linear-gradient(90deg, #3B82F6 0%, #18C97A 30%, #F5A623 60%, #E8445A 100%);
      margin: 1rem 0;
      overflow: visible;
    }

    .imc-marker {
      position: absolute;
      top: 50%;
      transform: translate(-50%, -50%);
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background: white;
      border: 3px solid var(--primary-dark);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
      transition: left 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .imc-labels {
      display: flex;
      justify-content: space-between;
      font-size: 0.7rem;
      color: var(--text-muted);
    }

    .goal-item {
      padding: 1rem;
      border-radius: var(--radius);
      background: var(--bg);
      border: 1px solid var(--border);
      margin-bottom: 0.75rem;
    }

    .goal-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 0.6rem;
    }

    .goal-name {
      font-size: 0.88rem;
      font-weight: 600;
    }

    .goal-pct {
      font-size: 0.85rem;
      font-weight: 700;
      color: var(--primary-dark);
    }

    .selected-activity-card {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      padding: 12px 14px;
      border-radius: var(--radius);
      border: 1px solid var(--border);
      background: var(--bg);
      margin-bottom: 0.75rem;
    }

    .selected-activity-accent {
      width: 12px;
      height: 12px;
      border-radius: 999px;
      flex-shrink: 0;
      margin-top: 4px;
    }

    .selected-activity-title {
      font-weight: 700;
      font-size: 0.92rem;
      color: var(--text-primary);
    }

    .selected-activity-meta {
      font-size: 0.78rem;
      color: var(--text-muted);
      margin-top: 4px;
      line-height: 1.35;
    }

    .selected-activity-pill {
      margin-left: auto;
      padding: 4px 8px;
      border-radius: 999px;
      font-size: 0.72rem;
      font-weight: 700;
      white-space: nowrap;
      color: white;
    }
  </style>
</head>

<body>

  <?php
  $stats = $statistiques ?? [];
  $history = $historiquePoids ?? ['labels' => [], 'poids' => []];
  $simulation = $donneesSimulation ?? ['labels' => [], 'datasets' => []];
  $activitesSelectionnees = $activitesSelectionnees ?? [];
  $objectifActif = $objectifActif ?? null;

  $fullName = trim(($stats['user_name'] ?? 'Utilisateur'));
  $nom = trim((string) ($stats['nom'] ?? ''));
  $prenom = trim((string) ($stats['prenom'] ?? ''));
  $username = trim((string) ($stats['username'] ?? session()->get('username') ?? 'Utilisateur'));
  $email = trim((string) ($stats['email'] ?? session()->get('email') ?? ''));
  $poidsActuel = (float) ($stats['poids_actuel'] ?? 0);
  $poidsInitial = (float) ($stats['poids_initial'] ?? 0);
  $progressKg = (float) ($stats['progression_kg'] ?? 0);
  $progressPct = (float) ($stats['progression_pourcent'] ?? 0);
  $taille = (float) ($stats['taille'] ?? 0);
  $age = (int) ($stats['age'] ?? 0);
  $dureeRegime = (int) ($stats['duree_regime_jours'] ?? 0);
  $regimeActif = $stats['regime_actif'] ?? null;
  $joursEcoules = (int) ($stats['jours_ecoules'] ?? 0);
  $variationPoids = (float) ($stats['variation_poids'] ?? 0);
  $variationPoidsLabel = ($variationPoids > 0 ? '+' : '') . number_format($variationPoids, 2) . ' kg';
  $variationPoidsBadgeClass = $variationPoids == 0.0 ? 'badge-gray' : 'badge-green';

  $imc = 0;
  if ($taille > 0 && $poidsActuel > 0) {
      $imc = round($poidsActuel / pow($taille / 100, 2), 1);
  }

  $initials = strtoupper(substr($nom, 0, 1) . substr($prenom, 0, 1));
  if ($initials === '') {
      $initials = strtoupper(substr($username !== '' ? $username : $fullName, 0, 1));
  }
  ?>

  <?php $sessionUser = $user ?? []; ?>
  <script>
    window.NF_USER = {
      prenom: "<?= esc($prenom) ?>",
      nom: "<?= esc($nom) ?>",
      username: "<?= esc($username) ?>",
      email: "<?= esc($email) ?>",
      role_user: "<?= esc((string) ($sessionUser['role_user'] ?? session()->get('role_user') ?? '')) ?>",
      gold: <?= session()->get('user_option') === 'gold' ? 'true' : 'false' ?>
    };
  </script>

  <div id="pageContent">
    <div class="page-content">

      <!-- Welcome Banner -->
      <div class="card" style="margin-bottom:1.5rem;background:linear-gradient(135deg, #0D1F14, #1A3327);border:none;overflow:visible">
        <div class="card-body" style="display:flex;align-items:center;justify-content:space-between;padding:1.75rem 2rem">
          <div>
            <p style="color:rgba(255,255,255,0.5);font-size:0.85rem;margin-bottom:0.3rem">Bienvenue de retour 👋</p>
            <h2 style="color:white;font-size:1.6rem;margin-bottom:0.5rem" id="welcomeName"><?= esc($fullName) ?></h2>
            <p style="color:rgba(255,255,255,0.55);font-size:0.9rem">Votre progression : <strong style="color:var(--primary)" id="welcomeGoal"><?= esc(number_format(abs($progressKg), 2)) ?> kg</strong> — Continuez comme ça !</p>
          </div>
          <div style="text-align:right">
            <div style="font-family:'Sora',sans-serif;font-size:3rem;font-weight:800;color:var(--primary);line-height:1" id="welcomeBMI"><?= esc(number_format($imc, 1)) ?></div>
            <div style="color:rgba(255,255,255,0.45);font-size:0.8rem">IMC actuel</div>
            <span class="badge badge-green" style="margin-top:6px"><?= $imc < 18.5 ? 'Insuffisant' : ($imc < 25 ? 'Poids normal' : ($imc < 30 ? 'Surpoids' : 'Obesite')) ?></span>
          </div>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="stats-grid">
        <div class="stat-card green anim-fade-up delay-1">
          <div style="display:flex;align-items:center;justify-content:space-between">
            <div class="stat-icon green"><i class="fa-solid fa-fire"></i></div>
            <span class="stat-trend up"><i class="fa-solid fa-arrow-up"></i> <?= esc(number_format(abs($progressPct), 1)) ?>%</span>
          </div>
          <div class="stat-value"><?= esc(number_format($poidsInitial, 2)) ?></div>
          <div class="stat-label">Poids initial (kg)</div>
          <div class="progress-bar">
            <div class="progress-fill" style="width:<?= esc((string) min(100, max(5, abs($progressPct)))) ?>%"></div>
          </div>
        </div>

        <div class="stat-card gold anim-fade-up delay-2">
          <div style="display:flex;align-items:center;justify-content:space-between">
            <div class="stat-icon gold"><i class="fa-solid fa-weight-scale"></i></div>
            <span class="stat-trend <?= $progressKg < 0 ? 'down' : 'up' ?>"><i class="fa-solid fa-arrow-<?= $progressKg < 0 ? 'down' : 'up' ?>"></i> <?= esc(number_format(abs($progressKg), 2)) ?> kg</span>
          </div>
          <div class="stat-value" id="statPoids"><?= esc(number_format($poidsActuel, 2)) ?></div>
          <div class="stat-label">Poids actuel (kg)</div>
          <div class="progress-bar">
            <div class="progress-fill gold" style="width:<?= esc((string) min(100, max(5, ($poidsInitial > 0 ? ($poidsActuel / $poidsInitial) * 100 : 0)))) ?>%"></div>
          </div>
        </div>

        <div class="stat-card info anim-fade-up delay-3">
          <div style="display:flex;align-items:center;justify-content:space-between">
            <div class="stat-icon info"><i class="fa-solid fa-calendar-days"></i></div>
            <span class="stat-trend up"><i class="fa-solid fa-clock"></i> <?= esc((string) $dureeRegime) ?>j</span>
          </div>
          <div class="stat-value"><?= esc((string) $dureeRegime) ?></div>
          <div class="stat-label">Duree regime (jours)</div>
          <div class="progress-bar">
            <div class="progress-fill info" style="width:<?= esc((string) min(100, max(5, ($dureeRegime / 90) * 100))) ?>%"></div>
          </div>
        </div>

        <div class="stat-card danger anim-fade-up delay-4">
          <div style="display:flex;align-items:center;justify-content:space-between">
            <div class="stat-icon danger"><i class="fa-solid fa-bowl-food"></i></div>
            <span class="stat-trend up" style="color:var(--danger)"><i class="fa-solid fa-check"></i> Actif</span>
          </div>
          <div class="stat-value" style="font-size:1.05rem"><?= esc($regimeActif ? mb_substr($regimeActif, 0, 12) : 'Aucun') ?></div>
          <div class="stat-label">Regime en cours</div>
          <div class="progress-bar">
            <div class="progress-fill" style="width:<?= esc((string) ($regimeActif ? 100 : 0)) ?>%;background:linear-gradient(90deg,var(--danger),#C0392B)"></div>
          </div>
        </div>
      </div>

      <!-- Row 1: Chart + IMC -->
      <div class="dashboard-grid">

        <!-- Weight Chart -->
        <div class="card">
          <div class="card-header">
            <h3><i class="fa-solid fa-chart-line" style="color:var(--primary);margin-right:6px"></i> Évolution du poids</h3>
            <div class="flex gap-2">
              <span class="badge badge-gray"><?= esc((string) $joursEcoules) ?> jours</span>
              <span class="badge <?= esc($variationPoidsBadgeClass) ?>"><?= esc($variationPoidsLabel) ?></span>
            </div>
          </div>
          <div class="card-body">
            <canvas id="weightChart" style="width:100%;height:200px"></canvas>
          </div>
        </div>

        <!-- Simulation Widget -->
        <div class="card">
          <div class="card-header">
            <h3><i class="fa-solid fa-chart-area" style="color:var(--gold);margin-right:6px"></i> Simulation poids selon regime</h3>
            <a href="#" style="font-size:0.82rem;color:var(--primary)">Projection</a>
          </div>
          <div class="card-body" style="padding-top:1rem">
            <canvas id="simulationChart" style="width:100%;height:280px"></canvas>
          </div>
        </div>
      </div>

      <!-- Row 2 : Régime actif + Activités récentes + Objectifs -->
      <div class="dashboard-grid-3">

        <!-- Régime actif -->
        <div class="card">
          <div class="card-header">
            <h3><i class="fa-solid fa-bowl-food" style="color:var(--primary);margin-right:6px"></i> Régime actif</h3>
            <a href="regimes.html" style="font-size:0.82rem;color:var(--primary)">Voir tout</a>
          </div>
          <div class="card-body">
            <div class="regime-item">
              <div class="regime-icon" style="background:#E8FAF3;font-size:1.4rem">🥗</div>
              <div style="flex:1;min-width:0">
                <div class="regime-name"><?= esc($regimeActif ?? 'Aucun regime actif') ?></div>
                <div class="regime-cal"><?= esc((string) $dureeRegime) ?> jours de suivi</div>
                <div class="regime-progress progress-bar" style="margin-top:6px">
                  <div class="progress-fill" style="width:<?= esc((string) min(100, max(5, ($dureeRegime / 30) * 100))) ?>%"></div>
                </div>
              </div>
              <span class="badge badge-green"><?= $regimeActif ? 'Actif' : 'Aucun' ?></span>
            </div>

            <div style="margin-top:1.25rem">
              <div class="flex justify-between" style="font-size:0.82rem;color:var(--text-muted);margin-bottom:0.5rem">
                <span>Progression globale</span>
                <span style="color:var(--primary-dark);font-weight:700"><?= esc(number_format(abs($progressPct), 1)) ?>%</span>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" style="width:<?= esc((string) min(100, max(5, abs($progressPct)))) ?>%"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Activités récentes -->
        <div class="card">
          <div class="card-header">
            <h3><i class="fa-solid fa-dumbbell" style="color:var(--info);margin-right:6px"></i> Activités sélectionnées</h3>
            <a href="activities.html" style="font-size:0.82rem;color:var(--primary)">Voir tout</a>
          </div>
          <div class="card-body" style="padding-top:0.75rem">
            <?php if (!empty($activitesSelectionnees)): ?>
              <?php foreach ($activitesSelectionnees as $index => $activity): ?>
                <?php
                  $sportNom = (string) ($activity['sport_nom'] ?? 'Sport');
                  $objectifLibelle = (string) ($activity['objectif_libelle'] ?? 'Objectif');
                  $timeLabel = (string) ($activity['time_label'] ?? '-');
                  $calories = (float) ($activity['calories_brulees'] ?? 0);
                  $duree = (int) ($activity['duree_recommandee'] ?? 0);
                  $cardColor = $index % 3 === 0 ? 'var(--primary)' : ($index % 3 === 1 ? 'var(--info)' : 'var(--gold)');
                ?>
                <div class="selected-activity-card">
                  <div class="selected-activity-accent" style="background:<?= esc($cardColor) ?>"></div>
                  <div class="activity-text">
                    <div class="selected-activity-title"><?= esc($sportNom) ?></div>
                    <div class="selected-activity-meta"><?= esc($objectifLibelle) ?></div>
                    <div class="selected-activity-meta"><?= esc(number_format($calories, 1)) ?> kcal/min · <?= esc((string) $duree) ?> min</div>
                  </div>
                  <div class="selected-activity-pill" style="background:<?= esc($cardColor) ?>">
                    <?= esc($timeLabel) ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="selected-activity-card">
                <div class="selected-activity-accent" style="background:var(--text-muted)"></div>
                <div class="activity-text">
                  <div class="selected-activity-title">Aucune activité sélectionnée</div>
                  <div class="selected-activity-meta">Choisis un sport pour l’afficher ici.</div>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Objectifs & Historique poids -->
        <div class="card">
          <div class="card-header">
            <h3><i class="fa-solid fa-bullseye" style="color:var(--danger);margin-right:6px"></i> Mes objectifs</h3>
          </div>
          <div class="card-body">
            <?php if ($objectifActif): ?>
              <div class="goal-item">
                <div class="goal-header">
                  <span class="goal-name"><?= esc((string) ($objectifActif['libelle'] ?? 'Objectif')) ?></span>
                  <span class="goal-pct" style="color:<?= esc((string) ($objectifActif['color'] ?? 'var(--primary-dark)')) ?>">Actif</span>
                </div>
                <div class="progress-bar">
                  <div class="progress-fill" style="background:<?= esc((string) ($objectifActif['color'] ?? 'var(--primary-dark)')) ?>;width:100%"></div>
                </div>
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px">Objectif en cours</div>
              </div>
            <?php else: ?>
              <div class="goal-item">
                <div class="goal-header">
                  <span class="goal-name">Aucun objectif actif</span>
                  <span class="goal-pct">-</span>
                </div>
                <div class="progress-bar">
                  <div class="progress-fill" style="width:0%"></div>
                </div>
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px">Choisis un régime pour activer un objectif</div>
              </div>
            <?php endif; ?>

            <div style="margin-top:1rem">
              <div style="font-size:0.82rem;font-weight:700;margin-bottom:0.75rem;font-family:'Sora',sans-serif">Historique du poids</div>
              <?php if (!empty($history['labels']) && !empty($history['poids'])): ?>
                <?php
                $count = count($history['labels']);
                $start = max(0, $count - 5);
                $prev = null;
                for ($i = $start; $i < $count; $i++):
                  $label = (string) ($history['labels'][$i] ?? '');
                    $value = (float) $history['poids'][$i];
                    $diff = $prev !== null ? round($value - $prev, 1) : 0.0;
                    $isUp = $diff > 0;
                    $prev = $value;
                ?>
                <div class="weight-row">
                  <span class="weight-date"><?= esc($label) ?></span>
                  <span class="weight-val"><?= esc(number_format($value, 2)) ?> kg</span>
                  <span class="weight-diff <?= $isUp ? 'up' : 'down' ?>"><?= $isUp ? '▲' : '▼' ?> <?= esc(number_format(abs($diff), 1)) ?></span>
                </div>
                <?php endfor; ?>
              <?php else: ?>
                <div class="weight-row">
                  <span class="weight-date">Aucune donnee</span>
                  <span class="weight-val">-</span>
                  <span class="weight-diff down">-</span>
                </div>
              <?php endif; ?>

              <!-- Formulaire d'ajout de poids -->
              <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border)">
                <form method="post" action="<?= site_url('/dashboard/add-poids') ?>" style="display:flex;gap:8px;flex-wrap:wrap">
                  <?= csrf_field() ?>
                  <input type="date" name="date_save" required style="flex:1;min-width:140px;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:0.85rem;background:var(--bg)" />
                  <input type="number" name="poids" placeholder="Poids (kg)" step="0.01" min="1" max="500" required style="flex:0 1 100px;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:0.85rem;background:var(--bg)" />
                  <button type="submit" style="padding:8px 16px;background:var(--primary);color:white;border:none;border-radius:var(--radius-sm);font-weight:600;font-size:0.85rem;cursor:pointer;transition:var(--transition);display:flex;align-items:center;gap:6px" onmouseover="this.style.background='var(--primary-dark)'" onmouseout="this.style.background='var(--primary)'"><i class="fa-solid fa-check"></i> Enregistrer</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script src="<?= base_url('assets/js/app.js') ?>"></script>
  <script src="<?= base_url('assets/js/layout.js') ?>"></script>
  <script>
    if (typeof NF_LAYOUT !== 'undefined' && NF_LAYOUT.inject) {
      NF_LAYOUT.inject('dashboard', 'Tableau de bord', 'Dashboard');
    }
    const mc = document.getElementById('mainContent');
    const content = document.getElementById('pageContent');
    if (mc && content) {
      mc.appendChild(content);
    }
  </script>
  
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const historyData = <?= json_encode($history, JSON_UNESCAPED_UNICODE) ?>;
      const simulationData = <?= json_encode($simulation, JSON_UNESCAPED_UNICODE) ?>;

      const weightCtx = document.getElementById('weightChart');
      if (weightCtx && historyData.labels && historyData.labels.length) {
        new Chart(weightCtx, {
          type: 'line',
          data: {
            labels: historyData.labels,
            datasets: [{
              label: 'Poids reel (kg)',
              data: historyData.poids,
              borderColor: 'rgb(24, 201, 122)',
              backgroundColor: 'rgba(24, 201, 122, 0.15)',
              borderWidth: 3,
              pointRadius: 3,
              tension: 0.35,
              fill: true
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: true }
            }
          }
        });
      }

      const simulationCtx = document.getElementById('simulationChart');
      if (simulationCtx && simulationData.labels && simulationData.labels.length) {
        new Chart(simulationCtx, {
          type: 'line',
          data: {
            labels: simulationData.labels,
            datasets: simulationData.datasets || []
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: true }
            }
          }
        });
      }

    });
  </script>
</body>

</html>
