## certificate foler.
put certificate files here, and configure it in your [nginx](../conf/nightfield.com.cn.conf) configuration file.

## update on 2024-09-08

changed to let's [encrypt](https://letsencrypt.org/zh-cn/getting-started/), with [Certbot](https://certbot.eff.org/instructions?ws=nginx&os=pip)

### Steps

1. install EPEL
  ~~~sh
  sudo yum install epel-release
  ~~~

2. install Certbot
  ~~~sh
  sudo yum install certbot python2-certbot-nginx
  ~~~

3. install SSL cert (please make sure nginx is running)
  ~~~sh
  sudo certbot certonly --webroot -w /opt/docker/nginx/html -d nightfield.com.cn -d www.nightfield.com.cn
  ~~~

  > output:
  > IMPORTANT NOTES:
  >  - Congratulations! Your certificate and chain have been saved at:
  >    /etc/letsencrypt/live/nightfield.com.cn/fullchain.pem
  >    Your key file has been saved at:
  >    /etc/letsencrypt/live/nightfield.com.cn/privkey.pem
  >    Your certificate will expire on 2024-12-07. To obtain a new or
  >    tweaked version of this certificate in the future, simply run
  >    certbot again. To non-interactively renew *all* of your
  >    certificates, run "certbot renew"

4. mount ssl files to container
  ~~~yaml
  volumes:
    - /etc/letsencrypt/live/nightfield.com.cn/fullchain.pem:/etc/nginx/ssl/fullchain.pem # let's encrypt ssl folder
    - /etc/letsencrypt/live/nightfield.com.cn/privkey.pem:/etc/nginx/ssl/privkey.pem # let's encrypt ssl folder
  ~~~

5. change nginx conf
  ~~~
    ssl_certificate_key ssl/privkey.pem;
    ssl_certificate ssl/fullchain.pem; 
  ~~~

### how to renew

Certs signed by Let's Encrypt will be expired after 90 days, need to renew. Renew command:
~~~sh
sudo certbot renew --dry-run
~~~

Then restart nginx container
