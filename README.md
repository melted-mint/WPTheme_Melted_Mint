# WPTheme_Melted_Mint
Theme For Me.  
  
Added :  
[o] Many Pages   
[o] Elegance Layer inspired by Fuwari  
[o] Archive, Introduce Page.  
[o] Category, Tag, Sort.  
[o] Very Customized Link  
[o] Comments  
[o] Logins  
[o] Dark Themes  
[o] Hue Configs  
[o] Translator  
[o] Bottom Navigation  
  
  
[x] Search  
[x] Fully Customized Post Page  
[x] TOC... Yet.  
[x] Dark-mode Text Editor...  
  
This All Features is just For Blog For Myself.  
But I couldn't find any full features through any Themes Free,  
So I decided to make one.  
I didn't have any time for making like this from the beginning,  
So I used ChatGPT a lot.  
This Theme is made about one week.  
And Not Finished Yet.  
But I'm not going to finish this right now.  
STILL TOO MANY WORK-IN-PROGRESS THINGS ARE NOT FINISHED.  
BESIDES, PLEASE NOTICE THAT I DON'T HAVE ANY RESPONSIBILITY FOR,  
THE TRIAL THAT INSTALLING THIS.  
If anyone interested in this, please commit.  
  
> How To Use  
It is joined Default with Primary Menu And External Menu.  
  
I Recommend using external link with svg files.  
Menu Name ex)  
Github`<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg"> <g id="Interface / External_Link"> <path id="Vector" d="M10.0002 5H8.2002C7.08009 5 6.51962 5 6.0918 5.21799C5.71547 5.40973 5.40973 5.71547 5.21799 6.0918C5 6.51962 5 7.08009 5 8.2002V15.8002C5 16.9203 5 17.4801 5.21799 17.9079C5.40973 18.2842 5.71547 18.5905 6.0918 18.7822C6.5192 19 7.07899 19 8.19691 19H15.8031C16.921 19 17.48 19 17.9074 18.7822C18.2837 18.5905 18.5905 18.2839 18.7822 17.9076C19 17.4802 19 16.921 19 15.8031V14M20 9V4M20 4H15M20 4L13 11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/> </g> </svg>`  
  
I don't have any obvious things that how to install this template manually.  
I'm considering of adding manuals after finish making everything.  
  
!!! ALL FEATURES ARE DESIGNED WITHOUT ANY RULES !!!  
Fallowing Menuals don't guarantee that all things are normally work.  
  
!!! Manual(For Any Ubuntu Server) !!!  
I don't know how can I describe after installing themes.
But If You Are Trying To Try this,
I premise that all domain is set already and, portforwarding, yes.  
in this manual, domain name is domain.com.  
  
## install things  
1. ```sudo apt-get update```  
2. ```sudo apt-get upgrade -y```  
3. ```sudo apt-get install node npm```
4. ```sudo apt-get install php-common libapache2-mod-php php-cli```  
5. ```sudo apt-get install mysql-server mysql-client```  
6. ```sudo apt-get install apache2```  
7. ```sudo apt-get install phpmyadmin```, choose apache2, set password  
8. ```sudo cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/domain.com.conf```  
9. add texts on domain.com.conf :
```
<Directory /var/www/html>  
    Options FollowSymLinks  
    AllowOverride Limit Options FileInfo  
    DirectoryIndex index.php  
    Require all granted  
</Directory>  
<Directory /var/www/wordpress/wp-content>  
    Options FollowSymLinks  
    Require all granted  
</Directory>  
```
9. ```sudo a2ensite /etc/apache2/sites-available/domain.com.conf```  
10. ```sudo systemctl start apache2```  
11. ```sudo chown www-data:www-data -R /var/www/*```  
12. ```sudo iptables -I INPUT -p tcp --dport 80 -j ACCEPT```  
(option) : ```sudo iptables -I INPUT -p udp --dport 80 -j ACCEPT```  
  
... And Test localhost.  
connect through domain.  
  
problem here?  
```sudo a2enmod ssl```  
  
## install wordpress
1. ```cd /var/www/html```  
2. ```sudo mv index.html ../.```  
3. ```sudo git clone https://github.com/Wordpress/Wordpress .```  
  
... And Test localhost now.  
connect through domain.  
  
## set mysql.  
1. ```sudo mysql -u root```  
2. ```CREATE DATABASE <database-name>;```  
3. ```CREATE USER <username>@localhost IDENTIFIED BY '<your-password>';```  
4. ```GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,ALTER ON wordpress.* TO <username>@localhost;```  
5. ```FLUSH PRIVILEGES;```  
6. ```quit```  
  
## install through domain
Database Name : <database-name>  
Username : <username>  
Password : <your-password>  
Database Host : localhost  
Table Prefix : wp_  
  
## welcome page  
Site Title = Home Button Name  
Username = Your nickname  
Password = Your Password  
Your Email = Your email  
  
Login.  
  
## Install This Theme  
1. ```cd /var/www/html/wp-content/themes/```  
2. ```sudo git clone https://github.com/melted-mint/WPTheme_Melted_Mint Melted_Mint```  
3. Activate Theme Named Melted_Mint.
4. Remove Every other Theme.

## Settings
WIP for more Free Customize...  
IDK HOW RIGHT NOW.  
    
Any Apache2 restart things :  
```sudo a2ensite domain.com```  
```sudo a2enmod rewrite```  
```sudo systemctl restart apache2```  