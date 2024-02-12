-- Insertar usuario administrador
INSERT INTO kw_user (name, email, password) VALUES ('admin', 'admin@admin.com', 'admin');

-- Insertar grupo de administradores
INSERT INTO kw_group (name, description) VALUES ('Administradores', 'Grupo de usuarios con privilegios de administrador');

-- Insertar permiso de administración
INSERT INTO kw_permission (name, description) VALUES ('Administrar', 'Permiso para realizar acciones de administración');

-- Vincular usuario administrador al grupo de administradores
INSERT INTO kw_user_and_kw_group (id_kw_user, id_kw_group) VALUES ((SELECT id FROM kw_user WHERE name = 'admin'), (SELECT id FROM kw_group WHERE name = 'Administradores'));

-- Vincular grupo de administradores al permiso de administración
INSERT INTO kw_group_and_kw_permission (id_kw_group, id_kw_permission) VALUES ((SELECT id FROM kw_group WHERE name = 'Administradores'), (SELECT id FROM kw_permission WHERE name = 'Administrar'));
