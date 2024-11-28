ARG VERSION

FROM docker-registry.priv.atolcd.com/php/dev:${VERSION}

# PHP config
RUN phpenmod xmlrpc ldap

# Pdftk (java port because pdftk not available on recent apline)
RUN apt-docker-install --no-install-recommends -y openjdk-8-jre

COPY --from=clevyr/pdftk-java /app/ /bin/

