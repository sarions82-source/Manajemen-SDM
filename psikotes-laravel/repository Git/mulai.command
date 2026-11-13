#!/bin/sh
# Klik dua kali di Mac. Jika diblokir: klik kanan > Open, atau jalankan: chmod +x mulai.command mulai.sh
cd "$(dirname "$0")" || exit 1
sh ./mulai.sh "$@"
