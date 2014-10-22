=== Plugin Name ===

Plugin Name: oQey Gallery
Version: 0.4.8.5
Contributors: oQeySites.com
Donate link: http://oqeysites.com/donations-page/
Tags: photo, gallery, slideshow, widget, photographs, portfolio, album, flash, image, music, fullscreen, captions, picture, images, photos, iphone, ipad
Requires at least: 4.0.0
Tested up to: 4.0
Stable tag: 1.0.6

== Description ==

oQey Gallery is a Plugin for WordPress that gives its users the possibility to create and manage flash photo galleries / slideshows with an iPhone / iPad version built-in for all non-flash browsers and mobile devices that do not run Adobe Flash Player, i.e. iOS based devices like iPhone, iPad, iPod etc. The flash version supports music and skins, so users can change the way it looks like with a few simple clicks. Customizable, commercial skins are also available as well as custom photo galleries for professionals. This plugin uses built-in WP functions and a simple batch upload system. Multiple photo galleries are supported.


Links:


*	<a href="http://oqeysites.com/oqey-flash-gallery-plugin/" title="Demo gallery">Demo Gallery</a>
*	<a href="http://oqeysites.com/oqey-flash-gallery-plugin/oqey-gallery-faq/" title="FAQ">oQey Gallery FAQ</a> 
 
For more details, skins and examples and custom flash gallery, please visit <a href="http://oqeysites.com/" title="oQeySites">oqeysites.com</a> 


Features:

* Simple and intuitive gallery management
* Built-in flash slideshow with a simple music player
* Skinnable flash slideshow
* Free skins available and many more coming up
* Batch media upload
* Works with any wp theme
* Customizable slideshow size
* Drag & drop to sort images
* Custom skins on demand
* Insert in posts / pages with a single click
* iPhone / iPad detection
* Fullscreen support
* Advanced SEO tools for indexing photos in google and other major Search Engines
* Multiple play control - if you press play another instance of a slideshow in a page, 
  the started slideshow will stop playing
* Custom logo support | commercial skins
* Captions support | commercial skins
* Flash Watermark support
* Auto-crop option
* Continuous play option
* Reverse order function
* Import from existing slideshows / galleries created by supported plugins without duplicating content


== Installation ==

1. Unzip the plugin archive and put oqey-gallery  folder into your plugins directory (wp-content/plugins/)

2. Activate the plugin through the 'Plugins' menu in WordPress


== Frequently Asked Questions ==

= I have just updated the gallery to version 0.4.6 and it seems to be disabled. What should I do to enable it? =
The new update has Roles enabled, so updating the gallery is a little bit tricky. Administrator privileges are required and manual activation. Just deactivate and reactivate the plugin.

= oQey Gallery plugin need a special setup? =
No. Just make sure your server runs PHP Version 5.2+. Version 4 won't be supported.

= Can I have gallery custom size per post? =
Yes. You might specify the gallery width and height, i.e.: [oqeygallery id=1 width=600 height=450]

= I can`t get the photo gallery work with my theme. With the default theme it works all right though. =
In most cases your theme is missing the appropriate call to wp_head(), which is usually found in header.php. Please patch it, taking it from the default theme.? If you don`t know how to do this, the following steps might work for you. Do them at your own risk:

   1. In your admin panel, go to Plugins - Theme Editor
   2. On the right bar,click on Header
   3. Locate the line with <code></head></code>
   4. Insert the following link before it:
      <code><?php wp_head(); ?></code>
   5. Save 

= How should safe_mode be set? =
oQey Gallery plugin works fine with safe_mode=Off only. Please contact you server administrator
in order to switch safe_mode to 'off', if it is 'on'.

= How can I change the image size for the non-flash version of my gallery? =
Just edit css/oqeystyle.css and make all changes that you need.

== Screenshots ==

1. oQey Gallery - Flash Skin Demo
2. oQey Gallery - Management Screenshot
3. oQey Gallery - Music Manager Screenshot
4. oQey Gallery - Insert gallery in content
5. oQey Gallery - Roles Manager Screenshot
6. oQey Gallery - Skin Options Manager Screenshot
7. oQey Gallery - Skins Management Tab Screenshot
8. oQey Gallery - Video Management Screenshot


