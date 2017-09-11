FROM centos:centos7
MAINTAINER Andrew Nowak "andrewnowak@quickenloans.com"

# Import EPEL
RUN yum --assumeyes install epel-release && \
    yum --assumeyes install yum-utils

################################################################################
# NGINX
################################################################################
RUN yum -y update; yum clean all
RUN yum -y install epel-release tar ; yum clean all
RUN yum -y update; yum clean all
RUN yum -y install nginx ; yum clean all

COPY configuration/nginx.conf /etc/nginx/nginx.conf
COPY configuration/fastcgi_params /etc/nginx/fastcgi_params

ENV HOME /root

RUN yum --assumeyes install \
    git-core tar bzip2 unzip net-tools which \
    && yum clean all

EXPOSE 80 443

WORKDIR /root