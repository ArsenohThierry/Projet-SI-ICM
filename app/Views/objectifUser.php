<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Objectif — NutriFit</title>
	<link rel="stylesheet" href="/assets/css/style.css">
	<link rel="stylesheet" href="/assets/css/all.min.css">
</head>
<body>
<?php $sessionUser = $user ?? []; ?>
<header class="navbar" style="position:sticky; top:0; z-index:20; backdrop-filter: blur(14px);">
	<div class="navbar-left">
		<div style="display:flex; align-items:center; gap:10px;">
			<div class="logo-icon"><i class="fa-solid fa-leaf"></i></div>
			<span class="logo-text">Nutri<span>Fit</span></span>
		</div>
		<div>
			<div class="page-title">Objectif</div>
			<div class="breadcrumb">NutriFit / <span>Définir mon objectif</span></div>
		</div>
	</div>
	<div class="navbar-right">
		<div class="navbar-avatar" title="<?php echo esc($sessionUser['prenom'] ?? ''); ?>">
			<?php echo strtoupper(substr((string) ($sessionUser['prenom'] ?? 'J'), 0, 1) . substr((string) ($sessionUser['nom'] ?? 'R'), 0, 1)); ?>
		</div>
	</div>
</header>

<div class="imc-page">
	<div class="imc-wrapper">
			<div class="card">
				<div class="card-header">
					<h3><i class="fa-solid fa-bullseye" style="color:var(--primary); margin-right:8px;"></i>Définir mon objectif</h3>
				</div>
				<div class="card-body">
					<?php $objectifs = $objectifs ?? []; ?>
					<div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-bottom:1rem;">
						<?php foreach ($objectifs as $obj): ?>
							<div class="objectif-card" data-id="<?php echo (int)$obj['id']; ?>" style="flex:1 1 200px; padding:1rem; border:1px solid var(--border); border-radius:10px; cursor:pointer; background:var(--bg-card);">
								<div style="font-weight:700; font-size:1rem; margin-bottom:0.25rem"><?php echo esc($obj['libelle']); ?></div>
								<div style="font-size:0.85rem; color:var(--text-muted);">Choisir cet objectif</div>
							</div>
						<?php endforeach; ?>
					</div>

					<form method="post" action="/objectif" id="objectif-form">
						<input type="hidden" name="objectif_id" id="objectif_id" value="">
						<button type="submit" class="btn btn-primary btn-block" id="confirm-btn" disabled>
							<i class="fa-solid fa-check"></i> Confirmer
						</button>
					</form>
				</div>
			</div>
	</div>
</div>
			<script>
				(function(){
					const cards = document.querySelectorAll('.objectif-card');
					const input = document.getElementById('objectif_id');
					const btn = document.getElementById('confirm-btn');
					let selected = null;

					cards.forEach(c => {
						c.addEventListener('click', () => {
							cards.forEach(x => x.style.borderColor = 'var(--border)');
							c.style.borderColor = 'var(--primary)';
							selected = c.getAttribute('data-id');
							input.value = selected;
							btn.disabled = false;
						});
					});
				})();
			</script>
			</body>
			</html>
