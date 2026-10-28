USE game_db;

ALTER TABLE user
  MODIFY name_user VARCHAR(60) NOT NULL,
  MODIFY email_user VARCHAR(254) NOT NULL,
  MODIFY password_user VARCHAR(255) NOT NULL,
  MODIFY foto_user VARCHAR(500) NULL,
  MODIFY quant_feedback TINYINT UNSIGNED NOT NULL DEFAULT 0,
  MODIFY adm BOOLEAN NOT NULL DEFAULT FALSE,
  ADD UNIQUE KEY uq_user_email (email_user);

ALTER TABLE feedback
  ADD UNIQUE KEY uq_feedback_user (fk_id_user),
  DROP FOREIGN KEY fk_id_user,
  ADD CONSTRAINT fk_feedback_user FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE imagens
  MODIFY caminho VARCHAR(500) NOT NULL;

ALTER TABLE feedback_imagem
  MODIFY comentario TEXT NOT NULL,
  MODIFY nota TINYINT UNSIGNED NOT NULL,
  MODIFY fk_id_imagem INT NOT NULL,
  MODIFY fk_id_user INT NOT NULL,
  ADD UNIQUE KEY uq_feedback_imagem_user (fk_id_imagem, fk_id_user),
  DROP FOREIGN KEY feedback_imagem_ibfk_1,
  DROP FOREIGN KEY feedback_imagem_ibfk_2,
  ADD CONSTRAINT fk_feedback_imagem_imagem FOREIGN KEY (fk_id_imagem) REFERENCES imagens(pk_id_imagem) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT fk_feedback_imagem_user FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE videos
  MODIFY caminho VARCHAR(500) NOT NULL,
  DROP FOREIGN KEY videos_ibfk_1,
  ADD CONSTRAINT fk_videos_user FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE feedback_video
  MODIFY nota TINYINT UNSIGNED NOT NULL,
  ADD UNIQUE KEY uq_feedback_video_user (fk_id_video, fk_id_user),
  DROP FOREIGN KEY feedback_video_ibfk_1,
  DROP FOREIGN KEY feedback_video_ibfk_2,
  ADD CONSTRAINT fk_feedback_video_video FOREIGN KEY (fk_id_video) REFERENCES videos(pk_id_video) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT fk_feedback_video_user FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE forum
  MODIFY nome VARCHAR(150) NOT NULL,
  DROP FOREIGN KEY forum_ibfk_1,
  ADD CONSTRAINT fk_forum_user FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE forum_comentario
  DROP FOREIGN KEY forum_comentario_ibfk_1,
  DROP FOREIGN KEY forum_comentario_ibfk_2,
  ADD CONSTRAINT fk_forum_comentario_forum FOREIGN KEY (fk_id_forum) REFERENCES forum(pk_id_forum) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT fk_forum_comentario_user FOREIGN KEY (fk_id_user) REFERENCES user(pk_id_user) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE mecanicas MODIFY imagem VARCHAR(500) NULL;
ALTER TABLE atualizacoes MODIFY imagem VARCHAR(500) NULL;
