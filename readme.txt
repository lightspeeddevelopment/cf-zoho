=== LSX Zoho CRM Addon for Caldera Forms ===
Contributors: feedmymedia, lightspeedwp, eleshar, krugazul, jacquesvdh, ignusvermaak
Tags: Caldera Forms, zoho, crm, zoho-crm
Requires at least: 5.0
Tested up to: 5.5.1
Requires PHP: 7.0
Stable tag: 2.0.6
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

Integrate Caldera Forms with Zoho CRM on your WordPress website.

== Description ==

[Caldera Forms](https://wordpress.org/plugins/caldera-forms/) is a different kind of WordPress form builder. Developed to be responsive, intuitive and meet the needs of the modern WordPress site builder. Caldera Forms is the free WordPress form builder plugin that does more.

The LSX Zoho CRM Addon for Caldera Forms allows users to automate their day-to-day business activities by allowing them to focus on selling without having to worry about digging through data. Use this free extension to track your sales activities and gain complete understanding of your sales cycle. Easily create or update a lead, contact or any other object in Zoho CRM when an entry is created in Caldera Forms.

== Installation ==

1. Ensure you have the Caldera Forms plugin installed 
2. Upload the LSX Zoho CRM Caldera Forms Add-on to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go the the plugin options page `/wp-admin/options-general.php?page=lsx_cf_zoho` and follow instructions on configuring your Zoho CRM to allow access.
5. Create a Caldera Form and click the `Processors` tab to select the relevant Zoho processor(s) you wish to apply to the form

== Frequently Asked Questions ==
 
= How do I allow the plugin access to my Zoho CRM? =
 
Go to `https://accounts.zoho.eu/developerconsole` or `https://accounts.zoho.com/developerconsole` (depending on the region your Zoho CRM is registered in), and add a client ID with the settings shown on the LSX CF Zoho Options page.

= I have added a new field to my Zoho CRM and its not showing up the Caldera Forms processor, how do I resolve this?

In order for this plugin to work at efficient speeds, Zoho CRM fields and users data are cached. If you have added new users or fields to your Zoho CRM, go to the LSX CF Zoho Options page, check the "Flush Cache" checkbox and click the "Save Settings" button. This will flush all cached settings. Next time you edit a processor, new fields and/or users will be displayed.

== Field Formatting ==

* Date fields need to be set in the format yyyy-mm-dd otherwise they will fail.
* The date magic tag can be used in the following format "{date:Y-m-d}"

== Processor Configuration ==

* When setting a "Layout" you will need to include the name and the ID separated by a | symbol.  The name goes first, and then the ID. e.g "Direct|11111111111"
