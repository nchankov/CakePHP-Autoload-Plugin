<?php
/**
 * https://github.com/nchankov/CakePHP-Autoload-Plugin
 * Class which will include all files under webroot/js or webroot/css directories.
 * 
 * @author Nik Chankov
 *
 * Quick Usage:
 * 
 * Load all js and css
 * 	<?php echo $this->Autoload->all();?>
 * 
 * Load only javascript files
 * 	<?php echo $this->Autoload->javascript();?>
 * 
 * Load only stylesheets
 * 	<?php echo $this->Autoload->css();?> 
 * 
 * Load only views 
 * 	<?php echo $this->Autoload->views();?>
 * 
 * Load javascript or css view files
 * 	<?php echo $this->Autoload->views('js');?> OR 
 *  <?php echo $this->Autoload->views('css');?>
 * 
 * Include all files requested as array
 * 	<?php echo $this->Autoload->includes('css', $css);?> OR 
 *  <?php echo $this->Autoload->includes('js', $js);?>
 */
App::uses('AppHelper', 'View/Helper');
App::uses('Html', 'View/Helper');

class AutoloadHelper extends AppHelper {
    //Used helpers
    var $helpers = array('Html');
    
    /**
     * return all javascript collections
     * @return string
     */
    function javascript(){
		//Get all files from Root VENDORS
		$files = $this->walker(VENDORS.DS.'js', null, 'js');
		$files = array_merge($files, $this->walker(APP.DS.'vendors'.DS.'js', null, 'js'));
		$files = array_merge($files, $this->walker(JS, null, 'js'));
		//controller specific
		$files = array_merge($files, $this->controller_specific());
		$files = array_unique($files);
		
		$files = $this->strip_slash($files);
		$collection = array();
		foreach($files as $file){
		    $collection[] = $this->Html->script($file);
		}
		return implode("", $collection);
    }
    
    /**
     * Return all css collection
     * @return string
     */
    function css(){
		//Get all files required
		$files = $this->walker(VENDORS.DS.'css', null, 'css');
		$files = array_merge($files, $this->walker(APP.DS.'vendors'.DS.'css', null, 'css'));
		$files = array_merge($files, $this->walker(CSS, null, 'css'));
		//controller specific
		$files = array_merge($files, $this->controller_specific('css'));
		$files = array_unique($files);
		$files = $this->strip_slash($files);
		$collection = array();
		foreach($files as $file){
		    $collection[] = $this->Html->css($file);
		}
		return implode("", $collection);
    }
    
    /**
     * Return both js and css collections
     * @return string
     */
    function all(){
		$collection = array($this->javascript(), $this->css());
		return implode($collection);
    }
    
    /**
     * Remove first slash from the relative urls, because Javascript
     * @param  array $array
     * @return array
     */
    protected function strip_slash($array = array()){
		foreach($array as $key=>$value){
		    if(substr($value, 0, 1) == '/'){
			$array[$key] = substr($value, 1);
		    }
		}
		return $array;
    }

    /**
     * Walks recursivelly on all files under specified directory.
     * return list for inclusion
     * 
     * @param  string $from      [description]
     * @param  string $web_dir   [description]
     * @param  string $extension [description]
     * @return array
     */
    protected function walker( $from, $web_dir = null, $extension){
		if(!is_dir($from)){
		    return array();
		}
		$d = dir($from);
		$dirs = array();
		$files = array();
		//First loop is to add files into returned array
		while (false !== ($entry = $d->read())) {
		    //Exclude some special entries
		    if( $entry == '.' || $entry == '..' || substr($entry, 0, 1) == '.' || $entry == 'views'){continue;}
		    if(is_dir($from.DS.$entry)){
			//sub directories
			$dirs[] = $entry;
		    }
		    //adding only files with specified extension
		    if(is_file($from.DS.$entry) && (substr($entry, -1*strlen($extension)) == $extension)){
			//Small hack because the using / or some string, cause problem for loading properly the urls/
			if($web_dir == null){
			    $files[] = $entry;
			} else {
			    $files[] = $web_dir.DS.$entry;
			}
		    }
		}
		$d->close();
		//Sort files by name
		sort($files);
		//this one is to walk through all directories underneath
		foreach($dirs as $entry){
		    $files = array_merge($files, $this->walker( $from.DS.$entry, $web_dir.'/'.$entry, $extension));
		}
		return $files;
    }
    
    /**
     * Function which will check for the files in specific controller and 
     * action under JS/views and CSS/views directories
     * 
     * @param  string $type
     * @return array
     */
    protected function controller_specific($type = 'js'){
		$controller = $this->params['controller'];
	        $action = $this->params['action'];
		$filed = array();
		if($type == 'css'){
		    $files = $this->walker( CSS.'views/'.$controller, 'views/'.$controller, 'css');
		}
		if($type == 'js'){
		    $files = $this->walker( JS.'views/'.$controller, 'views/'.$controller, 'js');
		}
		return $files;
    }
    
    /**
     * Return only view specific files
     * @param  text $type
     * @return string
     */
    public function views($type = null){
		$collection = array();
		if($type == 'js' || $type == null){
			$files = $this->controller_specific('js');
			$files = array_unique($files);
			$files = $this->strip_slash($files);
			foreach($files as $file){
			    $collection[] = $this->Html->script($file);
			}
		}
		if($type == 'css' || $type == null){
			$files = $this->controller_specific('css');
			$files = array_unique($files);
			$files = $this->strip_slash($files);
			foreach($files as $file){
			    $collection[] = $this->Html->css($file);
			}
		}
		return implode("", $collection);
    }

    /**
     * Include all files which are provided as an array in the second parameter
     * @param  string $type
     * @param  array  $files
     * @return string
     */
    public function includes($type = 'js', $files = array()){
    	$collection = array();
    	if($type == 'css'){
    		foreach($files as $file){
    			$collection[] = $this->Html->css($file);
    		}
    	}
    	if($type == 'js'){
    		foreach($files as $file){
    			$collection[] = $this->Html->script($file);
    		}
    	}
    	return implode("", $collection);
    }
}