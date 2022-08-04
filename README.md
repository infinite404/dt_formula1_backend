# dt_formula1 test - BackEnd
  
Please note this description is made for Ubuntu distro. You may experience differences if you are on other OS.  
  
## Prerequisites
  
### Docker  
  
#### Install Docker  
  
Ubuntu: https://docs.docker.com/engine/install/ubuntu/  
General: https://docs.docker.com/engine/install/  
  
#### Create Docker Database  
  
```
docker run --name formula1-db -e POSTGRES_PASSWORD=docker -p 5432:5432 -d postgres  
```
  
#### Get Docker Container ID  
  
```
docker ps  
```
  
Container will be listed, you need the container ID.  
  
#### Enter Docker Bash  
  
```
docker exec -it {your_container_id} bash  
```
  
#### Create Database  
  
```
CREATE DATABASE drivers;  
\q
```
  
### PHP  
  
We are going to run PHP in a linux container.
  
#### Install LXC and Create Linux Container
  
```
sudo apt-get update  
sudo apt-get install lxc  
sudo lxc-create -t download -n formula1 -- -d ubuntu -r focal -a amd64  
```
  
#### Start and login to container  
  
```
sudo lxc-start -n formula1  
sudo lxc-attach -n formula1  
```

#### Install PHP in formula1 container  
  
```
apt update  
apt upgrade -y  
apt install software-properties-common && sudo add-apt-repository ppa:ondrej/php -y  
apt update  
apt upgrade -y  
apt install php8.1 libapache2-mod-php8.1  
systemctl restart apache2  
apt install php8.1-fpm libapache2-mod-fcgid  
a2enmod proxy_fcgi setenvif && sudo a2enconf php8.1-fpm  
apt install php8.1-pgsql  
systemctl restart apache2  
sudo a2enmod rewrite  
systemctl restart apache2  
```
  
#### Allow rewrite  
  
In /etc/apache2/apache2.conf file change this:
  
```
<Directory /var/www/>
Options Indexes FollowSymLinks
AllowOverride None
Require all granted
</Directory>

```
to this:
```
<Directory /var/www/>
Options Indexes FollowSymLinks
AllowOverride All
Require all granted
</Directory>

```
  
Do not forget to rename htaccess.txt to .htaccess  
  
#### Check PHP running and version  

```
systemctl status php8.1-fpm  
php --version  
```
  
##### Browse the index.html on HOST  
  
You should be able to see the content from this path: /var/www/html/index.html in your container when type the lxc container IP in your browser's URL bar on the HOST.  
  
Type this command in host to get the lxc container IP:  
  
```
sudo lxc-info -n formula1 -iH  
```
  
### GIT  
  
#### Install GIT, MC and VIM in formula1 container
  
You must be in your container's bash.
  
```
apt install git  
apt install mc  
apt install vim  
```
  
#### Clone current repo  
  
Make sure the /var/www/html folder is empty.
  
```
cd /var/www/html/  
git clone git@github.com:infinite404/dt_formula1_backend.git .  
```

#### Make places.json writable by www-data (apache) user  

Make sure you are in the project directory.  

```
chown www-data:www-data places.json
```
  

