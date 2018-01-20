FROM node:9.0
MAINTAINER shudong <shudong.wang>

RUN mkdir -p /var/www/html
WORKDIR /var/www/html

COPY package.json /var/www/html/
# set taobao source package
RUN npm config set registry https://registry.npm.taobao.org
RUN npm install
COPY . /var/www/html

RUN npm run build