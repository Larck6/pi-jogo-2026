CREATE TABLE IF NOT EXISTS user(
    pk_id_user INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name_user VARCHAR(30) NOT NULL,
    email_user VARCHAR(50) NOT NULL,
    password_user VARCHAR(60) NOT NULL,
    quant_feedback INT DEFAULT 0,
    adm BOOLEAN DEFAULT FALSE
);

CREATE TABLE IF NOT EXISTS feedback(
    pk_id_feedback INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    fk_id_user INT NOT NULL,
    feedback VARCHAR(500) NOT NULL,
    nota INT NOT NULL,
    CONSTRAINT fk_feedback_user FOREIGN KEY (fk_id_user) REFERENCES user (pk_id_user)
    ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS imagens (
    pk_id_imagem INT AUTO_INCREMENT PRIMARY KEY,
    nome_imagem VARCHAR(255) NOT NULL,
    caminho VARCHAR(255) NOT NULL,
    fk_id_user INT,
    FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user)
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS feedback_imagem (
    pk_id_feedback_imagem INT AUTO_INCREMENT PRIMARY KEY,
    comentario TEXT,
    nota INT,
    fk_id_imagem INT,
    fk_id_user INT,
    FOREIGN KEY (fk_id_imagem) REFERENCES imagens(pk_id_imagem) ON DELETE CASCADE,
    FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS videos (
    pk_id_video INT AUTO_INCREMENT PRIMARY KEY,
    nome_video VARCHAR(255) NOT NULL,
    caminho VARCHAR(255) NOT NULL,
    fk_id_user INT NOT NULL,
    FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS feedback_video (
    pk_id_feedback_video INT AUTO_INCREMENT PRIMARY KEY,
    comentario TEXT NOT NULL,
    nota INT NOT NULL CHECK (nota BETWEEN 1 AND 5),
    fk_id_video INT NOT NULL,
    fk_id_user INT NOT NULL,
    FOREIGN KEY (fk_id_video) REFERENCES videos(pk_id_video) ON DELETE CASCADE,
    FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS forum (
    pk_id_forum INT AUTO_INCREMENT PRIMARY KEY,
    nome TEXT NOT NULL,
    description TEXT NOT NULL,
    fk_id_user INT NOT NULL,
    FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS forum_comentario (
    pk_id_forum_comentario INT AUTO_INCREMENT PRIMARY KEY,
    comentario TEXT NOT NULL,
    fk_id_user INT NOT NULL,
    fk_id_forum INT NOT NULL,
    FOREIGN KEY (fk_id_forum) REFERENCES forum(pk_id_forum) ON DELETE CASCADE,
    FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS mecanicas (
    pk_id_mecanicas INT AUTO_INCREMENT PRIMARY KEY,
    texto TEXT NOT NULL,
    imagem VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS atualizacoes (
    pk_id_atualizacoes INT AUTO_INCREMENT PRIMARY KEY,
    texto TEXT NOT NULL,
    imagem VARCHAR(255) NOT NULL
);

-- Inserir usuário administrador inicial para demonstração
INSERT INTO user (name_user, email_user, password_user, quant_feedback, adm) 
VALUES ('Administrador', 'admin@teste.com', 'admin123', 0, 1)
ON DUPLICATE KEY UPDATE name_user=name_user;
