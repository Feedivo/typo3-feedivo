# Changelog

All notable changes to the Feedivo extension for TYPO3. The extension is
available in the [TYPO3 Extension Repository](https://extensions.typo3.org/extension/feedivo)
and on [Packagist](https://packagist.org/packages/feedivo/typo3-feedivo).

## 1.0.0 – 2026-09-15

Initial release for TYPO3 12.4, 13.4 and 14.

- Content element "Feedivo feed" with a feed select by name and a fallback
  field for the feed ID
- Backend module *Site Management → Feedivo* to connect the installation with
  a Feedivo connection ID, list the covered feeds and disconnect
- Preview of the selected feed in the page module
- Content Security Policy mutations for sites with TYPO3's CSP enabled
- Site set for TYPO3 13/14 and static TypoScript template for TYPO3 12
- Own Fluid layout; `settings.layout` switches to a theme's content layout
