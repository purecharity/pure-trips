# Pure Charity Trips WordPress Plugin

A plugin to display trips from the Pure Charity app on your WordPress site.

It depends on the Pure Charity Base Plugin being installed and it's credentials configured to work.

Installation

In order to install the plugin:

1. Copy the `/purecharity-wp-trips` folder to the `/wp-content/plugins` on your WP install
2. Activate the plugin through the ‘Plugins’ menu in WordPress
3. You’re done!

## Shortcodes

### Trip Listing
`[trips]`

Possible parameters:
* `country` - The country to fetch trips from.
* `region` - The region to fetch events from.
* `query` - A search query for trips.
* `starts_at` - YYYY-MM-DD date to only show trips that start after that date.
* `ends_at` - YYYY-MM-DD date to only show trips that start before that date.
* `past` - 'true' if you only want to show past trips.
* `include_past` - set to true if you want to show events starting at any date
* `tag` - A tag to filter trips.
* `sort` - Sort direction.

### Single Trip
`[trip slug=trip-slug]`

Possible parameters:
* `slug` - The slug of the trip on the Pure Charity app.

## Template Tags

### Trip List

Function:
`pc_trips()`

Parameters:

The parameters are passed through an Object's attributes.

```php
 $options = new stdClass;
 $options->param = 'value';
```

Possible Attributes:
* `id` - The ID of the trip you want to fetch.
* `per_page` - The amount of trips to return per page.
* `country` - The country to fetch trips from.
* `region` - The region to fetch events from.
* `query` - A search query for trips.
* `upcoming` - set to false if you only want to show events starting at any date
* `tag` - A tag to filter trips.
* `sort` - Sort direction.

Returns:
A Trip `Object`

Return Object Attributes:
* `id` - int
* `name` - string
* `slug` - string
* `about` - string
* `description` - string
* `region` - string
* `category` - string
* `country` - string
* `field_partner_id` - int
* `leaders` - array of objects
	* `id` - int
	* `name` - string
	* `public_url` - string
* `tickets` - array of objects
	* `id` - int
	* `name`- string
	* `description` - string
	* `price` - string
	* `availability_starts_at` - string(eg: 2014-07-28T15:57:08Z)
	* `availability_ends_at` - string(eg: 2014-07-28T15:57:08Z)
	* `minimum_deposit` - string(eg: 100.0)
	* `public_url` - string
* `registration_state` - string
* `starts_at` - string(eg: 2015-04-30T00:00:00Z)
* `ends_at` - string(eg: 2015-04-30T00:00:00Z)
* `types` - array of strings
* `images` - object
	* `large` - string
	* `medium` - string
	* `small` - string
* `trip_tags` - array of strings
* `public_url` - string

Example (Show the name and description of a trip on your custom page):

```php
	<?php $trip = pc_trip(201); ?>
	<h1><?php echo $trip->name ?></h1>
	<p><?php echo $trip->about ?></p>
```

### Single Trip Information

Function:
`pc_trip($id)`

Parameters:
* $id - The ID of the trip you want to fetch.

Returns:
A Trip `Object`

Return Object Attributes:
* `id` - int
* `name` - string
* `slug` - string
* `about` - string
* `description` - string
* `region` - string
* `category` - string
* `country` - string
* `field_partner_id` - int
* `leaders` - array of objects
	* `id` - int
	* `name` - string
	* `public_url` - string
* `tickets` - array of objects
	* `id` - int
	* `name`- string
	* `description` - string
	* `price` - string
	* `availability_starts_at` - string(eg: 2014-07-28T15:57:08Z)
	* `availability_ends_at` - string(eg: 2014-07-28T15:57:08Z)
	* `minimum_deposit` - string(eg: 100.0)
	* `public_url` - string
* `registration_state` - string
* `starts_at` - string(eg: 2015-04-30T00:00:00Z)
* `ends_at` - string(eg: 2015-04-30T00:00:00Z)
* `types` - array of strings
* `images` - object
	* `large` - string
	* `medium` - string
	* `small` - string
