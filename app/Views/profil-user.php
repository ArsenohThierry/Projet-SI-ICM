<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil — NutriFit</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
    <style>
        .profile-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .profile-wrapper {
            width: 100%;
            max-width: 620px;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .avatar-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Sora', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            color: white;
            box-shadow: 0 8px 24px var(--primary-glow);
            flex-shrink: 0;
        }

        .info-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.9rem 0;
            border-bottom: 1px solid var(--border);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            background: var(--primary-light);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .info-label {
            font-size: 0.75rem;
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .info-value {
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-primary);
        }

        .stat-mini {
            flex: 1;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.1rem 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
            transition: var(--transition);
        }

        .stat-mini:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }

        .stat-mini-value {
            font-family: 'Sora', sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
            line-height: 1;
        }

        .stat-mini-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .stats-row {
            display: flex;
            gap: 1rem;
        }
    </style>
</head>

<body>

    <?php
    /* ============================================================
       VARIABLES À RELIER AU CONTROLLER
       $user = tableau associatif retourné par le DAO/Controller
       ============================================================ */
    $id             = $user['id'];
    $nom            = $user['nom'];
    $prenom         = $user['prenom'];
    $email          = $user['email'];
    $username       = $user['username'];
    $poids_initial  = $user['poids_initial'];   
    $genre          = $user['genre'];           
    $taille         = $user['taille'];    

    /* -- Calculs dérivés -- */
    $initiales = strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));
    $taille_m = $taille / 100;
    $imc = round($poids_initial / ($taille_m * $taille_m), 1);
    $genre_icon = ($genre === 'Femme') ? 'fa-venus' : 'fa-mars';
    $genre_badge = ($genre === 'Femme') ? 'badge-info' : 'badge-green';
    ?>

    <div class="profile-page">
        <div class="profile-wrapper anim-fade-up">

            <!-- Brand -->
            <div style="display:flex; align-items:center; gap:10px;">
                <div class="logo-icon">
                    <i class="fa-solid fa-leaf" style="color:white;"></i>
                </div>
                <span class="logo-text">Nutri<span>Fit</span></span>
            </div>

            <!-- Header Profil -->
            <div class="card">
                <div class="card-body" style="display:flex; align-items:center; gap:1.5rem;">
                    <div class="avatar-circle"><?php echo $initiales; ?></div>
                    <div style="flex:1;">
                        <div
                            style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap; margin-bottom:0.3rem;">
                            <h2 style="font-size:1.5rem;"><?php echo htmlspecialchars($prenom . ' ' . $nom); ?></h2>
                            <span class="badge <?php echo $genre_badge; ?>">
                                <i class="fa-solid <?php echo $genre_icon; ?>"></i>
                                <?php echo $genre; ?>
                            </span>
                        </div>
                        <div style="color:var(--text-muted); font-size:0.88rem;">
                            <i class="fa-solid fa-at"
                                style="margin-right:5px;"></i><?php echo htmlspecialchars($username); ?>
                        </div>
                        <div style="color:var(--text-muted); font-size:0.82rem; margin-top:3px;">
                            <i class="fa-solid fa-hashtag" style="margin-right:5px;"></i>ID : <?php echo $id; ?>
                        </div>
                    </div>
                    <a href="edit_profil.php" class="btn btn-outline btn-sm" style="flex-shrink:0;">
                        <i class="fa-solid fa-pen"></i> Modifier
                    </a>
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
            <div style="display:flex; gap:1rem;" class="delay-3 anim-fade-up">
                <a href="dashboard.php" class="btn btn-outline" style="flex:1;">
                    <i class="fa-solid fa-arrow-left"></i> Tableau de bord
                </a>
                <a href="logout.php" class="btn btn-danger" style="flex:1;">
                    <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                </a>
            </div>

        </div>
    </div>

</body>

</html>