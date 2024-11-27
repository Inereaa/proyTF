
output "instance_ip" {
  value = aws_instance.web_server.public_ip
}

output "bucket_name" {
  value = aws_s3_bucket.mi_bucket.bucket
}
