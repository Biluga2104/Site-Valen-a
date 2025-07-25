# ChangeLog

## 2.4.5.1
* FIX: Resolved issue with YouTube Playlist source not displaying correctly;

## 2.4.5
* ADD: Repeater Field source for Video Playlist widget (dynamic video list from post meta);
* FIX: minor bug;
* FIX: Fixed XSS vulnerability by sanitizing dynamic HTML attributes in all JetBlog widgets;

## 2.4.4.1
* FIX: Ensure all widgets settings properly escaped before output;

## 2.4.4
* FIX: Resolved fatal error caused by untranslated widget meta in WPML environments;
* FIX: Fixed playback issue in the Video Playlist widget when switching between videos.
* UPD: Removed unused REST API endpoint `elementor-template` for improved security and performance;

## 2.4.3.1
* FIX: XSS vulnerability in Video Playlist widget by sanitizing data attributes;
* FIX: prevent unauthorized access to templates in REST API.

## 2.4.3
* FIX: Incorrect term filter behavior in Smart Posts List widget;
* FIX: minor bugs.

## 2.4.2.1
* FIX: minor bugs.

## 2.4.2
* ADD: Compatibility with Elementor 3.26;
* FIX: Incorrect display conditions for some controls in the Smart Posts List widget.

## 2.4.1
* FIX: "Use Custom Query" condition in the Text Ticker widget.

## 2.4.0
* ADD: Allow to show publication date in the Video Playlist widget;
* ADD: Ability to display RSS feeds in the Text Ticker widget;
* UPD: JetDashboard module to v2.2.0;
* FIX: minor bugs.

## 2.3.8.1
* Fixed: minor bugs

## 2.3.8
* ADD: Allow to show author avatar in Smart Post List, Smart Post Tiles and Text Ticker widgets;
* ADD: Mobile options for smart post tiles;
* UPD: Allow to query posts by multiple post types in Smart Post List, Smart Post Tiles and Text Ticker widgets;
* UPD: Responsive options for Smart Post List widget.

## [2.3.7.1](https://github.com/ZemezLab/jet-blog/releases/tag/2.3.7.1)
* Fixed: minor bugs

