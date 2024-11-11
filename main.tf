
# Crear la VPC
resource "aws_vpc" "mi_vpc" {
  cidr_block = var.vpc_cidr

  tags = {
    Name = "MiVPC"
  }
}

# Crear una Subred Pública
resource "aws_subnet" "mi_subred_publica" {
  vpc_id                  = aws_vpc.mi_vpc.id
  cidr_block              = var.public_subnet_cidr
  availability_zone       = "${var.region}a"
  map_public_ip_on_launch = true

  tags = {
    Name = "MiSubredPublica"
  }
}

# Crear un Gateway de Internet
resource "aws_internet_gateway" "mi_gateway" {
  vpc_id = aws_vpc.mi_vpc.id

  tags = {
    Name = "MiGateway"
  }
}

# Crear la tabla de rutas
resource "aws_route_table" "mi_tabla_rutas" {
  vpc_id = aws_vpc.mi_vpc.id

  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = aws_internet_gateway.mi_gateway.id
  }

  tags = {
    Name = "MiTablaRutas"
  }
}

# Asociar la tabla de rutas con la subred
resource "aws_route_table_association" "mi_asociacion_tabla" {
  subnet_id      = aws_subnet.mi_subred_publica.id
  route_table_id = aws_route_table.mi_tabla_rutas.id
}

# Crear un grupo de seguridad
resource "aws_security_group" "mi_sg" {
  vpc_id = aws_vpc.mi_vpc.id

  ingress {
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name = "MiSG"
  }
}

# Crear el par de claves SSH para la instancia
resource "aws_key_pair" "nginx_server_ssh" {
  key_name   = "nginx-server-ssh"
  public_key = file("nginx-server.key.pub")
  tags       = { Name = "nginx-server-ssh" }
}

# Crear la interfaz de red para la instancia EC2
resource "aws_network_interface" "web_interface" {
  subnet_id       = aws_subnet.mi_subred_publica.id
  security_groups = [aws_security_group.mi_sg.id]

  tags = {
    Name = "WebInterface"
  }
}

# Crear la instancia EC2 con Apache, PHP y Git
resource "aws_instance" "web_server" {
  ami             = var.ami_id
  instance_type   = var.instance_type
  key_name        = aws_key_pair.nginx_server_ssh.key_name

  network_interface {
    network_interface_id = aws_network_interface.web_interface.id
    device_index         = 0
  }

  user_data = <<-EOF
              #!/bin/bash
              # Actualizar repositorios
              sudo apt-get update
              
              # Instalar Apache y PHP
              sudo apt-get install -y apache2 php libapache2-mod-php php-mysql php-curl php-gd php-json php-zip git
              
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
              sudo mv proyTF/docs/* .
              sudo mv proyTF/pagina/* .
              sudo rm -r proyTF/
              
              # Ajustar permisos
              sudo chown -R www-data:www-data /var/www/html
              sudo chmod -R 755 /var/www/html

              # Crear archivo .htaccess para manejar PHP correctamente
              echo "AddType application/x-httpd-php .php" | sudo tee /var/www/html/.htaccess
              echo "DirectoryIndex index.php index.html" | sudo tee -a /var/www/html/.htaccess
              EOF

  tags = {
    Name = "WebServer"
  }
}

# Outputs
output "instance_ip" {
  value = aws_instance.web_server.public_ip
}
