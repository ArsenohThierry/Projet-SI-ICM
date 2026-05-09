<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier activite</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
</head>
<body>

<?php $sessionUser = $user ?? []; ?>
<?php $sportData = isset($sport) && is_array($sport) ? $sport : []; ?>
<?php $sportObjectif = isset($so) && is_array($so) ? $so : []; ?>
<?php $objectifsList = isset($objectifs) && is_array($objectifs) ? $objectifs : []; ?>
<?php /** @var array{id:int, nom:string} $sportData */ ?>
<?php /** @var array<string, mixed> $sportObjectif */ ?>
<?php /** @var array<int, array{id:int, libelle:string}> $objectifsList */ ?>
<script>
  window.NF_USER = {
    prenom: "<?php echo esc($sessionUser['prenom'] ?? ''); ?>",
    nom: "<?php echo esc($sessionUser['nom'] ?? ''); ?>",
    email: "<?php echo esc($sessionUser['email'] ?? ''); ?>",
    role_user: "<?php echo esc($sessionUser['role_user'] ?? ''); ?>",
    gold: <?php echo session()->get('user_option') === 'gold' ? 'true' : 'false'; ?>
  };
</script>

<div id="pageContent">
    <div class="page-header">
        <div class="header-title">
            <div class="logo-icon" style="background: var(--info);">
                <i class="fa-solid fa-pen" style="color:white;"></i>
            </div>
            <div>
                <h2>Modifier activite</h2>
                <p class="text-muted">Mettre à jour les parametres de l'activite.</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="/admin/sports" class="btn btn-outline">Retour</a>
        </div>
    </div>

    <?php $errors = session()->getFlashdata('errors') ?? []; ?>
    <?php if (!empty($errors)): ?>
      <div class="toast warning" style="position:relative; margin-bottom:1rem;">
        <i class="fa-solid fa-exclamation-triangle toast-icon"></i>
        <span><?php echo esc(implode(' | ', $errors)); ?></span>
      </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3>Informations principales</h3>
        </div>
        <div class="card-body">
            <form method="post" action="/admin/sports/<?php echo (int) ($sportData['id'] ?? 0); ?>/update" class="form-stack">
                <div class="form-group">
                    <label class="form-label">Nom</label>
                    <input class="form-control" name="nom" value="<?php echo esc(old('nom', $sportData['nom'] ?? '')); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Objectif</label>
                    <select name="objectif_id" class="form-control" required>
                        <option value="">-- Choisir --</option>
                        <?php foreach ($objectifsList as $o): ?>
                            <option value="<?php echo (int) $o['id']; ?>" <?php echo old('objectif_id', $sportObjectif['objectif_id'] ?? '') == $o['id'] ? 'selected' : ''; ?>><?php echo esc($o['libelle']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Âge minimum</label>
                        <input type="number" class="form-control" name="age_min" value="<?php echo esc(old('age_min', $sportObjectif['age_min'] ?? '')); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Âge maximum (optionnel)</label>
                        <input type="number" class="form-control" name="age_max" value="<?php echo esc(old('age_max', $sportObjectif['age_max'] ?? '')); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Calories brûlées (par minute)</label>
                        <input type="number" step="0.1" class="form-control" name="calories_brulees" value="<?php echo esc(old('calories_brulees', $sportObjectif['calories_brulees'] ?? '')); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Durée recommandée (minutes/jour)</label>
                        <input type="number" class="form-control" name="duree_recommandee" value="<?php echo esc(old('duree_recommandee', $sportObjectif['duree_recommandee'] ?? '')); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Genre</label>
                    <select name="genre" class="form-control">
                        <option value="" <?php echo old('genre', $sportObjectif['genre'] ?? '') === '' ? 'selected' : ''; ?>>Tous</option>
                        <option value="M" <?php echo old('genre', $sportObjectif['genre'] ?? '') === 'M' ? 'selected' : ''; ?>>Masculin</option>
                        <option value="F" <?php echo old('genre', $sportObjectif['genre'] ?? '') === 'F' ? 'selected' : ''; ?>>Féminin</option>
                    </select>
                </div>

                <div class="form-actions">
                    <a href="/admin/sports" class="btn btn-outline">Annuler</a>
                    <button class="btn btn-primary" type="submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/assets/js/app.js"></script>
<script src="/assets/js/layout.js"></script>
<script>
  NF_LAYOUT.inject('admin-sports', 'Administration', 'Activites');
  const mainContentNode = document.getElementById('mainContent');
  const pageContentNode = document.getElementById('pageContent');
  if (mainContentNode && pageContentNode) {
    mainContentNode.appendChild(pageContentNode);
  }
</script>

</body>
</html>
