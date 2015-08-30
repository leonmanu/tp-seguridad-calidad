CREATE DATABASE thewall;

USE thewall;

CREATE TABLE USUARIO (
	id_usuario int not null auto_increment primary key,
	rol set('Administrador','Comun'),
	nombre varchar(30),
	apellido varchar(30),
	mail varchar(50),
	nombre_usuario varchar(30),
	contraseña varchar(50)
);


CREATE TABLE MURO (
	id_muro int not null auto_increment primary key,
	id_usuario int not null,
	privacidad set('propietario','todos','registrados'),
	foreign key(id_usuario) references USUARIO(id_usuario) ON DELETE CASCADE
);

CREATE TABLE MENSAJE (
	id_mensaje int not null auto_increment primary key,
	id_usuario int not null,
	id_muro int not null,
	contenido varchar (280),
	fecha date,
	foreign key(id_usuario) references USUARIO(id_usuario) ON DELETE CASCADE,
	foreign key(id_muro) references MURO(id_muro) ON DELETE CASCADE
);
