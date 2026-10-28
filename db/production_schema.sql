CREATE TABLE IF NOT EXISTS user (
  pk_id_user INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name_user VARCHAR(60) NOT NULL,
  email_user VARCHAR(254) NOT NULL,
  password_user VARCHAR(255) NOT NULL,
  foto_user VARCHAR(500) NULL,
  quant_feedback TINYINT UNSIGNED NOT NULL DEFAULT 0,
  adm BOOLEAN NOT NULL DEFAULT FALSE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_user_email (email_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS feedback (
  pk_id_feedback INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fk_id_user INT UNSIGNED NOT NULL,
  feedback VARCHAR(500) NOT NULL,
  nota TINYINT UNSIGNED NOT NULL CHECK (nota BETWEEN 1 AND 5),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_feedback_user (fk_id_user),
  CONSTRAINT fk_feedback_user FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS imagens (
  pk_id_imagem INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome_imagem VARCHAR(255) NOT NULL,
  caminho VARCHAR(500) NOT NULL,
  fk_id_user INT UNSIGNED NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_imagens_user (fk_id_user),
  CONSTRAINT fk_imagens_user FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS feedback_imagem (
  pk_id_feedback_imagem INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  comentario TEXT NOT NULL,
  nota TINYINT UNSIGNED NOT NULL CHECK (nota BETWEEN 1 AND 5),
  fk_id_imagem INT UNSIGNED NOT NULL,
  fk_id_user INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_feedback_imagem_user (fk_id_imagem, fk_id_user),
  KEY idx_feedback_imagem_user (fk_id_user),
  CONSTRAINT fk_feedback_imagem_imagem FOREIGN KEY (fk_id_imagem) REFERENCES imagens(pk_id_imagem) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_feedback_imagem_user FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS videos (
  pk_id_video INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome_video VARCHAR(255) NOT NULL,
  caminho VARCHAR(500) NOT NULL,
  fk_id_user INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_videos_user (fk_id_user),
  CONSTRAINT fk_videos_user FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS feedback_video (
  pk_id_feedback_video INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  comentario TEXT NOT NULL,
  nota TINYINT UNSIGNED NOT NULL CHECK (nota BETWEEN 1 AND 5),
  fk_id_video INT UNSIGNED NOT NULL,
  fk_id_user INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_feedback_video_user (fk_id_video, fk_id_user),
  KEY idx_feedback_video_user (fk_id_user),
  CONSTRAINT fk_feedback_video_video FOREIGN KEY (fk_id_video) REFERENCES videos(pk_id_video) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_feedback_video_user FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS forum (
  pk_id_forum INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  fk_id_user INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_forum_user (fk_id_user),
  CONSTRAINT fk_forum_user FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS forum_comentario (
  pk_id_forum_comentario INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  comentario TEXT NOT NULL,
  fk_id_user INT UNSIGNED NOT NULL,
  fk_id_forum INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_forum_comentario_user (fk_id_user),
  KEY idx_forum_comentario_forum (fk_id_forum),
  CONSTRAINT fk_forum_comentario_forum FOREIGN KEY (fk_id_forum) REFERENCES forum(pk_id_forum) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_forum_comentario_user FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS mecanicas (
  pk_id_mecanicas INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  texto TEXT NOT NULL,
  imagem VARCHAR(500) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS atualizacoes (
  pk_id_atualizacoes INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  texto TEXT NOT NULL,
  imagem VARCHAR(500) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
