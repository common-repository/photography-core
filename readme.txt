=== Photography Core ===
Contributors: TigrouMeow
Tags: photography, lightroom, gutenberg, photo, image, gallery, retina, responsive, theme, theme
Donate link: https://commerce.coinbase.com/checkout/d047546a-77a8-41c8-9ea9-4a950f61832f
Requires at least: 4.8
Tested up to: 5.2
Stable tag: 0.3.0

Photography Core is the heart of the themes made for image lovers. Features are the API, helpers, Gutenberg blocks, Lightroom, etc.

== Description ==

Photographers simply want to have a nice portfolio with WordPress, with a beautiful theme, possibly linked to Lightroom, without spending too much time on the technical details. This is why Photography Core was made. It is possible to switch between any theme using the Photography Core and everything will still work, automatically. No need to work on the new theme every time. Everything in this plugin is light, efficient, but try to cover every part required by most photography websites.

***There aren't many downloads or reviews because this plugin is actually part of the architecture of themes, so it's probably not a plugin you would like just to download by itself. But you could, and develop your own theme on it.***

=== FEATURES ===

* Post Type: Collections
* Taxonomies: Folders, Keywords
* Featured Images for Folders
* Drag & Drop ordering for Folders
* Drag & Drop ordering for Collections ([Post Types Order](https://wordpress.org/plugins/post-types-order/) required)
* SEO (doesn't overdo it; basics which are good enough)
* Internal API (to retrieve the collections, folders, their hierarchy, etc)
* Gutenberg Blocks: Collections, Folders, Keywords, Section Header, Search
* Shortcodes (the same as for the Gutenberg Blocks)
* Seamless integration with WP/LR Sync (Lightroom)
* Works with Polylang

=== THEMES RECOMMENDATIONS ===

Currently, three themes are known to use Photography Core. But if you would like to build your own theme, do not hesitate to use this plugin and to contact us so that we can add you here.

* [Hikari](https://meowapps.com). Used by the urban explorer [Thomas Jorion](https://thomasjorion.com) but also by [Jordy Meow](https://jordymeow.com).
* [Yuzu](https://meowapps.com). Used by the very famous photographer [Yann Arthus-Bertrand](http://yannarthusbertrand2.org).
* [Kurayami](https://meowapps.com). This one will be awesome, but currently in development.

=== PLUGINS RECOMMENDATIONS ===

Those plugins aren't requires by the Photography Core and are simply recommendations. Remember, avoid using too many plugins, as they slow down your website and increase chance of encountering issues.

* Gallery: Meow Gallery
* Lightbox: Meow Lighbox
* Lightroom: WP/LR Sync
* Order Collections: [Post Types Order](https://wordpress.org/plugins/post-types-order/)
* Contact Form: Ninja Forms
* Fonts: Google Fonts for WordPress
* Google Analytics: Analytify - Google Analytics Dashboard
* Multilanguage: Polylang
* Better search: Relevanssi
* More SEO features: The SEO Framework

=== AVAILABLE FILTERS ===

* pcore_collections_order: Change the order of the collections. Override this filter and return either 'date' (default order, by Publish Date), 'title' (order by the Title of the collection) or by a custom 'ORDER BY'.
* pcore_folders_order: Change the order of the folders. Override this filter and return either 'tax_position' (default order, by position - this plugin has this feature built in), 'title' (order by the Title of the folder) or by a custom 'ORDER BY'.

== Installation ==

1. Upload `photography-core` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Upgrade Notice ==

Replace all the files. Nothing else to do.

== Frequently Asked Questions ==

Nothing yet.

== Changelog ==

= 0.3.0 =
* Add: Folder to Gutenberg Category Editor.

= 0.2.9 =
* Add: Support for Post Types Order.

= 0.2.8 =
* Add: Compatibility with Polylang.

= 0.2.6 =
* Add: Align wide and full for the blocks.
* Add: Filters to allow ordering of folders and collections.

= 0.2.4 =
* Update: Files for ordering folders were missing.

= 0.2.2 =
* Update: Folders can now be ordered by drag and drop.

= 0.2.0 =
* Update: Compatibility with WordPress 5.

= 0.1.5 =
* Update: Revisions for Collections.

= 0.1.4 =
* Add: Gutenberg blocks updated for latest version of Gutenberg.
* Update: Minor fix.

= 0.1.2 =
* Add: SEO now included. To avoid heavy SEO plugins.

= 0.1.0 =
* Add: Gutenberg blocks.

= 0.0.2 =
* Update: Support for WP 4.9.

= 0.0.1 =
* First release.
