<?php
	namespace Admin\Model;
	use Think\Model;
	class AdvertisementModel extends Model{
		//错误信息验证
		protected $_validate=array(
			array('name','require','广告标题不能为空!'),
			array('height','require','图片高度不能为空!'),
			array('width','require','图片宽度不能为空!'),
			array('height','/^\d+(\.\d+)?$/','图片高度必须为数字格式!',0,'regex',1),
			array('width','/^\d+(\.\d+)?$/','图片宽度必须为数字格式!',0,'regex',1),
		);
	}
?>
