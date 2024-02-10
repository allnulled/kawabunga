CREATE TABLE IF NOT EXISTS usuarios_por_confirmar (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) UNIQUE,
    correo VARCHAR(255) UNIQUE,
    contrasenya VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS usuarios (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) UNIQUE,
    correo VARCHAR(255) UNIQUE,
    contrasenya VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS grupos (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) UNIQUE,
    descripcion TEXT
);

CREATE TABLE IF NOT EXISTS permisos (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) UNIQUE,
    descripcion TEXT
);

CREATE TABLE IF NOT EXISTS usuarios_y_grupos (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    id_usuario INTEGER,
    id_grupo INTEGER,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (id_grupo) REFERENCES grupos(id)
);

CREATE TABLE IF NOT EXISTS grupos_y_permisos (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    id_grupo INTEGER,
    id_permiso INTEGER,
    FOREIGN KEY (id_grupo) REFERENCES grupos(id),
    FOREIGN KEY (id_permiso) REFERENCES permisos(id)
);

CREATE TABLE IF NOT EXISTS sesiones (
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
	id_usuario INTEGER,
	token VARCHAR(255),
	FOREIGN KEY (id_usuario) REFERENCES usuarios (id)
);