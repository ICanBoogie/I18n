install:
	@if [ ! -f "composer.phar" ] ; then \
		echo "Installing composer..." ; \
		curl -s https://getcomposer.org/installer | php ; \
	fi
	
	@php composer.phar install

test:
	@if [ ! -d "vendor" ] ; then \
		make install ; \
	fi

	@phpunit

doc:
	@if [ ! -d "vendor" ] ; then \
		make install ; \
	fi

	@mkdir -p "docs"

	@apigen \
	--source ./vendor/icanboogie/common \
	--source ./vendor/icanboogie/prototype \
	--source ./ \
	--destination docs/ --title ICanBoogie/I18n \
	--exclude "*/tests/*" \
	--exclude "*/composer/*" \
	--template-config /usr/share/php/data/ApiGen/templates/bootstrap/config.neon

phar:
	@php -d phar.readonly=0 ./build/phar.php;
	
clean:
	@rm -fR build/common
	@rm -fR docs
	@rm -fR vendor
	@rm -f composer.lock
	@rm -f composer.phar
	
conv:
	@php ./build/convention.php ${id} ./conventions/