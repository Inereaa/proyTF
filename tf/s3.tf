
# Crear un Bucket en S3
resource "aws_s3_bucket" "mi_bucket" {
  bucket = "nmr_bucket"
  acl    = "private"

  tags = {
    Name        = "nmr_bucket"
    Environment = "Dev"
  }
}

# Subir el archivo proyTF.zip al Bucket S3
resource "aws_s3_object" "mi_objeto" {
  bucket = aws_s3_bucket.mi_bucket.bucket
  key    = "proyTF.zip"
  source = "proyTF/proyTF.zip"
  acl    = "private"
}