<?php
/*
Plugin Name: Prism for WordPress
Plugin URI: https://neosmart.net/
Description: Translates fenced codeblocks (<code>```</code>) to prism
Author: Mahmoud Al-Qudsi
Version: 0.1
Author URI: https://neosmart.net/
*/

add_filter("the_content", "wp_prism_code_block", 1);

function prism_resolve_dependencies($langs) {
    //derived from https://github.com/PrismJS/prism/blob/gh-pages/components.js#L38
    //markup, clike, and javascript are included by default, always
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
    //maps possible user language choices to prism language code
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
    $langs = array();
    $replacements = 0;

    $new_content = preg_replace_callback('/^(?:\<pre\>[\r\n]*)?``` *([a-zA-Z0-9+._-]*)[\r\n]+(.*?)```(?:\r?\n?\<\/pre\>)/ms',
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

        wp_enqueue_script("prism", "https://cdnjs.cloudflare.com/ajax/libs/prism/1.6.0/prism.min.js",
            array(), null, true);
        // wp_enqueue_style("prism-css", "https://cdnjs.cloudflare.com/ajax/libs/prism/1.6.0/themes/prism.min.css",
        //     array(), null);
        wp_enqueue_style("prism-ghcolors",
            "https://cdn.rawgit.com/PrismJS/prism-themes/master/themes/prism-ghcolors.css",
            array(), null);

        //enqueue the dependencies for languages we used
        $requirements = prism_resolve_dependencies($langs);
        $requirement_handles = array();
        foreach ($requirements as $requirement) {
            wp_enqueue_script("prism-{$requirement}",
                "https://cdnjs.cloudflare.com/ajax/libs/prism/1.6.0/components/prism-{$requirement}.min.js",
                array("prism"), null, true);
            array_push($requirement_handles, "prism-" . $requirement);
        }

        //enqueue the individual language scripts
        foreach($langs as $lang) {
            wp_enqueue_script("prism-{$lang}",
                "https://cdnjs.cloudflare.com/ajax/libs/prism/1.6.0/components/prism-{$lang}.min.js",
                $requirement_handles, null, true);
        }
        return $new_content;
    }

    return $content;
}

?>
