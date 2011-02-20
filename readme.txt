=== Plugin Name ===
Plugin Name: oQey Gallery
Version: 0.4.2
Contributors:  oQeySites.com
Donate link: http://oqeysites.com/donations-page/
Tags:  photo, gallery, slideshow, album, flash, images, music, media, fullscreen, gallery in post, photo-albums, custom slideshow, picture, pictures, image, skinnable gallery, oqey, custom flash, oqey gallery, wp custom slideshow, slideshow with music, gallery with music
Requires at least: 3.0.0
Tested up to: 3.0.5
Stable tag: 0.4.2

== Description ==
oQey Gallery is a Plugin for Wordpress that let users create and manage flash slideshows with a non-flash version of gallery built-in for all non-flash browsers and mobile devices like iPhone / iPad / iPod etc. The flash version supports music and skins, so users can change the way it looks with a few simple clicks. Customizable, commercial skins are also available as well as custom galleries for professionals. This plugin uses built-in WP functions and a simple batch upload system. Multiple galleries are supported.

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
* Advanced SEO tools for indexing photos
* Multiple play control - if you press play another instance of a slideshow in a page, 
  the started slideshow will stop playing
* Custom logo support - commercial skins
* Flash Watermark support  


== Installation ==
1. Unzip the plugin archive and put oqey-gallery  folder into your plugins directory (wp-content/plugins/)
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= oQey Gallery plugin need a special setup? =
No. Just make sure your server runs PHP Version 5+. Version 4 won't be supported.

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

1. oQey Gallery Edit
2. oQey Gallery Settings
3. oQey Gallery Music management
4. oQey Gallery Flash Preview
5. oQey Gallery Skin management
6. oQey Gallery Insert Button
7. oQey Gallery Gallery List



== Changelog ==

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