// Download components.js from
// https://raw.githubusercontent.com/PrismJS/prism/gh-pages/components.js
import * as components from "./components.js";

const defaultDependencies = new Set(["clike", "markup", "javascript"]);

function resolveDependencies(lang: string, dependencies = new Set()) {
	const language = components.languages[lang];
	if (!language.require) {
		return dependencies;
	}
	if (Array.isArray(language.require)) {
		for (const dep of language.require) {
			if (!defaultDependencies.has(dep)) {
				dependencies.add(dep);
			}
			resolveDependencies(dep, dependencies);
		}
	} else {
		const dep = language.require;
		if (!defaultDependencies.has(dep)) {
			dependencies.add(dep);
		}
		resolveDependencies(dep, dependencies);
	}

	return dependencies;
}

type IDictionary = { [index: string]: string[] };

// Emit a PHP dependency map, like the following:
// $deps = array(
// 	"c" => array(
// 		"bison",
// 		"cpp",
// 		"objectivec",
// 	),
// 	...
// 	);
function printPhpMap(dependencyMap: IDictionary) {
	console.log(`$deps = array(`);
	for (const lang in dependencyMap) {
		const deps = dependencyMap[lang];

		console.log(`    "${lang}" => array(`);
		for (const dep of deps) {
			console.log(`        "${dep}",`);
		}
		console.log(`    ),`);
	}
	console.log(`);`);
}

(function main() {
	let dependencyMap: IDictionary = {};
	for (const lang in components.languages) {
		const dependencies = resolveDependencies(lang);
		if (dependencies.size > 0) {
			dependencyMap[lang] = Array.from(<any>dependencies.keys());
		}
	}

	printPhpMap(dependencyMap);
})();
