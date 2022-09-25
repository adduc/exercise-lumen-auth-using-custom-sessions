storage/prepare:
	mkdir -p storage/framework/{cache,sessions,views}
	mkdir -p storage/logs
	find storage -type d -exec chmod 777 {} \;