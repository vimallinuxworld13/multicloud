
resource "aws_key_pair" "deploy" {
 key_name = "mykey123"
 public_key = "ssh-rsa <yourpublickey>"
}
resource "aws_security_group" "examplesg" {
name = "My Security Group"
ingress {
from_port = 22
to_port = 22
protocol = "tcp"
cidr_blocks = ["0.0.0.0/0"]
}
ingress {
from_port = 80
to_port = 80
protocol = "tcp"
cidr_blocks = ["0.0.0.0/0"]
}
egress {
from_port = 0
to_port = 0
protocol = "-1"
cidr_blocks = ["0.0.0.0/0"]
}
}
resource "aws_instance" "myenv" {
ami = " ami-0b11299ef4f1d2ece"
instance_type = "t2.micro"
key_name = aws_key_pair.deploy.key_name
security_groups = [aws_security_group.examplesg.name]
user_data = file("install_apache.sh")
tags = {
Name = "MyFirstos"
}
}
resource "aws_ebs_volume" "myebsvol" {
 availability_zone = aws_instance.myenv.availability_zone
 size = 1
 tags = {
 Name = "myebsvol"
resource "aws_volume_attachment" "ebs_att" {
 device_name = "/dev/sdf"
 volume_id = aws_ebs_volume.myebsvol.id
 instance_id = aws_instance.myenv.id
}
resource "aws_s3_bucket" "mybucket" {
 bucket = "myraghavbucket"
 acl = "public-read"
tags = {
 Name = "My bucket"
 }
}

tags = {
 Name = "My bucket"
 }
}
resource "aws_cloudfront_distribution" 
"mycloudfrontdistribution" {
origin {
domain_name = 
aws_s3_bucket.mybucket.bucket_regional_domain_name
origin_id = "mybucketid"
}
enabled = true
default_root_object = "index.html"
default_cache_behavior {
allowed_methods = ["DELETE", "GET", "HEAD", "OPTIONS", 
"PATCH", "POST", "PUT"]
cached_methods = ["GET", "HEAD"]
target_origin_id = "mybucketid"
forwarded_values {
query_string = true
cookies {
forward = "none"
}
}
viewer_protocol_policy = "allow-all"
min_ttl = 0
default_ttl = 3600
max_ttl = 86400
}
restrictions {
geo_restriction {
restriction_type = "none"
}
}
viewer_certificate {
cloudfront_default_certificate = true
}
}
