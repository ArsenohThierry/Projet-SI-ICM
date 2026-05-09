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
            min-height: calc(100vh - 64px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.5rem;
            background: var(--bg);
        }

        .imc-wrapper {
            width: 100%;
            max-width: 560px;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        /* --- Hero IMC --- */
        .imc-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .imc-result-value {
            font-family: 'Sora', sans-serif;
            font-size: 5.5rem;
            font-weight: 800;
            line-height: 1;
            color: var(--primary);
            letter-spacing: -0.03em;
        }

        .imc-unit {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 0.88rem;
            color: var(--text-secondary);
            margin-bottom: 2px;
        }

        .imc-unit-sub {
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        /* --- Barre --- */
        .imc-scale { display: flex; flex-direction: column; gap: 0.6rem; }

        .imc-scale-bar {
            height: 12px;
            border-radius: 99px;
            background: linear-gradient(to right,
                #3B82F6 0%,
                #18C97A 25%,
                #F5A623 60%,
                #E8445A 100%
            );
            position: relative;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }

        .imc-scale-cursor {
            position: absolute;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: white;
            border: 3px solid var(--dark);
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
            transition: left 0.7s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .imc-scale-labels {
            display: flex;
            justify-content: space-between;
            font-size: 0.7rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* --- Catégories --- */
        .imc-categories {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
        }

        .imc-category-item {
            text-align: center;
            padding: 0.65rem 0.25rem;
            border-radius: var(--radius);
            border: 1.5px solid var(--border);
            font-size: 0.72rem;
            font-weight: 600;
            font-family: 'Sora', sans-serif;
            color: var(--text-muted);
            background: var(--bg-card);
            transition: var(--transition);
        }

        .cat-range {
            display: block;
            font-size: 0.63rem;
            font-weight: 400;
            font-family: 'DM Sans', sans-serif;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .active-info   { border-color:#3B82F6;        background:var(--info-light);    color:#3B82F6; }
        .active-green  { border-color:var(--primary);  background:var(--primary-light); color:var(--primary-dark); }
        .active-gold   { border-color:var(--gold);     background:var(--gold-light);    color:var(--gold-dark); }
        .active-danger { border-color:var(--danger);   background:var(--danger-light);  color:var(--danger); }

        .active-info   .cat-range,
        .active-green  .cat-range,
        .active-gold   .cat-range,
        .active-danger .cat-range { color: inherit; opacity: 0.7; }

        /* --- Conseil pill --- */
        .conseil-pill {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            background: var(--primary-light);
            border: 1px solid var(--primary);
            border-radius: var(--radius-lg);
            padding: 1rem 1.25rem;
        }
        .conseil-pill i { color: var(--primary); margin-top: 2px; flex-shrink: 0; }
        .conseil-pill span { font-size: 0.88rem; color: var(--text-secondary); line-height: 1.5; }
    </style>
</head>
<body>

<?php $sessionUser = $user ?? []; ?>

<script>
window.NF_USER = {
    prenom   : "<?php echo esc($sessionUser['prenom']    ?? ''); ?>",
    nom      : "<?php echo esc($sessionUser['nom']       ?? ''); ?>",
    email    : "<?php echo esc($sessionUser['email']     ?? ''); ?>",
    role_user: "<?php echo esc($sessionUser['role_user'] ?? ''); ?>",
    gold     : <?php echo session()->get('user_option') === 'gold' ? 'true' : 'false'; ?>
};
</script>

<?php
    $hasResult     = isset($imc);
    $categorie     = $badgeClass = $activeClass = $icone = $conseil = '';
    $cursorPercent = 0;

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

<!-- Navbar -->
<header class="navbar" style="position:sticky; top:0; z-index:20; backdrop-filter:blur(14px);">
    <div class="navbar-left">
        <div style="display:flex; align-items:center; gap:10px;">
            <div class="logo-icon"><i class="fa-solid fa-leaf"></i></div>
            <span class="logo-text">Nutri<span>Fit</span></span>
        </div>
        <div>
            <div class="page-title">IMC</div>
            <div class="breadcrumb">NutriFit / <span>Calcul IMC</span></div>
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
<div class="imc-page">
    <div class="imc-wrapper anim-fade-up" id="pageContent">

        <?php if (session()->getFlashdata('error')): ?>
            <div class="toast error" style="position:relative;">
                <i class="fa-solid fa-times-circle toast-icon"></i>
                <span><?php echo esc(session()->getFlashdata('error')); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($hasResult): ?>

            <!-- ① Carte résultat principale -->
            <div class="card delay-1 anim-fade-up">
                <div class="card-header">
                    <h3>
                        <i class="fa-solid fa-weight-scale" style="color:var(--primary); margin-right:8px;"></i>
                        Résultat de votre IMC
                    </h3>
                    <span class="badge <?php echo $badgeClass; ?>">
                        <i class="fa-solid <?php echo $icone; ?>"></i>
                        <?php echo $categorie; ?>
                    </span>
                </div>

                <div class="card-body" style="display:flex; flex-direction:column; gap:1.75rem;">

                    <!-- Valeur + unité -->
                    <div class="imc-hero">
                        <div style="display:flex; align-items:flex-end; gap:0.75rem;">
                            <div class="imc-result-value"><?php echo number_format($imc, 1); ?></div>
                            <div style="padding-bottom:0.75rem;">
                                <div class="imc-unit">kg/m²</div>
                                <div class="imc-unit-sub">Indice de Masse Corporelle</div>
                            </div>
                        </div>

                        <!-- Pictogramme contextuel -->
                        <div style="
                            width:72px; height:72px;
                            border-radius:var(--radius-lg);
                            display:flex; align-items:center; justify-content:center;
                            font-size:2rem;
                            background:var(--primary-light); color:var(--primary-dark);
                            flex-shrink:0;">
                            <i class="fa-solid <?php echo $icone; ?>"></i>
                        </div>
                    </div>

                    <!-- Barre de graduation -->
                    <div class="imc-scale">
                        <div style="font-size:0.75rem; font-weight:600; font-family:'Sora',sans-serif; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">
                            Échelle IMC
                        </div>
                        <div class="imc-scale-bar">
                            <div class="imc-scale-cursor" style="left:<?php echo $cursorPercent; ?>%;"></div>
                        </div>
                        <div class="imc-scale-labels">
                            <span>&lt; 18.5</span>
                            <span>18.5 – 24.9</span>
                            <span>25 – 29.9</span>
                            <span>≥ 30</span>
                        </div>
                    </div>

                    <!-- Catégories -->
                    <div class="imc-categories">
                        <div class="imc-category-item <?php echo ($imc < 18.5) ? 'active-info' : ''; ?>">
                            Maigreur <span class="cat-range">&lt; 18.5</span>
                        </div>
                        <div class="imc-category-item <?php echo ($imc >= 18.5 && $imc < 25) ? 'active-green' : ''; ?>">
                            Normal <span class="cat-range">18.5 – 24.9</span>
                        </div>
                        <div class="imc-category-item <?php echo ($imc >= 25 && $imc < 30) ? 'active-gold' : ''; ?>">
                            Surpoids <span class="cat-range">25 – 29.9</span>
                        </div>
                        <div class="imc-category-item <?php echo ($imc >= 30) ? 'active-danger' : ''; ?>">
                            Obésité <span class="cat-range">≥ 30</span>
                        </div>
                    </div>

                </div>

                <!-- Conseil -->
                <div class="card-footer">
                    <div class="conseil-pill">
                        <i class="fa-solid fa-lightbulb"></i>
                        <span><?php echo $conseil; ?></span>
                    </div>
                </div>
            </div>

            <!-- ② CTA Objectif -->
            <a href="/objectif" class="btn btn-primary btn-block btn-lg delay-2 anim-fade-up">
                <i class="fa-solid fa-bullseye"></i>
                Définir mon objectif
                <i class="fa-solid fa-arrow-right" style="margin-left:auto;"></i>
            </a>

            <!-- ③ Recalculer -->
            <a href="/imc" class="btn btn-outline btn-block delay-3 anim-fade-up">
                <i class="fa-solid fa-rotate-left"></i>
                Recalculer mon IMC
            </a>

        <?php else: ?>

            <!-- Aucun résultat : invite à calculer -->
            <div class="card anim-fade-up" style="text-align:center; padding:2.5rem 2rem;">
                <div style="font-size:3rem; margin-bottom:1rem;">⚖️</div>
                <h2 style="font-size:1.3rem; margin-bottom:0.5rem;">Aucun résultat disponible</h2>
                <p style="color:var(--text-muted); font-size:0.9rem; margin-bottom:1.5rem;">
                    Lancez le calcul de votre IMC pour obtenir votre résultat personnalisé.
                </p>
                <a href="/imc/calcul" class="btn btn-primary btn-lg">
                    <i class="fa-solid fa-calculator"></i> Calculer mon IMC
                </a>
            </div>

        <?php endif; ?>

    </div>
</div>

<script>
    NF_LAYOUT.inject('imc', 'IMC', 'Calcul IMC');
    const mc      = document.getElementById('mainContent');
    const content = document.getElementById('pageContent');
    if (mc && content) mc.appendChild(content);
</script>

</body>
</html>