create database game_db;

create table user(
pk_id_user int not null auto_increment primary key,
name_user varchar(30) not null,
email_user varchar(30) not null,
password_user varchar(60) not null,
quant_feedback int,
adm boolean DEFAULT FALSE
);

create table feedback(
pk_id_feedback int not null auto_increment primary key,
fk_id_user int not null,
feedback varchar(500) not null,
nota int not null,
constraint fk_id_user foreign key (fk_id_user) references user (pk_id_user)
on delete cascade on update cascade
);

CREATE TABLE imagens (
    pk_id_imagem INT AUTO_INCREMENT PRIMARY KEY,
    nome_imagem VARCHAR(255) NOT NULL,
    caminho VARCHAR(255) NOT NULL,
    fk_id_user INT,
    FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user)
        ON DELETE SET NULL

);
CREATE TABLE feedback_imagem (
    pk_id_feedback_imagem INT AUTO_INCREMENT PRIMARY KEY,
    comentario TEXT,
    nota INT,
    fk_id_imagem INT,
    fk_id_user INT,
    FOREIGN KEY (fk_id_imagem) REFERENCES imagens(pk_id_imagem),
    FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user)
);


CREATE TABLE videos (
    pk_id_video INT AUTO_INCREMENT PRIMARY KEY,
    nome_video VARCHAR(255) NOT NULL,
    caminho VARCHAR(255) NOT NULL,
    fk_id_user INT NOT NULL,
    FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user)
);


CREATE TABLE feedback_video (
    pk_id_feedback_video INT AUTO_INCREMENT PRIMARY KEY,
    comentario TEXT NOT NULL,
    nota INT NOT NULL CHECK (nota BETWEEN 1 AND 5),
    fk_id_video INT NOT NULL,
    fk_id_user INT NOT NULL,
    FOREIGN KEY (fk_id_video) REFERENCES videos(pk_id_video),
    FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user)
);
CREATE TABLE forum (
    pk_id_forum INT AUTO_INCREMENT PRIMARY KEY,
    nome TEXT NOT NULL,
    description TEXT NOT NULL,
    fk_id_user INT NOT NULL,
    FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user)
);

CREATE TABLE forum_comentario (
    pk_id_forum_comentario INT AUTO_INCREMENT PRIMARY KEY,
    comentario TEXT NOT NULL,
    fk_id_user INT NOT NULL,
    fk_id_forum INT NOT NULL,
    FOREIGN KEY (fk_id_forum) REFERENCES forum(pk_id_forum),
    FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user)
);

CREATE TABLE mecanicas (
    pk_id_mecanicas INT AUTO_INCREMENT PRIMARY KEY,
    texto TEXT NOT NULL,
    imagem VARCHAR(255) NOT NULL
);

CREATE TABLE atualizacoes (
    pk_id_atualizacoes INT AUTO_INCREMENT PRIMARY KEY,
    texto TEXT NOT NULL,
    imagem VARCHAR(255) NOT NULL
);