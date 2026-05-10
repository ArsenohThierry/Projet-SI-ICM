<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utiliser un code — NutriFit</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
    <link rel="icon" href="/assets/logo.png">
    <style>
        .redeem-page {
            min-height: 100vh;
            background: var(--bg);
        }

        .redeem-body {
            min-height: calc(100vh - 72px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.5rem;
        }

        .redeem-wrapper {
            width: 100%;
            max-width: 760px;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .redeem-card .card-header h3 {
            font-size: 1.15rem;
        }

        .redeem-card .form-label {
            font-size: 0.88rem;
            font-weight: 600;
        }

        .redeem-card .form-control {
            font-size: 0.88rem;
            padding: 0.75rem 0.9rem;
        }

        .redeem-card .form-actions .btn {
            font-size: 0.88rem;
            padding: 0.55rem 0.95rem;
        }
    </style>
</head>
<body class="redeem-page">

<?php $sessionUser = $user ?? []; ?>

<header class="navbar" style="position:sticky; top:0; z-index:20; backdrop-filter:blur(14px);">
    <div class="navbar-left">
        <div style="display:flex; align-items:center; gap:10px;">
            <div class="logo-icon"><i class="fa-solid fa-leaf"></i></div>
            <span class="logo-text">Nutri<span>Fit</span></span>
        </div>
        <div>
            <div class="page-title">Codes</div>
            <div class="breadcrumb">NutriFit / <span>Créditer le compte</span></div>
        </div>
    </div>
    <div class="navbar-right">
        <div class="navbar-avatar" title="<?php echo esc($sessionUser['prenom'] ?? ''); ?>">
            <?php echo strtoupper(
                substr((string) ($sessionUser['prenom'] ?? 'J'), 0, 1) .
                substr((string) ($sessionUser['nom'] ?? 'R'), 0, 1)
            ); ?>
        </div>
    </div>
</header>

<div class="redeem-body">
    <div class="redeem-wrapper anim-fade-up">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="toast error" style="position:relative;">
                <i class="fa-solid fa-times-circle toast-icon"></i>
                <span><?php echo esc((string) session()->getFlashdata('error')); ?></span>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="toast success" style="position:relative;">
                <i class="fa-solid fa-check-circle toast-icon"></i>
                <span><?php echo esc((string) session()->getFlashdata('success')); ?></span>
            </div>
        <?php endif; ?>

        <?php $errors = session()->getFlashdata('errors') ?? []; ?>
        <?php if (!empty($errors)): ?>
            <div class="toast warning" style="position:relative;">
                <i class="fa-solid fa-exclamation-triangle toast-icon"></i>
                <span><?php echo esc(implode(' | ', $errors)); ?></span>
            </div>
        <?php endif; ?>

        <div class="card redeem-card">
            <div class="card-header">
                <div class="header-title">
                    <div class="logo-icon" style="background: var(--primary);">
                        <i class="fa-solid fa-ticket" style="color:white;"></i>
                    </div>
                    <div>
                        <h2>Valider un code</h2>
                        <p class="text-muted">Saisissez votre code pour créditer votre compte.</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="/profile" class="btn btn-outline">Retour</a>
                </div>
            </div>

            <div class="card-body">
                <form method="post" action="/codes/redeem-register" class="form-stack">
                    <div class="form-group">
                        <label class="form-label">Code</label>
                        <input class="form-control" name="code" value="<?php echo esc(old('code')); ?>" required>
                    </div>
                    <div class="form-actions">
                        <a href="/abonnement" class="btn btn-outline">Annuler</a>
                        <button class="btn btn-primary" type="submit">Appliquer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>