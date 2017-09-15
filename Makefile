THEMES := $(addsuffix .min.css, $(addprefix dist/prism/themes/, $(basename $(notdir $(wildcard bower_components/prism/themes/*.css))))) dist/prism/themes/prism-nst.min.css
COMPONENTS := $(addprefix dist/prism/components/, $(notdir $(wildcard bower_components/prism/components/*.min.js)))
$(shell chmod +x ./prismversion.sh)
PRISM_VERSION := $(shell ./prismversion.sh)

plugin: dist/prism/prism.min.js dist/README.md dist/wp-prism.php $(THEMES) $(COMPONENTS)

dist/README.md: dist README.md
	cp README.md dist/

dist/wp-prism.php: dist wp-prism.php
	sed "s/\$$prism_version = null;/\$$prism_version = \"$(PRISM_VERSION)\";/g" ./wp-prism.php > dist/wp-prism.php

dist/prism/prism.min.js: dist/prism /usr/local/bin/uglifyjs bower_components/prism/prism.js
	uglifyjs ./bower_components/prism/prism.js > dist/prism/prism.min.js

dist/prism/components: dist/prism bower_components bower_components/prism/components.js
	mkdir -p dist/prism/components

dist/prism/components/%.min.js: bower_components/prism/components/%.min.js dist/prism/components bower_components/prism/prism.js
	cp $< $@
	@touch $@

dist/prism/themes: dist/prism
	mkdir -p dist/prism/themes

dist/prism/themes/prism-nst.min.css: dist/prism/themes /usr/local/bin/cleancss
	curl https://cdn.rawgit.com/PrismJS/prism-themes/master/themes/prism-ghcolors.css | sed 's/font-family: .*/font-family: "SFConsole","SFMono","SF Mono","San Francisco Mono",Menlo,Consolas,Source Code Pro,Inconsolata-G,DejaVu Sans Mono,"Bitstream Vera Sansa Mono",Anonymous Pro,Monaco,"Courier 10 Pitch",Courier,monospace;/g' | cleancss > dist/prism/themes/prism-nst.min.css

dist/prism/themes/%.min.css: bower_components/prism/themes/%.css dist/prism/themes bower_components/prism/prism.js /usr/local/bin/cleancss
	cleancss $< > $@
	@touch $@

dist:
	mkdir -p dist

dist/prism: dist
	mkdir -p dist/prism

bower_components:
	bower install

/usr/local/bin/uglifyjs:
	@echo Installing uglifyjs
	sudo npm i -g uglify-js

/usr/local/bin/cleancss:
	@echo Installing clean-css-cli
	sudo npm i -g clean-css-cli

bower_components/prism/prism.js: /usr/local/bin/bower bower.json
	bower update
	@touch bower_components/prism/prism.js

/usr/local/bin/bower:
	sudo npm i -g bower

.PHONY: plugin
