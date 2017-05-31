<?php
App::import('Vendor','unit_convertor/unit_convertor');
App::import('Vendor','hook/hook');

/*
$price_break_from_to = array
[sell_price] => 45
[product_price_break] => Array([0] => Array(
												[deleted] =>
												[range_from] => 5
												[range_to] => 10
												[unit_price] => 25
										))
[company_price_break] => Array([0] => Array(
												[deleted] =>
												[range_from] => 5
												[range_to] => 10
												[unit_price] => 25
										))
[discount] => 10

*/

class cal_price{

	public $arr_list_type = array();
	public $arr_material_lib = array();
	public $v_error='';
	public $unit_convertor;
	public $default_unit_length = 'ft';
	public $field_change = '';
	public $price_break_from_to = array();//default

	protected $v_sum = 0;

	//ki hieu lien giua length va area
	public $length_key=array(
			'Sq. ft.'	=> 'ft',
			'Sq.ft.'	=> 'ft',
			'Sq.in.'	=> 'in',
			'Sq.cm.'	=> 'cm',
			'Sq.m.'		=> 'm',
			'Sq.yard'	=> 'yard',
			'Sq.mm.'	=> 'mm',
		); //default

	public $arr_product_items = array();


	function __construct(){
		$this->_set_unit_convertor();
	}
	function _set_unit_convertor(){
		$this->unit_convertor = new unit_convertor;
	}


	// tính chi phí trung bình cho mỗi diện tích của nguyên vật liệu
	public function cal_unit_price_for_product(){
		$arr_ret = array();
		$this->check_product_items();
		$arr = $this->arr_product_items;
		if($arr['oum_depend']==$arr['oum']){ //oum_depend va oum deu giong nhau
			$this->arr_product_items['unit_price'] = (float)$arr['sell_price'];

		//unit - unit
		}else if($arr['sell_by'] == 'unit' && $arr['oum_depend']=='unit'){
			$this->arr_product_items['unit_price'] = (float)$arr['sell_price'];

		//unit - Sq. ft.
		}else if($arr['sell_by'] == 'unit' && $arr['oum_depend']!='unit'){
			if(isset($this->length_key[$arr['oum_depend']]))
				$this->default_unit_length = $this->length_key[$arr['oum_depend']];
			$this->cal_area();
			if($this->arr_product_items['area']!=0)
				$this->arr_product_items['unit_price'] = (float)$arr['sell_price']/$this->arr_product_items['area'];
			else
				$this->arr_product_items['unit_price'] = 0;

		//area - unit
		}else if($arr['sell_by'] == 'area' && $arr['oum_depend']=='unit'){
			if(isset($this->length_key[$arr['oum_depend']]))
				$this->default_unit_length = $this->length_key[$arr['oum_depend']];
			$this->cal_area();
			$this->arr_product_items['unit_price'] = (float)$arr['sell_price']*$this->arr_product_items['area'];

		//area - Sq.ft.
		}else if($arr['sell_by'] == 'area' && $arr['oum_depend']!='unit'){ //oum_depend va oum khac nhau va cung la nhom area
			$newoum_value = $this->unit_convertor->unit_convertor(1,$arr['oum'],$arr['oum_depend'],5);
			if($newoum_value>0)
				$this->arr_product_items['unit_price'] = (float)$arr['sell_price']*$newoum_value;

		}
		$this->arr_product_items['unit_price'] = (float)$this->arr_product_items['unit_price'];
		$arr_ret = $this->arr_product_items;
		return $arr_ret;
	}


	// tính entry line cho quotation, SO,SI,PO
	public function cal_price_items(){
		$arr_ret = array();
		$this->check_product_items();
		$this->cal_area();
		$hook = new hook($this->arr_product_items);
		$this->cal_adj_qty();
		if($this->field_change!='sell_price'){ //Neu field thay doi la sell_price thi khong ap dung gia giam
			$this->cal_sell_price_by_price_break();
		}
		if($this->field_change!='sell_price' && $this->field_change!='products_name'){
			$this->arr_product_items['sell_price'] += (float)$this->arr_product_items['plus_sell_price'];
		}

		$this->cal_unit_price();
		$this->cal_sub_total();
		$this->cal_tax();
		$this->cal_amount();
		$arr_ret = $this->arr_product_items;
		return $arr_ret;
	}


