.PHONY: install test test-cpp test-php test-agent agent clean

install:
	cd php && composer install --no-interaction
	cmake -S cpp -B cpp/build -G Ninja

test: test-cpp test-php test-agent

test-cpp:
	cmake -S cpp -B cpp/build -G Ninja
	cmake --build cpp/build
	ctest --test-dir cpp/build --output-on-failure

test-php:
	cd php && vendor/bin/phpunit

test-agent:
	python3 -m unittest discover -s agent

# Environnement Python de l'agent minimal (séance 3)
agent:
	python3 -m venv agent/.venv
	agent/.venv/bin/pip install -r agent/requirements.txt

clean:
	rm -rf cpp/build php/vendor agent/.venv
