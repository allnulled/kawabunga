CREATE TABLE IF NOT EXISTS kw_user_to_confirm (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE,
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS kw_user (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE,
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS kw_group (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE,
    description TEXT
);

CREATE TABLE IF NOT EXISTS kw_permission (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE,
    description TEXT
);

CREATE TABLE IF NOT EXISTS kw_user_and_kw_group (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    id_kw_user INTEGER,
    id_kw_group INTEGER,
    FOREIGN KEY (id_kw_user) REFERENCES kw_user(id),
    FOREIGN KEY (id_kw_group) REFERENCES kw_group(id)
);

CREATE TABLE IF NOT EXISTS kw_group_and_kw_permission (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    id_kw_group INTEGER,
    id_kw_permission INTEGER,
    FOREIGN KEY (id_kw_group) REFERENCES kw_group(id),
    FOREIGN KEY (id_kw_permission) REFERENCES kw_permission(id)
);

CREATE TABLE IF NOT EXISTS kw_session (
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
	id_kw_user INTEGER,
	token VARCHAR(255),
	FOREIGN KEY (id_kw_user) REFERENCES kw_user (id)
);