* `trip_tags` - array of strings
* `public_url` - string

Example (Show a list of events):

```php
 	<?php $trips = pc_trips(); ?>
 	<?php foreach($trips->events as $trip){ ?>
		<h1><?php echo $trip->name ?></h1>
		<p><?php echo $trip->about ?></p>
 	<?php } ?>
```

### Regions list

Function:
`pc_trip_regions()`

Returns:
A Regions `Object`

Return Object Attributes:
* `regions` - array of objects
	* `id` - int
	* `name` - string

Example (Show a list of regions):

```php
 	<?php $regions = pc_trips_regions(); ?>
 	<ul>
	 	<?php foreach($regions as $region){ ?>
			<li><?php echo $region->name ?></li>
	 	<?php } ?>
 	</ul>
```

### Countries list

Function:
`pc_trip_countries()`

Returns:
A Countries `Object`

Return Object Attributes:
* `regions` - array of strings

Example (Show a list of countries):

```php
 	<?php $countries = pc_trips_countries(); ?>
 	<ul>
	 	<?php foreach($countries as $country){ ?>
			<li><?php echo $country ?></li>
	 	<?php } ?>
 	</ul>
```

### Dates list

Function:
`pc_trip_dates()`

Returns:
An array of Dates

Return Object Attributes:
* `key` - YYYY-MM format
* `value` - Month YYYY format

Example (Show a list of dates):

```php
 	<?php $dates = pc_trips_dates(); ?>
 	<ul>
	 	<?php foreach($dates as $date){ ?>
			<li><?php echo $date ?></li>
	 	<?php } ?>
 	</ul>
```

### Tags list

Function:
`pc_trip_tags()`

Returns:
A Tags `Object`

Return Object Attributes:
* `tags` - array of strings

Example (Show a list of tags):

```php
 	<?php $tags = pc_trips_tags(); ?>
 	<ul>
	 	<?php foreach($tags as $tag){ ?>
			<li><?php echo $tag ?></li>
	 	<?php } ?>
 	</ul>
```

## Installation

Search and replace the entire folder (all Case Sensitive):

1. purecharity-wp-trips > plugin-name
2. Purecharity_Wp_Trips > Plugin_Name
3. purecharity_wp_trips > plugin_name
3. Pure Charity Trips > Plugin Name
4. `Pure Charity <dev@purecharity.com>` > `Foo Bar <you@server.com>` (Your name and E-mail)

The Boilerplate can be installed in one of two ways both of which are documented below. Note that because of its directory structure, the Boilerplate cannot be installed “as-is.”

Instead, the options are:

### Copying a Directory

1. Copy the `trunk` directory into your `wp-content/plugins` directory. You may wish to rename this to something else.
2. In the WordPress dashboard, navigation to the *Plugins* page
Locate the menu item that reads “The WordPress Plugin Boilerplate.”
3. Click on *Activate.*

### Creating a Symbolic Link

#### On Linux or OS X

1. Copy the `WordPress-Plugin-Boilerplate` directory into your `wp-content/plugins` directory.
2. Create a symbolic link between the `trunk` directory and the plugin. For example: `ln -s purecharity-wp-trips/trunk /path/to/wordpress/wp-content/plugins/purecharity-wp-trips`
3. In the WordPress dashboard, navigation to the *Plugins* page
Locate the menu item that reads “The WordPress Plugin Boilerplate.”
4. Click on *Activate.*

#### On Windows

1. Copy the `WordPress-Plugin-Boilerplate` directory into your `wp-content/plugins` directory.
2. Create a symbolic link between the `trunk` directory and the plugin. For example: `mklink /J path\to\wp-content\plugins \path\to\WordPress-Plugin-Boilerplate\trunk\purecharity-wp-trips`
3. In the WordPress dashboard, navigation to the *Plugins* page
Locate the menu item that reads “The WordPress Plugin Boilerplate.”
4. Click on *Activate.*

Note that this will activate the source code of the Boilerplate, but because the Boilerplate has no real functionality so no menu  items, meta boxes, or custom post types will be added.

