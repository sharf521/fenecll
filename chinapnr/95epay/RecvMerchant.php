<?
require 'func.php';



$para=array(
	"OrdAmt"=>sprintf("%.2f",$OrdAmt),
	"Pid"=>11,
	"MerPriv"=>$MerPriv,
	"UsrSn"=>time().rand(1000,9999)
);

$para['Sign']=md5_sign($para,'fenecll');

$para['GateId']=$_POST['GateId'];


$sHtml = "<form id='fupaysubmit' name='fupaysubmit' action='http://pay.fuyuandai.com/gar/RecvMerchant.php' method='post' style='display:none'>";
while (list ($key, $val) = each ($para)) {
	$sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
}

//submit按钮控件请不要含有name属性
$sHtml = $sHtml."<input type='submit'></form>";

$sHtml = $sHtml."<script>document.forms['fupaysubmit'].submit();</script>";

echo $sHtml;

?>