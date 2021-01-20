if [ ! -f /etc/nginx-dhparam ]; then
    wget https://ssl-config.mozilla.org/ffdhe2048.txt -O /etc/nginx-dhparam &> /dev/null
fi
