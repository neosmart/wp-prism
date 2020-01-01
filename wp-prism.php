<?php
/*
Plugin Name: WP-Prism
Plugin URI: https://neosmart.net/blog/2017/wp-prism/
Description: Translates fenced codeblocks (<code>```</code>) to syntax-highlighted &lt;pre&gt; and &lt;code&gt; snippets
Author: Mahmoud Al-Qudsi
Version: 0.1
Author URI: https://neosmart.net/
*/

add_filter("the_content", "wp_prism_code_block", 2);

$prism_version = null;
$prism_root = plugin_dir_url(__FILE__) . "prism";

function prism_resolve_dependencies($langs) {
    // Derived from https://github.com/PrismJS/prism/blob/gh-pages/components.js#L38
    // markup, clike, and javascript are included by default, always
    $deps = array(
        "c" => array(
            "bison",
            "cpp",
            "objectivec",
        ),
        "ruby" => array(
            "crystal",
            "haml",
        ),
        "css" => array(
            "less",
            "sass",
            "scss",
        ),
        "java" => array(
            "scala",
        ),
        "basic" => array(
            "vbnet",
        ),
    );

    $requirements = array();

    foreach($deps as $requirement => $req_langs) {
        foreach ($req_langs as $lang) {
            if (array_search($lang, $langs) !== FALSE) {
                array_push($requirements, $requirement);
                break;
            }
        }
    }

    return $requirements;
}

function prism_language_map($lang) {
    // Map possible user language choices to prism language code
    $lang_map = array(
        "c++" => "cpp",
        "c#" => "csharp",
        "asp" => "aspnet",
        "asp.net" => "aspnet",
    );

    if (array_key_exists($lang, $lang_map)) {
        return $lang_map[$lang];
    }
    return $lang;
}

function wp_prism_code_block($content) {
    global $prism_root, $prism_version;

    $langs = array();
    $replacements = 0;

    // echo "<!--$content-->";
    $new_content = preg_replace_callback('/^(?:\<pre\>\s*)?``` *([a-zA-Z0-9+._-]*)[\r\n]+(.*?)```\s*(?:\<\/pre\>)?/sim',
        function($match) use (&$langs) {
            $lang = prism_language_map(strtolower($match[1]));
            if (strlen($lang) > 0) {
                array_push($langs, $lang);
                return "<pre><code class=\"language-{$lang}\">{$match[2]}</code></pre>";
            }
            return "<pre><code>{$match[2]}</code></pre>";
        },
        $content, -1, $replacements);

    if ($replacements > 0) {
        $langs = array_unique($langs);

        wp_enqueue_script("prism", $prism_root . "/prism.min.js",
            array(), $prism_version, true);
        wp_enqueue_style("prism-nst", $prism_root . "/themes/prism-nst.min.css",
            array(), $prism_version);

        // Enqueue the dependencies for languages we used
        $requirements = prism_resolve_dependencies($langs);
        $requirement_handles = array('prism');
        foreach ($requirements as $requirement) {
            wp_enqueue_script("prism-{$requirement}", $prism_root . "/components/prism-{$requirement}.min.js",
                array("prism"), $prism_version, true);
            array_push($requirement_handles, "prism-" . $requirement);
        }

        // Enqueue the individual language scripts
        foreach($langs as $lang) {
            wp_enqueue_script("prism-{$lang}", $prism_root . "/components/prism-{$lang}.min.js",
                $requirement_handles, $prism_version, true);
        }
        return $new_content;
    }

    return $content;
}

?>
