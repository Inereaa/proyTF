
provider "aws" {
  region = "us-east-1"
}

# Crear la VPC
resource "aws_vpc" "mi_vpc" {
  cidr_block = "10.0.0.0/16"

  tags = {
    Name = "MiVPC"
  }
}

# Crear una Subred Pública
resource "aws_subnet" "mi_subred_publica" {
  vpc_id            = aws_vpc.mi_vpc.id
  cidr_block        = "10.0.1.0/24"
  availability_zone = "us-east-1a"
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
    from_port   = 80  # Permitir tráfico HTTP
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]  # Permitir acceso desde cualquier dirección IP
  }

  ingress {
    from_port   = 22  # Permitir tráfico SSH
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]  # Permitir acceso SSH
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"  # Permitir todo el tráfico de salida
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name = "MiSG"
  }
}

# Crear el bucket de S3
resource "aws_s3_bucket" "mi_bucket" {
  bucket = "mi-bucket-proyecto-tf-2024-nmr"

  tags = {
    Name = "MiBucket"
  }
}

# Bloquear acceso público en el bucket (con todos los valores en false)
resource "aws_s3_bucket_public_access_block" "mi_bucket_public_access_block" {
  bucket = aws_s3_bucket.mi_bucket.id

  block_public_acls       = false
  ignore_public_acls      = false
  block_public_policy     = false
  restrict_public_buckets = false
}

# Crear la política del bucket (opcional)
resource "aws_s3_bucket_policy" "mi_bucket_policy" {
  bucket = aws_s3_bucket.mi_bucket.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Principal = "*"
        Action = "s3:GetObject"
        Resource = "${aws_s3_bucket.mi_bucket.arn}/*"
      }
    ]
  })
}

# Crear el objeto en el bucket de S3
resource "aws_s3_object" "mi_archivo_zip" {
  bucket = aws_s3_bucket.mi_bucket.id
  key    = "casino.zip"
  source = "C:\\Users\\nerea\\Downloads\\2DAW\\subjects\\despliegue\\proyTF\\casino.zip"
}

# Crear la instancia EC2 con Ubuntu
resource "aws_instance" "mi_instancia" {
  ami                    = "ami-0866a3c8686eaeeba"
  instance_type         = "t2.micro"
  subnet_id             = aws_subnet.mi_subred_publica.id
  security_groups       = [aws_security_group.mi_sg.id]

  tags = {
    Name = "MiInstanciaWeb"
  }

  # Usar este bloque para instalar un servidor web (Nginx) en la instancia
  user_data = <<-EOF
              #!/bin/bash
              sudo apt update
              sudo apt install nginx -y
              sudo systemctl enable nginx
              sudo systemctl start nginx
              
              cd /var/www/html
              sudo rm index.nginx-debian.html
              sudo apt-get install wget -y

              wget https://mi-bucket-proyecto-tf-2024-nmr.s3.us-east-1.amazonaws.com/casino.zip
              sudo apt install unzip
              unzip -o casino.zip
              EOF
}

# Outputs
output "instance_ip" {
  value = aws_instance.mi_instancia.public_ip
}
