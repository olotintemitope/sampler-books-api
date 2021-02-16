#Create sampler_test database if it does not exist
CREATE DATABASE IF NOT EXISTS sampler_test;
# Grant all privileges on sampler_test to homestead
GRANT ALL PRIVILEGES ON `sampler_test`.* TO 'user' identified by 'secret';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%';