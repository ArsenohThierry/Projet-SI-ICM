<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sport — NutriFit</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
        <link rel="icon" href="/assets/logo.png">
    <style>
        .sport-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--bg);
        }

        .sport-body {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.5rem;
        }

        .sport-wrapper {
            width: 100%;
            max-width: 900px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* En-tête de section */
        .sport-heading {
            text-align: center;
        }
        .sport-heading h1 {
            font-size: 1.9rem;
            margin-bottom: 0.4rem;
        }
        .sport-heading p {
            color: var(--text-muted);
            font-size: 0.92rem;
        }

        /* Grille des cartes sport */
        .sport-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
        }

        .sport-card {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 1.5rem;
            border: 2px solid var(--border);
            border-radius: var(--radius-lg);
            background: var(--bg-card);
            cursor: pointer;
            transition: var(--transition);
            user-select: none;
        }

        .sport-card:hover {
            border-color: var(--primary);
            background: var(--primary-light);
            transform: translateY(-3px);
            box-shadow: var(--shadow);
        }

        .sport-card.selected {
            border-color: var(--primary);
            background: var(--primary-light);
            box-shadow: 0 0 0 4px var(--primary-glow), var(--shadow);
        }

        .sport-check {
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
        .sport-card.selected .sport-check {
            display: flex;
        }

        .sport-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-lg);
            background: var(--primary-light);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            transition: var(--transition);
        }
        .sport-card:hover .sport-icon,
        .sport-card.selected .sport-icon {
            background: var(--primary);
            color: white;
        }

        .sport-label {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--text-primary);
            line-height: 1.3;
        }

        .sport-stats {
            font-size: 0.75rem;
            color: var(--text-muted);
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            width: 100%;
        }

        .sport-stat {
            display: flex;
            justify-content: space-between;
            padding: 0.3rem 0;
        }

        .sport-stat-label {
            color: var(--text-muted);
        }

        .sport-stat-value {
            font-weight: 600;
            color: var(--text-primary);
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
    </style>
</head>
<body class="sport-page">

<?php $sessionUser = $user ?? []; ?>

<!-- Navbar -->
<header class="navbar" style="position:sticky; top:0; z-index:20; backdrop-filter:blur(14px);">
    <div class="navbar-left">
        <div style="display:flex; align-items:center; gap:10px;">
            <div class="logo-icon"><i class="fa-solid fa-leaf"></i></div>
            <span class="logo-text">Nutri<span>Fit</span></span>
        </div>
        <div>
            <div class="page-title">Sport</div>
            <div class="breadcrumb">NutriFit / <span>Choisir un sport</span></div>
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
<div class="sport-body">
    <div class="sport-wrapper anim-fade-up">

        <!-- En-tête -->
        <div class="sport-heading">
            <span class="badge badge-purple" style="margin-bottom:0.75rem;">
                <i class="fa-solid fa-dumbbell"></i> Étape 3 sur 3
            </span>
            <h1>Quel sport pratiquer ?</h1>
            <p>Sélectionnez le sport adapté à votre objectif, votre âge et votre condition physique.</p>
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

        <!-- Grille des sports -->
        <?php
            $sports = $sports ?? [];

            $iconMap = [
                'marche'   => 'fa-person-walking',
                'course'   => 'fa-person-running',
                'velo'     => 'fa-person-biking',
                'natation' => 'fa-person-swimming',
                'muscul'   => 'fa-dumbbell',
                'corde'    => 'fa-square',
                'danse'    => 'fa-music',
                'randonne' => 'fa-person-hiking',
            ];
            $defaultIcons = [
                'fa-heart-pulse', 'fa-fire', 'fa-bolt', 'fa-star',
                'fa-shield-halved', 'fa-ribbon', 'fa-medal', 'fa-trophy'
            ];

            function getIconForSport(string $nom, int $index, array $iconMap, array $defaults): string {
                $lower = mb_strtolower($nom);
                foreach ($iconMap as $key => $icon) {
                    if (str_contains($lower, $key)) return $icon;
                }
                return $defaults[$index % count($defaults)];
            }
        ?>

        <div class="sport-grid">
            <?php foreach ($sports as $i => $sport):
                $sportObjectifId = (int) ($sport['id'] ?? 0);
                $nom = htmlspecialchars($sport['nom'] ?? 'Sport ' . ($i + 1));
                $icon = getIconForSport($sport['nom'] ?? '', $i, $iconMap, $defaultIcons);
                $caloriesBrulees = (float) ($sport['calories_brulees'] ?? 0);
                $dureeReco = (int) ($sport['duree_recommandee'] ?? 0);
                $genre = $sport['genre'] ?? null;
            ?>
                <div class="sport-card"
                     data-id="<?php echo $sportObjectifId; ?>"
                     data-label="<?php echo $nom; ?>"
                     data-icon="<?php echo $icon; ?>">

                    <div class="sport-check"><i class="fa-solid fa-check"></i></div>

                    <div style="display: flex; align-items: center; gap: 0.75rem; width: 100%;">
                        <div class="sport-icon">
                            <i class="fa-solid <?php echo $icon; ?>"></i>
                        </div>
                        <div class="sport-label"><?php echo $nom; ?></div>
                    </div>

                    <div class="sport-stats">
                        <div class="sport-stat">
                            <span class="sport-stat-label"><i class="fa-solid fa-fire"></i> Calories</span>
                            <span class="sport-stat-value"><?php echo number_format($caloriesBrulees, 1); ?> cal/min</span>
                        </div>
                        <div class="sport-stat">
                            <span class="sport-stat-label"><i class="fa-solid fa-hourglass-end"></i> Durée</span>
                            <span class="sport-stat-value"><?php echo $dureeReco; ?> min/jour</span>
                        </div>
                        <?php if ($genre): ?>
                        <div class="sport-stat">
                            <span class="sport-stat-label"><i class="fa-solid fa-<?php echo ($genre === 'M') ? 'mars' : 'venus'; ?>"></i> Genre</span>
                            <span class="sport-stat-value"><?php echo ($genre === 'M') ? 'Hommes' : 'Femmes'; ?></span>
                        </div>
                        <?php else: ?>
                        <div class="sport-stat">
                            <span class="sport-stat-label"><i class="fa-solid fa-people-group"></i> Genre</span>
                            <span class="sport-stat-value">Tous</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($sports)): ?>
            <div class="alert alert-info">
                <i class="fa-solid fa-info-circle"></i>
                Aucun sport disponible pour votre profil.
            </div>
        <?php endif; ?>

        <!-- Zone confirmation -->
        <form method="post" action="/sport" id="sport-form">
            <input type="hidden" name="sport_objectif_id" id="sport_objectif_id" value="">
            <input type="hidden" name="date_debut" id="date_debut" value="<?php echo esc($date_debut_regime ?? ''); ?>">

            <div class="confirm-zone">
                <div class="confirm-selected-info" id="confirm-info">
                    <div class="confirm-icon-preview" id="confirm-icon-preview">
                        <i class="fa-solid fa-hand-pointer" style="color:var(--text-muted);"></i>
                    </div>
                    <div>
                        <div class="confirm-placeholder" id="confirm-placeholder">Aucun sport sélectionné</div>
                        <div class="confirm-name" id="confirm-name" style="display:none;"></div>
                        <div class="confirm-sub"  id="confirm-sub"  style="display:none;">Sport sélectionné</div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" id="confirm-btn" disabled>
                    <i class="fa-solid fa-check"></i> Confirmer mon sport
                </button>
            </div>
        </form>

    </div>
</div>

<script>
(function () {
    const cards       = document.querySelectorAll('.sport-card');
    const input       = document.getElementById('sport_objectif_id');
    const btn         = document.getElementById('confirm-btn');
    const placeholder = document.getElementById('confirm-placeholder');
    const confirmName = document.getElementById('confirm-name');
    const confirmSub  = document.getElementById('confirm-sub');
    const iconPreview = document.getElementById('confirm-icon-preview');

    cards.forEach(card => {
        card.addEventListener('click', () => {
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
