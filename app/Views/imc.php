<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat IMC — NutriFit</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
    <style>
        .imc-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .imc-wrapper {
            width: 100%;
            max-width: 480px;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .imc-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 0.5rem;
        }

        .imc-result-value {
            font-family: 'Sora', sans-serif;
            font-size: 5rem;
            font-weight: 800;
            line-height: 1;
            color: var(--primary);
        }

        .imc-scale {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .imc-scale-bar {
            height: 10px;
            border-radius: 99px;
            background: linear-gradient(
                to right,
                #3B82F6 0%,        /* Maigreur */
                #18C97A 25%,       /* Normal */
                #F5A623 60%,       /* Surpoids */
                #E8445A 100%       /* Obésité */
            );
            position: relative;
        }

        .imc-scale-cursor {
            position: absolute;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: white;
            border: 3px solid var(--dark);
            box-shadow: var(--shadow);
            transition: left 0.6s cubic-bezier(0.4,0,0.2,1);
        }

        .imc-scale-labels {
            display: flex;
            justify-content: space-between;
            font-size: 0.72rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .imc-categories {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
        }

        .imc-category-item {
            text-align: center;
            padding: 0.6rem 0.25rem;
            border-radius: var(--radius);
            border: 1.5px solid var(--border);
            font-size: 0.72rem;
            font-weight: 600;
            font-family: 'Sora', sans-serif;
            color: var(--text-muted);
            background: var(--bg-card);
            transition: var(--transition);
        }

        .imc-category-item .cat-range {
            display: block;
            font-size: 0.65rem;
            font-weight: 400;
            font-family: 'DM Sans', sans-serif;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .imc-category-item.active-info    { border-color: #3B82F6; background: var(--info-light);    color: #3B82F6; }
        .imc-category-item.active-green   { border-color: var(--primary); background: var(--primary-light); color: var(--primary-dark); }
        .imc-category-item.active-gold    { border-color: var(--gold);    background: var(--gold-light);    color: var(--gold-dark); }
        .imc-category-item.active-danger  { border-color: var(--danger);  background: var(--danger-light);  color: var(--danger); }
    </style>
</head>
<body>

<?php $sessionUser = $user ?? []; ?>
<script>
    window.NF_USER = {
        prenom: "<?php echo esc($sessionUser['prenom'] ?? ''); ?>",
        nom: "<?php echo esc($sessionUser['nom'] ?? ''); ?>",
        email: "<?php echo esc($sessionUser['email'] ?? ''); ?>",
        role_user: "<?php echo esc($sessionUser['role_user'] ?? ''); ?>",
        gold: <?php echo session()->get('user_option') === 'gold' ? 'true' : 'false'; ?>
    };
</script>

<?php
    $hasResult = isset($imc);

    // Calcul de la catégorie IMC
    $categorie     = '';
    $badgeClass    = '';
    $activeClass   = '';
    $icone         = '';
    $cursorPercent = 0;
    $conseil       = '';

    if ($hasResult) {
        if ($imc < 18.5) {
            $categorie     = 'Maigreur';
            $badgeClass    = 'badge-info';
            $activeClass   = 'active-info';
            $icone         = 'fa-arrow-trend-down';
            $cursorPercent = max(2, ($imc / 18.5) * 20);
            $conseil       = 'Votre poids est inférieur à la normale. Un suivi nutritionnel est conseillé.';
        } elseif ($imc < 25) {
            $categorie     = 'Normal';
            $badgeClass    = 'badge-green';
            $activeClass   = 'active-green';
            $icone         = 'fa-circle-check';
            $cursorPercent = 20 + (($imc - 18.5) / 6.5) * 35;
            $conseil       = 'Votre poids est dans la plage normale. Continuez vos bonnes habitudes !';
        } elseif ($imc < 30) {
            $categorie     = 'Surpoids';
            $badgeClass    = 'badge-gold';
            $activeClass   = 'active-gold';
            $icone         = 'fa-triangle-exclamation';
            $cursorPercent = 55 + (($imc - 25) / 5) * 25;
            $conseil       = 'Un léger surpoids détecté. Adopter une alimentation équilibrée peut aider.';
        } else {
            $categorie     = 'Obésité';
            $badgeClass    = 'badge-red';
            $activeClass   = 'active-danger';
            $icone         = 'fa-circle-exclamation';
            $cursorPercent = min(98, 80 + (($imc - 30) / 10) * 18);
            $conseil       = 'Un suivi médical personnalisé est fortement recommandé.';
        }
    }
?>

<div id="pageContent">
    <div class="imc-page">
        <div class="imc-wrapper anim-fade-up">

        <?php if (session()->getFlashdata('error')): ?>
        <div class="toast error" style="position:relative; margin-bottom:1rem;">
            <i class="fa-solid fa-times-circle toast-icon"></i>
            <span><?php echo esc(session()->getFlashdata('error')); ?></span>
        </div>
        <?php endif; ?>

        <!-- Brand -->
        <div class="imc-brand">
            <div class="logo-icon" style="background: var(--primary);">
                <i class="fa-solid fa-leaf" style="color:white;"></i>
            </div>
            <span class="logo-text">Nutri<span>Fit</span></span>
        </div>


        <?php if ($hasResult): ?>
        <!-- Card resultat principal -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-weight-scale" style="color:var(--primary); margin-right:8px;"></i>Resultat de votre IMC</h3>
                <span class="badge <?php echo $badgeClass; ?>">
                    <i class="fa-solid <?php echo $icone; ?>"></i>
                    <?php echo $categorie; ?>
                </span>
            </div>
            <div class="card-body" style="display:flex; flex-direction:column; gap:1.5rem;">

                <!-- Valeur IMC -->
                <div style="display:flex; align-items:flex-end; gap:1rem;">
                    <div class="imc-result-value"><?php echo number_format($imc, 1); ?></div>
                    <div style="padding-bottom:0.6rem;">
                        <div style="font-family:'Sora',sans-serif; font-weight:700; font-size:0.85rem; color:var(--text-secondary);">kg/m²</div>
                        <div style="font-size:0.78rem; color:var(--text-muted);">Indice de Masse Corporelle</div>
                    </div>
                </div>

                <!-- Barre de graduation -->
                <div class="imc-scale">
                    <div class="imc-scale-bar">
                        <div class="imc-scale-cursor" style="left: <?php echo $cursorPercent; ?>%;"></div>
                    </div>
                    <div class="imc-scale-labels">
                        <span>< 18.5</span>
                        <span>18.5 – 24.9</span>
                        <span>25 – 29.9</span>
                        <span>≥ 30</span>
                    </div>
                </div>

                <!-- Categories -->
                <div class="imc-categories">
                    <div class="imc-category-item <?php echo ($imc < 18.5) ? 'active-info' : ''; ?>">
                        Maigreur
                        <span class="cat-range">< 18.5</span>
                    </div>
                    <div class="imc-category-item <?php echo ($imc >= 18.5 && $imc < 25) ? 'active-green' : ''; ?>">
                        Normal
                        <span class="cat-range">18.5 – 24.9</span>
                    </div>
                    <div class="imc-category-item <?php echo ($imc >= 25 && $imc < 30) ? 'active-gold' : ''; ?>">
                        Surpoids
                        <span class="cat-range">25 – 29.9</span>
                    </div>
                    <div class="imc-category-item <?php echo ($imc >= 30) ? 'active-danger' : ''; ?>">
                        Obésité
                        <span class="cat-range">≥ 30</span>
                    </div>
                </div>
            </div>

            <!-- Conseil -->
            <div class="card-footer" style="display:flex; align-items:center; gap:10px;">
                <i class="fa-solid fa-lightbulb" style="color:var(--primary); flex-shrink:0;"></i>
                <span style="font-size:0.85rem; color:var(--text-secondary);"><?php echo $conseil; ?></span>
            </div>
        </div>
        <?php endif; ?>

        </div>
    </div>
</div>

    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/layout.js"></script>
    <script>
        NF_LAYOUT.inject('imc', 'IMC', 'Calcul IMC');
        const mc = document.getElementById('mainContent');
        const content = document.getElementById('pageContent');
        if (mc && content) {
            mc.appendChild(content);
        }
    </script>

</body>
</html>