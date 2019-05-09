# Kirby 3 Sendmentions (Send Webmentions)

⚠️ This plugin has been created for my personal use; use with caution, but feel invited to contribute or build upon!

The Kirby3 Sendmentions plugin attempts to send Webmentions(https://www.w3.org/TR/webmention/) to all URLs linked from a page's content when it is saved with status "listed". This notifies the receiving site that a page of theirs has been linked to, and allows them to display these, if desired. If the receiving site does not provide a webmention endpoint, sending a pingback is attempted instead. As an option, the plugin can also request all linked pages to be archived on archive.org (option disabled by default; see "Setup" below).

Most websites that receive webmentions (such as those sent out by this plugin) will not only verify that a link indeed exists, but parse the source page in order to determine post title, author info etc. Therefore, using the appropriate microformat markup in your templates is strongly recommended (even though not technically a precondition).

NB. The plugin functionality only covers outgoing webmentions (notifying linked websites about content published on the Kirby website). Receiving webmentions from other sites and displaying them in page templates requires a separate solution.

****

## Installation

### Download

Download and copy this repository to `/site/plugins/kirby3-sendmentions`.

### Git Submodule
You can add the plugin as a Git submodule.

    $ cd your/project/root
    $ git submodule add https://github.com/sebastiangreger/kirby3-sendmentions.git site/plugins/kirby3-sendmentions
    $ git submodule update --init --recursive
    $ git commit -am "Add Kirby3 Send Mentions plugin"

Run these commands to update the plugin:

    $ cd your/project/root
    $ git submodule foreach git checkout master
    $ git submodule foreach git pull
    $ git commit -am "Update submodules"
    $ git submodule update --init --recursive

## Setup

To display a table of sent mentions in the Panel, add the following to the according blueprint in `site/blueprints/pages`:

```yaml
sections:
  pings:
    type: pings
```

By default, webmentions are sent for all pages as they are published. You may want to fine tune your setup using the available options.

## Options

The plugin can be configured in your `site/config/config.php`:

### Sending webmentions for specific templates only

By default, webmentions are sent for all pages as they are published; to limit the sending to specific templates, provide an array of template names to include

```php
'sgkirby.sendmentions.templates' => [ 'default', 'article', 'event' ],
```

### Saving linked pages to archive.org

By default, no URLs are sent to archive org for saving. To activate pinging archive.org for all templates, provide an empty array

```php
'sgkirby.sendmentions.archiveorg' => [],
```

For more granular control, you may also limit pinging archive.org by providing an array of template names to include

```php
'sgkirby.sendmentions.archiveorg' => [ 'default', 'article', 'event' ],
```

## Features

### Plugin features

- [x] only send webmentions on pages with status "listed"
- [x] allow limitation to specific templates
- [x] fall back to sending Pingback, if no Webmention endpoint present
- [x] store target URL, timestamp, and HTTP code for every webmention sent
- [x] display list of sent webmentions in panel (optional, via blueprint)
- [x] ping archive.org with target URLs (optional, off by default)
- [ ] passes endpoint discovery tests 1-23 on [webmention.rocks](https://webmention.rocks) (23 still missing)

### Roadmap/Ideas

- [ ] send webmentions asynchronously to avoid lag in panel when saving
- [ ] resend webmentions after editing content ("update" webmentions; currently disabled to avoid spamming on fast edits - can be buffered in asynchronous process)
- [ ] send webmentions for deleted posts (or posts changed to other status than "listed"?)
- [ ] send webmentions to URLs removed from content
- [ ] find and implement best URL regex to scan for URLs in content
- [ ] look for URLs in (customizable) fields other than the main content
- [ ] reuse cached endpoint URLs for time period defined by HTTP headers
- [ ] implement a size limit; not sending webmentions for extraordinarily huge pages

## Requirements

Kirby 3.1.3+(https://getkirby.com)

## Credits

Inspiration and code snippets from:

- https://github.com/bastianallgeier/kirby-webmentions
- https://github.com/sebsel/seblog-kirby-webmentions

Included vendor libraries:

- https://github.com/indieweb/mention-client-php
- https://github.com/microformats/php-mf2

## License

Kirby 3 Sendmentions is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

Copyright © 2019 Sebastian Greger(https://sebastiangreger.net)

It is discouraged to use this plugin in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.
