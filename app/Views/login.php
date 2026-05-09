<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NutriFit — Connexion & Inscription</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/login.css">
  <link rel="stylesheet" href="/assets/css/all.min.css">
</head>

<body>

  <!-- ========== AUTH PAGE ========== -->
  <div class="auth-page">

    <!-- Left — Visual Panel -->
    <div class="auth-visual">
      <div class="auth-visual-bg"></div>
      <div class="auth-visual-orb orb-1"></div>
      <div class="auth-visual-orb orb-2"></div>
      <div class="auth-visual-orb orb-3"></div>

      <!-- Logo -->
      <div class="auth-visual-logo">
        <div class="logo-icon"><i class="fa-solid fa-leaf"></i></div>
        <span class="logo-text">Nutri<span>Fit</span></span>
      </div>

      <!-- Content -->
      <div class="auth-visual-content">
        <h1>Votre santé,<br><em>notre priorité.</em></h1>
        <p>Suivez votre nutrition, vos activités<br>et atteignez vos objectifs santé.</p>
        <div class="auth-features">
          <div class="auth-feature"><i class="fa-solid fa-check"></i> Suivi IMC & poids personnalisé</div>
          <div class="auth-feature"><i class="fa-solid fa-check"></i> Régimes alimentaires sur mesure</div>
          <div class="auth-feature"><i class="fa-solid fa-check"></i> Activités sportives guidées</div>
          <div class="auth-feature"><i class="fa-solid fa-check"></i> Abonnement Gold — 15% de réduction</div>
        </div>
      </div>
    </div>

    <!-- Right — Form Panel -->
    <div class="auth-form-section">

      <?php if (session()->getFlashdata('error')): ?>
        <div class="toast error" style="position:relative; margin-bottom:1rem;">
          <i class="fa-solid fa-times-circle toast-icon"></i>
          <span><?php echo esc(session()->getFlashdata('error')); ?></span>
        </div>
      <?php endif; ?>

      <?php $errors = session()->getFlashdata('errors') ?? []; ?>
      <?php if (!empty($errors)): ?>
        <div class="toast warning" style="position:relative; margin-bottom:1rem;">
          <i class="fa-solid fa-exclamation-triangle toast-icon"></i>
          <span><?php echo esc(implode(' | ', $errors)); ?></span>
        </div>
      <?php endif; ?>

      <!-- Tab Switcher -->
      <div class="auth-tabs">
        <div class="auth-tab active" id="tab-login" onclick="switchTab('login')">Connexion</div>
        <div class="auth-tab" id="tab-register" onclick="switchTab('register')">Inscription</div>
      </div>

      <!-- ─── LOGIN PANEL ─── -->
      <div id="panel-login" class="auth-panel active">
        <form method="post" action="/login">
        <div class="auth-header" style="margin-bottom:0">
          <h2>Bon retour ! </h2>
          <p>Connectez-vous à votre espace NutriFit.</p>
        </div>

        <div class="form-group">
          <label class="form-label">Nom d'utilisateur</label>
          <div class="input-wrapper">
            <i class="fa-solid fa-user input-icon"></i>
            <input type="text" class="form-control" id="loginUsername" name="username" placeholder="votre_pseudo" value="<?php echo esc(old('username')); ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Mot de passe</label>
          <div class="input-wrapper">
            <i class="fa-solid fa-lock input-icon"></i>
            <input type="password" class="form-control" id="loginPassword" name="password" placeholder="••••••••" required>
            <button class="input-action toggle-password" type="button">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
        </div>

        <div class="flex justify-between items-center" style="margin-top:-0.25rem">
          <label
            style="display:flex;align-items:center;gap:8px;font-size:0.85rem;color:var(--text-secondary);cursor:pointer">
            <input type="checkbox" style="accent-color:var(--primary)"> Se souvenir de moi
          </label>
          <a href="#" style="font-size:0.83rem;color:var(--primary);font-weight:600">Mot de passe oublié ?</a>
        </div>

        <button class="btn btn-primary btn-block btn-lg" type="submit" style="margin-top:0.25rem">
          <i class="fa-solid fa-arrow-right-to-bracket"></i> Se connecter
        </button>

        <div class="auth-link">
          Pas encore de compte ? <a href="#" onclick="switchTab('register')">S'inscrire gratuitement</a>
        </div>
        </form>
      </div>

      <!-- ─── REGISTER PANEL ─── -->
      <div id="panel-register" class="auth-panel">
        <form method="post" action="/register" id="register-form">

        <!-- Step Indicator -->
        <div class="step-indicator">
          <div class="step-item">
            <div class="step-num done" id="step-num-1"><i class="fa-solid fa-check" style="font-size:0.65rem"></i></div>
            <div>
              <div class="step-label active" id="step-lbl-1">Infos perso</div>
            </div>
          </div>
          <div class="step-line" id="step-line-1"></div>
          <div class="step-item">
            <div class="step-num" id="step-num-2">2</div>
            <div>
              <div class="step-label" id="step-lbl-2">Infos physiques</div>
            </div>
          </div>
        </div>

        <!-- Step 1: Personal info -->
        <div id="reg-step-1" class="register-steps">
          <div class="auth-header" style="margin-bottom:0.5rem">
            <h2>Créez votre compte</h2>
            <p>Étape 1 — Informations personnelles</p>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Nom</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-user input-icon"></i>
                <input type="text" class="form-control" id="reg-nom" name="nom" placeholder="Rakoto" value="<?php echo esc(old('nom')); ?>" required>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Prénom</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-user input-icon"></i>
                <input type="text" class="form-control" id="reg-prenom" name="prenom" placeholder="Jean" value="<?php echo esc(old('prenom')); ?>" required>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Nom d'utilisateur</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-at input-icon"></i>
              <input type="text" class="form-control" id="reg-username" name="username" placeholder="jean_rakoto" value="<?php echo esc(old('username')); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Email</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-envelope input-icon"></i>
              <input type="email" class="form-control" id="reg-email" name="email" placeholder="jean@exemple.mg" value="<?php echo esc(old('email')); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Mot de passe</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-lock input-icon"></i>
              <input type="password" class="form-control" id="reg-password" name="password" placeholder="Min. 8 caractères" required>
              <button class="input-action toggle-password" type="button">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
            <span class="form-hint">Au moins 8 caractères avec lettres et chiffres</span>
          </div>

            <button class="btn btn-primary btn-block btn-lg" type="button" onclick="goToStep2()">
            Suivant <i class="fa-solid fa-arrow-right"></i>
          </button>

          <div class="auth-link">
            Déjà un compte ? <a href="#" onclick="switchTab('login')">Se connecter</a>
          </div>
        </div>

        <!-- Step 2: Physical info -->
        <div id="reg-step-2" class="register-steps" style="display:none;flex-direction:column;gap:1rem">
          <div class="auth-header" style="margin-bottom:0.5rem">
            <h2>Votre profil santé</h2>
            <p>Étape 2 — Informations physiques</p>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Taille (cm)</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-ruler-vertical input-icon"></i>
                <input type="number" class="form-control" id="reg-taille" name="taille" placeholder="170" min="100" max="250"
                  value="<?php echo esc(old('taille')); ?>" required>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Poids (kg)</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-weight-scale input-icon"></i>
                <input type="number" class="form-control" id="reg-poids" name="poids_initial" placeholder="72" min="20" max="300" value="<?php echo esc(old('poids_initial')); ?>" required>
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Âge</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-cake-candles input-icon"></i>
                <input type="number" class="form-control" id="reg-age" name="age" placeholder="28" min="10" max="120" value="<?php echo esc(old('age')); ?>" required>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Genre</label>
              <div class="gender-row">
                <input type="radio" name="genre" id="genre-h" value="Homme" class="gender-opt" <?php echo old('genre', 'Homme') === 'Homme' ? 'checked' : ''; ?>>
                <label for="genre-h" class="gender-card">
                  <i class="fa-solid fa-mars" style="color:#3B82F6"></i> Homme
                </label>
                <input type="radio" name="genre" id="genre-f" value="Femme" class="gender-opt" <?php echo old('genre') === 'Femme' ? 'checked' : ''; ?>>
                <label for="genre-f" class="gender-card">
                  <i class="fa-solid fa-venus" style="color:#E84E8A"></i> Femme
                </label>
              </div>
            </div>
          </div>

          <div class="flex gap-3" style="margin-top:0.5rem">
            <button class="btn btn-outline" type="button" onclick="goToStep1()" style="flex:1">
              <i class="fa-solid fa-arrow-left"></i> Retour
            </button>
            <button class="btn btn-primary" type="submit" style="flex:2">
              <i class="fa-solid fa-user-plus"></i> Créer mon compte
            </button>
          </div>
        </div>
        </form>
      </div>
      <!-- end register panel -->

    </div>
    <!-- end form section -->

  </div>

  <script src="../assets/js/app.js"></script>
  <script>
    /* ── Tab Switching ── */
    function switchTab(tab) {
      ['login', 'register'].forEach(t => {
        document.getElementById(`tab-${t}`).classList.toggle('active', t === tab);
        document.getElementById(`panel-${t}`).classList.toggle('active', t === tab);
      });
    }

    /* ── Registration Steps ── */
    function goToStep2() {
      const fields = ['reg-nom', 'reg-prenom', 'reg-username', 'reg-email', 'reg-password'];
      const empty = fields.some(id => !document.getElementById(id).value.trim());
      if (empty) { Toast.show('Veuillez remplir tous les champs.', 'error'); return; }

      const pwd = document.getElementById('reg-password').value;
      if (pwd.length < 8) { Toast.show('Le mot de passe doit contenir au moins 8 caractères.', 'error'); return; }

      document.getElementById('reg-step-1').style.display = 'none';
      const s2 = document.getElementById('reg-step-2');
      s2.style.display = 'flex';
      s2.style.animation = 'fadeInUp 0.3s ease';

      // Update step indicator
      document.getElementById('step-num-2').classList.add('active');
      document.getElementById('step-lbl-2').classList.add('active');
      document.getElementById('step-line-1').classList.add('done');
      initPasswordToggles();
    }

    function goToStep1() {
      document.getElementById('reg-step-2').style.display = 'none';
      document.getElementById('reg-step-1').style.display = 'flex';
      document.getElementById('reg-step-1').style.flexDirection = 'column';
      document.getElementById('reg-step-1').style.animation = 'fadeInUp 0.3s ease';
      document.getElementById('step-num-2').classList.remove('active');
      document.getElementById('step-lbl-2').classList.remove('active');
      document.getElementById('step-line-1').classList.remove('done');
    }

    /* Init password toggles on load */
    document.addEventListener('DOMContentLoaded', () => {
      initPasswordToggles();
      const authTab = '<?php echo esc(session()->getFlashdata('auth_tab') ?? 'login'); ?>';
      if (authTab === 'register') {
        switchTab('register');
        if (document.getElementById('reg-taille').value || document.getElementById('reg-poids').value) {
          goToStep2();
        }
      }
    });
  </script>

</body>

</html>