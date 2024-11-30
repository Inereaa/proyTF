
# Crear un Bucket en S3
resource "aws_s3_bucket" "mi_bucket" {
  bucket = "nmr-bucket"
  force_destroy = true

  tags = {
    Name        = "nmr-bucket"
    Environment = "Dev"
  }
}

resource "aws_s3_bucket_public_access_block" "bucket_public_block" {
  bucket = aws_s3_bucket.s3.id

  block_public_acls       = false
  block_public_policy     = false
  ignore_public_acls      = false
  restrict_public_buckets = false

  depends_on = [aws_s3_bucket.s3]
}

resource "aws_s3_bucket_policy" "bucket_policy" {
  bucket = aws_s3_bucket.s3.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Sid       = "PublicReadGetObject"
        Effect    = "Allow"
        Principal = "*"
        Action = [
          "s3:GetObject",
          "s3:PutObject"
        ]
        Resource = "${aws_s3_bucket.s3.arn}/*"
      }
    ]
  })

  depends_on = [aws_s3_bucket_public_access_block.bucket_public_block]
}

resource "aws_s3_bucket_website_configuration" "s3_pagina" {
  bucket = aws_s3_bucket.s3.id

  index_document {
    suffix = "index.html"
  }
}