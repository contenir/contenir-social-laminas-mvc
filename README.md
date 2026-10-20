# contenir/social-laminas-mvc

[![Continuous Integration](https://github.com/contenir/contenir-social-laminas-mvc/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/contenir/contenir-social-laminas-mvc/actions/workflows/continuous-integration.yml)
[![codecov](https://codecov.io/gh/contenir/contenir-social-laminas-mvc/graph/badge.svg)](https://codecov.io/gh/contenir/contenir-social-laminas-mvc)

The Laminas MVC view helpers for [contenir/social](https://github.com/contenir/contenir-social):

- `socialProfiles()` writes the site's profiles as escaped links with `rel="me"`, icons and accessible names;
- `share()` writes the page's share buttons, built from the page's own metadata, and the small script behind
  "Copy link" and "Share…".

Profiles and share buttons are set in the CMS, by the [Social add-on](https://github.com/contenir-extension/social).
Settings are read from the site's `config['settings']`, as the CMS writes them, so there is nothing to configure on
the site.

## Requirements

- PHP 8.3, 8.4 or 8.5
- laminas/laminas-view 2.30 or later

## Install

```bash
composer require contenir/social-laminas-mvc
```

Add `Contenir\Social\Laminas\Mvc` to the application's modules (laminas-component-installer offers to).

## Profiles

```php
<?= $this->socialProfiles() ?>
```

This replaces the links layouts build by hand today, such as
`href="https://instagram.com/<?= $this->Settings()->social->social_instagram ?>"`. Those print whatever is stored,
unescaped, and break when a handle is stored with an `@` or an address is stored where a handle was expected.
`socialProfiles()` reads every form the CMS has stored, writes the canonical address, escapes it, and leaves out
anything that isn't a profile on its network.

```html
<ul class="social social--profiles">
  <li class="social__item social__item--instagram">
    <a class="social__link" href="https://www.instagram.com/yourname/" rel="me noopener" target="_blank"
       aria-label="Instagram (opens in a new tab)"><svg class="social__icon" …></svg></a>
  </li>
  …
</ul>
```

For markup of a site's own, `$this->socialProfiles()->profiles()` gives the `Profiles` themselves: `all()`,
`get(Network::Instagram)`, `sameAs()` and `twitterSite()`.

## Share buttons

```php
<?= $this->share() ?>
```

writes the buttons the site chose in the CMS, in its order, and after the first set on a page, the script for
"Copy link" and "Share…". The page is described by the metadata the layout already has:

| Value | Read from, in order |
|---|---|
| address | `og:url`, the canonical link, the current address |
| title | `og:title`, the page's `<title>` |
| description | `og:description`, the description meta tag |
| image (needed for Pinterest) | `og:image` |

contenir/resource-laminas-mvc's `resourceMeta()` writes all of these, so a page that calls it needs nothing more.
`share()` reads them when it is called, so call it after `resourceMeta()`: in the page's template, or in the layout.
A template can give any of them itself, and addresses starting with `/` are made absolute on the current host:

```php
<?= $this->share(['url' => $entry->url, 'title' => $entry->title, 'image' => $entry->image]) ?>
```

Nothing reaches a network until a visitor clicks: the buttons are plain links, with no third-party scripts, counts
or cookies.

### The script and the CSP nonce

The script is written inline, once per page. If the application registers a
`Contenir\Social\Laminas\Mvc\NonceProviderInterface` service, it carries that nonce. To give one, or to leave the
script out and place it yourself:

```php
<?= $this->share(null, ['nonce' => $nonce]) ?>
<?= $this->share(null, ['script' => false]) ?>
…
<?= $this->plugin('share')->script($nonce) ?>
```

Without the script, "Copy link" and "Share…" stay hidden and every other button still works.

## Options

Both helpers take an array, or a `Contenir\Social\Html\Options`:

| Option | Default | |
|---|---|---|
| `icons` | `true` | draw each network's icon |
| `labels` | profiles: `false`; share: the site's setting | show each name beside its icon (always, without icons) |
| `new_tab` | `true` | open links in a new tab, and say so in each link's accessible name |
| `block` | `social` | the BEM block the classes are named after |
| `class` | | more classes for the list |

```php
<?= $this->socialProfiles(['block' => 'navbar', 'class' => 'navbar__nav--social']) ?>
```

The classes are `{block}`, `{block}--profiles` or `{block}--share`, `{block}__item`, `{block}__item--{network}`,
`{block}__link`, `{block}__button`, `{block}__icon`, `{block}__label` and `{block}__status` ("Copied"). No CSS is
included. Icons are 24 by 24 and take the text colour.

To change the defaults for the whole site:

```php
return [
    'contenir_social' => [
        'options' => ['block' => 'site-social', 'new_tab' => false],
    ],
];
```

## Licence

BSD-3-Clause. See [LICENSE.md](LICENSE.md). The icons' licences are in contenir/social's ICONS.md.