	//tinh them markup va margin
	public function cal_price_in_markup_margin(){
		$arr = $this->arr_product_items;
		if(isset($arr['unit_price']))
			$this->arr_product_items['unit_price'] = (float)$arr['unit_price'];
		else
			$this->arr_product_items['unit_price'] = 0;

		if(isset($arr['markup']))
			$this->arr_product_items['markup'] = (float)$arr['markup'];
		else
			$this->arr_product_items['markup'] = 0;

		if(isset($arr['margin']))
			$this->arr_product_items['margin'] = (float)$arr['margin'];
		else
			$this->arr_product_items['margin'] = 0;

		if(isset($arr['discount']))
			$this->arr_product_items['discount'] = (float)$arr['discount'];
		else
			$this->arr_product_items['discount'] = 0;

		if(isset($arr['quantity']))
			$this->arr_product_items['quantity'] = (float)$arr['quantity'];
		else
			$this->arr_product_items['quantity'] = 0;

		$arr = $this->arr_product_items;
		$more_markup = $arr['unit_price']*($arr['markup']/100);
		$more_margin = $arr['unit_price']*($arr['margin']/100);
		$more_discount = $arr['unit_price']*($arr['discount']/100);
		$this->arr_product_items['sub_total'] = ($arr['unit_price'] + $more_markup + $more_margin - $more_discount)*$arr['quantity'];
		//return $this->arr_product_items;
	}



	//kiem tra va set/format gia gia mac dinh
	public function check_product_items(){
		$arr = $this->arr_product_items;
		//taxper
		if(isset($arr['taxper']))
			$this->arr_product_items['taxper'] = (float)$arr['taxper'];
		else
			$this->arr_product_items['taxper'] = 0;
		//sizew_unit
		if(!isset($arr['sizew_unit']) || $arr['sizew_unit']=='')
			$this->arr_product_items['sizew_unit'] = 'in';
		//sizeh_unit
		if(!isset($arr['sizeh_unit']) || $arr['sizeh_unit']=='')
			$this->arr_product_items['sizeh_unit'] = 'in';
		//sizew
		if(isset($arr['sizew']))
			$this->arr_product_items['sizew'] = (float)$arr['sizew'];
		else
			$this->arr_product_items['sizew'] = 0;
		//sizeh
		if(isset($arr['sizeh']))
			$this->arr_product_items['sizeh'] = (float)$arr['sizeh'];
		else
			$this->arr_product_items['sizeh'] = 0;
		//quantity
		if(isset($arr['quantity']))
			$this->arr_product_items['quantity'] = (float)$arr['quantity'];
		else
			$this->arr_product_items['quantity'] = 0;

		//adj_qty
		$this->arr_product_items['adj_qty'] = 0;

		//oum
		if(!isset($arr['oum']))
			$this->arr_product_items['oum'] = 'unit';

		//oum_depend
		if(!isset($arr['oum_depend']))
			$this->arr_product_items['oum_depend'] = 'unit';
		if(isset($arr['oum_depend']) && $arr['oum_depend']=='Sq. ft.')
			$this->arr_product_items['oum_depend'] = 'Sq.ft.';

		//sell_price
		if(isset($arr['sell_price']))
			$this->arr_product_items['sell_price'] = (float)$arr['sell_price'];
		else
			$this->arr_product_items['sell_price'] = 0;

		//plus_sell_price
		if(isset($arr['plus_sell_price']))
			$this->arr_product_items['plus_sell_price'] = (float)$arr['plus_sell_price'];
		else
			$this->arr_product_items['plus_sell_price'] = 0;

		//plus_price
		if(isset($arr['plus_unit_price']))
			$this->arr_product_items['plus_unit_price'] = (float)$arr['plus_unit_price'];
		else
			$this->arr_product_items['plus_unit_price'] = 0;

		//reset unit_price
			$this->arr_product_items['unit_price'] = 0;
		//reset sub_total
			$this->arr_product_items['sub_total'] = 0;
		//reset tax
			$this->arr_product_items['tax'] = 0;
		//reset amount
			$this->arr_product_items['amount'] = 0;

	}



