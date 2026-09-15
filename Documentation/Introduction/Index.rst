.. _introduction:

============
Introduction
============

Feedivo collects the posts of your social media accounts (Instagram, Facebook,
Threads, Pinterest, YouTube) and turns them into **feeds**: filtered, sorted
and designed in the Feedivo account. This extension shows such a feed on a
TYPO3 page.

What the extension does
=======================

*  Adds the content element **Feedivo feed**. Editors pick a feed by name;
   nothing is copied and pasted.
*  Connects the installation with the Feedivo account once, in the backend
   module *Site Management → Feedivo*.
*  Renders the feed in the visitor's browser through Feedivo's widget. Layout,
   columns, filters and click behaviour are set in Feedivo and apply
   immediately — there is nothing to deploy.
*  Ships Content Security Policy mutations, so a site with TYPO3's CSP enabled
   shows the feed without manual policy work.

What it does not do
===================

The extension stores no posts and no media in TYPO3. It does not run scheduler
tasks and keeps no credentials in configuration files: the connection lives in
the system registry, the page output carries only a public embed key.

Privacy
=======

Rendering a feed loads the widget script and the feed HTML from Feedivo and the
images from Feedivo's media host in Germany; visitors have no contact with
Meta, Pinterest or Google. YouTube videos load their player from
youtube-nocookie.com only when a visitor clicks them. No cookies are set.

Compatibility
=============

TYPO3 12.4, 13.4 and 14 LTS, PHP 8.1 or newer, ``fluid_styled_content``.
