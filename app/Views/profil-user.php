<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil — NutriFit</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
    <link rel="icon" href="/assets/logo.png">
    <style>
        .profile-page {
            min-height: auto;
            display: block;
            padding: 0.35rem 0 0;
        }

        .profile-wrapper {
            width: 100%;
            max-width: 100%;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .profile-header-main {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .profile-header-name {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            flex-wrap: wrap;
            margin-bottom: 0.45rem;
        }

        .profile-header-username {
            color: var(--text-muted);
            font-size: 0.88rem;
            margin-bottom: 0.2rem;
        }

        .profile-header-id {
            color: var(--text-muted);
            font-size: 0.82rem;
            margin-bottom: 0.65rem;
        }

        .avatar-circle {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Sora', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            box-shadow: 0 8px 24px var(--primary-glow);
            flex-shrink: 0;
        }

        .info-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius);
            background: var(--primary-light);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .info-label {
            font-size: 0.78rem;
            font-family: 'Sora', sans-serif;
            font-weight: 600;
            color: var(--text-muted);
        }

        .info-value {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .stat-mini {
            flex: 1;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            transition: var(--transition);
        }

        .stat-mini:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }

        .stat-mini-value {
            font-family: 'Sora', sans-serif;
            font-size: 1.35rem;
            font-weight: 800;
            line-height: 1;
        }

        .stat-mini-label {
            font-size: 0.82rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .stats-row {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .profile-actions {
            display: flex;
            gap: 1rem;
            margin-top: 0.1rem;
        }

        @media (max-width: 900px) {
            .stats-row {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <?php
    /* ============================================================
       VARIABLES À RELIER AU CONTROLLER
       $user = tableau associatif retourné par le DAO/Controller
       ============================================================ */
    $id             = $user['id'] ?? 0;
    $nom            = $user['nom'] ?? '';
    $prenom         = $user['prenom'] ?? '';
    $email          = $user['email'] ?? '';
    $username       = $user['username'] ?? '';
    $poids_initial  = $user['poids_initial'] ?? 0;
    $genre          = $user['genre'] ?? '';
    $taille         = $user['taille'] ?? 0;
    $role_user      = $user['role_user'] ?? '';

    /* -- Calculs dérivés -- */
    $initiales = strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));
    $taille_m = $taille / 100;
    $imc = round($poids_initial / ($taille_m * $taille_m), 1);
    $genre_icon = ($genre === 'Femme') ? 'fa-venus' : 'fa-mars';
    $genre_badge = ($genre === 'Femme') ? 'badge-info' : 'badge-green';
    ?>

    <script>
        window.NF_USER = {
            prenom: "<?php echo esc((string) ($prenom ?? '')); ?>",
            nom: "<?php echo esc((string) ($nom ?? '')); ?>",
            username: "<?php echo esc((string) ($username ?? session()->get('username') ?? '')); ?>",
            email: "<?php echo esc((string) ($email ?? '')); ?>",
            objectif_choisi: "<?php echo esc((string) ($user['objectif_choisi'] ?? '')); ?>",
            regime_choisi: "<?php echo esc((string) ($user['regime_choisi'] ?? '')); ?>",
            sport_choisi: "<?php echo esc((string) ($user['sport_choisi'] ?? '')); ?>",
            role_user: "<?php echo esc((string) ($role_user ?? '')); ?>",
            gold: <?php echo session()->get('user_option') === 'gold' ? 'true' : 'false'; ?>
        };
    </script>

        <div id="pageContent">
            <div class="page-content">
            <div class="page-header">
                <div class="header-title">
                    <div class="logo-icon" style="background: var(--primary);">
                        <i class="fa-solid fa-user" style="color:white;"></i>
                    </div>
                    <div>
                        <h2>Mon profil</h2>
                        <p class="text-muted">Informations personnelles et sante.</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="/codes/redeem" class="btn btn-outline btn-sm">Codes</a>
                    <a href="/imc" class="btn btn-primary btn-sm">IMC</a>
                </div>
            </div>

            <div class="profile-page">
                <div class="profile-wrapper anim-fade-up">

            <!-- Header Profil -->
            <div class="card">
                <div class="card-body profile-header-main">
                    <div class="avatar-circle"><?php echo $initiales; ?></div>
                    <div style="flex:1;">
                        <div class="profile-header-name">
                            <h2 style="font-size:1.15rem;"><?php echo htmlspecialchars($prenom . ' ' . $nom); ?></h2>
                            <span class="badge <?php echo $genre_badge; ?>">
                                <i class="fa-solid <?php echo $genre_icon; ?>"></i>
                                <?php echo $genre; ?>
                            </span>
                        </div>
                        <div class="profile-header-username">
                            <i class="fa-solid fa-at"
                                style="margin-right:5px;"></i><?php echo htmlspecialchars($username); ?>
                        </div>
                        <div class="profile-header-id">
                            <i class="fa-solid fa-hashtag" style="margin-right:5px;"></i>ID : <?php echo $id; ?>
                        </div>
                        <a href="edit_profil.php" class="btn btn-outline btn-sm" style="flex-shrink:0;">
                            <i class="fa-solid fa-pen"></i> Modifier
                        </a>
                    </div>
                </div>
            </div>

                <!-- Stats rapides -->
                <div class="stats-row delay-1 anim-fade-up">
                    <div class="stat-mini">
                        <div class="stat-mini-value" style="color:var(--primary);">
                            <?php echo number_format($poids_initial, 1); ?> <span
                                style="font-size:1rem; font-weight:500; color:var(--text-muted);">kg</span>
                        </div>
                        <div class="stat-mini-label"><i class="fa-solid fa-weight-scale" style="margin-right:4px;"></i>Poids
                            initial</div>
                    </div>
                    <div class="stat-mini">
                        <div class="stat-mini-value" style="color:var(--info);">
                            <?php echo number_format($taille, 0); ?> <span
                                style="font-size:1rem; font-weight:500; color:var(--text-muted);">cm</span>
                        </div>
                        <div class="stat-mini-label"><i class="fa-solid fa-ruler-vertical"
                                style="margin-right:4px;"></i>Taille</div>
                    </div>
                    <div class="stat-mini">
                        <div class="stat-mini-value" style="color:var(--gold);">
                            <?php echo $imc; ?>
                        </div>
                        <div class="stat-mini-label"><i class="fa-solid fa-calculator" style="margin-right:4px;"></i>IMC
                            calculé</div>
                    </div>
                </div>

                <!-- Informations détaillées -->
                <div class="card delay-2 anim-fade-up">
                    <div class="card-header">
                        <h3><i class="fa-solid fa-address-card"
                                style="color:var(--primary); margin-right:8px;"></i>Informations personnelles</h3>
                    </div>
                    <div class="card-body" style="padding-top:0.25rem; padding-bottom:0.25rem;">

                        <div class="info-row">
                            <div class="info-icon"><i class="fa-solid fa-user"></i></div>
                            <div style="flex:1;">
                                <div class="info-label">Prénom</div>
                                <div class="info-value"><?php echo htmlspecialchars($prenom); ?></div>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-icon"><i class="fa-solid fa-user-tag"></i></div>
                            <div style="flex:1;">
                                <div class="info-label">Nom</div>
                                <div class="info-value"><?php echo htmlspecialchars($nom); ?></div>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-icon"><i class="fa-solid fa-at"></i></div>
                            <div style="flex:1;">
                                <div class="info-label">Nom d'utilisateur</div>
                                <div class="info-value"><?php echo htmlspecialchars($username); ?></div>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
                            <div style="flex:1;">
                                <div class="info-label">Email</div>
                                <div class="info-value"><?php echo htmlspecialchars($email); ?></div>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-icon"><i class="fa-solid <?php echo $genre_icon; ?>"></i></div>
                            <div style="flex:1;">
                                <div class="info-label">Genre</div>
                                <div class="info-value">
                                    <span class="badge <?php echo $genre_badge; ?>">
                                        <i class="fa-solid <?php echo $genre_icon; ?>"></i>
                                        <?php echo $genre; ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-icon" style="background:var(--info-light); color:var(--info);">
                                <i class="fa-solid fa-ruler-vertical"></i>
                            </div>
                            <div style="flex:1;">
                                <div class="info-label">Taille</div>
                                <div class="info-value"><?php echo number_format($taille, 1); ?> cm</div>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-icon" style="background:var(--gold-light); color:var(--gold-dark);">
                                <i class="fa-solid fa-weight-scale"></i>
                            </div>
                            <div style="flex:1;">
                                <div class="info-label">Poids initial</div>
                                <div class="info-value"><?php echo number_format($poids_initial, 1); ?> kg</div>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-icon" style="background:var(--danger-light); color:var(--danger);">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <div style="flex:1;">
                                <div class="info-label">Mot de passe</div>
                                <div class="info-value" style="letter-spacing:0.2em; color:var(--text-muted);">••••••••
                                </div>
                            </div>
                            <a href="change_password.php" class="btn btn-sm btn-outline">Changer</a>
                        </div>

                    </div>
                </div>

                <!-- Actions -->
                <div class="profile-actions delay-3 anim-fade-up">

                    <a href="/profile/export-pdf" class="btn btn-outline" style="flex:1;">
                        <i class="fa-solid fa-file-pdf"></i> Exporter en PDF
                    </a>

                    <a href="/logout" class="btn btn-danger" style="flex:1;">
                        <i class="fa-solid fa-right-from-bracket"></i> Deconnexion
                    </a>
                </div>

            </div>
        </div>
    </div>
    </div>

    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/layout.js"></script>
    <script>
        NF_LAYOUT.inject('profile', 'Profil', 'Mon compte');
        const mc = document.getElementById('mainContent');
        const content = document.getElementById('pageContent');
        if (mc && content) {
            mc.appendChild(content);
        }
    </script>

</body>

</html>