Examples are slated to be added to the [Boilerplate’s website](http://wppb.io) as the site continues to grow.

## Recommended Tools

### i18n Tools

The WordPress Plugin Boilerplate uses a variable to store the text domain used when internationalizing strings throughout the Boilerplate. To take advantage of this method, there are tools that are recommended for providing correct, translatable files:

* [Poedit](http://www.poedit.net/)
* [makepot](http://i18n.svn.wordpress.org/tools/trunk/)
* [i18n](https://github.com/grappler/i18n)

Any of the above tools should provide you with the proper tooling to internationalize the plugin.

## License

The WordPress Plugin Boilerplate is licensed under the GPL v2 or later.

> This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License, version 2, as published by the Free Software Foundation.

> This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

> You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA

A copy of the license is included in the root of the plugin’s directory. The file is named `LICENSE`.

## Important Notes

### Licensing

The WordPress Plugin Boilerplate is licensed under the GPL v2 or later; however, if you opt to use third-party code that is not compatible with v2, then you may need to switch to using code that is GPL v3 compatible.

For reference, [here's a discussion](http://make.wordpress.org/themes/2013/03/04/licensing-note-apache-and-gpl/) that covers the Apache 2.0 License used by [Bootstrap](http://twitter.github.io/bootstrap/).

### Includes

Note that if you include your own classes, or third-party libraries, there are three locations in which said files may go:

* `purecharity-wp-trips/includes` is where functionality shared between the dashboard and the public-facing parts of the side reside
* `purecharity-wp-trips/admin` is for all dashboard-specific functionality
* `purecharity-wp-trips/public` is for all public-facing functionality

Note that previous versions of the Boilerplate did not include `Purecharity_Wp_Trips_Loader` but this class is used to register all filters and actions with WordPress.

The example code provided shows how to register your hooks with the Loader class. More information will be provided in the upcoming documentation on the website.

### Assets

The `assets` directory contains three files.

1. `banner-772x250.png` is used to represent the plugin’s header image.
2. `icon-256x256.png` is a used to represent the plugin’s icon image (which is new as of WordPress 4.0).
3. `screenshot-1.png` is used to represent a single screenshot of the plugin that corresponds to the “Screenshots” heading in your plugin `README.txt`.

The WordPress Plugin Repository directory structure contains three directories:

1. `assets`
2. `branches`
3. `trunk`

The Boilerplate offers support for `assets` and `trunk` as `branches` is something that isn’t often used and, when it is, is done so under advanced circumstances.

When committing code to the WordPress Plugin Repository, all of the banner, icon, and screenshot should be placed in the `assets` directory of the Repository, and the core code should be placed in the `trunk` directory.

### What About Other Features?

The previous version of the WordPress Plugin Boilerplate included support for a number of different projects such as the [GitHub Updater](https://github.com/afragen/github-updater).

These tools are not part of the core of this Boilerplate, as I see them as being additions, forks, or other contributions to the Boilerplate.

The same is true of using tools like Grunt, Composer, etc. These are all fantastic tools, but not everyone uses them. In order to  keep the core Boilerplate as light as possible, this feature have been removed and will be introduced in other editions, and will be listed and maintained on the project homepage

# Credits

The WordPress Plugin Boilerplate was started in 2011 by [Tom McFarlin](http://twitter.com/tommcfarlin/) and his since included a number of great contributions.

The current version of the Boilerplate was developed in conjunction with [Josh Eaton](https://twitter.com/jjeaton), [Ulrich Pogson](https://twitter.com/grapplerulrich), and [Brad Vincent](https://twitter.com/themergency).

The homepage is based on a design as provided by [HTML5Up](http://html5up.net), the Boilerplate logo was designed by  Rob McCaskill of [BungaWeb](http://bungaweb.com), and the site `favicon` was created by [Mickey Kay](https://twitter.com/McGuive7).

## Documentation, FAQs, and More

Because this version is a major rewrite of the core plugin, we’re working to create an entire site around the Boilerplate. If you’re interested, please [let me know](http://tommcfarlin.com/contact) and we’ll see what we can do.
