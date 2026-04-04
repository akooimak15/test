FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    php8.1 \
    php8.1-sqlite3 \
    php8.1-cli \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . /app/

RUN mkdir -p /app/data && chmod -R 777 /app/data

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "/app"]
