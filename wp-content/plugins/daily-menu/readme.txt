=== daily-menu ===
Tags: menu, canteen, dish, restaurant
Requires at least: 4.1.1
Tested up to: 4.3.1
Stable tag: 0.7
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Daily menu provides facilities for canteen management.

== Description ==

Daily menu is designed for canteen management, or other restaurant in which you want to focus one particular menu per day.

In the administration side, create several dishes that you will use later to compose your "daily menu". Once this step is done, create day-to-day menus

Insert the following shortcode to dynamically show the menus of the week : [dm_menu period=week]. Parameter "period" can also be 4weeks if you want to show the menus for the next 4 weeks.

You can also add a wiget on your slide bar with the menu of the day, or the coming next menu, if it is 4 PM or after.  

This plugin provides french translation.

This plugin uses jTable (http://www.jtable.org/).

== Installation ==

Manual installation: extract the content of the archive into your plugin directory.
Use [dm_menu period=week] in your articles or pages to show the menus of the week.
Use the daily-menu widget to show the menu of the day in your columns

== Screenshots ==

1. Dishes management
2. Menus management
3. Daily menu

== Changelog ==

= 0.6 =
* Bug fix : french translation throws errors when adding menus
* Bug fix : shortcode is not printed at the good place in pages
* Bug fix : 4weeks shortcode is not working when weeks are over two years

= 0.6 =
* Adding the 4weeks period
* Bug fix : do no show type of dish if there is no dish to show

= 0.5 =
* Bug fix : short code shows more than one week
* Not showing dish type in widget when there is no corresponding dish in current menu
* Default CSS style enhancement
* Sortings default

= 0.4 =
* Bug fix : "Fatal error: Class 'SELF' not found"
* Adding the possibility to choose css style for shortcode and admin tables
* Adding dates in table headers
* Other bugs fix

= 0.3 =
* Adding style to the shortcode. Now you can define a background picture for each dish.
* Adding the ability to manage sub-types of dish
* Reorganizing admin menus
* Security improvements

= 0.2 =
* Fix freeze when adding dish/menu with wordpress 4.1
* Adding a widget to show the menu of the day

= 0.1 =
* Inital version