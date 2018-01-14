# wp-prism

_githubesque syntax highlighting and code fencing plugin for WordPress._

wp-prism brings GitHub-styled code-fencing and prism-powered syntax highlighting to your WordPress installation, written from scratch to be as freaky fast as possible.

wp-prism currently supports syntax highlighting for 41 languages and _only loads the JavaScript and CSS to highlight your syntax when needed._

You can say goodbye to page bloat and long load times, because it makes no sense to do this any other way.

## What it does

wp-prism does exactly what it says on the box: it takes a code-fenced block of source code nested in your WordPress pages and posts, like this:

    ```rust
    fn main() {
        println!("Hello, world!");
    }
    ```
And turns into a syntax-highlighted work of art, like this:

```rust
fn main() {
    println!("Hello, world!");
}
```

## How to install

Just grab a copy of `wp-prism` from the WordPress plugins repository or clone our github repo from [https://github.com/neosmart/wp-prism](https://github.com/neosmart/wp-prism).

There is no configuration needed.

## Avoiding whitespace mangling

WordPress (well, TinyMCE) loves to mangle whitespace in posts. As such, wp-prism supports (and recommends) embedding your code fragments in `<pre>` tags if you're going to use the visual editor (or will use the visual editor at any point). wp-prism detects the outer `<pre>` and takes care not to emit a second `<pre>` tag in such cases.

To illustrate with an example:

    <pre>
    ```cpp
    void Greet(const char *name)
    {
        printf("Hello %s!\n", name);
    }
    ```
    </pre>

```cpp
void Greet(const char *name)
{
    printf("Hello %s!\n", name);
}
```
