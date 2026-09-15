# Feedivo for TYPO3

Show your Instagram, Facebook, Threads, Pinterest and YouTube posts on a TYPO3
website as a content element. Feeds are built and designed in your
[Feedivo](https://feedivo.de) account; TYPO3 only places them.

- Content element **Feedivo feed** with a feed select by name
- One connection per installation, set up once in *Site Management → Feedivo*
- Layout, columns, filters and click behaviour come from Feedivo and apply
  immediately, no deployment needed
- Content Security Policy mutations included for sites with TYPO3's CSP enabled
- No feed data stored in TYPO3: the feed is rendered in the visitor's browser
  from Feedivo; images are served as copies from Feedivo's servers in Germany

Supports TYPO3 12.4, 13.4 and 14 LTS on PHP 8.1 or newer.

## Installation

```bash
composer require feedivo/typo3-feedivo
```

Or install the extension `feedivo` from the TYPO3 Extension Repository. Then:

1. Include the TypoScript: add the site set **Feedivo** to your site
   (TYPO3 13+) or the static template **Feedivo** to your root template (TYPO3 12).
2. In your Feedivo account create a connection of type **TYPO3**
   (*Connections → TYPO3*) and copy its connection ID.
3. In the TYPO3 backend open *Site Management → Feedivo*, enter the ID, pick the
   site and connect.
4. Add the content element **Feedivo feed** to a page and select a feed.

## Configuration

*Admin Tools → Settings → Extension Configuration → feedivo*

| Setting | Default | Purpose |
|---|---|---|
| `baseUrl` | `https://feedivo.de` | Address of the Feedivo service |
| `additionalHosts` | `https://cdn.feedivo.de` | Extra origins for the Content Security Policy (feed media) |
| `verifyTls` | on | Disable only for a local test instance with a self-signed certificate |

## Privacy

Rendering a feed loads the widget script and the feed HTML from Feedivo and the
images from Feedivo's media host; no request reaches Meta, Pinterest or Google.
YouTube videos load their player from youtube-nocookie.com only when a visitor
clicks them. The extension sets no cookies.

## License

GPL-2.0-or-later. Feedivo is a product of Jan Feiler, Germany.
