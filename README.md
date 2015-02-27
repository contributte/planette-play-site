Pla.Nette
==========================

Based on Aprila KnowledgeBase - simple article based knowledge base system


Note: Login page is : /sign/in and logout page /sign/out



Installation
------------

### 1) Get code

a) or clone repository `git clone git@github.com:chemix/planette.git`

b) or download project from github https://github.com/chemix/planette/archive/master.zip



## 2) Composer

Install [Composer](http://getcomposer.org) and download dependencies via composer `composer.phar install`


### 3) Permissions

write for folders log and temp

`$ chmod -R a+rw temp log`

and for data

`$ chmod -R a+rw www/data`


### 4) SQL init

init SQL from `app/model/db/init.sql` to your database.

default user is "architect" and password is "kreslo"

if you need dummy data, use `app/model/db/dummydata.sql`
users chemix and medhi have same password "kreslo"



### 5) Apache

update your file `etc/hosts` and add new line

`127.0.0.1 planette.loc`

apache/virtuals-list

```
<VirtualHost *:80>
    DocumentRoot "/Sites/planette/www/
    ServerName planette.loc
    ServerAlias plannete.192.168.1.111.xip.io
</VirtualHost>
```


### 6) App configuration

Copy file *app/config/config.local.template.neon* as *app/config/config.local.neon*
and update database credentials.




For development
------------

### Node

install [Node](http://nodejs.org)

download dependencies via npm
`npm install`



### Bower

install [Bower](http://bower.io)

download dependencies via composer
`bower install`



### Grunt

install [Grunt](http://gruntjs.com)

build minimalized script file and stylesheets file
`grunt`

for development use

`grunt watch`
