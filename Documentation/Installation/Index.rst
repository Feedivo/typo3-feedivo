.. _installation:

============
Installation
============

Composer
========

.. code-block:: bash

   composer require feedivo/typo3-feedivo

Classic mode
============

Install the extension ``feedivo`` from the TYPO3 Extension Repository, or
unpack the ZIP from your Feedivo account into :file:`typo3conf/ext/feedivo`
and activate it in the Extension Manager.

Include the TypoScript
======================

TYPO3 13 and 14
   Add the site set **Feedivo** to your site (*Site Management → Sites →
   Sets*), after the set that provides ``lib.contentElement`` (Fluid Styled
   Content or your theme).

TYPO3 12
   Include the static template **Feedivo** in your root TypoScript template,
   after *Fluid Styled Content* — the content element builds on
   ``lib.contentElement``.

The element brings its own Fluid layout, so it renders with Fluid Styled
Content as well as with themes that define ``lib.contentElement`` themselves.

Connect with Feedivo
====================

#. In your Feedivo account open *Connections* and create a connection of type
   **TYPO3**. Choose which feeds it may show and copy its **connection ID**.
#. In the TYPO3 backend open *Site Management → Feedivo*, paste the ID, pick
   the site the feed will appear on and select **Connect**.
#. Feedivo now allows the feed on that site's domain. If the site serves further
   domains (for example one per language), add them on the connection's page in
   Feedivo.

.. note::

   The connection ID is a credential: whoever knows it can move the connection
   to another website. It is never written into the page output.
