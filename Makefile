plugin: bower_components/prism/prism.js dist dist/prism/prism.min.js dist/README.md dist/wp-prism.php components themes

dist/README.md: README.md
	cp README.md dist/

dist/wp-prism.php: wp-prism.php
	cp wp-prism.php dist/

dist/prism/prism.min.js: /usr/local/bin/uglifyjs bower_components/prism/prism.js
	uglifyjs ./bower_components/prism/prism.js > dist/prism/prism.min.js

dist/prism/components: bower_components bower_components/prism/components.js
	mkdir dist/prism/components
	cp -a ./bower_components/prism/components/*.min.js dist/prism/components/

dist/prism/themes: bower_components bower_components/prism/themes/
	mkdir dist/prism/themes

themes: dist/prism/themes $(wildcard dist/prism/themes/*.css) dist/prism/themes/prism-nst.css
	for css in `find bower_components/prism/themes/ -iname "*.css"`; do cleancss $${css} > dist/prism/themes/`basename -s .css $${css}`.min.css; done

dist/prism/themes/prism-nst.css:
	curl https://cdn.rawgit.com/PrismJS/prism-themes/master/themes/prism-ghcolors.css | sed 's/font-family: .*/font-family: "SFConsole","SFMono","SF Mono","San Francisco Mono",Menlo,Consolas,Source Code Pro,Inconsolata-G,DejaVu Sans Mono,"Bitstream Vera Sansa Mono",Anonymous Pro,Monaco,"Courier 10 Pitch",Courier,monospace;/g' | cleancss > dist/prism/themes/prism-nst.min.css

components: dist/prism/components

dist:
	mkdir -p dist/prism

bower_components:
	bower install

/usr/local/bin/uglifyjs:
	@echo Installing uglifyjs
	sudo npm i -g uglify-js

/usr/local/bin/cleancss:
	@echo Installing clean-css-cli
	sudo npm i -g clean-css-cli

bower_components/prism/prism.js: bower.json
	bower update

.PHONY: plugin components themes
