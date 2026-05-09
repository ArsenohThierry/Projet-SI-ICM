-- Create admin account (replace password_hash with a real bcrypt hash)
INSERT INTO user (
    nom,
    prenom,
    email,
    password_hash,
    username,
    poids_initial,
    genre,
    taille,
    age,
    role_user
) VALUES (
    'Admin',
    'System',
    'admin@example.com',
    '$2y$10$cYlKyDunmTflGQlq0wNNCuaE8PiKxJBaiTVGWKBnKuTdFOGbLqB0i',
    'admin',
    70,
    'Homme',
    170,
    30,
    'admin'
);

-- mot de passe tsy crypté anio : admin123456lol