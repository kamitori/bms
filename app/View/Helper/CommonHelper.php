<?php
function translate($str){
	if(isset($_SESSION['default_lang'])){
		if(isset($_SESSION['arr_language_'.$_SESSION['default_lang']][$str]) && $_SESSION['arr_language_'.$_SESSION['default_lang']][$str] != ''){
			return $_SESSION['arr_language_'.$_SESSION['default_lang']][$str];
		}else
			return $str;
	}else{
		return $str;
	}
}
class CommonHelper extends AppHelper {

	// function format_date($date='', $show_hour_mitnute = true) {
	// 	if($date=='')
	// 		$date = time();
	// 	if (!is_numeric($date))
	// 		$date = strtotime($date);
	// 	if ($show_hour_mitnute) {
	// 		return date('d M, Y H:i', $date);
	// 	}
	// 	return date('d M, Y', $date);
	// }
	//Dùng để lock sub tab bằng js
	// 	$controller : tên của controller hiện tại
	//	$arr_permission: mảng permission đã được set từ AppController, gồm 2 phần current_permission và inactive_permission
	//	Output: js
	function check_lock_sub_tab($controller,$arr_permission){
		/*
			-Mảng têncontroller chứa các giá trị:
				- Khóa là id của sub_tab
				- Giá trị là quyền của sub_tab đó dựa vào nếu có nhiều quyền cần xét thì phân cách bằng:
					-	|| (nghĩa là 'hoặc') kiểm tra 1 trong tất cả
					- 	&& (nghĩa là 'và') kiểm tra tất cả
					=>Sự phân biệt này nhằm để kiểm tra nhiều quyền mà chỉ cần nhập 1 chuỗi duy nhất
			-Trong js.ctp ở $(function(){}) của mỗi module chỉ cần gọi:
				<?php $this->Common->check_lock_sub_tab($controller,$arr_permission); ?>
		*/
		//Tung, sửa ngày 09/01/2014, để tránh gán dư thừa những controller không cần thiết
		$data = array();
		switch ($controller) {
			case 'companies':
				$data = array('contacts'=>'contacts','addresses'=>'companies','enquiries'=>'enquiries','jobs'=>'jobs','tasks'=>'tasks','products'=>'products','quotes'=>'quotations','orders'=>'salesorders||purchaseorders','shipping'=>'shippings','account'=>'salesaccounts','rfqs' => 'companies','documents'=>'documents_tab||docs','communications'=>'communications_tab','other'=>'companies');
				break;
			case 'contacts':
				$data = array('addresses'=>'contacts','enquiries'=>'enquiries','jobs'=>'jobs','tasks'=>'tasks','products'=>'products','quotes'=>'quotations','orders'=>'salesorders||purchaseorders','shipping'=>'shippings','account'=>'salesaccounts','personal'=>'personal_tab','leave'=>'leave_tab','rate'=>'rates_wages_tab','expense'=>'expenses_tab','workings_holidays'=>'workings_holidays_tab','user_refs'=>'contacts','documents'=>'documents_tab||docs','communications'=>'communications_tab||communications','other'=>'contacts');
				break;
			case 'jobs':
				$data = array('general'=>'jobs','resources'=>'jobs','tasks'=>'tasks','quotes'=>'quotations','orders'=>'salesorders','shipping'=>'shippings','invoices'=>'salesinvoices','documents'=>'documents_tab||docs','other'=>'jobs');
				break;
			case 'tasks':
				$data = array('general'=>'tasks','resources'=>'resources_tab','tasks'=>'tasks','quotes'=>'quotations','orders'=>'salesorders','shipping'=>'shippings','invoices'=>'salesinvoices','documents'=>'documents_tab||docs','other'=>'jobs');
				break;
			case 'quotations':
				$data = array('line_entry'=>'quotations','text_entry'=>'quotations','rfqs'=>'quotations','documents'=>'documents_tab||docs','other'=>'other_tab','asset_tags'=>'quotations');
				break;
			case 'salesinvoices':
				$data = array('line_entry'=>'salesinvoices','text_entry'=>'salesinvoices','receipt'=>'salesinvoices','documents'=>'documents_tab||docs','other'=>'other_tab');
				break;
			case 'salesorders':
				$data = array('line_entry'=>'salesorders','text_entry'=>'salesorders','tasks'=>'tasks','ship_invoice'=>'ship_invoice_tab||shippings||salesinvoices','documents'=>'documents_tab||docs','other'=>'other_tab','asset_tags'=>'salesorders');
				break;
			case 'enquiries':
				$data = array('general'=>'enquiries','quotes'=>'quotations','tasks'=>'tasks','documents'=>'documents_tab||docs','other'=>'enquiries');
				break;
			case 'salesaccounts':
				$data = array('invoices'=>'salesinvoices','receipts'=>'receipts','communications'=>'communications_tab||communications','tasks'=>'tasks','other'=>'salesaccounts');
				break;
			case 'purchaseorders':
				$data = array('line_entry'=>'purchaseorders','text_entry'=>'purchaseorders','shipping_received'=>'purchaseorders||shippings','supplier_invoice'=>'purchaseorders','tasks'=>'tasks','documents'=>'documents_tab||docs','other'=>'other_tab');
				break;
			case 'shippings':
				$data = array('line_entry'=>'shippings','text_entry'=>'shippings','tracking'=>'shippings','documents'=>'documents_tab||docs','other'=>'shippings');
				break;
			case 'products':
				$data = array('general'=>'products','costings'=>'products','pricing'=>'products','stock'=>'products','units_serials'=>'units','batches'=>'batchs','purchasing'=>'purchaseorders','orders'=>'salesorders','shipping'=>'shippings','invoices'=>'salesinvoices','documents'=>'documents_tab||docs','other'=>'products','description'=>'products','printing'=>'products');
				break;
			case 'locations':
				$data = array('general'=>'locations', 'bookings'=>'locations','other'=>'locations');
				break;
			case 'units':
				$data = $units = array('general'=>'units','bookings'=>'units');
				break;
			default:
				$data = array();
				break;
		}
		if(!empty($data)){
			$html = '';
			$i = 0;
			//Remove id => ko gọi ajax được, đương nhiên phải tự cấm thêm bằng PHP trong function
			foreach($data as $sub_tab=>$con){
				$is_array = false;
				if(strpos($con,'||')!==false){
					$is_array = true;
					$type="or";
					$delimiter = '||';
				} else if (strpos($con,'&&')!==false){
					$is_array = true;
					$type="and";
					$delimiter = '&&';
				}
				if($is_array){
					$permission = explode($delimiter, $con);
					foreach($permission as $k=>$v){
						if(strpos($v, '_tab')===false)
							$permission[$k] = $v.'_@_entry_@_view';
						else
							$permission[$k] = $controller.'_@_'.$v.'_@_view';
					}
					if(!$this->check_permission_array($permission,$arr_permission,$type)){
						$html .= '$("#'.$sub_tab.'", ".ul_tab").remove();';
						/*$html .= '$("#'.$sub_tab.'").addClass("disabled_li");';
						$html .= '$("#'.$sub_tab.'").removeAttr("id");';*/
						$i++;
					}
				} else {
					$permission = $con.'_@_entry_@_view';
					if(strpos($con,'_tab')!==false)
						$permission = $controller.'_@_'.$con.'_@_view';

					if(!$this->check_permission($permission,$arr_permission)){
						$html .= '$("#'.$sub_tab.'", ".ul_tab").remove();';
						/*$html .= '$("#'.$sub_tab.'").addClass("disabled_li");';
						$html .= '$("#'.$sub_tab.'").removeAttr("id");';*/
						$i++;
					}
				}
			}
			//Nếu cấm tất cả subtab, thì div ngoài cùng hidden luôn
			if($i==count($data)){
				//Cụm module của anh Nam parent div có id ko đồng nhất
				if(in_array($controller,array('enquiries','jobs','stages','tasks')))
					$html .= '$("#'.$controller.'_sub_content").addClass("hidden");';
				else
					$html .= '$("#load_subtab").addClass("hidden");';
			}
			echo $html;
		}
	}
	//Dùng để check permission
	// 	$permission : permission là string cần kiểm tra EX: companies_@_entry_@_view
	//	$arr_permission: mảng permission đã được set từ AppController, gồm 2 phần current_permission và inactive_permission
	//	$option nếu là $option thì $permission là companies_@_options_@_ , dùng để kiểm tra controller có quyền trên option hay ko
	//	Output: true or false
	function check_permission($permission, $arr_permission, $options=false) {
		$permission = strtolower($permission);
		$tmp = explode('_@_', $permission);
		$controller = $tmp[0];
		 //pr($permission);die;
		if(!CHECK_PERMISSION || $_SESSION['arr_user']['contact_name'] == 'System Admin')
			return true;
		if(isset($arr_permission['inactive_permission'][$permission]))
			return false;
		if(isset($arr_permission['current_permission']['all']))
			return true;
		if($options){
			foreach($arr_permission['current_permission'] as $key=>$value)
				if(strpos($key, $permission)!==false)
					return true;
		} else {
			if(isset($arr_permission['current_permission'][$permission]))
				return true;
		}
		return false;
	}
	//Dùng để check permission
	// 	$permission : permission là array cần kiểm tra EX: array('companies_@_entry_@_view','companies_@_entry_@_edit')
	//	$arr_permission: mảng permission đã được set từ AppController, gồm 2 phần current_permission và inactive_permission
	//	$type là and hoặc or
	//	$option nếu là $option thì $permission là companies_@_options_@_ , dùng để kiểm tra controller có quyền trên option hay ko
	//	Output: true or false
	function check_permission_array($permission, $arr_permission,$type="and",$options=false){
		$i = 0;
		if($options){
			$permission = strtolower($permission);
			$tmp = explode('_@_', $permission);
			$controller = $tmp[0];
			foreach($permission as $key=>$value)
				$permission[$key] = $controller.'_@_options_@_'.$value;
		} else {
			foreach($permission as $key=>$value)
				$permission[$key] = strtolower($value);
		}
		if(!CHECK_PERMISSION || $_SESSION['arr_user']['contact_name'] == 'System Admin')
			return true;
		if($type=="and"){
			//AND:
			//Nếu 1 giá trị trong mảng permission tồn tại trong inactive_permission, return false
			foreach($permission as $value){
				if(isset($arr_permission['inactive_permission'][$value]))
					return false;
			}
		} else if($type=="or"){
			//OR:
			//unset nhứng giá trị trong mảng permission tồn tại trong inactive_permission
			foreach($permission as $key => $value){
				if(isset($arr_permission['inactive_permission'][$value]))
					unset($permission[$key]);
			}
			//Nếu đã unset tất cả, return false
			if(empty($permission))
				return false;
		}
		if(isset($arr_permission['current_permission']['all']))
			return true;
		if($type=="and"){
			//AND sai 1 => sai het
			foreach($permission as $value){
				if(!isset($arr_permission['current_permission'][$value]))
					return false;
			}
			return true;
		}
		else if($type=="or"){
			//OR dung 1 => dung het
			foreach($permission as $value){
				if(isset($arr_permission['current_permission'][$value]))
					return true;
			}
		}
		return false;
	}
	//Dùng để bỏ những liên kết đến những module ko có quyền view, sử dụng trên module anh Vũ
	// 	$arr_link : là array chứa tên controller tồn tại trong permission
	//	$arr_permission: mảng permission đã được set từ AppController, gồm 2 phần current_permission và inactive_permission
	//	Output: js
	function unlink_modules($arr_link,$arr_permission){
		if(!empty($arr_link)){
			$html = '';
			foreach($arr_link as $controller=>$value){
				if(!$this->check_permission($controller.'_@_entry_@_view',$arr_permission))
					foreach($value as $val){
						$html .= '$(".link_to_'.$val.'").removeClass();';
					}
			}
			echo $html;
		}
	}
	//Dùng để bỏ những liên kết đến những module ko có quyền view, sử dụng trên module anh Nam
	// 	$controller : là tên của controller đang đứng
	//	$arr_permission: mảng permission đã được set từ AppController, gồm 2 phần current_permission và inactive_permission
	//	Output: js
	function unlink_crm_modules($controller,$arr_permission){
		if(!isset($arr_permission['current_permission']['all'])){
			$data = array();
			/*
				-Lấy mảng permisson, lọc bằng tên của controller, chỉ cần có tên controller trong permission,
					hiển nhiên nó sẽ quyền view (edit hay delete phải có view).
				-$data[$controller] = true; do chỉ cần lấy tên của controller, nên có thể trùng nhau nếu để ở value
				-Mảng data chỉ cần key, value ko có tác dụng
				-Đẩy mảng PHP vào array của js
				-Tìm những element có href trong $controller_form_auto_save, và regex dựa trên href của element, nếu ko có nếu controller 		trong permission, ko tồn tại trong href => nó ko có quyền view, remove href, chuyển thành span
			*/
			foreach($arr_permission['current_permission'] as $permission=>$all){
				$permission = strtolower($permission);
				$tmp = explode('_@_', $permission);
				$controller_key = $tmp[0];
				$data[$controller_key] = true;
			}
			if(!empty($data)){
				$html = 'var array = [];'.PHP_EOL;
				$i = 0;
				foreach($data as $controller_key=>$useless){
					$html .='array['.$i.'] = "'.$controller_key.'";'.PHP_EOL;
					$i++;
				}
				$html .= '
					var href = "";
					var exist = "";
					$("#'.$controller.'_form_auto_save").find("[href]").each(function(){
						exist = false;
						href = $(this).attr("href");
						if(href!=undefined&&href!=""&&href!="javascript:void(0)"){
							for(var i = 0; i < array.length; i++){
								if(href.match(array[i])!=null){
									exist = true;
									break;
								}
							}
							if(!exist){
								$(this).removeAttr("href").replaceWith(function(){
							        return $("<span>" + $(this).html() + "</span>");
							    });
							}
						}
					});
				';
				echo $html;
			}
		}
	}
	/**
	 * Convert Object
	 * @param type $array
	 * @return boolean
	 */
	function convert_object($array) {
		if (is_array($array))
			return (object) $array;
		else
			return FALSE;
	}

	/*
	 * Convert Array
	 * @param type $object
	 * @return boolean
	 */

	function convert_array($object) {
		if (is_object($object))
			return (array) $object;
		else
			return FALSE;
	}

	function check_null($value = '') {
		if (isset($value))
			return $value;
		else
			return NULL;
	}

	function check_value($val1 = '', $val2 = '') {
		if ($val1 == $val2)
			return TRUE;
		else
			return FALSE;
	}

	function format_currency($num, $afterComma = -1){
		if(is_string($num))
			$num = str_replace(',', '', $num);
		$num = (float)$num;
		if($afterComma == -1)
			$afterComma = $_SESSION['format_currency'];
		$num = round($num,$afterComma);
		return number_format($num,$afterComma);
	}

	function format_date($date='', $show_hour_mitnute = false) {
		if(!isset($_SESSION['format_date']))
			$_SESSION['format_date'] = 'd M, Y';
		$format_date = $_SESSION['format_date'];
		if($date=='')
			$date = time();
		else if(is_object($date))
			$date = $date->sec;
		else if (!is_numeric($date))
			$date = strtotime($date);
		if ($show_hour_mitnute)
			return date($format_date.' H:i', $date);
		return date($format_date, $date);
	}
}