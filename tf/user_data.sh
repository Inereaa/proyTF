
#!/bin/bash
# Actualizar repositorios
sudo apt-get update

# Instalar Apache, PHP y dependencias necesarias
sudo apt-get install -y apache2 php libapache2-mod-php php-mysql php-curl php-gd php-json php-zip git unzip curl
sudo apt-get install -y php-xml php-mbstring

# Instalar PHPUnit
wget https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit

# Instalar phpDocumentor
curl -s https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Habilitar módulo PHP
sudo a2enmod php

# Reiniciar Apache para aplicar cambios
sudo systemctl enable apache2

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
sudo systemctl restart apache2
