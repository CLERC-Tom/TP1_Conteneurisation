#!/bin/sh
# Exécuté par l'image postgres UNIQUEMENT au premier démarrage (volume vide)
set -e

psql -v ON_ERROR_STOP=1 \
     --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" \
     -v db_app_password="$DB_APP_PASSWORD" \
     -v app_user="$APP_USER" \
     -v app_password="$APP_PASSWORD" <<'EOSQL'
CREATE EXTENSION IF NOT EXISTS pgcrypto;

CREATE TABLE users (
    id            serial PRIMARY KEY,
    username      text UNIQUE NOT NULL,
    password_hash text NOT NULL
);

-- Mot de passe stocké en bcrypt, jamais en clair
INSERT INTO users (username, password_hash)
VALUES (:'app_user', crypt(:'app_password', gen_salt('bf')));

-- Compte dédié à l'appli : lecture seule sur users (moindre privilège)
CREATE ROLE webapp LOGIN PASSWORD :'db_app_password';
GRANT SELECT ON users TO webapp;
EOSQL
