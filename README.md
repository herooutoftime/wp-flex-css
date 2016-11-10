Directly edit your CSS files in WP-Admin without additional <style> tags.

## Description

## Usage

1. In your CSS file(s):
    1. For each flexible CSS declaration add: `/*! @edit: VARIABLE_NAME*/` right before the CSS rule
    1. Make sure the web-server has write permissions!
1. In Flex-CSS plugin settings:
    1. For each previously defined variable (e.g. VARIABLE_NAME) add a new setting containing:
        1. the variable name (e.g. VARIABLE_NAME)
        1. the expected value (e.g. VARIABLE_VALUE)
    1. Save your settings
    1. Hooray! Your CSS is updated!

## Installation

This is the most common way to install a plugin

1. Download the [ZIP](https://github.com/herooutoftime/wp-flex-css/archive/master.zip)
1. Upload `wp-flex-css` folder to your `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Done!


If any issues occur, please file an issue: https://github.com/herooutoftime/slick-wordpress-gallery/issues/new


## Changelog

### 1.0.0
* Initial version

## WP-Info

* Contributors: herooutoftime
* Tags: css, dynamic
* Requires at least: 4.2.1
* Tested up to: 4.5.3
* Stable tag: 4.5
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html