	//tinh sell price dua vao bang price break
	public function cal_sell_price_by_price_break(){
		$price_break = $this->price_break_from_to;
		if(!isset($price_break['sell_price_plus']))
			$price_break['sell_price_plus'] = 0;
		//Dò trong bảng company_price_break trước
		if(isset($price_break['company_price_break']) && is_array($price_break['company_price_break']) && count($price_break['company_price_break'])>0){

			$price_break['company_price_break'] = $this->aasort($price_break['company_price_break'],'range_from');

			foreach($price_break['company_price_break'] as $keys=>$value){
				if($this->arr_product_items['adj_qty']<=(float)$value['range_to'] && $this->arr_product_items['adj_qty']>=(float)$value['range_from']){
					//neu thoa dieu kien
					if(!isset($value['unit_price']))
						$value['unit_price'] = 0;
					$this->arr_product_items['sell_price'] = (float)$value['unit_price'] + (float)$price_break['sell_price_plus'];
					$this->price_break_from_to = $price_break; //luu lai bang price_break da sort
					return 'company_price_break';
				}
			}


		}


		//Nếu không có trong company_price_break thì tìm trong product_price_break
		if(isset($price_break['product_price_break']) && is_array($price_break['product_price_break']) && count($price_break['product_price_break'])>0){
			$price_break['product_price_break'] = $this->aasort($price_break['product_price_break'],'range_from');
			foreach($price_break['product_price_break'] as $keys=>$value){
				if($this->arr_product_items['adj_qty']<=(float)$value['range_to'] && $this->arr_product_items['adj_qty']>=(float)$value['range_from']){
					//neu thoa dieu kien
					if(!isset($value['unit_price']))
						$value['unit_price'] = 0;
					$this->arr_product_items['sell_price'] = (float)$value['unit_price'] + (float)$price_break['sell_price_plus'];
					$this->discount(); //và tính discount
					$this->price_break_from_to = $price_break; //luu lai bang price_break da sort
					return 'product_price_break';
				}
			}
		}


		//Ngược lại thì lấy sell_price trong price_break_from_to
		if(isset($price_break['sell_price'])){
			$this->arr_product_items['sell_price'] = (float)$price_break['sell_price'];
			$this->discount();//và tính discount
			return 'sell_price';
		}



	}



	//tính diện tích
	public function cal_area(){
		$arr = $this->arr_product_items;
		if(isset($arr['sizew']) && isset($arr['sizeh']) && (float)$arr['sizew']>0 && (float)$arr['sizeh']>0){
			if(!isset($arr['sizew_unit']))
				$arr['sizew_unit'] = 'in'; //unit default
			if(!isset($arr['sizew_unit']))
				$arr['sizew_unit'] = 'in';//unit default
			$sizew = (float)$arr['sizew'];
			$sizeh = (float)$arr['sizeh'];
			$sizew = $this->unit_convertor->unit_convertor($sizew,$arr['sizew_unit'],$this->default_unit_length,5);
			$sizeh = $this->unit_convertor->unit_convertor($sizeh,$arr['sizeh_unit'],$this->default_unit_length,5);
			$this->arr_product_items['area'] =  (float)$sizew * (float)$sizeh;

		}else if(isset($arr['sell_by']) && $arr['sell_by']=='unit'){
			$this->arr_product_items['area'] = 1;
		}else{
			$this->arr_product_items['area'] = 0;
		}
	}

	//tính chu vi
	public function cal_perimeter(){
		$arr = $this->arr_product_items;
		if(isset($arr['sizew']) && isset($arr['sizeh']) && (float)$arr['sizew']>0 && (float)$arr['sizeh']>0){
			$this->arr_product_items['perimeter'] =  2*((float)$sizew + (float)$sizeh);

		}else if(isset($arr['sell_by']) && $arr['sell_by']=='unit'){
			$this->arr_product_items['perimeter'] = 1;
		}else{
			$this->arr_product_items['perimeter'] = 0;
		}
	}


	//tính tổng diện tích = dien tich x so luong
	public function cal_adj_qty(){
		$arr = $this->arr_product_items;
		if(isset($arr['sell_by']) && strtolower($arr['sell_by'])=='area'){
			$this->arr_product_items['adj_qty'] = (float)$arr['quantity']*(float)$arr['area'];
		}else{
			$this->arr_product_items['adj_qty'] = (float)$arr['quantity'];
		}
	}


	//tính Unit price
	public function cal_unit_price(){
		$arr = $this->arr_product_items;
		if(isset($arr['sell_by']) && ($arr['sell_by']=='unit' || $arr['sell_by']=='Unit'))
			$this->arr_product_items['unit_price'] = (float)$arr['sell_price'];
		else if(isset($arr['sell_price']) && $arr['sell_price']!='' && isset($arr['area']))
			$this->arr_product_items['unit_price'] = (float)$arr['sell_price']*(float)$arr['area'];
		else
			$this->arr_product_items['unit_price'] = 0;

		//more price / plus_unit_price
		if(isset($arr['plus_unit_price']) && (float)$arr['plus_unit_price']>0)
			$this->arr_product_items['unit_price'] += (float)$arr['plus_unit_price'];
	}

