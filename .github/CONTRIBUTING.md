# How to contribute

Forgge is completely open source and we encourage everybody to participate by:

- ⭐ the project on GitHub (https://github.com/Collaborar/forgge)
- Posting bug reports (https://github.com/Collaborar/forgge/issues)
- Posting feature suggestions (https://github.com/Collaborar/forgge/issues)
- Posting and/or answering questions (https://github.com/Collaborar/forgge/issues)
- Submitting pull requests (https://github.com/Collaborar/forgge/pulls)
- Sharing your excitement about Forgge with your community

## Development setup

1. Fork this repository.
2. Open up your theme directory in your terminal of choice.
3. Clone your fork e.g. `git clone git@github.com:your-username/forgge.git forgge`.
4. Run `cd forgge/ && composer install`.
5. Run `mkdir ../forgge-dev && cd ../forgge-dev`.
6. Run `printf '<?php\n' > web.php && printf '<?php\n' > admin.php && printf '<?php\n' > ajax.php`.
7. Open up your theme's `functions.php` file in your editor and add the following lines at the top:
    ```php
    use Forgge\Facades\Forgge;

    require_once( 'forgge/vendor/autoload.php' );

    add_action( 'init', function() {
        session_start(); // required only if you use Flash and OldInput
    } );

    \App::make()->bootstrap( [
        'routes'              => [
            'web'   => __DIR__ . '/forgge-dev/web.php',
            'admin' => __DIR__ . '/forgge-dev/admin.php',
            'ajax'  => __DIR__ . '/forgge-dev/ajax.php',
        ],
    ] );
    ```
8. To make sure everything is running correctly, open up the new `forgge-dev/web.php` file and add this:
    ```php
    <?php
    use Forgge\Facades\Route;

    \App::route()->get()
        ->url( '/' )
        ->handle( function () {
            return \App::output( 'Hello World!' );
        } );
    ```
1. Now open up your site's homepage and if everything is setup correctly it should read `Hello World!`.

## Running tests

To setup and run tests for Forgge, follow the steps outlined in `tests/README.md`.

## Pull Requests

- Pull request branches MUST follow this format: `{issue-number}-{short-description}`.
  Example: `12345-fix-route-condition`
- Pull requests MUST target the `master` branch
- Pull requests MUST NOT break unit tests
- Pull requests MUST follow the current code style
- Pull requests SHOULD include unit tests for new code/features
