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

      <!-- Tab Switcher -->
      <div class="auth-tabs">
        <div class="auth-tab active" id="tab-login" onclick="switchTab('login')">Connexion</div>
        <div class="auth-tab" id="tab-register" onclick="switchTab('register')">Inscription</div>
      </div>

      <!-- ─── LOGIN PANEL ─── -->
      <div id="panel-login" class="auth-panel active">
        <div class="auth-header" style="margin-bottom:0">
          <h2>Bon retour ! </h2>
          <p>Connectez-vous à votre espace NutriFit.</p>
        </div>

        <div class="form-group">
          <label class="form-label">Nom d'utilisateur</label>
          <div class="input-wrapper">
            <i class="fa-solid fa-user input-icon"></i>
            <input type="text" class="form-control" id="loginUsername" placeholder="votre_pseudo" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Mot de passe</label>
          <div class="input-wrapper">
            <i class="fa-solid fa-lock input-icon"></i>
            <input type="password" class="form-control" id="loginPassword" placeholder="••••••••" required>
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

        <button class="btn btn-primary btn-block btn-lg" onclick="handleLogin()" style="margin-top:0.25rem">
          <i class="fa-solid fa-arrow-right-to-bracket"></i> Se connecter
        </button>

        <div class="auth-link">
          Pas encore de compte ? <a href="#" onclick="switchTab('register')">S'inscrire gratuitement</a>
        </div>
      </div>

      <!-- ─── REGISTER PANEL ─── -->
      <div id="panel-register" class="auth-panel">

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
                <input type="text" class="form-control" id="reg-nom" placeholder="Rakoto" required>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Prénom</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-user input-icon"></i>
                <input type="text" class="form-control" id="reg-prenom" placeholder="Jean" required>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Nom d'utilisateur</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-at input-icon"></i>
              <input type="text" class="form-control" id="reg-username" placeholder="jean_rakoto" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Email</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-envelope input-icon"></i>
              <input type="email" class="form-control" id="reg-email" placeholder="jean@exemple.mg" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Mot de passe</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-lock input-icon"></i>
              <input type="password" class="form-control" id="reg-password" placeholder="Min. 8 caractères" required>
              <button class="input-action toggle-password" type="button">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
            <span class="form-hint">Au moins 8 caractères avec lettres et chiffres</span>
          </div>

          <button class="btn btn-primary btn-block btn-lg" onclick="goToStep2()">
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
                <input type="number" class="form-control" id="reg-taille" placeholder="170" min="100" max="250"
                  required>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Poids (kg)</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-weight-scale input-icon"></i>
                <input type="number" class="form-control" id="reg-poids" placeholder="72" min="20" max="300" required>
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Âge</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-cake-candles input-icon"></i>
                <input type="number" class="form-control" id="reg-age" placeholder="28" min="10" max="120" required>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Genre</label>
              <div class="gender-row">
                <input type="radio" name="genre" id="genre-h" value="homme" class="gender-opt" checked>
                <label for="genre-h" class="gender-card">
                  <i class="fa-solid fa-mars" style="color:#3B82F6"></i> Homme
                </label>
                <input type="radio" name="genre" id="genre-f" value="femme" class="gender-opt">
                <label for="genre-f" class="gender-card">
                  <i class="fa-solid fa-venus" style="color:#E84E8A"></i> Femme
                </label>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Mon objectif</label>
            <div class="objective-grid">
              <div>
                <input type="radio" name="objectif" id="obj-prendre" value="prendre" class="objective-opt" checked>
                <label for="obj-prendre" class="objective-card">
                  <div class="objective-icon" style="color:#3B82F6"><i class="fa-solid fa-arrow-up"></i></div>
                  <span class="objective-label">Prendre du poids</span>
                </label>
              </div>
              <div>
                <input type="radio" name="objectif" id="obj-perdre" value="perdre" class="objective-opt">
                <label for="obj-perdre" class="objective-card">
                  <div class="objective-icon" style="color:#E8445A"><i class="fa-solid fa-arrow-down"></i></div>
                  <span class="objective-label">Perdre du poids</span>
                </label>
              </div>
              <div>
                <input type="radio" name="objectif" id="obj-imc" value="imc" class="objective-opt">
                <label for="obj-imc" class="objective-card">
                  <div class="objective-icon" style="color:#18C97A"><i class="fa-solid fa-bullseye"></i></div>
                  <span class="objective-label">IMC idéal</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex gap-3" style="margin-top:0.5rem">
            <button class="btn btn-outline" onclick="goToStep1()" style="flex:1">
              <i class="fa-solid fa-arrow-left"></i> Retour
            </button>
            <button class="btn btn-primary" onclick="handleRegister()" style="flex:2">
              <i class="fa-solid fa-user-plus"></i> Créer mon compte
            </button>
          </div>
        </div>
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

    /* ── Login Handler ── */
    function handleLogin() {
      const u = document.getElementById('loginUsername').value.trim();
      const p = document.getElementById('loginPassword').value;
      if (!u || !p) { Toast.show('Veuillez remplir tous les champs.', 'error'); return; }

      // Demo: accept any credentials
      const user = Storage.get('nf_user', null) || {
        nom: 'Rakoto', prenom: 'Jean', username: u,
        email: 'jean@exemple.mg', poids: 78, taille: 175, age: 28,
        genre: 'homme', objectif: 'perdre', gold: false
      };
      Storage.setUser(user);
      Toast.show('Connexion réussie ! Bienvenue ' + (user.prenom || u) + ' ', 'success');
      setTimeout(() => window.location.href = 'dashboard.html', 1200);
    }

    /* ── Register Handler ── */
    function handleRegister() {
      const taille = document.getElementById('reg-taille').value;
      const poids = document.getElementById('reg-poids').value;
      const age = document.getElementById('reg-age').value;
      if (!taille || !poids || !age) { Toast.show('Veuillez remplir tous les champs physiques.', 'error'); return; }

      const user = {
        nom: document.getElementById('reg-nom').value,
        prenom: document.getElementById('reg-prenom').value,
        username: document.getElementById('reg-username').value,
        email: document.getElementById('reg-email').value,
        taille: +taille, poids: +poids, age: +age,
        genre: document.querySelector('[name="genre"]:checked')?.value || 'homme',
        objectif: document.querySelector('[name="objectif"]:checked')?.value || 'imc',
        gold: false,
        createdAt: new Date().toISOString()
      };
      Storage.setUser(user);
      Toast.show('Compte créé avec succès ! 🎉', 'success');
      setTimeout(() => window.location.href = 'dashboard.html', 1200);
    }

    /* Init password toggles on load */
    document.addEventListener('DOMContentLoaded', () => { initPasswordToggles(); });
  </script>

</body>

</html>