== Changelog ==

=0.4.8.5=

* GPL compatibility  fixed.

=0.4.8.4=

* WP Touch Plugin compatibility issues fixed

=0.4.8.3=

* new skins settings / advanced features added
* minor security updates
* advanced add-ons / tools support added


=0.4.8.2=

* Security updates
* Widget update


=0.4.8.1=

* Security updates

=0.4.8=

* RSS fixed - now you can view all the gallery images listed in RSS
* widgets support added
* some functionality improvements
* WordPress flash themes compatibility added for photography portfolio flash based websites
* iPhone / iPad / iOS compatibility improved
* minor design changes
* added option to disable flash gallery skins and use java slideshow only

=0.4.7=

* major functionality improvements
* a few server incompatibility issues fixed
* import from NextGEN galleries added
* transition type option added for commercial skins

=0.4.6=

* roles support added
* gallery management improvements
* tags support added for custom insertion
* skins management improvements
* get more skins auto-feed added
* install skins with a single click added

=0.4.5=

* reverse order sorting function added
* border option added
* auto-start option added
* captions option added
* captions position option added
* non-flash browsers display options added
* gallery title option added for non-flash browsers
* compatibility with windows servers improved

=0.4.4=

* width settings are now applied to the html / css version of the gallery as well
* a few compatibility issues fixed
* php version check added

=0.4.3=

* The music issue and a few minor bugs fixed.

=0.4.2=

* Each gallery post can have a custom size now
* Media content SEO improvements
* Continuous play option added
* Security updates

=0.4.1=

* Several compatibility issues fixed.

=0.4=

* Quick core bug fix

=0.3=

* A few bugs fixes.
* Flash slideshow size limits lifted
* Flash thumbnails auto-hide function added
* Flash photo resizing issues fixed
* Thumbnails hide option added to gallery settings

=0.2=

* This version just fixes a few minor bugs. 

=0.1=

* The first stable version.

== Upgrade Notice ==

=0.4.8.5=

* GPL compatibility fixed

=0.4.8.4=

* WP Touch Plugin compatibility issues fixed

=0.4.8.3=

* new skins settings / advanced features added
* minor security updates
* advanced add-ons / tools support added


=0.4.8.2=

* Security updates
* Widget update

=0.4.8.1=

* Security updates

=0.4.8=

* RSS fixed - now you can view all the gallery images listed in RSS
* widgets support added
* some functionality improvements
* WordPress flash themes compatibility added for photography portfolio flash based websites
* iPhone / iPad / iOS compatibility improved
* minor design changes
* added option to disable flash gallery skins and use java slideshow only

=0.4.7=

* major functionality improvements
* a few server incompatibility issues fixed
* import from NextGEN galleries added
* transition type option added for commercial skins

=0.4.6=

* roles support added
* gallery management improvements
* tags support added for custom insertion
* skins management improvements
* get more skins auto-feed added
* install skins with a single click added

=0.4.5=

* reverse order sorting function added
* border option added
* auto-start option added
* captions option added
* captions position option added
* non-flash browsers display options added
* gallery title option added for non-flash browsers
* compatibility with windows servers improved

=0.4.4=

* width settings are now applied to the html / css version of the gallery as well
* a few compatibility issues fixed
* php version check added

=0.4.3=

* The music issue and a few minor bugs fixed.

=0.4.2=

* Each gallery post can have a custom size now
* Media content SEO improvements
* Continuous play option added
* Security updates

=0.4.1=

* Several compatibility issues fixed.

=0.4=

* Quick core bug fix

=0.3=

* A few bugs fixes.
* Flash slideshow size limits lifted
* Flash thumbnails auto-hide function added
* Flash photo resizing issues fixed
* Thumbnails hide option added to gallery settings

=0.2=

* This version just fixes a few minor bugs.

=0.1=

* The first stable version.