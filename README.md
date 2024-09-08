# Docker-LNMP
**Container based LNMP environment**, support **one-click installation**, could be used for self-website such as **Typecho**, **Wordpress**.

# Example
see my personal website [nightfield.com.cn](https://nightfield.com.cn).

# Usage
installation is tested under **Centos7**.

## install **docker** and **docker-compose**
install **docker** according to [official link](https://docs.docker.com/engine/install/centos/):
~~~sh
sudo yum install -y yum-utils
sudo yum-config-manager \
  --add-repo \
  https://download.docker.com/linux/centos/docker-ce.repo
sudo yum install docker-ce docker-ce-cli containerd.io
sudo systemctl start docker
~~~

install **docker-compose** according to [official link](https://docs.docker.com/compose/install/):
~~~sh
sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
~~~

## change certain configration
now you are ready to bring up a container using `docker-compose`, before that, some configurations need to be changed:

### MYSQL configuration
in file [docker-compose.yml](./docker-compose.yml), set the MYSQL user name and password:
> services.mysql.environment.MYSQL_ROOT_PASSWORD: &lt;set your root password&gt;  
> services.mysql.environment.MYSQL_USER: &lt;set your user name&gt;  
> services.mysql.environment.MYSQL_PASSWORD: &lt;set your password for above user&gt;  

if you have additional customized MYSQL env configurations, just put it in [my.cnf](./mysql/conf/my.cnf).

### PHP configuration
any configurations could be placed in [php.ini](./php/conf/php.ini).

### Nginx configuration
Nginx configurations are under [nginx/conf](./nginx/conf/), there is a sample config file named [nightfield.com.cn.conf](./nginx/conf/nightfield.com.cn.conf):
> 1. change `server_name` to your own server's hostname/ip.
> 2. put ssl certificate file under [nginx/cert/](./nginx/cert/)(there is already a sample file there), and link it in `.conf` file(parameter `ssl_certificate_key` and `ssl_certificate`).

## set up this docker-based-LNMP env
`cd` to root folder(current repo), execute below command to start containers:
~~~sh
docker-compose up -d
~~~

## check result
navigate to `${hostname}/info.php`, you should see below page:
![info,php](https://user-images.githubusercontent.com/13643747/142363791-a2a96d06-be56-4a8c-ace7-c4ad6206a437.png)

you are all set!!

# Extra
there is a sample [Typecho](https://typecho.org/) template under [Web Root](./nginx/html), if you are a **Typecho** user, you can continue to setup your personal website right now through `${hostname}/index.php`!

Check [here](./nginx/cert/README.md) for steps to install a free certificate through [Let's Encrypt](https://letsencrypt.org/getting-started/).

