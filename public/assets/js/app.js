/* ============================================================
   NutriFit — Main JavaScript
   ============================================================ */

'use strict';

// ── Toast Notifications ──────────────────────────────────────
const Toast = {
  container: null,

  init() {
    this.container = document.createElement('div');
    this.container.className = 'toast-container';
    document.body.appendChild(this.container);
  },

  show(message, type = 'success', duration = 3500) {
    if (!this.container) this.init();

    const icons = { success: 'fa-check-circle', error: 'fa-times-circle', warning: 'fa-exclamation-triangle' };
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<i class="fa-solid ${icons[type]} toast-icon"></i><span>${message}</span>`;
    this.container.appendChild(toast);

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(20px)';
      toast.style.transition = 'all 0.3s';
      setTimeout(() => toast.remove(), 300);
    }, duration);
  }
};

// ── Modal Manager ────────────────────────────────────────────
const Modal = {
  open(id) {
    const overlay = document.getElementById(id);
    if (overlay) {
      overlay.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
  },

  close(id) {
    const overlay = document.getElementById(id);
    if (overlay) {
      overlay.classList.remove('open');
      document.body.style.overflow = '';
    }
  },

  init() {
    document.querySelectorAll('[data-modal-open]').forEach(btn => {
      btn.addEventListener('click', () => this.open(btn.dataset.modalOpen));
    });

    document.querySelectorAll('[data-modal-close], .modal-overlay').forEach(el => {
      el.addEventListener('click', (e) => {
        if (e.target === el) {
          const overlay = el.closest('.modal-overlay') || document.getElementById(el.dataset.modalClose);
          if (overlay) this.close(overlay.id);
        }
      });
    });

    document.querySelectorAll('.modal').forEach(modal => {
      modal.addEventListener('click', e => e.stopPropagation());
    });
  }
};

// ── Password Toggle ──────────────────────────────────────────
function initPasswordToggles() {
  document.querySelectorAll('.toggle-password').forEach(btn => {
    if (btn.dataset.passwordToggleReady === 'true') return;
    btn.dataset.passwordToggleReady = 'true';

    btn.addEventListener('click', () => {
      const input = btn.closest('.input-wrapper').querySelector('input');
      const icon  = btn.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa-solid fa-eye-slash';
        btn.setAttribute('aria-label', 'Masquer le mot de passe');
      } else {
        input.type = 'password';
        icon.className = 'fa-solid fa-eye';
        btn.setAttribute('aria-label', 'Afficher le mot de passe');
      }
    });
  });
}

// ── Sidebar Toggle (mobile) ──────────────────────────────────
function initSidebar() {
  const sidebar  = document.querySelector('.sidebar');
  const overlay  = document.querySelector('.sidebar-overlay');
  const hamburger = document.querySelector('.hamburger');

  if (!sidebar) return;

  function openSidebar()  { sidebar.classList.add('open');  if (overlay) overlay.style.display = 'block'; }
  function closeSidebar() { sidebar.classList.remove('open'); if (overlay) overlay.style.display = 'none'; }

  hamburger?.addEventListener('click', openSidebar);
  overlay?.addEventListener('click', closeSidebar);
}

// ── Active Nav Item ──────────────────────────────────────────
function setActiveNav() {
  const current = window.location.pathname.split('/').pop();
  document.querySelectorAll('.nav-item').forEach(item => {
    const href = item.getAttribute('href') || '';
    if (href.includes(current) && current !== '') {
      item.classList.add('active');
    }
  });
}

// ── Delete Confirmation ──────────────────────────────────────
function confirmDelete(name, callback) {
  const overlay = document.getElementById('deleteModal');
  if (overlay) {
    overlay.querySelector('.delete-item-name').textContent = name;
    Modal.open('deleteModal');
    const confirmBtn = overlay.querySelector('.btn-confirm-delete');
    const newBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);
    newBtn.addEventListener('click', () => {
      Modal.close('deleteModal');
      callback();
    });
  } else {
    if (confirm(`Supprimer "${name}" ?`)) callback();
  }
}

// ── Table Search ─────────────────────────────────────────────
function initTableSearch() {
  const searchInput = document.getElementById('tableSearch');
  if (!searchInput) return;

  searchInput.addEventListener('input', () => {
    const query = searchInput.value.toLowerCase();
    document.querySelectorAll('.searchable-row').forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(query) ? '' : 'none';
    });
  });
}

// ── BMI Calculator ───────────────────────────────────────────
function calculateBMI(weight, height) {
  const h = height / 100;
  return +(weight / (h * h)).toFixed(1);
}

function getBMICategory(bmi) {
  if (bmi < 18.5) return { label: 'Insuffisance pondérale', color: '#3B82F6', class: 'info' };
  if (bmi < 25)   return { label: 'Poids normal',           color: '#18C97A', class: 'green' };
  if (bmi < 30)   return { label: 'Surpoids',               color: '#F5A623', class: 'gold' };
  return                  { label: 'Obésité',               color: '#E8445A', class: 'danger' };
}

// ── Mini Line Chart (Canvas) ──────────────────────────────────
function drawMiniChart(canvas, data, color = '#18C97A') {
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  const W = canvas.width  = canvas.offsetWidth;
  const H = canvas.height = canvas.offsetHeight;

  const min = Math.min(...data);
  const max = Math.max(...data);
  const range = max - min || 1;

  const points = data.map((v, i) => ({
    x: (i / (data.length - 1)) * W,
    y: H - ((v - min) / range) * (H * 0.75) - H * 0.1
  }));

  // Gradient fill
  const grad = ctx.createLinearGradient(0, 0, 0, H);
  grad.addColorStop(0, color + '55');
  grad.addColorStop(1, color + '00');

  ctx.beginPath();
  ctx.moveTo(points[0].x, points[0].y);
  for (let i = 1; i < points.length; i++) {
    const cp = { x: (points[i-1].x + points[i].x) / 2, y: (points[i-1].y + points[i].y) / 2 };
    ctx.quadraticCurveTo(points[i-1].x, points[i-1].y, cp.x, cp.y);
  }
  ctx.lineTo(points[points.length-1].x, points[points.length-1].y);
  ctx.lineTo(W, H); ctx.lineTo(0, H);
  ctx.closePath();
  ctx.fillStyle = grad;
  ctx.fill();

  // Line
  ctx.beginPath();
  ctx.moveTo(points[0].x, points[0].y);
  for (let i = 1; i < points.length; i++) {
    const cp = { x: (points[i-1].x + points[i].x) / 2, y: (points[i-1].y + points[i].y) / 2 };
    ctx.quadraticCurveTo(points[i-1].x, points[i-1].y, cp.x, cp.y);
  }
  ctx.lineTo(points[points.length-1].x, points[points.length-1].y);
  ctx.strokeStyle = color;
  ctx.lineWidth = 2.5;
  ctx.stroke();

  // Dots
  points.forEach(p => {
    ctx.beginPath();
    ctx.arc(p.x, p.y, 4, 0, Math.PI * 2);
    ctx.fillStyle = color;
    ctx.fill();
    ctx.strokeStyle = 'white';
    ctx.lineWidth = 2;
    ctx.stroke();
  });
}

// ── Bar Chart (Canvas) ───────────────────────────────────────
function drawBarChart(canvas, labels, data, color = '#18C97A') {
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  const W = canvas.width  = canvas.offsetWidth;
  const H = canvas.height = canvas.offsetHeight;
  const PAD = 30;
  const max = Math.max(...data);
  const barW = (W - PAD * 2) / data.length;
  const gap = barW * 0.25;

  // Y grid lines
  ctx.strokeStyle = '#D9EDE5';
  ctx.lineWidth = 1;
  for (let i = 0; i <= 4; i++) {
    const y = PAD + ((H - PAD * 2) / 4) * i;
    ctx.beginPath();
    ctx.moveTo(PAD, y);
    ctx.lineTo(W - PAD, y);
    ctx.stroke();
  }

  data.forEach((v, i) => {
    const barH = ((v / max) * (H - PAD * 2 - 20));
    const x = PAD + i * barW + gap / 2;
    const y = H - PAD - barH;

    const grad = ctx.createLinearGradient(0, y, 0, H - PAD);
    grad.addColorStop(0, color);
    grad.addColorStop(1, color + '66');

    ctx.beginPath();
    ctx.roundRect(x, y, barW - gap, barH, [4, 4, 0, 0]);
    ctx.fillStyle = grad;
    ctx.fill();

    // Label
    ctx.fillStyle = '#8BA99A';
    ctx.font = '11px DM Sans, sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText(labels[i], x + (barW - gap) / 2, H - 8);
  });
}

// ── Donut Chart (Canvas) ─────────────────────────────────────
function drawDonutChart(canvas, values, colors, total) {
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  const W = canvas.width  = canvas.offsetWidth;
  const H = canvas.height = canvas.offsetHeight;
  const cx = W / 2, cy = H / 2;
  const r = Math.min(W, H) / 2 - 10;

  let start = -Math.PI / 2;
  values.forEach((v, i) => {
    const angle = (v / total) * Math.PI * 2;
    ctx.beginPath();
    ctx.moveTo(cx, cy);
    ctx.arc(cx, cy, r, start, start + angle);
    ctx.closePath();
    ctx.fillStyle = colors[i];
    ctx.fill();
    start += angle;
  });

  // Hole
  ctx.beginPath();
  ctx.arc(cx, cy, r * 0.6, 0, Math.PI * 2);
  ctx.fillStyle = '#fff';
  ctx.fill();
}

// ── Form validation ──────────────────────────────────────────
function validateRequired(form) {
  let valid = true;
  form.querySelectorAll('[required]').forEach(field => {
    if (!field.value.trim()) {
      field.classList.add('error');
      valid = false;
    } else {
      field.classList.remove('error');
    }
  });
  return valid;
}

// ── Local Storage helpers ────────────────────────────────────
const Storage = {
  get: (key, def = []) => {
    try { return JSON.parse(localStorage.getItem(key)) ?? def; }
    catch { return def; }
  },
  set: (key, val) => localStorage.setItem(key, JSON.stringify(val)),
  getUser: () => Storage.get('nf_user', null),
  setUser: (u)  => Storage.set('nf_user', u),
};

// ── Init ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  Toast.init();
  Modal.init();
  initPasswordToggles();
  initSidebar();
  setActiveNav();
  initTableSearch();
});
