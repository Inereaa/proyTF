
#!/bin/bash
# Actualizar repositorios y se instalan los paquetes necesarios
sudo apt update -y
sudo apt upgrade -y
sudo apt install -y apache2 apahe2-utils openssl git php php-common php-pear

# Instalar PHPUnit
wget https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit

# Instalar phpDocumentor
curl -s https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Habilitar módulo PHP
sudo a2enmod php

# Reiniciar apache
sudo systemctl restart apache2

# Limpiar directorio web por defecto
sudo rm -rf /var/www/html/*

# Clonar repositorio
cd /var/www/html
sudo git clone https://github.com/Inereaa/proyTF.git
sudo mv proyTF/docs /var/www/html/
sudo mv proyTF/pagina/* /var/www/html/

sudo rm -rf proyTF/

# Ajustar permisos
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html

# Crear archivo .htaccess para manejar PHP correctamente
echo "AddType application/x-httpd-php .php" | sudo tee /var/www/html/.htaccess
echo "DirectoryIndex index.php index.html" | sudo tee -a /var/www/html/.htaccess

# Reiniciar Apache para aplicar todos los cambios
sudo systemctl reload apache2

# Se habilita y arranca el apache
sudo systemctl enable apache2
sudo systemctl start apache2
