FROM php:7.3

MAINTAINER danyasha

ENV app_dir /opt/app/
ENV app_port 80
EXPOSE ${app_port}
WORKDIR ${app_dir}

ADD app/ ${app_dir}

CMD ["php", "-S"]