---
lang: en
permalink: overview
title: Splash Sync plugin for Sylius
description: Synchronize Sylius customers, addresses, products, stocks, orders and invoices with all your business applications.
updated: 2026-09-28
---

Welcome to the documentation of the Splash Sync plugin for Sylius! :wave:

This plugin connects your [Sylius](https://sylius.com) store, the eCommerce framework built on Symfony, to
[Splash Sync](https://www.splashsync.com): your customers, products, orders and invoices are shared with every
other application of your Splash account (ERP, CRM, accounting, marketplaces, other stores...) and kept up to
date automatically.

### Key features

:busts_in_silhouette: **Merge your customers data**
: Customers and addresses are shared with your CRM, ERP or other stores. Linked objects are merged into a
  single Splash entity, so a change made anywhere is synchronized everywhere.

:package: **Synchronize products & stocks**
: Products, variants, prices, images and stock levels are kept in sync across all your sales channels.
  Stocks are critical for your business: avoid errors with automated updates.

:shopping_cart: **Export orders & invoices**
: Your store becomes one of your sales channels. Orders and invoices are exported to your ERP or accounting
  software, with items, totals, payments and shipments.

:bar_chart: **Consolidate your financial analytics**
: All your sales, from every channel, in a single place... with no effort.

> [!NOTE]
> Invoices are **read only**: they are exported from Sylius, never written by Splash.

### Compatibility

| Component | Supported versions |
|---|---|
| Sylius | 1.6+ |
| PHP | 7.4 and 8.x |
| Channels | One default channel, set in the plugin configuration |

### Where to start?

1. **Install the plugin** in your Sylius application.
2. **Connect it** to your Splash account.
3. Review the **configuration reference**.
4. Discover what can be **synchronized**.

### Contributing

This plugin is open source and part of the [Splash Sync](https://www.splashsync.com) project.
Any pull request is welcome!
