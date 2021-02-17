create database subeapp;

use subeapp;


create table tblRoles(
	id int auto_increment not null,
	nombreRol varchar(100) not null,
	estado bit default 1,
	created_at datetime default current_timestamp,
	constraint pk_roles primary key(id)
);

create table tblUsuarios(
	id int auto_increment not null,
    nombre varchar(200) ,
	correo varchar(100) not null,
	contrasena text not null,
	estado bit default 0,
	idRol int not null,
	correoVerificado bit default 0,
	created_at datetime default current_timestamp,
	updated_at datetime default current_timestamp,
	constraint pk_users primary key(id),
	constraint fk_roles foreign key(idRol ) references tblRoles(id)
);


create table tblCategorias(
	id int auto_increment not null,
	nombre varchar(60) not null,
	descripcion text,
	estado bit default 1,
	creadoPor int not null,
	created_at datetime default current_timestamp,
	constraint pk_categorias primary key(id),
	constraint fk_usuario foreign key(creadoPor) references tblUsuarios(id)

);




create table tblProductos(
	id int auto_increment,
	nombre varchar(100) not null,
	descripcion text,
	estado bit default 1,
	precio decimal,
	imagen text,
	idCategoria int not null,
	creadoPor int not null,
	created_at datetime default current_timestamp,
	updated_at datetime default current_timestamp,
	constraint pk_productos primary key(id),
	constraint fk_categoria foreign key(idCategoria) references tblCategorias(id),
	constraint fk_usuario_productos foreign key(creadoPor) references tblUsuarios(id)
);

create table tblMensajesFrmcontacto(
	id int auto_increment not null,
	correo varchar(100) not null,
	nombre varchar(100) not null,
	mensaje text not null,
	created_at datetime default current_timestamp,
    constraint pk_mensajesfrmcontacto primary key(id)
);
