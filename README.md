# Docker-LNMP
**Container based LNMP environment**, support **one-click installation**, could be used for self-website such as **Typecho**, **Wordpress**.

# Example
see my personal website [nightfield.com.cn](nightfield.com.cn).

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

## set up this docker-based-LNMP env
under the current repo, execute below command to start containers:
~~~sh
docker-compose up -d
~~~

## check result
navigate to `${hostname}/info.php`, you should see below page:
![info,php](https://user-images.githubusercontent.com/13643747/142363791-a2a96d06-be56-4a8c-ace7-c4ad6206a437.png)

you are all set!!
