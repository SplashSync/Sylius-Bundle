---
lang: fr
permalink: overview
title: Plugin Splash Sync pour Sylius
description: Synchronisez clients, adresses, produits, stocks, commandes et factures Sylius avec toutes vos applications métier.
updated: 2026-09-28
translation:
    from:        en
    source_hash: cb294c91
    mode:        llm
---

Bienvenue dans la documentation du plugin Splash Sync pour Sylius ! :wave:

Ce plugin connecte votre boutique [Sylius](https://sylius.com), le framework eCommerce basé sur Symfony, à
[Splash Sync](https://www.splashsync.com) : vos clients, produits, commandes et factures sont partagés avec
toutes les autres applications de votre compte Splash (ERP, CRM, comptabilité, marketplaces, autres
boutiques...) et tenus à jour automatiquement.

### Fonctionnalités clés

:busts_in_silhouette: **Fusionnez les données de vos clients**
: Clients et adresses sont partagés avec votre CRM, votre ERP ou vos autres boutiques. Les objets liés sont
  fusionnés en une seule entité Splash : une modification faite n'importe où est synchronisée partout.

:package: **Synchronisez produits & stocks**
: Produits, variantes, prix, images et niveaux de stock restent synchronisés sur tous vos canaux de vente.
  Les stocks sont critiques pour votre activité : évitez les erreurs grâce aux mises à jour automatiques.

:shopping_cart: **Exportez commandes & factures**
: Votre boutique devient l'un de vos canaux de vente. Commandes et factures sont exportées vers votre ERP ou
  votre logiciel comptable, avec lignes, totaux, paiements et expéditions.

:bar_chart: **Consolidez vos analyses financières**
: Toutes vos ventes, de tous vos canaux, en un seul endroit... sans effort.

> [!NOTE]
> Les factures sont en **lecture seule** : elles sont exportées depuis Sylius, jamais écrites par Splash.

### Compatibilité

| Composant | Versions supportées |
|---|---|
| Sylius | 1.6+ |
| PHP | 7.4 et 8.x |
| Canaux | Un canal par défaut, défini dans la configuration du plugin |

### Par où commencer ?

1. **Installez le plugin** dans votre application Sylius.
2. **Connectez-le** à votre compte Splash.
3. Consultez la **configuration de référence**.
4. Découvrez ce qui peut être **synchronisé**.

### Contribuer

Ce plugin est open source et fait partie du projet [Splash Sync](https://www.splashsync.com).
Toutes les pull requests sont les bienvenues !
