create database regime character set utf8mb4 collate utf8mb4_general_ci;
use regime;

create table user (
    id int auto_increment primary key,
    nom varchar(255),
    prenom varchar(255),
    email varchar(255),
    password_hash varchar(255),
    username varchar(255),
    poids_initial float,
    genre enum('Homme', 'Femme'), 
    taille float
);

create table poids_user (
    id int auto_increment primary key,
    user_id int,
    poids float,
    date_save datetime,
    foreign key (user_id) references user(id)
);

create table code (
    id int auto_increment primary key, 
    valeur varchar(255),
    montant float,
    status enum('unused', 'used')
);

create table `option` (  -- ✅ backticks car mot réservé MySQL
    id int auto_increment primary key,
    libelle varchar(255),
    montant float
);

create table mouvement (
    id int auto_increment primary key,
    type enum('mampiditra', 'mamoaka'),
    user_id int,
    montant float,
    date_mouvement datetime,
    foreign key (user_id) references user(id)
);

create table user_option (
    id int auto_increment primary key,
    user_id int,
    option_id int,
    date_save datetime,
    foreign key (user_id) references user(id),
    foreign key (option_id) references `option`(id)  -- ✅ backticks
);

create table regime (
    id int auto_increment primary key,
    pourcentage_viande float,
    pourcentage_volaille float,
    pourcentage_poisson float,
    montant float,
    variation_poids float
);

create table user_regime (
    id int auto_increment primary key,
    user_id int,
    regime_id int,
    date_debut datetime,
    duree int,
    foreign key (user_id) references user(id),
    foreign key (regime_id) references regime(id)
);

create table sport (
    id int auto_increment primary key,
    nom varchar(255)
);