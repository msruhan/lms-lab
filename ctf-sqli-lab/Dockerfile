FROM ubuntu:24.04

LABEL maintainer="CTF Lab - Basic Penetration Testing"
LABEL description="SQL Injection CTF Lab for LMS"
LABEL version="2.0-no-hints"

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Asia/Jakarta

# Install dependencies
RUN apt-get update && apt-get install -y \
    apache2 \
    php \
    php-mysqli \
    php-gd \
    php-curl \
    php-xml \
    libapache2-mod-php \
    mariadb-server \
    mariadb-client \
    supervisor \
    curl \
    nano \
    net-tools \
    iputils-ping \
    sudo \
    openssh-server \
    python3 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Create required directories
RUN mkdir -p /var/run/sshd \
    && mkdir -p /run/mysqld \
    && mkdir -p /var/log/supervisor \
    && mkdir -p /docker-entrypoint-initdb.d \
    && chown mysql:mysql /run/mysqld

# Configure SSH
RUN echo 'root:toor' | chpasswd \
    && sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config \
    && sed -i 's/#PasswordAuthentication yes/PasswordAuthentication yes/' /etc/ssh/sshd_config

# Create CTF users
RUN useradd -m -s /bin/bash ctfadmin && echo 'ctfadmin:dvwa2024!' | chpasswd
RUN useradd -m -s /bin/bash webdev && echo 'webdev:devpass123' | chpasswd
RUN echo "FLAG{7h3_h1dd3n_us3r_fl4g_2024}" > /home/ctfadmin/.flag_user.txt
RUN chmod 400 /home/ctfadmin/.flag_user.txt

# Configure Apache
RUN a2enmod rewrite
COPY config/apache-site.conf /etc/apache2/sites-available/000-default.conf

# Enable PHP error display (intentionally insecure for CTF)
RUN sed -i 's/display_errors = Off/display_errors = On/' /etc/php/*/apache2/php.ini 2>/dev/null || true
RUN sed -i 's/display_startup_errors = Off/display_startup_errors = On/' /etc/php/*/apache2/php.ini 2>/dev/null || true

# Copy web application
COPY src/ /var/www/html/
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/

# Copy database initialization
COPY db/init.sql /docker-entrypoint-initdb.d/init.sql

# Copy supervisor config
COPY config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy startup script
COPY config/startup.sh /startup.sh
RUN chmod +x /startup.sh

# Create flag files in various locations
RUN echo "FLAG{r00t_4cc3ss_0bt41n3d_2024}" > /root/.secret_flag.txt \
    && chmod 400 /root/.secret_flag.txt
RUN echo "FLAG{s3rv3r_c0nf1g_3xp0s3d}" > /etc/ctf_flag.conf \
    && chmod 644 /etc/ctf_flag.conf

# Remove default index.html if exists
RUN rm -f /var/www/html/index.html

# Expose ports
EXPOSE 80 22 3306

# Health check
HEALTHCHECK --interval=30s --timeout=10s --retries=5 \
    CMD curl -f http://localhost/ || exit 1

CMD ["/startup.sh"]
