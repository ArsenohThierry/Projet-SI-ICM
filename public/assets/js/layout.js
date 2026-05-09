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
    const user = this.getUser() || { prenom: 'Jean', nom: 'Rakoto', gold: false };
    const initials = ((user.prenom || 'J')[0] + (user.nom || 'R')[0]).toUpperCase();

    const navItems = [
      { id: 'imc',         icon: 'fa-calculator',    label: 'IMC',               href: '/imc' },
      { id: 'profile',     icon: 'fa-user',          label: 'Profil',            href: '/profile' },
      { id: 'dashboard',   icon: 'fa-gauge',         label: 'Tableau de bord',   href: 'dashboard.html' },
      { id: 'regimes',     icon: 'fa-bowl-food',     label: 'Régimes',           href: 'regimes.html',   badge: null },
      { id: 'activities',  icon: 'fa-dumbbell',      label: 'Activités',         href: 'activities.html' },
      { id: 'abonnement',  icon: 'fa-crown',         label: 'Abonnement Gold',   href: 'abonnement.html', gold: true },
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
              <div class="user-name">${user.prenom} ${user.nom}</div>
              <div class="user-role">${user.email || 'utilisateur'}</div>
            </div>
            ${user.gold ? '<span class="gold-tag"><i class="fa-solid fa-crown"></i> GOLD</span>' : ''}
          </div>
        </div>
      </aside>
    `;
  },

  navbar(title, subtitle) {
    const user = this.getUser() || { prenom: 'Jean', nom: 'Rakoto' };
    const initials = ((user.prenom || 'J')[0] + (user.nom || 'R')[0]).toUpperCase();
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
          <div class="search-box">
            <i class="fa-solid fa-search"></i>
            <input type="text" id="tableSearch" placeholder="Rechercher...">
          </div>
          <button class="nav-btn" data-tooltip="Notifications">
            <i class="fa-solid fa-bell"></i>
            <span class="notif-dot"></span>
          </button>
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
  }
};