## [2.3.7](https://github.com/ZemezLab/jet-blog/releases/tag/2.3.7)
* Added: [Crocoblock/suggestions#7410](https://github.com/Crocoblock/suggestions/issues/7410)
* Added: [Crocoblock/suggestions#7411](https://github.com/Crocoblock/suggestions/issues/7411)
* Added: `Start Time` option in the Video Playlist widget
* Added: new icons of widgets
* Fixed: escaping custom fields in the Smart Posts List widget
* Fixed: minor issues

## [2.3.6](https://github.com/ZemezLab/jet-blog/releases/tag/2.3.6)
* Added: [Crocoblock/suggestions#7107](https://github.com/Crocoblock/suggestions/issues/7107)
* Fixed: channel YouTube videos
* Fixed: work on mobile device in the Smart Posts List widget
* Updated: Vue js module

## [2.3.5.2](https://github.com/ZemezLab/jet-blog/releases/tag/2.3.5.2)
* Fixed: elementor-template Rest API endpoint

## [2.3.5.1](https://github.com/ZemezLab/jet-blog/releases/tag/2.3.5.1)
* Fixed: security issue

## [2.3.5](https://github.com/ZemezLab/jet-blog/releases/tag/2.3.5)
* Updated: JetDashboard Module

## [2.3.4](https://github.com/ZemezLab/jet-blog/releases/tag/2.3.4)
* Fixed: Сompatibility global styles for elementor 3.15.0

## [2.3.3](https://github.com/ZemezLab/jet-blog/releases/tag/2.3.3)
* Fixed: Better sanitize custom callbacks before execute

## [2.3.2](https://github.com/ZemezLab/jet-blog/releases/tag/2.3.2)
* Fixed: `Posts Offset` option for mobile device in the Smart Posts List widget
* Fixed: custom query and builder query filter by terms
* Fixed: minor issues
* Added: `Responsive` control for posts columns in the Smart Posts List widget
* Added: Smart Posts Tiles, Text Tickets compatibility with Elementor Dropdown Menu

## [2.3.1](https://github.com/ZemezLab/jet-blog/releases/tag/2.3.1)
* Fixed: replace Font Awesome icons with svg in widgets
* Fixed: сompatibility with Elementor 3.7
* Fixed: minor issues


## [2.3.0](https://github.com/ZemezLab/jet-blog/releases/tag/2.3.0)
* Added: Smart List and JetEngine Query Builder compatibility;
* Added: Smart Tiles and JetEngine Query Builder compatibility;
* Added: Text Ticker and JetEngine Query Builder compatibility.

## [2.2.17](https://github.com/ZemezLab/jet-blog/releases/tag/2.2.17)
* Added: [Crocoblock/suggestions#2333](https://github.com/Crocoblock/suggestions/issues/2333)
* Fixed: minor issues

## [2.2.16](https://github.com/ZemezLab/jet-blog/releases/tag/2.2.16)
* Fixed: elementor 3.6 compatibility

## [2.2.15](https://github.com/ZemezLab/jet-blog/releases/tag/2.2.15)
* Fixed: widgets compatibility with new breakpoints
* Fixed: RTL issues

## [2.2.14](https://github.com/ZemezLab/jet-blog/releases/tag/2.2.14)
* Fixed: prevent php warning
* Fixed: post title/meta typography issue
* Fixed: slick js dependencies issue

## [2.2.13](https://github.com/ZemezLab/jet-blog/releases/tag/2.2.13)
* Update: JetDashboard module to v2.0.8
* Added: output validation for html tags settings
* Added: Elementor compatibility tag
* Fixed: RTL and minor styles issues

## [2.2.12](https://github.com/ZemezLab/jet-blog/releases/tag/2.2.12)
* Update: JetDashboard module to v2.0.4

## [2.2.11](https://github.com/ZemezLab/jet-blog/releases/tag/2.2.11)
* Update: JetDashboard module to v2.0.3

## [2.2.10](https://github.com/ZemezLab/jet-blog/releases/tag/2.2.10)
* Update: JetDashboard module to v2.0.2
* Fixed: Text ticker widget on safari
* Fixed: excerpt length on ajax in the Smart Posts List widget
* Fixed: compatibility with Elementor 3.0

## [2.2.9](https://github.com/ZemezLab/jet-blog/releases/tag/2.2.9)
* Added: `Order` and `Order by` controls in the Smart Posts List and Smart Posts Tiles widgets
* Fixed: prevent js error

## [2.2.8](https://github.com/ZemezLab/jet-blog/releases/tag/2.2.8)
* Added: an ability filter posts by terms for Custom post types
* Fixed: `Available Widgets` options
* Fixed: [Crocoblock/suggestions#1324](https://github.com/Crocoblock/suggestions/issues/1324)

## [2.2.7](https://github.com/ZemezLab/jet-blog/releases/tag/2.2.7)
* Added: an ability to use the %current_id% macros to exclude current post

## [2.2.6](https://github.com/ZemezLab/jet-blog/releases/tag/2.2.6)
* Added: `To Top` control in the Smart Posts List widget
* Fixed: not working pagination for CPT in the Smart Posts List widget

## [2.2.5](https://github.com/ZemezLab/jet-blog/releases/tag/2.2.5)
* Added: `Posts Offset` control in the Smart Posts List widget
* Added: an ability to use dynamic tags in the `Include posts by IDs` and `Exclude posts by IDs` controls
* Added: multiple improvements
* Fixed: [Crocoblock/suggestions/#970](https://github.com/Crocoblock/suggestions/issues/970)

## [2.2.4](https://github.com/ZemezLab/jet-blog/releases/tag/2.2.4)
* Added: the number of possible columns is increased to 8 in the Smart Posts List widget
* Added: scrolling to the top of the widget after a click on pagination arrow in the Smart Posts List widget

## [2.2.3](https://github.com/ZemezLab/jet-blog/releases/tag/2.2.3)
* Added: support for Font Awesome 5 and SVG icons
* Added: `Show Post Title` option in the Posts Navigation widget
* Fixed: issue [#618](https://github.com/Crocoblock/suggestions/issues/618)

## [2.2.2](https://github.com/ZemezLab/jet-blog/releases/tag/2.2.2) - 21.02.2020
* Update: Jet-Dashboard module to v1.0.10
* Fixed: compatibility with Elementor 2.9

## [2.2.1](https://github.com/ZemezLab/jet-blog/releases/tag/2.2.1) - 02.01.2020
* Update: JetPlugin Dashboard
* Added: compatibility with ACF date fields
* Update: max value for the Posts Offset control in the Smart Posts Tiles widget
* Fixed: Smart Tiles height in IE
* Fixed: prevent php error on update DB

## [2.2.0](https://github.com/ZemezLab/jet-blog/releases/tag/2.2.0) - 29.11.2019
* Added: JetPlugin Dashboard
* Fixed: minor issues

## [2.1.22](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.22) - 06.11.2019
* Added: `Margin` controls to the post buttons in the Smart Posts List widget
* Fixed: posts grid in the Smart Posts List widget
* Fixed: minor issues

## [2.1.21](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.21) - 02.10.2019
* Added: date callbacks for prepare meta values
* Added: compatibility for the upcoming release of JetStylesManager plugin
* Fixed: Smart Tiles mobile display with multiple rows

## [2.1.20](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.20) - 30.08.2019
* Added: an ability to use the Posts Navigation widget in single pages
* Update: widgets icons in the Editor

## [2.1.19](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.19) - 11.07.2019
* Added: need helps links
* Added: Editor Load Level Option

## [2.1.18](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.18) - 18.06.2019
* Fixed: minor CSS issue in the Text Ticker widget

## [2.1.17](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.17) - 12.06.2019
* Added: `Post Type` control in the Text Ticker widget
* Added: `Post Title Max Length` and `Multiline Typing` controls in the Text Ticker widget
* Added: the ability to display videos from YouTube channel or playlist in the Video Playlist widget
* Added: `Alignment` responsive control for custom fields in Smart Posts Tiles and Smart Posts Listing widgets

## [2.1.16](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.16) - 31.05.2019
* Added: New layout for Smart Post Listing widget
* Fixed: vertical content alignment in the Smart Tiles widget

## [2.1.15](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.15) - 16.05.2019
* Fixed: WCAG compatibility in the Smart Posts Tiles Widget

## [2.1.14](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.14) - 09.05.2019
* Added: `Available Widgets` option in the JetBlog Settings Page
* Added: better RTL compatibility in widgets
* Update: minify assets

## [2.1.13](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.13) - 03.05.2019
* Added: `Custom Thumbnail` control in the Video Playlist Widget
* Added: the ability using dynamic tags in the Video Playlist Widget
* Update: replace `home_url` with `site_url` in framework loader

## [2.1.12](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.12) - 18.04.2019
* Added: filter hook 'cx_include_module_url' in the `CX_Loader` class

## [2.1.11](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.11) - 26.03.2019
* Added: the ability displaying CPT terms in the Smart Posts List Widget and the Smart Posts Tiles Widget
* Update: framework to CX
* Fixed: smart tiles layout in IE

## [2.1.10](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.10)
* FIX: Taxonomy Tiles on mobile layout
* FIX: via placeholder url

## [2.1.9](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.9)
* ADD: better RTL compatibility in the Text Ticker Widget
* ADD: better RTL compatibility in the Smart Posts Tiles Widget
* ADD: `Excerpt Trimmed Ending` option in the Smart Posts List Widget
* ADD: `Excerpt Trimmed Ending` option in the Smart Posts Tiles Widget
* ADD: `Autoplay` option in the Smart Posts Tiles Widget
* ADD: `Image Size` option in the Smart Posts Tiles Widget

## [2.1.8](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.8)
* ADD: RU localization
* ADD: bug fixes

## [2.1.7](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.7)
* FIX: Custom Query and Navigation Arrows processing

## [2.1.6](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.6)
* ADD: Allow to use custom taxonomies in Filter by Terms
* ADD: JS trigger on Smart List scripts initialization
* ADD: PHP hook to add custom navigation controls in Smart Posts List

## [2.1.5](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.5)
* FIX: Initialize YouTube iframe API only on pages with playlist

## [2.1.4](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.4)
* UPD: Compatibility with Rich Text Excerpts plugin

## [2.1.3](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.3)
* ADD: Custom Query controls

## [2.1.2](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.2)
* UPD: cherry framework v1.5.10

## [2.1.1](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.1)
* ADD: multiple performance improvements and bug fixes
* FIX: Smart Listing Post content aligment styles
* UPD: cherry framework v1.5.9

## [2.1.0](https://github.com/ZemezLab/jet-blog/releases/tag/2.1.0)

* ADD: 'After Excerpt' value for Meta Position control in the Smart Posts List Widget
* ADD: Alignment controls for posts title and posts excerpt in the Smart Posts List Widget
* ADD: 'filter_alignment' control in the Smart Posts List Widget
* ADD: 'Read More Button' for simple post in the Smart Posts List Widget
* ADD: 'Title Max Length' options in the Smart Posts List Widget
* FIX: RTL styles
* FIX: Alt and title attributes for images

## [2.0.0](https://github.com/ZemezLab/jet-blog/releases/tag/2.0.0)

* ADD: Posts Pagination widget
* ADD: Posts Navigation widget
* UPD: Allow to use Smart Posts Listing as archive template in Elementor Pro 2.0.0
* UPD: Allow to use Smart Tiles as archive template in Elementor Pro 2.0.0
* FIX: Better compatibility with Internet Explorer 11+

## [1.2.1](https://github.com/ZemezLab/jet-blog/releases/tag/1.2.1)

* FIX: Elementor 2.0 compatibility

## [1.2.0](https://github.com/ZemezLab/jet-blog/releases/tag/1.2.0)

* ADD: Allow to query posts by custom fields in Smart List, Smart Tiles and Text Ticker widgets
* ADD: Allow to query posts by IDs in Smart Listing and Smart Tiles widgets
* FIX: Smart Listing - correctly handle active classes in categories filter
* FIX: Smart Tiles - boxes gap on mobile

## [1.1.4.3](https://github.com/ZemezLab/jet-blog/releases/tag/1.1.4.3)

* UPD: force HTTPS in Vimeo API URL

## [1.1.4.2](https://github.com/ZemezLab/jet-blog/releases/tag/1.1.4.2)

* FIX: Smart Tiles - Terms Links Styles - vertical alignment option

## [1.1.4.1](https://github.com/ZemezLab/jet-blog/releases/tag/1.1.4.1)

* UPD: Video PlayList behavior on mobile devices

## [1.1.4](https://github.com/ZemezLab/jet-blog/releases/tag/1.1.4)

* UPD: Video PlayList behavior on mobile devices

## [1.1.3](https://github.com/ZemezLab/jet-blog/releases/tag/1.1.3)

* FIX: prevent video IDs parsing failures

## [1.1.2](https://github.com/ZemezLab/jet-blog/releases/tag/1.1.2)

* FIX: Better compatibility with Autooptimize plugin;
* FIX: Prevent errors when using Video Playlist inside template shortcode in tabs;

## [1.1.1](https://github.com/ZemezLab/jet-blog/releases/tag/1.1.1)

* UPD: translation files;
* ADD: offset and exclude posts by IDs for Text Ticker widget;
* ADD: offset and exclude posts by IDs options for Smart Tiles;

## [1.1.0](https://github.com/ZemezLab/jet-blog/archive/1.1.0.zip)

* ADD: Custom post types and custom fields for Smart Listing;
* ADD: Custom post types and custom fields for Smart Tiles;

## [1.0.1](https://github.com/ZemezLab/jet-blog/archive/1.0.1.zip)

* UPD: Smart List styling fixes;
* FIX: Video Playlist, thumbnails columns width;

## 1.0.0

* Initial release;
