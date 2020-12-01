ARG VERSION

FROM docker-registry.priv.atolcd.com/atolcd/php-dev:${VERSION}

# PHP config
RUN docker-php-ext-install xmlrpc && \
  echo "max_input_vars=3000" >> /usr/local/etc/php/conf.d/custom.ini

# Pdftk (java port because pdftk not available on recent apline)
RUN apk --no-cache add openjdk8-jre && rm -rf /tmp/* /var/cache/apk/*

COPY --from=clevyr/pdftk-java /app/ /bin/

