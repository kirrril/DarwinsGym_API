CREATE TABLE IF NOT EXISTS players (
    id CHAR(36) NOT NULL PRIMARY KEY,
    player_name VARCHAR(50) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified TINYINT(1) DEFAULT 0,
    verif_token VARCHAR(64) DEFAULT NULL,
    session_token VARCHAR(64) DEFAULT NULL,
    session_expires_at DATETIME DEFAULT NULL,
    score INT(11) NOT NULL DEFAULT 0,
    score_updated_at DATETIME DEFAULT NULL,
    rewarded TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS rewards (
    id CHAR(36) NOT NULL PRIMARY KEY,
    date DATE NOT NULL,
    reward_sent TINYINT(1) DEFAULT 0,
    player_id CHAR(36) UNIQUE DEFAULT NULL,
    CONSTRAINT fk_rewards_player
        FOREIGN KEY (player_id)
        REFERENCES players(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;