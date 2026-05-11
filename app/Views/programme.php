<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Programme — NutriFit</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/all.min.css">
  <link rel="icon" href="/assets/logo.png">
  <style>
    .programme-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 1rem;
    }

    .programme-card {
      scroll-margin-top: 90px;
      min-height: 100%;
    }

    .programme-icon {
      width: 44px;
      height: 44px;
      border-radius: var(--radius);
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--primary-light);
      color: var(--primary-dark);
      font-size: 1rem;
      flex-shrink: 0;
    }

    .programme-empty {
      color: var(--text-muted);
      font-size: 0.9rem;
      margin: 0.75rem 0 1rem;
    }

    .programme-name {
      font-size: 1.15rem;
      margin-bottom: 0.4rem;
    }

    .programme-meta {
      display: grid;
      gap: 0.55rem;
      margin: 1rem 0;
    }

    .programme-row {
      display: flex;
      justify-content: space-between;
      gap: 1rem;
      border-bottom: 1px solid var(--border);
      padding-bottom: 0.5rem;
      font-size: 0.88rem;
    }

    .programme-row span:first-child {
      color: var(--text-muted);
    }

    .programme-row span:last-child {
      color: var(--text-primary);
      font-weight: 600;
      text-align: right;
    }

    /* Styles pour les onglets type mockup */
    .programme-tabs-wrapper {
      margin-bottom: 1.5rem;
    }

    .programme-tabs {
      display: flex;
      gap: 3px;
      background: var(--color-background-secondary);
      border-radius: var(--border-radius-md);
      padding: 3px;
      margin-bottom: 1.5rem;
    }

    .programme-tab {
      flex: 1;
      padding: 8px 12px;
      border-radius: 6px;
      font-size: 13px;
      text-align: center;
      cursor: pointer;
      color: var(--color-text-secondary);
      transition: all 0.15s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      background: transparent;
      border: none;
      font-weight: 500;
    }

    .programme-tab i {
      font-size: 14px;
    }

    .programme-tab.active {
      background: var(--color-background-primary);
      color: #15803d;
      border: 0.5px solid var(--color-border-tertiary);
      box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .programme-tab:hover:not(.active) {
      background: var(--color-background-hover);
      color: var(--color-text-primary);
    }

    .programme-panels {
      position: relative;
    }

    .programme-panel {
      display: none;
      animation: fadeIn 0.2s ease;
    }

    .programme-panel.active {
      display: block;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(5px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .programme-badge {
      background: #dcfce7;
      color: #15803d;
      font-size: 10px;
      font-weight: 500;
      padding: 2px 8px;
      border-radius: 20px;
      display: inline-block;
    }

    .programme-progress {
      margin-top: 0.75rem;
    }

    .programme-progress-label {
      display: flex;
      justify-content: space-between;
      font-size: 0.7rem;
      color: var(--text-muted);
      margin-bottom: 0.25rem;
    }

    .programme-progress-bar {
      height: 6px;
      background: var(--border);
      border-radius: 10px;
      overflow: hidden;
    }

    .programme-progress-fill {
      height: 100%;
      background: #16a34a;
      border-radius: 10px;
      transition: width 0.3s ease;
    }

    .programme-info-box {
      background: #dcfce7;
      border-radius: var(--radius);
      padding: 0.75rem;
      margin-top: 1rem;
    }

    .programme-info-box p {
      font-size: 0.75rem;
      color: #15803d;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    @media (max-width: 1100px) {
      .programme-grid { grid-template-columns: 1fr; }
      .programme-tabs { flex-direction: column; gap: 5px; background: transparent; padding: 0; }
      .programme-tab { justify-content: flex-start; padding: 10px; background: var(--card-bg); border: 1px solid var(--border); }
      .programme-tab.active { border-color: #16a34a; }
    }
  </style>
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
  <div class="page-content">
    <div class="page-header">
      <div class="header-title">
        <div class="logo-icon" style="background: var(--primary);">
          <i class="fa-solid fa-list-check" style="color:white;"></i>
        </div>
        <div>
          <h2>Mes choix</h2>
          <p class="text-muted">Objectif, régime et activité sportive actuellement enregistrés.</p>
        </div>
      </div>
    </div>

    <!-- Onglets type mockup -->
    <div class="programme-tabs-wrapper">
      <div class="programme-tabs">
        <button class="programme-tab active" data-target="objectif">
          <i class="fa-solid fa-bullseye"></i> Objectif
        </button>
        <button class="programme-tab" data-target="regime">
          <i class="fa-solid fa-bowl-food"></i> Régime
        </button>
        <button class="programme-tab" data-target="sport">
          <i class="fa-solid fa-person-running"></i> Sport
        </button>
      </div>
    </div>

    <!-- Panels -->
    <div class="programme-panels">
      <!-- Panel Objectif -->
      <div class="programme-panel active" id="panel-objectif">
        <div class="programme-grid" style="grid-template-columns: 1fr;">
          <section class="card programme-card">
            <div class="card-body">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <div style="display:flex;align-items:center;gap:0.85rem;">
                  <div class="programme-icon"><i class="fa-solid fa-bullseye"></i></div>
                  <div>
                    <h3>Objectif choisi</h3>
                    <p class="text-muted" style="font-size:0.82rem;">Votre direction principale.</p>
                  </div>
                </div>
                <span class="programme-badge">Actif</span>
              </div>

              <?php if (!empty($objectif)): ?>
                <h4 class="programme-name"><?php echo esc($objectif['libelle'] ?? 'Objectif'); ?></h4>
                <div class="programme-meta">
                  <div class="programme-row">
                    <span>Date de choix</span>
                    <span><?php echo !empty($objectif['date_save']) ? esc(date('d/m/Y', strtotime($objectif['date_save']))) : 'Non défini'; ?></span>
                  </div>
                  <?php if (!empty($imc_actuel)): ?>
                  <div class="programme-row">
                    <span>IMC actuel</span>
                    <span style="color:#ea580c;"><?php echo esc(number_format($imc_actuel, 1, ',', ' ')); ?></span>
                  </div>
                  <?php endif; ?>
                  <?php if (!empty($poids_actuel)): ?>
                  <div class="programme-row">
                    <span>Poids actuel</span>
                    <span><?php echo esc(number_format($poids_actuel, 2, ',', ' ')); ?> kg</span>
                  </div>
                  <?php endif; ?>
                  <?php if (!empty($poids_cible)): ?>
                  <div class="programme-row">
                    <span>Poids cible</span>
                    <span style="color:#16a34a;"><?php echo esc(number_format($poids_cible, 2, ',', ' ')); ?> kg ✓</span>
                  </div>
                  <?php endif; ?>
                </div>

                <?php if (!empty($progression)): ?>
                <div class="programme-progress">
                  <div class="programme-progress-label">
                    <span>Progression</span>
                    <span style="color:#16a34a; font-weight:500;"><?php echo esc($progression); ?>%</span>
                  </div>
                  <div class="programme-progress-bar">
                    <div class="programme-progress-fill" style="width: <?php echo esc($progression); ?>%;"></div>
                  </div>
                </div>
                <?php endif; ?>
              <?php else: ?>
                <p class="programme-empty">Aucun objectif n'a encore été choisi.</p>
              <?php endif; ?>

              <a href="/objectif" class="btn btn-outline btn-sm" style="margin-top: 1rem;">
                <i class="fa-solid fa-pen"></i> Modifier
              </a>
            </div>
          </section>
        </div>
      </div>

      <!-- Panel Régime -->
      <div class="programme-panel" id="panel-regime">
        <div class="programme-grid" style="grid-template-columns: 1fr;">
          <section class="card programme-card">
            <div class="card-body">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <div style="display:flex;align-items:center;gap:0.85rem;">
                  <div class="programme-icon"><i class="fa-solid fa-bowl-food"></i></div>
                  <div>
                    <h3>Régime choisi</h3>
                    <p class="text-muted" style="font-size:0.82rem;">Votre plan alimentaire actif.</p>
                  </div>
                </div>
                <span class="programme-badge">Actif</span>
              </div>

              <?php if (!empty($regime)): ?>
                <h4 class="programme-name"><?php echo esc($regime['nom'] ?? 'Régime'); ?></h4>
                <div class="programme-meta">
                  <div class="programme-row"><span>Début</span><span><?php echo !empty($regime['date_debut']) ? esc(date('d/m/Y', strtotime($regime['date_debut']))) : 'Non défini'; ?></span></div>
                  <div class="programme-row"><span>Durée</span><span><?php echo esc((int) ($regime['duree'] ?? 0)); ?> semaines</span></div>
                  <div class="programme-row"><span>Montant</span><span><?php echo esc(number_format((float) ($regime['montant'] ?? 0), 0, ',', ' ')); ?> Ar / jour</span></div>
                  <div class="programme-row"><span>Variation</span><span style="color:#16a34a;">+<?php echo esc(number_format((float) ($regime['variation_poids'] ?? 0), 2, ',', ' ')); ?> kg / semaine</span></div>
                </div>

                <?php if (!empty($regime['repartition'])): ?>
                  <?php foreach ($regime['repartition'] as $cat => $pct): ?>
                  <div class="programme-progress">
                    <div class="programme-progress-label">
                      <span><?php echo esc($cat); ?></span>
                      <span style="color:#16a34a;"><?php echo esc($pct); ?>%</span>
                    </div>
                    <div class="programme-progress-bar">
                      <div class="programme-progress-fill" style="width: <?php echo esc($pct); ?>%;"></div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              <?php else: ?>
                <p class="programme-empty">Aucun régime n'a encore été choisi.</p>
              <?php endif; ?>

              <a href="/regime" class="btn btn-outline btn-sm" style="margin-top: 1rem;">
                <i class="fa-solid fa-pen"></i> Modifier
              </a>
            </div>
          </section>
        </div>
      </div>

      <!-- Panel Sport -->
      <div class="programme-panel" id="panel-sport">
        <div class="programme-grid" style="grid-template-columns: 1fr;">
          <section class="card programme-card">
            <div class="card-body">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <div style="display:flex;align-items:center;gap:0.85rem;">
                  <div class="programme-icon"><i class="fa-solid fa-person-running"></i></div>
                  <div>
                    <h3>Sport choisi</h3>
                    <p class="text-muted" style="font-size:0.82rem;">Votre activité recommandée.</p>
                  </div>
                </div>
                <span class="programme-badge">Actif</span>
              </div>

              <?php if (!empty($sport)): ?>
                <h4 class="programme-name"><?php echo esc($sport['nom'] ?? 'Sport'); ?></h4>
                <div class="programme-meta">
                  <div class="programme-row"><span>Objectif lié</span><span><?php echo esc($sport['objectif_libelle'] ?? 'Non défini'); ?></span></div>
                  <div class="programme-row"><span>Calories</span><span><?php echo esc(number_format((float) ($sport['calories_brulees'] ?? 0), 1, ',', ' ')); ?> kcal / min</span></div>
                  <div class="programme-row"><span>Durée séance</span><span><?php echo esc((int) ($sport['duree_recommandee'] ?? 0)); ?> minutes</span></div>
                  <?php if (!empty($sport['calories_brulees']) && !empty($sport['duree_recommandee'])): ?>
                  <div class="programme-row"><span>Total / séance</span><span style="color:#16a34a;"><?php echo esc(number_format($sport['calories_brulees'] * $sport['duree_recommandee'], 0, ',', ' ')); ?> kcal</span></div>
                  <?php endif; ?>
                </div>

                <div class="programme-info-box">
                  <p><i class="fa-solid fa-circle-info"></i> À pratiquer régulièrement</p>
                </div>
              <?php else: ?>
                <p class="programme-empty">Aucun sport n'a encore été choisi.</p>
              <?php endif; ?>

              <a href="/sport" class="btn btn-outline btn-sm" style="margin-top: 1rem;">
                <i class="fa-solid fa-pen"></i> Modifier
              </a>
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Gestion des onglets
  document.querySelectorAll('.programme-tab').forEach(tab => {
    tab.addEventListener('click', function() {
      const target = this.dataset.target;
      
      // Désactiver tous les tabs
      document.querySelectorAll('.programme-tab').forEach(t => t.classList.remove('active'));
      // Activer le tab cliqué
      this.classList.add('active');
      
      // Désactiver tous les panels
      document.querySelectorAll('.programme-panel').forEach(panel => panel.classList.remove('active'));
      // Activer le panel correspondant
      const activePanel = document.getElementById('panel-' + target);
      if (activePanel) activePanel.classList.add('active');
    });
  });
</script>

<script src="/assets/js/app.js"></script>
<script src="/assets/js/layout.js"></script>
<script>
  if (typeof NF_LAYOUT !== 'undefined' && NF_LAYOUT.inject) {
    NF_LAYOUT.inject('programme', 'Mes choix', 'Programme actuel', <?php echo $balance ?? 0; ?>);
  }
  const mc = document.getElementById('mainContent');
  const content = document.getElementById('pageContent');
  if (mc && content) mc.appendChild(content);
</script>

</body>
</html>