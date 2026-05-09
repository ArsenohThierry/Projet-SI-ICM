create table objectif (
    id int primary key auto_increment,
    libelle varchar(255) not null
);

alter table regime add column objectif_id int;
alter table regime add foreign key (objectif_id) references objectif(id);
alter table regime add column nom varchar(255);

alter table user add column age int;

create table user_objectif (
    id int auto_increment primary key,
    user_id int,
    objectif_id int,
    date_save datetime,
    foreign key (user_id) references user(id),
    foreign key (objectif_id) references objectif(id)
);

create table sport_objectif (
    id int auto_increment primary key,
    sport_id int,
    objectif_id int,
    age_min int,
    age_max int,
    calories_brulees float, -- calorie brulees par minute
    duree_recommandee int, -- en minutes par jour
    genre varchar(1), -- 'M' pour masculin, 'F' pour féminin, null pour tous 
    foreign key (sport_id) references sport(id),
    foreign key (objectif_id) references objectif(id)
);

create table user_sport (
    id int auto_increment primary key,
    user_id int,
    sport_objectif_id int,
    date_save datetime,
    foreign key (user_id) references user(id),
    foreign key (sport_objectif_id) references sport_objectif(id)
);



