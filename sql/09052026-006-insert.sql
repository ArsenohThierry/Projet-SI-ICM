insert into objectif (libelle) values ('Perte de poids'), ('Prise de poids'), ('IMC Ideal');

insert into regime (pourcentage_viande, pourcentage_volaille, pourcentage_poisson, montant, variation_poids, objectif_id, nom) values
(20, 35, 45, 12.50, -1.5, (select id from objectif where libelle = 'Perte de poids'), 'Regime Deficit Leger'),
(15, 40, 45, 14.00, -2.5, (select id from objectif where libelle = 'Perte de poids'), 'Regime Proteine Controlee'),
(10, 30, 60, 13.00, -3.5, (select id from objectif where libelle = 'Perte de poids'), 'Regime Poisson Leger'),
(35, 40, 25, 11.50, 1.5, (select id from objectif where libelle = 'Prise de poids'), 'Regime Energie Plus'),
(40, 35, 25, 13.50, 2.5, (select id from objectif where libelle = 'Prise de poids'), 'Regime Force et Masse'),
(30, 45, 25, 12.00, 3.5, (select id from objectif where libelle = 'Prise de poids'), 'Regime Hypercalorique Equilibre'),
(30, 35, 35, 10.50, 0.0, (select id from objectif where libelle = 'IMC Ideal'), 'Regime Equilibre Classique'),
(25, 35, 40, 11.00, 0.0, (select id from objectif where libelle = 'IMC Ideal'), 'Regime Equilibre Sportif'),
(20, 40, 40, 12.50, 0.0, (select id from objectif where libelle = 'IMC Ideal'), 'Regime Equilibre Premium');

insert into sport (nom) values
('Marche rapide'),
('Course a pied'),
('Velo'),
('Natation'),
('Musculation'),
('Corde a sauter'),
('Danse cardio'),
('Randonee');

insert into sport_objectif (sport_id, objectif_id, age_min, age_max, calories_brulees, duree_recommandee, genre) values
((select id from sport where nom = 'Marche rapide'), (select id from objectif where libelle = 'Perte de poids'), 18, 65, 4.8, 45, null),
((select id from sport where nom = 'Course a pied'), (select id from objectif where libelle = 'Perte de poids'), 18, 45, 9.5, 30, null),
((select id from sport where nom = 'Corde a sauter'), (select id from objectif where libelle = 'Perte de poids'), 15, 40, 12.0, 20, null),
((select id from sport where nom = 'Danse cardio'), (select id from objectif where libelle = 'Perte de poids'), 16, 50, 6.5, 40, 'F'),

((select id from sport where nom = 'Musculation'), (select id from objectif where libelle = 'Prise de poids'), 18, 45, 5.0, 50, 'M'),
((select id from sport where nom = 'Musculation'), (select id from objectif where libelle = 'Prise de poids'), 18, 45, 4.5, 45, 'F'),
((select id from sport where nom = 'Velo'), (select id from objectif where libelle = 'Prise de poids'), 18, 60, 7.0, 60, null),
((select id from sport where nom = 'Randonee'), (select id from objectif where libelle = 'Prise de poids'), 20, 65, 6.0, 90, null),

((select id from sport where nom = 'Natation'), (select id from objectif where libelle = 'IMC Ideal'), 12, 70, 8.0, 40, null),
((select id from sport where nom = 'Marche rapide'), (select id from objectif where libelle = 'IMC Ideal'), 12, 70, 4.5, 45, null),
((select id from sport where nom = 'Velo'), (select id from objectif where libelle = 'IMC Ideal'), 14, 70, 7.5, 50, null),
((select id from sport where nom = 'Danse cardio'), (select id from objectif where libelle = 'IMC Ideal'), 16, 60, 6.0, 35, null);