	//tính Sub total
	public function cal_sub_total(){
		$arr = $this->arr_product_items;
		if(isset($arr['unit_price']) && $arr['unit_price']!='' && isset($arr['quantity']))
			$this->arr_product_items['sub_total'] = round((float)$arr['unit_price']*(float)$arr['quantity'],2);
		else
			$this->arr_product_items['sub_total'] = 0;

	}

	//tính Tax
	public function cal_tax(){
		$arr = $this->arr_product_items;
		if(isset($arr['taxper']) && $arr['taxper']!='' && isset($arr['sub_total']))
			$this->arr_product_items['tax'] = round(((float)$arr['taxper']/100)*(float)$arr['sub_total'],3);
		else
			$this->arr_product_items['tax'] = 0;
	}

	//tính Amount
	public function cal_amount(){
		$arr = $this->arr_product_items;
		if(isset($arr['sub_total']) && $arr['sub_total']!='' && isset($arr['tax']))
			$this->arr_product_items['amount'] = round((float)$arr['sub_total']+(float)$arr['tax'],2);
	}



	public function change_unit_all($keys=''){
		//truy xuất vào bảng đơn vị để lấy giá trị
		$temp = explode($keys);
		if(count($temp)>1){
			require_once("../unit_convertor/unit_convertor.php");
			$converter = new unit_convertor;

			$this->arr_product[$temp[0]] = $converter->unit_convertor($this->arr_product[$temp[0]],$this->arr_product[$keys],$this->arr_price_break[$keys],2);

		}
	}


	public function cal_price(){
		$this->cal_price_items();
		/*if(count($this->arr_price_break['table'])>0)
			$this->arr_price_break['table'] = krsort($this->arr_price_break['table']);//n->0

		if(isset($arr_product['amount']))
			$this->print_charge();

		if(isset($arr_product['amount']))
			$this->pre_press_charge();

		if(isset($arr_product['amount']))
			$this->fabrication_charge();

		if(isset($arr_product['amount']))
			$this->discount();*/
	}


	public function print_charge(){
		if(isset($this->arr_price_break) && count($this->arr_price_break)>0){
			//chuyen doi don vi
			if($this->arr_price_break['volume_unit']!=$this->arr_product['volume_unit'])
				$this->change_unit('volume_unit');
			if($this->arr_price_break['amount_unit']!=$this->arr_product['amount_unit'])
				$this->change_unit('amount_unit');

			//tinh gia
			if(isset($this->arr_price_break['table'])){
				$temp_amount = (float)$this->arr_product['amount'];//vd:15
				//lap vong bang gia tu n-> 0.vd: 20,10,5,1
				foreach($this->arr_price_break['table'] as $v_k=>$arr_vl){
					if((float)$v_k<=$temp_amount && $temp_amount>=0){ //10<15
						$temp_amount = $temp_amount -(float)$v_k;//15-10
						if(isset($arr_vl[1]))
							$this->v_sum += (float)$arr_vl[1]; //sum=50
						else if(isset($arr_vl[0]))
							$this->v_sum += (float)$arr_vl[0]*(float)$v_k;//or 5*10
					}else
						continue;
				}
			}


		}else
			$this->v_error .= "price_break not set\n";
	}


	public function pre_press_charge(){
		$this->v_sum = $this->v_sum + 0;
	}


	public function fabrication_charge(){
		$this->v_sum = $this->v_sum + 0;
	}


	public function discount(){
		if(isset($this->price_break_from_to['discount']))
			$this->arr_product_items['sell_price'] = (1-((float)$this->price_break_from_to['discount']/100))*$this->arr_product_items['sell_price'];
	}


	public function change_unit($keys=''){
		//truy xuất vào bảng đơn vị để lấy giá trị
		$temp = explode($keys);
		if(count($temp)>1){
			require_once("../unit_convertor/unit_convertor.php");
			$converter = new unit_convertor;

			$this->arr_product[$temp[0]] = $converter->unit_convertor($this->arr_product[$temp[0]],$this->arr_product[$keys],$this->arr_price_break[$keys],2);

		}
	}


	// Sort mảng theo giá trị key, hàm đơn giản
	public function aasort(&$array=array(), $key='',$order=1) {
		$sorter=array();
		$ret=array();
		if(is_array($array) && count($array)>0){
			reset($array);
			foreach ($array as $ii => $va) {
				$sorter[$ii]=$va[$key];
			}
		}
		if($order==1)
			asort($sorter);
		else
			arsort($sorter);

		foreach ($sorter as $ii => $va) {
			$ret[$ii]=$array[$ii];
		}
		$array=$ret;
		return $array;
	}



}