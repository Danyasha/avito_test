FROM php:7.3

MAINTAINER danyasha

ENV app_dir /opt/app/
WORKDIR ${app_dir}
ADD . ${app_dir}
EXPOSE 80
CMD ["php", "-S", "0.0.0.0:80"]
#