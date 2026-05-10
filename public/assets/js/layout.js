/* ============================================================
   NutriFit — Shared Layout Component
   ============================================================ */

const NF_LAYOUT = {
  getUser() {
    if (typeof window !== 'undefined' && window.NF_USER) return window.NF_USER;
    if (typeof Storage !== 'undefined') return Storage.getUser() || null;
    return null;
  },

  sidebar(activePage) {
    const user = this.getUser() || { username: 'utilisateur', email: '', gold: false };
    const username = user.username || (user.email ? user.email.split('@')[0] : `${user.prenom || ''} ${user.nom || ''}`.trim()) || 'Utilisateur';
    const email = user.email || '';
    const initials = `${(user.nom || '')[0] || ''}${(user.prenom || '')[0] || ''}`.toUpperCase() || username[0].toUpperCase();
    const isAdmin = user.role_user === 'admin';

    const navItems = isAdmin ? [
      { id: 'admin-regimes', icon: 'fa-bowl-food', label: 'Régimes alimentaires', href: '/admin/regimes' },
      { id: 'admin-sports', icon: 'fa-dumbbell', label: 'Activités sportives', href: '/admin/sports' },
      { id: 'admin-codes', icon: 'fa-ticket', label: 'Codes', href: '/admin/codes' },
      { id: 'profile', icon: 'fa-user', label: 'Profil', href: '/profile' }
    ] : [
      { id: 'dashboard', icon: 'fa-gauge', label: 'Tableau de bord', href: '/dashboard' },
      { id: 'imc', icon: 'fa-calculator', label: 'IMC', href: '/imc' },
      { id: 'programme', icon: 'fa-bullseye', label: 'programme', href: '/programme' },
      { id: 'profile', icon: 'fa-user', label: 'Profil', href: '/profile' },
      { id: 'codes', icon: 'fa-ticket', label: 'Codes', href: '/codes/redeem' }
    ];

    const links = navItems.map(n => `
      <a href="${n.href}" class="nav-item ${activePage === n.id ? 'active' : ''}">
        <i class="fa-solid ${n.icon} nav-icon"></i>
        ${n.label}
        ${n.badge ? `<span class="nav-badge">${n.badge}</span>` : ''}
        ${n.gold && !activePage.includes('abonnement') ? '<span class="nav-badge" style="background:var(--gold);color:white">PRO</span>' : ''}
      </a>
    `).join('');

    return `
      <div class="sidebar-overlay"></div>
      <aside class="sidebar">
        <div class="sidebar-logo">
          <div class="logo-icon"><i class="fa-solid fa-leaf"></i></div>
          <span class="logo-text">Nutri<span>Fit</span></span>
        </div>

        <nav class="sidebar-nav">
          <div class="nav-section-title">Menu</div>
          ${links}
          <div class="nav-section-title" style="margin-top:1rem">Compte</div>
          <a href="/logout" class="nav-item">
            <i class="fa-solid fa-right-from-bracket nav-icon"></i> Déconnexion
          </a>
        </nav>

        <div class="sidebar-footer">
          <div class="sidebar-user">
            <div class="user-avatar">${initials}</div>
            <div>
              <div class="user-name">${username}</div>
              <div class="user-role">${email}</div>
            </div>
            ${user.gold ? '<span class="gold-tag"><i class="fa-solid fa-crown"></i> GOLD</span>' : ''}
          </div>
        </div>
      </aside>
    `;
  },

  navbar(title, subtitle) {
    const user = this.getUser() || { prenom: 'Jean', nom: 'Rakoto' };
    const initials = `${(user.nom || '')[0] || ''}${(user.prenom || '')[0] || ''}`.toUpperCase() || 'JR';
    const balance = Number(user.balance || 0);
    const balanceFormatted = (isNaN(balance) ? '0' : balance.toLocaleString('fr-FR', { maximumFractionDigits: 0 }));

    return `
      <header class="navbar">
        <div class="navbar-left">
          <button class="hamburger"><i class="fa-solid fa-bars"></i></button>
          <div>
            <div class="page-title">${title}</div>
            ${subtitle ? `<div class="breadcrumb">NutriFit / <span>${subtitle}</span></div>` : ''}
          </div>
        </div>
        <div class="navbar-right">
          <button class="nav-btn" data-tooltip="Notifications">
            <i class="fa-solid fa-bell"></i>
            <span class="notif-dot"></span>
          </button>
          <div class="nav-balance" style="display:flex;align-items:center;gap:0.45rem;padding:0.45rem 0.75rem;border:1px solid var(--border);border-radius:999px;background:var(--bg-card);margin-right:0.5rem;">
            <span style="font-size:0.8rem;color:var(--text-muted);">Compte</span>
            <strong style="font-family:'Sora',sans-serif;color:var(--primary);">${balanceFormatted} Ar</strong>
          </div>
          <div class="navbar-avatar" title="${user.prenom}">${initials}</div>
        </div>
      </header>
    `;
  },

  deleteModal() {
    return `
      <div class="modal-overlay" id="deleteModal">
        <div class="modal" style="max-width:420px">
          <div class="modal-header">
            <h3 style="display:flex;align-items:center;gap:8px">
              <span style="color:var(--danger)"><i class="fa-solid fa-trash"></i></span> Supprimer
            </h3>
            <button class="modal-close" data-modal-close="deleteModal"><i class="fa-solid fa-xmark"></i></button>
          </div>
          <div class="modal-body">
            <div style="text-align:center;padding:1rem 0">
              <div style="width:64px;height:64px;background:var(--danger-light);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:1.6rem;color:var(--danger)">
                <i class="fa-solid fa-triangle-exclamation"></i>
              </div>
              <h3 style="margin-bottom:0.5rem">Confirmer la suppression</h3>
              <p style="color:var(--text-muted);font-size:0.9rem">
                Voulez-vous vraiment supprimer <strong class="delete-item-name"></strong> ?<br>
                Cette action est irréversible.
              </p>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" data-modal-close="deleteModal">Annuler</button>
            <button class="btn btn-danger btn-confirm-delete">
              <i class="fa-solid fa-trash"></i> Supprimer
            </button>
          </div>
        </div>
      </div>
    `;
  },

  inject(activePage, title, subtitle) {
    document.body.insertAdjacentHTML('afterbegin', this.sidebar(activePage));
    document.body.insertAdjacentHTML('afterbegin', '<div class="main-content" id="mainContent"></div>');
    const mc = document.getElementById('mainContent');
    mc.insertAdjacentHTML('afterbegin', this.navbar(title, subtitle));
    document.body.insertAdjacentHTML('beforeend', this.deleteModal());
    // Try to refresh balance from server (if API available)
    try {
      if (typeof window !== 'undefined' && window.fetch) {
        fetch('/api/balance', { credentials: 'same-origin' })
          .then(r => r.json())
          .then(j => {
            if (j && typeof j.balance !== 'undefined') {
              const el = document.querySelector('.nav-balance strong');
              if (el) el.textContent = Number(j.balance).toLocaleString('fr-FR', { maximumFractionDigits: 0 }) + ' Ar';
            }
          }).catch(() => {});
      }
    } catch (e) {}
  }
};
