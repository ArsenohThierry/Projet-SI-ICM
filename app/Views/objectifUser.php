<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Objectif — NutriFit</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
        <link rel="icon" href="/assets/logo.png">
    <style>
        .objectif-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--bg);
        }

        .objectif-body {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.5rem;
        }

        .objectif-wrapper {
            width: 100%;
            max-width: 700px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* En-tête de section */
        .objectif-heading {
            text-align: center;
        }
        .objectif-heading h1 {
            font-size: 1.9rem;
            margin-bottom: 0.4rem;
        }
        .objectif-heading p {
            color: var(--text-muted);
            font-size: 0.92rem;
        }

        /* Grille des cartes objectif */
        .objectif-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
        }

        .objectif-card {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            padding: 1.6rem 1rem;
            border: 2px solid var(--border);
            border-radius: var(--radius-lg);
            background: var(--bg-card);
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            user-select: none;
        }

        .objectif-card:hover {
            border-color: var(--primary);
            background: var(--primary-light);
            transform: translateY(-3px);
            box-shadow: var(--shadow);
        }

        .objectif-card.selected {
            border-color: var(--primary);
            background: var(--primary-light);
            box-shadow: 0 0 0 4px var(--primary-glow), var(--shadow);
        }

        .objectif-card.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f3f4f6;
            border-color: #d1d5db;
            transform: none;
            box-shadow: none;
        }

        .objectif-card.disabled:hover {
            transform: none;
            box-shadow: none;
            border-color: #d1d5db;
            background: #f3f4f6;
        }

        .objectif-check {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
        }
        .objectif-card.selected .objectif-check {
            display: flex;
        }

        .objectif-icon {
            width: 56px;
            height: 56px;
            border-radius: var(--radius-lg);
            background: var(--primary-light);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            transition: var(--transition);
        }
        .objectif-card:hover .objectif-icon,
        .objectif-card.selected .objectif-icon {
            background: var(--primary);
            color: white;
        }

        .objectif-label {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 0.92rem;
            color: var(--text-primary);
            line-height: 1.3;
        }

        .objectif-desc {
            font-size: 0.78rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        /* Zone de confirmation */
        .confirm-zone {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .confirm-selected-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .confirm-icon-preview {
            width: 42px;
            height: 42px;
            border-radius: var(--radius);
            background: var(--primary-light);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .confirm-placeholder {
            font-size: 0.88rem;
            color: var(--text-muted);
            font-style: italic;
        }

        .confirm-name {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--text-primary);
        }

        .confirm-sub {
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        /* Icônes par défaut selon index si non définies côté PHP */
        .icon-fallback { font-size: 1.4rem; }
    </style>
</head>
<body class="objectif-page">

<?php $sessionUser = $user ?? []; ?>

<!-- Navbar -->
<header class="navbar" style="position:sticky; top:0; z-index:20; backdrop-filter:blur(14px);">
    <div class="navbar-left">
        <div style="display:flex; align-items:center; gap:10px;">
            <div class="logo-icon"><i class="fa-solid fa-leaf"></i></div>
            <span class="logo-text">Nutri<span>Fit</span></span>
        </div>
        <div>
            <div class="page-title">Objectif</div>
            <div class="breadcrumb">NutriFit / <span>Définir mon objectif</span></div>
        </div>
    </div>
    <div class="navbar-right">
        <div class="navbar-avatar" title="<?php echo esc($sessionUser['prenom'] ?? ''); ?>">
            <?php echo strtoupper(
                substr((string)($sessionUser['prenom'] ?? 'J'), 0, 1) .
                substr((string)($sessionUser['nom']    ?? 'R'), 0, 1)
            ); ?>
        </div>
    </div>
</header>

<!-- Contenu -->
<div class="objectif-body">
    <div class="objectif-wrapper anim-fade-up">

        <!-- En-tête -->
        <div class="objectif-heading">
            <span class="badge badge-green" style="margin-bottom:0.75rem;">
                <i class="fa-solid fa-bullseye"></i> Étape 1 sur 1
            </span>
            <h1>Quel est votre objectif ?</h1>
            <p>Choisissez l'objectif qui correspond le mieux à votre démarche santé.</p>
        </div>

        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?php echo esc($errorMessage); ?>
            </div>
        <?php elseif (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?php echo esc((string) session()->getFlashdata('error')); ?>
            </div>
        <?php endif; ?>

        <!-- Grille des objectifs -->
        <?php
            $objectifs = $objectifs ?? [];
            $disableUnavailable = (bool) ($disableUnavailable ?? false);
            $availableObjectifIds = array_map('intval', $availableObjectifIds ?? []);

            /*
             * Icônes et descriptions de secours affichées si le libellé
             * ne contient pas de mot-clé reconnu — à adapter selon vos données.
             * Vous pouvez aussi ajouter une colonne `icone` et `description`
             * dans votre table objectif et les passer depuis le Controller.
             */
            $iconMap = [
                'perd'      => ['fa-arrow-trend-down', 'Réduire votre poids progressivement'],
                'mincir'    => ['fa-arrow-trend-down', 'Affiner votre silhouette'],
                'maint'     => ['fa-scale-balanced',   'Stabiliser votre poids actuel'],
                'stabilit'  => ['fa-scale-balanced',   'Maintenir votre équilibre'],
                'mass'      => ['fa-dumbbell',          'Développer votre musculature'],
                'muscl'     => ['fa-dumbbell',          'Renforcer votre corps'],
                'form'      => ['fa-heart-pulse',       'Améliorer votre condition physique'],
                'sant'      => ['fa-apple-whole',       'Adopter de meilleures habitudes'],
                'énergi'    => ['fa-bolt',              'Booster votre vitalité au quotidien'],
                'energ'     => ['fa-bolt',              'Booster votre vitalité au quotidien'],
            ];
            $defaultIcons = [
                'fa-star', 'fa-seedling', 'fa-sun', 'fa-fire',
                'fa-shield-halved', 'fa-ribbon'
            ];

            function getIconForObjectif(string $libelle, int $index, array $iconMap, array $defaults): array {
                $lower = mb_strtolower($libelle);
                foreach ($iconMap as $key => [$icon, $desc]) {
                    if (str_contains($lower, $key)) return [$icon, $desc];
                }
                return [$defaults[$index % count($defaults)], 'Choisir cet objectif'];
            }
        ?>

        <div class="objectif-grid">
            <?php foreach ($objectifs as $i => $obj):
                [$icon, $desc] = getIconForObjectif($obj['libelle'], $i, $iconMap, $defaultIcons);
                $objectifId = (int) $obj['id'];
                $isDisabled = $disableUnavailable && !in_array($objectifId, $availableObjectifIds, true);
            ?>
                <div class="objectif-card<?php echo $isDisabled ? ' disabled' : ''; ?>"
                     data-id="<?php echo $objectifId; ?>"
                     data-label="<?php echo htmlspecialchars($obj['libelle']); ?>"
                     data-icon="<?php echo $icon; ?>"
                     data-disabled="<?php echo $isDisabled ? '1' : '0'; ?>">

                    <div class="objectif-check"><i class="fa-solid fa-check"></i></div>

                    <div class="objectif-icon">
                        <i class="fa-solid <?php echo $icon; ?>"></i>
                    </div>
                    <div class="objectif-label"><?php echo htmlspecialchars($obj['libelle']); ?></div>
                    <div class="objectif-desc"><?php echo $desc; ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Zone confirmation -->
        <form method="post" action="/objectif" id="objectif-form">
            <input type="hidden" name="objectif_id" id="objectif_id" value="">

            <div class="confirm-zone">
                <div class="confirm-selected-info" id="confirm-info">
                    <div class="confirm-icon-preview" id="confirm-icon-preview">
                        <i class="fa-solid fa-hand-pointer" style="color:var(--text-muted);"></i>
                    </div>
                    <div>
                        <div class="confirm-placeholder" id="confirm-placeholder">Aucun objectif sélectionné</div>
                        <div class="confirm-name" id="confirm-name" style="display:none;"></div>
                        <div class="confirm-sub"  id="confirm-sub"  style="display:none;">Objectif sélectionné</div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" id="confirm-btn" disabled>
                    <i class="fa-solid fa-check"></i> Confirmer mon objectif
                </button>
            </div>
        </form>

    </div>
</div>

<script>
(function () {
    const cards       = document.querySelectorAll('.objectif-card');
    const input       = document.getElementById('objectif_id');
    const btn         = document.getElementById('confirm-btn');
    const placeholder = document.getElementById('confirm-placeholder');
    const confirmName = document.getElementById('confirm-name');
    const confirmSub  = document.getElementById('confirm-sub');
    const iconPreview = document.getElementById('confirm-icon-preview');

    cards.forEach(card => {
        card.addEventListener('click', () => {
            if (card.getAttribute('data-disabled') === '1') {
                return;
            }

            // Reset toutes les cartes
            cards.forEach(c => c.classList.remove('selected'));

            // Sélectionner la carte cliquée
            card.classList.add('selected');

            const id    = card.getAttribute('data-id');
            const label = card.getAttribute('data-label');
            const icon  = card.getAttribute('data-icon');

            input.value  = id;
            btn.disabled = false;

            // Mise à jour de la zone de confirmation
            placeholder.style.display = 'none';
            confirmName.style.display = 'block';
            confirmSub.style.display  = 'block';
            confirmName.textContent   = label;
            iconPreview.innerHTML     = `<i class="fa-solid ${icon}" style="color:var(--primary-dark);"></i>`;
            iconPreview.style.background = 'var(--primary-light)';
        });
    });
})();
</script>

</body>
</html>
