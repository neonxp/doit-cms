<?php


class Route
{
	public  $method = false;
	public $url='/news/:id+';
	public $closure=false;
	
	public function via($via=false){
		$this->method=$via;
	}
	
	public function map($url,$closure)
	{
		$this->url=$url;
		$this->closure=$closure;
	}
	public function check($url='/catalog', $method=false, $level="content"){
		$regex = $this->url;
		$regex = preg_replace(
			array('#\:[a-z_][a-zA-Z0-9_]*\+#','#\:[a-z_][a-zA-Z0-9_]*\*#','#\:[a-z_][a-zA-Z0-9_]*#')
			,array('(.+?)','(.*?)','([^\/]+?)')
		,$regex);
		if(!preg_match('#^'.$regex.'$#',$url)){
			return false;
		}
		if($method !== false && $this->method !== false){
			if(!in_array($method,$this->method)){
				return false;
			}
		}
		
		return true;
	}
	public function dispatch($url){
		$matches = array();
		$regex = $this->url;
		$regex = preg_replace(
			array('#\:[a-z_][a-zA-Z0-9_]*\+#','#\:[a-z_][a-zA-Z0-9_]*\*#','#\:[a-z_][a-zA-Z0-9_]*#'),array('(.+?)','(.*?)','([^\/]+?)')
		,$regex);
		preg_match('#^'.$regex.'$#',$url,$matches);
		unset($matches[0]);
		ob_start('doit_ob_error_handler');
		$_executionResult = call_user_func_array($this->closure,$matches);
		$_end = ob_get_contents();
		ob_end_clean();
		if (!is_null($_executionResult)) {
			$_end = $_executionResult;
		}else{
			//null; ob_start ничего не дал, return в контроллере не было
			//начинаем рулить шаблон
			if($_end == ''){
				$_end = d()->view;
		
			}
		}
		
		
		return $_end;
	}
	
}
