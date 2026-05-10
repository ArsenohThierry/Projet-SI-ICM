<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Régime — NutriFit</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
    <link rel="icon" href="/assets/logo.png">
    <style>
        .regime-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--bg);
        }

        .regime-body {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.5rem;
        }

        .regime-wrapper {
            width: 100%;
            max-width: 800px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* En-tête de section */
        .regime-heading {
            text-align: center;
        }

        .regime-heading h1 {
            font-size: 1.9rem;
            margin-bottom: 0.4rem;
        }

        .regime-heading p {
            color: var(--text-muted);
            font-size: 0.92rem;
        }

        /* Grille des cartes régime */
        .regime-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }

        .regime-card {
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

        .regime-card:hover {
            border-color: var(--primary);
            background: var(--primary-light);
            transform: translateY(-3px);
            box-shadow: var(--shadow);
        }

        .regime-card.selected {
            border-color: var(--primary);
            background: var(--primary-light);
            box-shadow: 0 0 0 4px var(--primary-glow), var(--shadow);
        }

        .regime-check {
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

        .regime-card.selected .regime-check {
            display: flex;
        }

        .regime-icon {
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

        .regime-card:hover .regime-icon,
        .regime-card.selected .regime-icon {
            background: var(--primary);
            color: white;
        }

        .regime-label {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--text-primary);
            line-height: 1.3;
        }

        .regime-stats {
            font-size: 0.75rem;
            color: var(--text-muted);
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            width: 100%;
        }

        .regime-stat {
            display: flex;
            justify-content: space-between;
            padding: 0.3rem 0;
        }

        .regime-stat-label {
            color: var(--text-muted);
        }

        .regime-stat-value {
            font-weight: 600;
            color: var(--text-primary);
        }

        .regime-price {
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid var(--border);
            width: 100%;
            text-align: right;
        }

        .regime-price-amount {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--primary);
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

        .pricing-summary {
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

        .pricing-summary-values {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .pricing-summary-title {
            font-family: 'Sora', sans-serif;
            font-size: 1rem;
            font-weight: 800;
            color: var(--text-primary);
        }

        .pricing-summary-subtitle {
            font-size: 0.88rem;
            color: var(--text-muted);
        }

        .pricing-summary-gold {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--gold-dark);
        }

        .pricing-summary-highlight {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            width: fit-content;
            padding: 0.3rem 0.6rem;
            border-radius: 999px;
            background: var(--gold-light);
            color: var(--gold-dark);
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.02em;
        }
    </style>
</head>

<body class="regime-page">

    <?php $sessionUser = $user ?? []; ?>
    <?php $isGold = (bool) ($isGold ?? (session()->get('user_option') === 'gold')); ?>

    <!-- Navbar -->
    <header class="navbar" style="position:sticky; top:0; z-index:20; backdrop-filter:blur(14px);">
        <div class="navbar-left">
            <div style="display:flex; align-items:center; gap:10px;">
                <div class="logo-icon"><i class="fa-solid fa-leaf"></i></div>
                <span class="logo-text">Nutri<span>Fit</span></span>
            </div>
            <div>
                <div class="page-title">Régime</div>
                <div class="breadcrumb">NutriFit / <span>Choisir un régime</span></div>
            </div>
        </div>
        <div class="navbar-right">
            <div class="badge <?php echo $isGold ? 'badge-gold' : 'badge-green'; ?>" style="margin-right:0.25rem;">
                <i class="fa-solid <?php echo $isGold ? 'fa-crown' : 'fa-circle-check'; ?>"></i>
                <?php echo $isGold ? 'GOLD' : 'FREE'; ?>
            </div>
            <div class="navbar-avatar" title="<?php echo esc($sessionUser['prenom'] ?? ''); ?>">
                <?php echo strtoupper(
                    substr((string) ($sessionUser['prenom'] ?? 'J'), 0, 1) .
                    substr((string) ($sessionUser['nom'] ?? 'R'), 0, 1)
                ); ?>
            </div>
        </div>
    </header>

    <!-- Contenu -->
    <div class="regime-body">
        <div class="regime-wrapper anim-fade-up">

            <!-- En-tête -->
            <div class="regime-heading">
                <span class="badge badge-blue" style="margin-bottom:0.75rem;">
                    <i class="fa-solid fa-utensils"></i> Étape 2 sur 2
                </span>
                <h1>Quel régime choisir ?</h1>
                <p>Sélectionnez le régime le mieux adapté à votre objectif et votre budget.</p>
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

            <!-- Grille des régimes -->
            <?php
            $regimes = $regimes ?? [];

            $iconMap = [
                'deficit' => 'fa-arrow-trend-down',
                'proteine' => 'fa-dumbbell',
                'poisson' => 'fa-fish',
                'energie' => 'fa-fire',
                'force' => 'fa-bolt',
                'equilibre' => 'fa-scale-balanced',
                'hypercal' => 'fa-star',
            ];
            $defaultIcons = [
                'fa-leaf',
                'fa-apple-whole',
                'fa-carrot',
                'fa-egg',
                'fa-cheese',
                'fa-drumstick-bite',
                'fa-utensils'
            ];

            function getIconForRegime(string $nom, int $index, array $iconMap, array $defaults): string
            {
                $lower = mb_strtolower($nom);
                foreach ($iconMap as $key => $icon) {
                    if (str_contains($lower, $key))
                        return $icon;
                }
                return $defaults[$index % count($defaults)];
            }
            ?>

            <div class="regime-grid">
                <?php foreach ($regimes as $i => $regime):
                    $regimeId = (int) ($regime['id'] ?? 0);
                    $nom = htmlspecialchars($regime['nom'] ?? 'Régime ' . ($i + 1));
                    $icon = getIconForRegime($regime['nom'] ?? '', $i, $iconMap, $defaultIcons);
                    $viande = (float) ($regime['pourcentage_viande'] ?? 0);
                    $volaille = (float) ($regime['pourcentage_volaille'] ?? 0);
                    $poisson = (float) ($regime['pourcentage_poisson'] ?? 0);
                    $montant = (float) ($regime['montant'] ?? 0);
                    $variation = (float) ($regime['variation_poids'] ?? 0);
                    ?>
                    <div class="regime-card" data-id="<?php echo $regimeId; ?>" data-label="<?php echo $nom; ?>"
                        data-icon="<?php echo $icon; ?>" data-montant="<?php echo $montant; ?>">

                        <div class="regime-check"><i class="fa-solid fa-check"></i></div>

                        <div style="display: flex; align-items: center; gap: 0.75rem; width: 100%;">
                            <div class="regime-icon">
                                <i class="fa-solid <?php echo $icon; ?>"></i>
                            </div>
                            <div class="regime-label"><?php echo $nom; ?></div>
                        </div>

                        <div class="regime-stats">
                            <div class="regime-stat">
                                <span class="regime-stat-label"><i class="fa-solid fa-drumstick-bite"></i> Viande</span>
                                <span class="regime-stat-value"><?php echo number_format($viande, 0); ?>%</span>
                            </div>
                            <div class="regime-stat">
                                <span class="regime-stat-label"><i class="fa-solid fa-feather"></i> Volaille</span>
                                <span class="regime-stat-value"><?php echo number_format($volaille, 0); ?>%</span>
                            </div>
                            <div class="regime-stat">
                                <span class="regime-stat-label"><i class="fa-solid fa-fish"></i> Poisson</span>
                                <span class="regime-stat-value"><?php echo number_format($poisson, 0); ?>%</span>
                            </div>
                            <div class="regime-stat" style="color: var(--primary); font-weight: 600;">
                                <span class="regime-stat-label">Variation poids</span>
                                <span
                                    class="regime-stat-value"><?php echo ($variation > 0 ? '+' : '') . number_format($variation, 1); ?>
                                    kg/mois</span>
                            </div>
                        </div>

                        <div class="regime-price">
                            <span class="regime-price-amount"><?php echo number_format($montant, 2); ?> €/jour</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($regimes)): ?>
                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle"></i>
                    Aucun régime disponible pour l'objectif sélectionné.
                </div>
            <?php endif; ?>

            <div class="pricing-summary" id="pricing-summary">
                <div class="pricing-summary-values">
                    <div class="pricing-summary-title" id="pricing-summary-title">Sélectionnez un régime pour voir son
                        prix</div>
                    <div class="pricing-summary-subtitle" id="pricing-summary-subtitle">Le prix du régime s'affichera
                        ici après votre choix.</div>
                    <div id="pricing-summary-gold"><span class="pricing-summary-highlight">GOLD -15%</span> sur le prix journalier.</div>
                </div>
                <button type="button" class="btn btn-outline btn-lg" id="subscribe-btn">
                    <i class="fa-solid fa-crown"></i> 
                    <?php echo $isGold ? 'Changer d\'abonnement' : 'S\'abonner'; ?>
                </button>
            </div>

            <!-- Zone confirmation -->
            <form method="post" action="/regime" id="regime-form">
                <input type="hidden" name="regime_id" id="regime_id" value="">

                <div class="confirm-zone">
                    <div class="confirm-selected-info" id="confirm-info">
                        <div class="confirm-icon-preview" id="confirm-icon-preview">
                            <i class="fa-solid fa-hand-pointer" style="color:var(--text-muted);"></i>
                        </div>
                        <div>
                            <div class="confirm-placeholder" id="confirm-placeholder">Aucun régime sélectionné</div>
                            <div class="confirm-name" id="confirm-name" style="display:none;"></div>
                            <div class="confirm-sub" id="confirm-sub" style="display:none;">Régime sélectionné</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" id="confirm-btn" disabled>
                        <i class="fa-solid fa-check"></i> Confirmer mon régime
                    </button>
                </div>

                <!-- Date de début -->
                <div
                    style="background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.25rem 1.5rem; margin-top: 1rem;">
                    <label for="date_debut"
                        style="display: block; margin-bottom: 0.75rem; font-weight: 600; color: var(--text-primary);">
                        <i class="fa-solid fa-calendar-days"></i> Quand souhaitez-vous commencer le régime ?
                    </label>
                    <input type="date" name="date_debut" id="date_debut" required min="<?php echo date('Y-m-d'); ?>"
                        value="<?php echo date('Y-m-d'); ?>"
                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius); font-size: 0.95rem;">
                    <small style="color: var(--text-muted); display: block; margin-top: 0.5rem;">
                        <i class="fa-solid fa-info-circle"></i> Vous pouvez choisir une date à partir d'aujourd'hui.
                    </small>
                </div>
            </form>

            <div style="text-align:center; font-size:0.86rem; color:var(--text-muted); margin-top:1rem;">
                NB : Nous pouvons livrer tous ce dont vous avez besoin selon votre plan
            </div>

        </div>
    </div>

    <script>
        (function () {
            const cards = document.querySelectorAll('.regime-card');
            const input = document.getElementById('regime_id');
            const btn = document.getElementById('confirm-btn');
            const placeholder = document.getElementById('confirm-placeholder');
            const confirmName = document.getElementById('confirm-name');
            const confirmSub = document.getElementById('confirm-sub');
            const iconPreview = document.getElementById('confirm-icon-preview');
            const summaryTitle = document.getElementById('pricing-summary-title');
            const summarySubtitle = document.getElementById('pricing-summary-subtitle');
            const summaryGold = document.getElementById('pricing-summary-gold');
            const subscribeBtn = document.getElementById('subscribe-btn');

            cards.forEach(card => {
                card.addEventListener('click', () => {
                    // Reset toutes les cartes
                    cards.forEach(c => c.classList.remove('selected'));

                    // Sélectionner la carte cliquée
                    card.classList.add('selected');

                    const id = card.getAttribute('data-id');
                    const label = card.getAttribute('data-label');
                    const icon = card.getAttribute('data-icon');
                    const montant = parseFloat(card.getAttribute('data-montant') || '0');
                    const goldMontant = montant * 0.85;

                    input.value = id;
                    btn.disabled = false;

                    // Mise à jour de la zone de confirmation
                    placeholder.style.display = 'none';
                    confirmName.style.display = 'block';
                    confirmSub.style.display = 'block';
                    confirmName.textContent = label;
                    iconPreview.innerHTML = `<i class="fa-solid ${icon}" style="color:var(--primary-dark);"></i>`;
                    iconPreview.style.background = 'var(--primary-light)';

                    summaryTitle.textContent = `${label} : ${montant.toFixed(2)} €/jour`;
                    summarySubtitle.textContent = `Avec GOLD, ce régime passe à ${goldMontant.toFixed(2)} €/jour.`;
                    summaryGold.innerHTML = `<span class="pricing-summary-highlight">GOLD -15%</span> Prix remisé : ${goldMontant.toFixed(2)} €/jour`;
                });
            });

            // Redirection du bouton S'abonner/Changer d'abonnement
            subscribeBtn.addEventListener('click', () => {
                window.location.href = '/abonnement';
            });
        })();
    </script>

</body>

</html>