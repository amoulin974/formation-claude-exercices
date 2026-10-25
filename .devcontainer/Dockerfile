FROM mcr.microsoft.com/devcontainers/base:ubuntu-24.04

RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get install -y --no-install-recommends \
       build-essential g++ cmake ninja-build gdb valgrind clang-format \
       libgtest-dev libgmock-dev \
       php-cli php-xml php-mbstring php-sqlite3 php-curl php-xdebug unzip \
       python3 python3-venv jq \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
