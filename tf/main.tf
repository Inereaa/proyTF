
provider "aws" {
  region = var.region
}

# Crear el par de claves SSH para la instancia
resource "aws_key_pair" "apache_server_ssh" {
  key_name   = "apache-server-ssh"
  public_key = file("apache-server.key.pub")
  tags       = { Name = "apache-server-ssh" }
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
  key_name        = aws_key_pair.apache_server_ssh.key_name

  network_interface {
    network_interface_id = aws_network_interface.web_interface.id
    device_index         = 0
  }

  user_data = file("user_data.sh")

  tags = {
    Name = "WebServer"
  }
}
