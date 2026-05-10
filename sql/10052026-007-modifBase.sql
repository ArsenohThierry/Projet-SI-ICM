alter table user_objectif add column objectif_applique_id int;
alter table user_objectif add foreign key (objectif_applique_id) references objectif(id);