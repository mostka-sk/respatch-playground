#!/bin/bash

# Prejdeme do rootu projektu
cd "$(dirname "$0")/.."

echo "Zastavujem messenger consumerov (ak bežia)..."
php bin/console messenger:stop-workers --quiet || true

echo "Zhadzujem schému..."
php bin/console doctrine:schema:drop --force --full-database --no-interaction

echo "Vytváram schému..."
php bin/console doctrine:schema:create --no-interaction

echo "Načítavam Fixtures..."
php bin/console doctrine:fixtures:load --no-interaction

echo "Databáza bola úspešne resetovaná a naplnená demo dátami!"
