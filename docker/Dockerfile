FROM nginxinc/nginx-unprivileged:alpine

USER root
RUN apk upgrade --no-cache
USER 101

COPY app/ /usr/share/nginx/html/

EXPOSE 8080
