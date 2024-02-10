-- Insertar usuario administrador
INSERT INTO usuarios (nombre, correo, contrasenya) VALUES ('admin', 'admin@admin.com', 'admin');

-- Insertar grupo de administradores
INSERT INTO grupos (nombre, descripcion) VALUES ('Administradores', 'Grupo de usuarios con privilegios de administrador');

-- Insertar permiso de administración
INSERT INTO permisos (nombre, descripcion) VALUES ('Administrar', 'Permiso para realizar acciones de administración');

-- Vincular usuario administrador al grupo de administradores
INSERT INTO usuarios_y_grupos (id_usuario, id_grupo) VALUES ((SELECT id FROM usuarios WHERE nombre = 'admin'), (SELECT id FROM grupos WHERE nombre = 'Administradores'));

-- Vincular grupo de administradores al permiso de administración
INSERT INTO grupos_y_permisos (id_grupo, id_permiso) VALUES ((SELECT id FROM grupos WHERE nombre = 'Administradores'), (SELECT id FROM permisos WHERE nombre = 'Administrar'));
