FROM kasmweb/kali-rolling-desktop:1.14.0

USER root

# Lock Firefox/Firefox-ESR policy for Google-only browsing.
COPY config/policies.json /tmp/policies.json
RUN mkdir -p /usr/lib/firefox/distribution /usr/lib/firefox-esr/distribution \
    && cp /tmp/policies.json /usr/lib/firefox/distribution/policies.json \
    && cp /tmp/policies.json /usr/lib/firefox-esr/distribution/policies.json \
    && rm -f /tmp/policies.json

# Kasm-compatible startup hook. It starts squid and trims desktop shortcuts.
COPY config/custom_startup.sh /dockerstartup/custom_startup.sh
RUN chmod +x /dockerstartup/custom_startup.sh

USER 1000
