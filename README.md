Automagically include all files in js ans css in your layout, so you need to drop the files and they will be included. 

## Why it's useful:
The helper load everything which is required for your project and has corresponding extension (.js or .css). You don't need to think "Is it included in my layout, or not?", just drop the file in app/webroot/js/ or app/webroot/css/ and it will be included automagically.

The helper doesn't include files and directories which are starting with . For example:
`js/.secretfile.js` or `js/.secret_dir` (same apply to css)
it's useful if you want to include some files manually or you don't want to include them at all.

You can add controller specific files under a special directory called `views` For example in your projects you have controller Posts and Users, some of the functionality is strictly related to posts and other to users. Instead bloating the root js/css folder you can add them under a special folder called `views`
For example, place your Posts files under:
app/webroot/js/views/posts/ and they will be included in Posts controller actions
Add Users specific under 
app/webroot/js/views/users/ and they will be included only in Users controller actions

With this helper you can structure your code more easily, because you can split the code in small files with specific names and they all will be included once they are in the proper palce. This will help you to maintain the code and it will be easy to read.

For example you can add the javascript which is responsible for ajax comment post under
js/views/posts/comments.js
js/views/posts/vote.js
js/views/posts/edit.js
The same apply for css.

## Quick start
* Download or checkout the plugin and place it in your `/app/Plugins` folder.
* Rename the plugin forlder to Autoload
* Ensure the plugin is loaded in app/Config/bootstrap.php by `CakePlugin::load('Autoload')` or `CakePlugin::loadAll()`;
* Include the helper in your AppController.php: 
   * `public $helpers = array('Autoload.Autoload');`
* Add the necessary code in your layout (example: App/View/Layouts/default.ctp)
   * Add in the header: `echo $this->Autoload->all();`. For other options see Function reference.

## Function reference

### Autoload::all()
The helper will include all javascript and css files related under the js and css folder in your document root. as well as corresponding files under views folders.
Example:
`$this->Autoload->all(); //include evetything`

### Autoload::javascript() & Autoload::css()
The helper will load only the specified file types in your layout. It's useful if you want to place javascripts at the bottom of the page while add the css in the page head.
Example:
`$this->Autoload->javascript(); //include all js files`
`$this->Autoload->css(); //include all css files` 

### Autoload::views($type=null)
This will load only the files under corresponing views. $type can be also 'js' or 'css' if you want to include only the specified type. 
Example:
`<?php echo $this->Autoload->views(); //include all views (both css and js) ?>`
`<?php echo $this->Autoload->views('js'); //include js views files ?>`
`<?php echo $this->Autoload->views('css'); //include css views files ?>`

### Autoload::includes($type='js', $js=array())
This will include all files which you specify in the array It's useful if you pass this array from your controller since it need some specific files 
Example:
`<?php echo $this->Autoload->views('js', array('jquery.js', 'plugin.js', '.secret-directory/plugin.specific.js'));?>`

If you want some files to be included before other ones rename them alphabetically or place them in sub folders.
For example the fillowing structure under js will be included like:
js/aplha.js
js/beta.js
js/alpha/alpha.js
js/alpha/beta.js
js/beta/alpha.js
js/beta/beta.js

So if you want to include jquery.js, place it in the root of js/ while the plugins need to be placed under plugins/ folder and then they will be included after the main library.

## Change log

* 1.0 Initial release