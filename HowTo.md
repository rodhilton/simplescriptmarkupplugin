# Introduction #

This guide will take you through installing the Simple Script Markup plugin into a wordpress installation and write your first blog-ready screenplay.

# Installing #

Download the plugin using the svn repository.  If you don't want to bother with SVN, you can grab the latest version by following the 'Source' tab above, then browsing the Subversion Repository.  Follow the link that says 'trunk' and grab the latest ssm.php file.  You can also follow [this](http://simplescriptmarkupplugin.googlecode.com/svn/trunk/) link to grab the ssm.php file.  Make sure you right click and save the target to your disk; opening the file in a browser and saving from there might add additional characters that will break it inside Wordpress.

Put that file in your wp-content/plugins directory in your wordpress installation.

Then log into your wordpress account, go to "plugins" and enable the Simple Script Markup Plugin.

# Using #

To use, simply make a post and include your SimpleScriptMarkup text inside of an 

&lt;ssm&gt;

 tag.  The plugin, when enabled, will automatically parse this text and output it, surrounding each element with an appropriate DIV tag.

The DIV tags reference classes.  Rather than force any kind of formatting, the SSM Plugin allows you to control how screenplays should look on your site.  Each element (action, character, parenthetical, dialogue, etc) gets its own class, as does the entire script (ssmscript class).

For an example css file, check the 'Downloads' link above and grab the [ssmstyle.css](http://simplescriptmarkupplugin.googlecode.com/files/ssmstyle.css) file.