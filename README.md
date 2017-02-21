# AddQuicktag
This plugin makes it easy to add Quicktags to the html - and visual-editor.

## Description
This plugin makes it easy to add Quicktags to the html - and visual-editor. It is possible to export your Quicktags as a JSON - file that can be imported in other installations of the plugin. 

WP-AddQuicktag for WordPress is originally created by [Roel Meurders](http://roel.meurders.nl/ "Roel Meurders"). The versions in the Repo of AddQuicktag are newer versions, this is a complete rewrite of version 2.0.0 with more functionalities.

The plugin can add configurable custom quicktags to the editor of every post type, including custom post types from other sources. You may choose a post type for which a quicktag shall show up in the editor.
If this should not work perfectly well for you, you may also use the hooks inside the plugin. See the examples and hint on the description below.

## Bugs, technical hints or contribute
Please give me feedback, contribute and file technical bugs on this [GitHub Repo](https://github.com/bueltge/AddQuicktag), use Issues.

## Installation
### Requirements
 * WordPress version 3.0 and later (tested at 4.1-Alpha (nightly build))

### Installation
 1. Unpack the download-package
 2. Upload the files to the `/wp-content/plugins/` directory
 3. Activate the plugin through the 'Plugins' menu in WordPress or for the Network, if you will use in Multisite for all Sites
 4. Got to 'Settings' menu and configure the plugin

## Screenshots
 1. [Settings area in WordPress 4.0-alpha](https://github.com/bueltge/AddQuicktag/blob/master/assets/screenshot-1.png)
 2. [HTML Editor with new Quicktags](https://github.com/bueltge/AddQuicktag/blob/master/assets/screenshot-3.png)
 3. [Visual editor with new Quicktags](https://github.com/bueltge/AddQuicktag/blob/master/assets/screenshot-4.png)

## Other Notes
### Hook for custom post types
The plugin add the quicktag on default to post types/ID `post`, `page` and `comment`. If you will also the plugin for other post types you can use a filter; see the follow example, an example plugin in the [Gist 1595155](https://gist.github.com/1595155) or use an example plugin iside this repo, the files end with `.example`.

```php
	// add custom function to filter hook 'addquicktag_post_types'
	add_filter( 'addquicktag_post_types', 'my_addquicktag_post_types' );
	/**
	 * Return array $post_types with custom post types
	 * 
	 * @param   $post_type Array
	 * @return  $post_type Array
	 */
	function my_addquicktag_post_types( $post_types ) {
		
		$post_types[] = 'edit-comments';
		
		return $post_types;
	}
```

### Hook for custom pages
Also it is possible to filter the pages inside the backend. On default was the scripts include the pages `post.php`, `post-new.php` and `comment.php`. The follow example change this for an another page.

```php
	add_filter( 'addquicktag_pages', 'my_addquicktag_pages' );
	/**
	 * Return array $page with custom page strings
	 * 
	 * @param   $page Array
	 * @return  $page Array
	 */
	function my_addquicktag_pages( $page ) {
		
		$page[] = 'edit-comments.php';
		
		return $page;
	}
```

See this Gist als example for add the Quicktags to the editor of comments: [Gist: 3076698](https://gist.github.com/3076698).
If you need the functionality, that the Quicktags of this plugin works on the Quickedit of comments as well, remove the `.example`-part of `addquicktag_quickedit_comment.php.example` filename. The file is a stand alone helper plugin for Add Quicktag and you'll need to activate this file (plugin) separately in 'Manage Plugins'.

### Hook for custom buttons
It is possible to add custom buttons to the editor, if the plugin is active. 
Is usefull to easyier add buttons about the solution of this plugin.

See the follow example to add buttons. The params inside the array is the same as in the settings of the plugin.

```php
	if ( class_exists( 'Add_Quicktag' ) ) :
	add_filter( 'addquicktag_buttons', 'my_addquicktag_buttons' );
	
	function my_addquicktag_buttons( $buttons ) {
	
	    $buttons[] = array(
	        'text'          => 'Permalink', 
	        'title'         => '',
	        'start'         => '[permalink]',
	        'end'           => '[/permalink]',
	        'access'        => '',
	        'order'         => 1,
	        'visual'        => 1,
	        'post'          => 0,
	        'page'          => 1,
	        'comment'       => 0,
	        'edit-comments' => 0
	    );
	    $buttons[] = array(
	        'text'          => 'Button', 
	        'title'         => '',
	        'start'         => '<span class="border blue">',
	        'end'           => '</span>',
	        'access'        => '',
	        'order'         => 2,
	        'visual'        => 1,
	        'post'          => 0,
	        'page'          => 1,
	        'comment'       => 0,
	        'edit-comments' => 0
	    );
	
	    return $buttons;
	}
	endif;
```


### License
Good news, this plugin is free for everyone! Since it's released under the GPL, you can use it free of charge on your personal or commercial blog. But if you enjoy this plugin, you can thank me and leave a [small donation](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6069955 "Paypal Donate link") for the time I've spent writing and supporting this plugin. And I really don't want to know how many hours of my life this plugin has already eaten ;)

### Translations
The plugin comes with various translations, please refer to the [WordPress Codex](http://codex.wordpress.org/Installing_WordPress_in_Your_Language "Installing WordPress in Your Language") for more information about activating the translation. If you want to help to translate the plugin to your language, please have a look at the .pot file which contains all defintions and may be used with a [gettext](http://www.gnu.org/software/gettext/) editor like [Poedit](http://www.poedit.net/) (Windows) or the plugin [Localization](http://wordpress.org/extend/plugins/codestyling-localization/) for WordPress.

### Contact & Feedback
The plugin is designed and developed by me ([Frank Bültge](http://bueltge.de))

Please let me know if you like the plugin or you hate it or whatever ... Please fork it, add an issue for ideas and bugs.

### Disclaimer
I'm German and my English might be gruesome here and there. So please be patient with me and let me know of typos or grammatical farts. Thanks
