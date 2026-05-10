<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonnement — NutriFit</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
    <link rel="icon" href="/assets/logo.png">
    <style>
        .abonnement-body { min-height: calc(100vh - 72px); display:flex; align-items:center; justify-content:center; padding:2.5rem 1.5rem; }
        .abonnement-wrapper { width:100%; max-width:900px; display:flex; flex-direction:column; gap:1.25rem; }
        .abonnement-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:1rem; }
        .option-card { background:var(--bg-card); border:2px solid var(--border); border-radius:var(--radius-lg); padding:1.25rem; display:flex; flex-direction:column; gap:0.85rem; box-shadow:var(--shadow-sm); }
        .option-card.gold { border-color:var(--gold); background:linear-gradient(180deg,var(--gold-light),var(--bg-card)); }
        .balance-card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:1.25rem 1.5rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; }
        .balance-amount { font-family:'Sora',sans-serif; font-size:1.9rem; font-weight:900; color:var(--primary); }
    </style>
</head>
<body>

<?php $sessionUser = $user ?? []; ?>
<?php $options = $options ?? []; ?>
<?php $balance = (float) ($balance ?? 0); ?>
<?php $isGold = session()->get('user_option') === 'gold'; ?>
<?php $flashError = session()->getFlashdata('error'); ?>
<?php $flashSuccess = session()->getFlashdata('success'); ?>

<div id="pageContent">
    <div class="abonnement-body">
        <div class="abonnement-wrapper anim-fade-up">
            <?php if ($flashError): ?>
                <div class="toast error" style="position:relative;">
                    <i class="fa-solid fa-times-circle toast-icon"></i>
                    <span><?php echo esc((string) $flashError); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($flashSuccess): ?>
                <div class="toast success" style="position:relative;">
                    <i class="fa-solid fa-check-circle toast-icon"></i>
                    <span><?php echo esc((string) $flashSuccess); ?></span>
                </div>
            <?php endif; ?>

            <div class="balance-card">
                <div>
                    <div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:0.25rem;">Montant du compte</div>
                    <div class="balance-amount"><?php echo number_format($balance, 0, ',', ' '); ?> Ar</div>
                </div>
                <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                    <a href="/dashboard" class="btn btn-outline">
                        <i class="fa-solid fa-arrow-left"></i> Retour
                    </a>
                    <a href="/codes/redeem" class="btn btn-outline">
                        <i class="fa-solid fa-plus"></i> Crediter le compte
                    </a>
                </div>
            </div>

            <div class="abonnement-grid">
                <?php foreach ($options as $option): ?>
                    <?php
                        $libelle = (string) ($option['libelle'] ?? 'Option');
                        $montant = (float) ($option['montant'] ?? 0);
                        $isGoldOption = mb_strtolower($libelle) === 'gold';
                    ?>
                    <div class="option-card <?php echo $isGoldOption ? 'gold' : 'free'; ?>">
                        <div class="option-top">
                            <div class="option-title"><?php echo esc(strtoupper($libelle)); ?></div>
                            <?php if ($isGoldOption): ?>
                                <span class="badge badge-gold"><i class="fa-solid fa-crown"></i> -15%</span>
                            <?php else: ?>
                                <span class="badge badge-green"><i class="fa-solid fa-circle-check"></i> Inclus</span>
                            <?php endif; ?>
                        </div>

                        <div class="option-price"><?php echo number_format($montant, 0, ',', ' '); ?> Ar</div>
                        <div class="option-note">
                            <?php if ($isGoldOption): ?>
                                Abonnement GOLD actif ou disponible pour réduire le prix de vos régimes de 15%.
                            <?php else: ?>
                                Accès gratuit de base.
                            <?php endif; ?>
                        </div>

                        <?php if ($isGoldOption): ?>
                            <div style="font-size:0.8rem; color:var(--text-muted); line-height:1.4;">Si votre solde est insuffisant, vous serez redirigé vers la page de crédit.</div>
                        <?php endif; ?>

                        <?php if ($isGoldOption && !$isGold): ?>
                            <form method="post" action="/abonnement/gold">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fa-solid fa-crown"></i> S'abonner
                                </button>
                            </form>
                        <?php elseif ($isGoldOption && $isGold): ?>
                            <form method="post" action="/abonnement/goldLogin">
                                <button type="submit" class="btn btn-primary btn-block" disabled>
                                    <i class="fa-solid fa-crown"></i> Abonnement Actuel
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="text-align:center; font-size:0.86rem; color:var(--text-muted); margin-top:1rem;">NB : Nous pouvons livrer tous ce dont vous avez besoin selon votre plan</div>
        </div>
    </div>
</div>

<script src="/assets/js/app.js"></script>
<script src="/assets/js/layout.js"></script>
<script>
    if (typeof NF_LAYOUT !== 'undefined' && NF_LAYOUT.inject) {
        NF_LAYOUT.inject('abonnement', 'Abonnement', 'Options disponibles', <?php echo $balance ?? 0; ?>);
    }
    const mc = document.getElementById('mainContent');
    const content = document.getElementById('pageContent');
    if (mc && content) mc.appendChild(content);
</script>

</body>
</html>
