# TMSM Frontend Optimizations

Plugin d'optimisations frontend pour Thermes Marins de Saint-Malo

## Installation

1. Téléchargez le plugin depuis GitHub
2. Installez et activez le plugin dans WordPress
3. Le plugin fonctionne automatiquement sans configuration supplémentaire

## Shortcodes disponibles

### [rss-with-image]

Affiche un flux RSS avec des images et un layout en grille, idéal pour les prestations et produits.

**Syntaxe :**
```
[rss-with-image rss="URL_DU_FLUX" columns="3" items="10" show_media="1" button_text="Voir la prestation"]
```

**Attributs :**
- `rss` (string, requis) : URL du flux RSS à afficher
- `show_author` (0|1, défaut: 0) : Afficher l'auteur
- `show_summary` (0|1, défaut: 0) : Afficher le résumé
- `show_date` (0|1, défaut: 0) : Afficher la date
- `show_media` (0|1, défaut: 0) : Afficher les images
- `show_price` (0|1, défaut: 0) : Afficher le prix (si disponible dans le flux)
- `items` (nombre, défaut: 10, max: 20) : Nombre d'éléments à afficher
- `columns` (nombre, défaut: 3) : Nombre de colonnes dans la grille
- `button_show` (true|false, défaut: true) : Afficher le bouton
- `button_text` (string, défaut: "Voir la prestation") : Texte du bouton

**Exemple :**
```
[rss-with-image rss="https://exemple.com/flux.rss" columns="2" items="6" show_media="1" show_date="1" button_text="En savoir plus"]
```

### [rss-with-image-activities]

Affiche un flux RSS d'activités avec un layout optimisé pour les événements et actualités.

**Syntaxe :**
```
[rss-with-image-activities rss="URL_DU_FLUX" columns="3" items="10" description_length="50"]
```

**Attributs :**
- `rss` (string, requis) : URL du flux RSS à afficher
- `show_author` (0|1, défaut: 0) : Afficher l'auteur
- `show_date` (0|1, défaut: 0) : Afficher la date
- `show_media` (0|1, défaut: 0) : Afficher les images
- `items` (nombre, défaut: 10, max: 20) : Nombre d'éléments à afficher
- `columns` (nombre, défaut: 3) : Nombre de colonnes (0 = auto)
- `in_lines` (0|1, défaut: 0) : Layout en lignes (1) ou grille (0)
- `button_show` (true|false, défaut: true) : Afficher le bouton
- `description_length` (nombre, défaut: 0) : Longueur de la description en mots (0 = complète)
- `button_text` (string, défaut: "En savoir plus") : Texte du bouton

**Exemple :**
```
[rss-with-image-activities rss="https://exemple.com/actualites.rss" description_length="30" show_date="1" button_text="Lire la suite"]
```

### [language_switcher]

Affiche un sélecteur de langues pour les sites multilingues utilisant Polylang.

**Syntaxe :**
```
[language_switcher]
```

**Attributs :** Aucun attribut configurable (le style s'adapte automatiquement au thème)

**Exemple :**
```
[language_switcher]
```

## Fonctionnalités intégrées

### Gravity Forms
- **Action personnalisée** : Possibilité de définir une action personnalisée pour les formulaires
- **Action conditionnelle** : Action dynamique basée sur la sélection d'un bouton radio
- **Format téléphone** : Formatage automatique pour les numéros français et internationaux
- **Nouveaux merge tags** : `{user_firstname}`, `{user_lastname}`, `{user_email}`, `{user_billingphone}`
- **Autocomplete désactivé** : Pour les champs numériques
- **Blocage domaines** : Empêche l'utilisation de domaines email non autorisés
- **Scripts en footer** : Force les scripts Gravity Forms en bas de page

### WooCommerce
- **Optimisations cache** : Vidage automatique du cache lors des ventes
- **Métadonnées produits** : Affichage d'infos dynamiques (livraison gratuite, retrait sur place)
- **Sélection de variations** : Boutons radio au lieu de sélecteurs déroulants
- **Protection email** : Blocage des domaines non autorisés
- **Page de réception** : Titre "Paiement échoué" si nécessaire
- **Robustesse mot de passe** : Ajustement des exigences
- **PayPal Express** : Adresse requise

### Google Tag Manager
- **Injection après body** : Pour le thème OceanWP
- **Exclusion commandes** : Ignore les commandes échouées ou anciennes
- **Scripts en footer** : Optimisation du chargement

### Polylang
- **Classes CSS** : Ajout de classes pour la langue courante
- **Variables JavaScript** : Exposition des données de langue
- **Shortcode switcher** : `[language_switcher]` pour changer de langue

### Optimisations SEO et Performance
- **Robots** : Meta robots automatique si site non public
- **YouTube** : Paramètres d'intégration optimisés (`modestbranding=1`, `showinfo=0`, `rel=0`)
- **Jetpack** : Désactivation des fonctionnalités automatiques (partage, likes)
- **Scripts footer** : Déplacement des scripts en bas de page
- **Suppression éléments** : Generator, shortlink, RSD, manifest

### Elementor
- **Recherche produits** : Formulaire de recherche ciblé WooCommerce avec la classe `woocommerce-searchform`

### Autres intégrations
- **Post Expirator** : Vidage cache à l'expiration des posts
- **WooCommerce Advanced Messages** : Emplacements personnalisés
- **User Requests** : Destinataire personnalisé pour les notifications RGPD

## Configuration Gravity Forms

### Remplacement d'action de formulaire

1. Dans les paramètres du formulaire, remplissez le champ "Action" avec une URL valide
2. Pour chaque champ nécessitant un nom personnalisé :
   - Activez "Autoriser le champ à être rempli dynamiquement"
   - Donnez un "Nom de paramètre"

### Action conditionnelle basée sur un bouton radio

1. Créez un champ radio avec la classe CSS `form-action-replacement`
2. Donnez une valeur (URL valide) à chaque option radio
3. JavaScript changera automatiquement l'action selon l'option sélectionnée

### Formulaire de demandes utilisateur RGPD

Créez un formulaire Gravity Forms avec un champ radio ayant le nom CSS `personal_data` :

1. **Contacter le DPO** - Confirmation/notification GF standard
2. **Modifier les données personnelles** - Confirmation/notification GF standard  
3. **Exporter les données personnelles** - Valeur : `export_personal_data`
4. **Supprimer les données personnelles** - Valeur : `remove_personal_data`

Les options 3 et 4 sont automatiquement intégrées au système WordPress de demandes utilisateur.

## Configuration RSS avec images

Pour que les flux RSS incluent les images, ajoutez ce code dans le `functions.php` du site source :

```php
add_action('rss2_item', function() {
    global $post;
    if (has_post_thumbnail($post->ID)) {
        $thumbnail_id = get_post_thumbnail_id($post->ID);
        $thumbnail = wp_get_attachment_image_src($thumbnail_id, 'thumbnail');
        if ($thumbnail) {
            echo '<enclosure url="' . esc_url($thumbnail[0]) . '" length="0" type="image/jpeg" />' . PHP_EOL;
        }
    }
});
```

## Support et développement

- **Auteur** : Arnaud Flament
- **Repository** : [https://github.com/thermesmarins/tmsm-frontend-optimizations](https://github.com/thermesmarins/tmsm-frontend-optimizations)
- **Licence** : GPL-3.0+
- **PHP requis** : 8.0+

## Changelog

Voir le fichier `CHANGELOG.md` pour l'historique des versions et modifications.
