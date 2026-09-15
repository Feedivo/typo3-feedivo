.. _configuration:

=============
Configuration
=============

Extension configuration
=======================

*Admin Tools → Settings → Extension Configuration → feedivo*

.. confval:: baseUrl

   :type: string
   :Default: ``https://feedivo.de``

   Address of the Feedivo service. Change it only for a self-hosted test
   instance.

.. confval:: additionalHosts

   :type: string
   :Default: ``https://cdn.feedivo.de``

   Comma-separated origins that serve feed media. Used for the Content Security
   Policy only.

.. confval:: verifyTls

   :type: boolean
   :Default: ``1``

   Verify Feedivo's TLS certificate. Disable only for a local test instance with
   a self-signed certificate.

Content Security Policy
=======================

If the frontend CSP of TYPO3 is enabled, the extension extends ``script-src``,
``style-src``, ``connect-src``, ``img-src`` and ``media-src`` by the Feedivo
origins above and ``frame-src`` by ``https://www.youtube-nocookie.com``. Sites
without CSP are not affected.

Themes
======

The element renders inside a Fluid layout named by
``tt_content.feedivo_feed.settings.layout``. The default ``FeedivoFeed`` is the
extension's own layout (frame and header as in Fluid Styled Content) and works
on every site. A theme with its own content layout names it instead, so the
element gets the theme's wrapper and header — for the Core theme "Camino":

.. code-block:: typoscript

   tt_content.feedivo_feed.settings.layout = Content/Default

Design
======

Layout, columns, aspect ratio, filters and click behaviour are set per feed in
Feedivo. The extension has no design options of its own — a change in Feedivo
shows on the TYPO3 page immediately.
