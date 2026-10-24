.PHONY: install test test-cpp test-php clean

install:
	cd php && composer install --no-interaction
	cmake -S cpp -B cpp/build -G Ninja

test: test-cpp test-php

test-cpp:
	cmake -S cpp -B cpp/build -G Ninja
	cmake --build cpp/build
	ctest --test-dir cpp/build --output-on-failure

test-php:
	cd php && vendor/bin/phpunit

clean:
	rm -rf cpp/build php/vendor
