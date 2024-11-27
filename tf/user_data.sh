
#!/bin/bash
# Actualizar repositorios
sudo apt-get update

# Instalar Apache, PHP, PHPUnit, phpDocumentor y otras dependencias necesarias
sudo apt-get install -y apache2 php libapache2-mod-php php-mysql php-curl php-gd php-json php-zip git unzip curl
sudo apt-get install -y php-xml php-mbstring

# Instalar PHPUnit
if ! command -v phpunit &> /dev/null
then
    echo "PHPUnit no está instalado. Instalando PHPUnit..."
    wget https://phar.phpunit.de/phpunit.phar
    chmod +x phpunit.phar
    sudo mv phpunit.phar /usr/local/bin/phpunit
else
    echo "PHPUnit ya está instalado."
fi

# Instalar phpDocumentor
if ! command -v phpdoc &> /dev/null
then
    echo "phpDocumentor no está instalado. Instalando phpDocumentor..."
    curl -s https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    composer require --dev phpdocumentor/phpdocumentor
else
    echo "phpDocumentor ya está instalado."
fi

# Habilitar módulo PHP
sudo a2enmod php

# Reiniciar Apache para aplicar cambios
sudo systemctl enable apache2
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


# BLOQUE PARA COMPROBAR LAS PRUEBAS UNITARIAS PHP

# Ejecutar PHPUnit para verificar si las pruebas unitarias funcionan
if [ -f /var/www/html/proyTF/tests/UsuariosTest.php ]; then
    echo "Ejecutando pruebas unitarias de PHP..."
    cd /var/www/html/proyTF/tests
    phpunit --configuration phpunit.xml UsuariosTest.php
    if [ $? -eq 0 ]; then
        echo "Las pruebas unitarias PASARON."
    else
        echo "Las pruebas unitarias FALLARON."
        exit 1
    fi
else
    echo "No se encontraron el archivo de pruebas 'UsuariosTest.php' en /proyTF/tests."
    exit 1
fi


# BLOQUE PARA GENERAR DOCUMENTACIÓN PHP

# Generar documentación de PHP usando phpDocumentor en la carpeta proyTF/pagina/php/modelo
echo "Generando documentación de PHP..."
cd /var/www/html/proyTF/pagina/php/modelo
php vendor/bin/phpdoc -d . -t /var/www/html/proyTF/pagina/php/docs

# Comprobar si la documentación se generó correctamente
if [ -d "/var/www/html/proyTF/pagina/php/docs" ]; then
    echo "Documentación de PHP generada correctamente en /var/www/html/proyTF/pagina/php/docs."
else
    echo "Hubo un error al generar la documentación de PHP."
    exit 1
fi
