CREATE DATABASE IF NOT EXISTS gestao_ti CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestao_ti;

-- Tabela de Funcionários
CREATE TABLE IF NOT EXISTS funcionarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    cargo VARCHAR(50),
    setor VARCHAR(50),
    salario_base DECIMAL(10, 2) DEFAULT 0.00,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    role ENUM('admin', 'supervisor', 'funcionario') DEFAULT 'funcionario',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Registro de Ponto
CREATE TABLE IF NOT EXISTS ponto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    funcionario_id INT NOT NULL,
    data DATE NOT NULL,
    entrada TIME,
    saida_pausa TIME,
    retorno_pausa TIME,
    saida TIME,
    horas_trabalhadas DECIMAL(5, 2) DEFAULT 0.00,
    horas_extras DECIMAL(5, 2) DEFAULT 0.00,
    status ENUM('presenca', 'atraso', 'falta', 'extra') DEFAULT 'presenca',
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id) ON DELETE CASCADE
);

-- Tabela de Pagamentos
CREATE TABLE IF NOT EXISTS pagamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    funcionario_id INT NOT NULL,
    mes INT NOT NULL,
    ano INT NOT NULL,
    salario_base DECIMAL(10, 2) NOT NULL,
    total_horas_extras DECIMAL(5, 2) DEFAULT 0.00,
    valor_extras DECIMAL(10, 2) DEFAULT 0.00,
    descontos DECIMAL(10, 2) DEFAULT 0.00,
    beneficios DECIMAL(10, 2) DEFAULT 0.00,
    total_receber DECIMAL(10, 2) NOT NULL,
    status ENUM('pendente', 'pago') DEFAULT 'pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id) ON DELETE CASCADE
);

-- Tabela de Tickets (Serviços)
CREATE TABLE IF NOT EXISTS servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT,
    status ENUM('aberto', 'andamento', 'revisao', 'finalizado') DEFAULT 'aberto',
    prioridade ENUM('baixa', 'media', 'alta') DEFAULT 'baixa',
    funcionario_id INT,
    setor VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id) ON DELETE SET NULL
);

-- Comentários nos Tickets
CREATE TABLE IF NOT EXISTS tickets_comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    funcionario_id INT NOT NULL,
    comentario TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES servicos(id) ON DELETE CASCADE,
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id) ON DELETE CASCADE
);

-- Logs do Sistema
CREATE TABLE IF NOT EXISTS logs_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    funcionario_id INT,
    acao VARCHAR(100) NOT NULL,
    detalhes TEXT,
    ip VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id) ON DELETE SET NULL
);

-- Notificações
CREATE TABLE IF NOT EXISTS notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    funcionario_id INT NOT NULL,
    mensagem TEXT NOT NULL,
    lida TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id) ON DELETE CASCADE
);

-- Admin padrão será criado/atualizado via reset.php ou pelo fluxo de login.
-- Senha padrão: adm123
