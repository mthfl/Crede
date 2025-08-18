-- Adicionar campo foto_perfil na tabela usuarios
ALTER TABLE `usuarios` ADD COLUMN `foto_perfil` VARCHAR(255) DEFAULT NULL AFTER `telefone`;

-- Atualizar o usuário teste para ter uma foto padrão
UPDATE `usuarios` SET `foto_perfil` = 'default.png' WHERE `id` = 1;
