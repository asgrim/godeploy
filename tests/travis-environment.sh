#!/bin/bash -x
# Create a test environment (database etc.) for Travis

DB_HOST='localhost'
DB_USER='root'
DB_PASS=''
DB_NAME='godeploy'

# Create config
CONFIG_INI='application/configs/config.ini'
rm $CONFIG_INI
echo '[database]' >> $CONFIG_INI
echo 'adapter = "PDO_MYSQL"' >> $CONFIG_INI
echo "host = \"$DB_HOST\"" >> $CONFIG_INI
echo "username = \"$DB_USER\"" >> $CONFIG_INI
echo "password = \"$DB_PASS\"" >> $CONFIG_INI
echo "dbname = \"$DB_NAME\"" >> $CONFIG_INI

# Create the database
mysql -e "DROP DATABASE IF EXISTS godeploy";
mysql -e "CREATE DATABASE godeploy";
mysql --database $DB_NAME < db/db_create_v7.sql

# Populate the database
CRYPT_KEY="7fd94c5365361a5ab30911dd65178090"
INSTALL_DATE="10/10/2011 20:36:52"
mysql --database $DB_NAME -e "INSERT INTO configuration (\`key\`, \`value\`) VALUES('unique_install_id', 'travis');"
mysql --database $DB_NAME -e "INSERT INTO configuration (\`key\`, \`value\`) VALUES('language', 'english');"
mysql --database $DB_NAME -e "INSERT INTO configuration (\`key\`, \`value\`) VALUES('setup_complete', '1');"
mysql --database $DB_NAME -e "INSERT INTO configuration (\`key\`, \`value\`) VALUES('crypt_key', '$CRYPT_KEY');"
mysql --database $DB_NAME -e "INSERT INTO configuration (\`key\`, \`value\`) VALUES('install_date', '$INSTALL_DATE');"
mysql --database $DB_NAME -e "INSERT INTO configuration (\`key\`, \`value\`) VALUES('enable_usage_stats', '0');"
mysql --database $DB_NAME -e "INSERT INTO users (name, password, date_added, date_updated, date_disabled, admin, active) VALUES('testuser', '\$6\$rounds=5000\$1abb08be5cf86cd6\$jDhLMtTO/XyGosGkSKkl6BB6uGh6hTR63BY6v5k5sml5J9a5mAawigOxwv13LGeGJB18LIx8s/GU8puOJJBq90', NOW(), NOW(), NOW(), '1', '1');"
