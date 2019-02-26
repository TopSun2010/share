<?php
error_reporting(E_ALL);
define("INTERFACES","/etc/network/interfaces");

include("config.php");

getConf();

$p=$_POST;

$netdev="eth0";
//保存算力监控到文件

if(isset($p['monitor'])){
  $monitor=trim($p['monitor']);
  if(!empty($monitor)){
    //需要程序执行用户有 此文件的7的权限
    file_put_contents('/home/ethos/hash.conf', $monitor);
  }
}
//保存算力监控结束
if (isset($p["ip"]) && isset($p["netmask"]) && isset($p["gateway"]) && isset($p["dns"]) && isset($p['static']) ){

	$file = INTERFACES;
	$interface=sprintf("
auto lo 
iface lo inet loopback

auto %s 
iface %s inet static
address %s
netmask %s
gateway %s
dns-nameservers %s 
dns-nameservers 8.8.8.8
dns-nameservers 8.8.4.4

",$netdev,$netdev,
$p["ip"],$p["netmask"],$p["gateway"],$p["dns"]
);
	$interface=str_replace("\x0d","",$interface);
	system("sudo touch $file ; sudo chmod a+w $file");
	file_put_contents($file,$interface);
	//echo "System is Rebooting now !";
	$reboot_mesg=" 网络设置完成, 系统将重启 ! 请等待 ... ";
	
	//exit;
}

if (isset($p['dhcp']) ){

	$file = INTERFACES;
	$interface=sprintf("
auto lo 
iface lo inet loopback

auto %s 
iface %s inet dhcp
dns-nameservers %s 
dns-nameservers 8.8.8.8
dns-nameservers 8.8.4.4

",$netdev,$netdev,$p["dns"]
);
	$interface=str_replace("\x0d","",$interface);
	system("sudo touch $file ; sudo chmod a+w $file");
	file_put_contents($file,$interface);
	//echo "System is Rebooting now !";
	$reboot_mesg=" 设置动态IP地址完成 ! 请等待 ... ";
	
	//exit;
}


if(isset($p['act']) && $p['act']=='save'){
	
	$CONFIG->mine_type=$p['mine_type'];

	unset($p['lang'],$p['act']);
	$CONFIG=$p;
	$errmsg="设置已保存! ".implode("&nbsp;",$errmsg);
	
	writeConf($CONFIG);
	getConf();
}


if (isset($p["restartnow"]) && $p["restartnow"]==1)
{
	$reboot_mesg="重启中";
}	




$network=explode("\n",file_get_contents(INTERFACES));
foreach($network as $row):
	$row=trim($row);
	if(substr($row,0,7)=="address") $ip[]     =trim(substr($row,8));
	if(substr($row,0,7)=="netmask") $netmask[]=trim(substr($row,8));
	if(substr($row,0,7)=="gateway") $gateway[]=trim(substr($row,8));
	if(substr($row,0,14)=="dns-nameserver")		$dns[]=trim(substr($row,15));
endforeach;




include("header.php");
exec("dmesg|grep eth0|grep '[0-9a-z][0-9a-z]:[0-9a-z][0-9a-z]:[0-9a-z][0-9a-z]:[0-9a-z][0-9a-z]:[0-9a-z][0-9a-z]:[0-9a-z][0-9a-z]'|awk '{print $9}'|cut -c 1-17",$mac);
include("pool.php");
?>
<script src="jquery-1.8.3.min.js"></script>
<div style="padding: 10px;">

  <table border="0" align="left" cellpadding="5" cellspacing="0">
    <tr>
      <td><form action="" method="post">
        <table align="left">
          <tr>
            <td colspan="9"><strong>网络设置</strong>&nbsp; 本机MAC：<?php echo $mac[0];?>&nbsp;&nbsp;&nbsp;<input type="submit" name="dhcp" value="设置本机为动态获取IP(DHCP)" /></td>
          </tr>
          <tr>
            <td nowrap="nowrap"><strong>IP:</strong></td>
            <td><input style="color:blue" type="text" name="ip" size="15" value="<?php echo $ip[0] ?>" /></td>
            <td nowrap="nowrap"><strong>掩码 </strong>: </td>
            <td><input style="color:blue" type="text" name="netmask" size="15" value="<?php echo $netmask[0] ?>" /></td>
            <td nowrap="nowrap"><strong>网关</strong>: </td>
            <td><input style="color:blue" type="text" name="gateway" size="15" value="<?php echo $gateway[0] ?>" /></td>
            <td nowrap="nowrap"><strong>DNS</strong> : </td>
            <td><input style="color:blue" type="text" name="dns" size="15" value="<?php echo $dns[0] ?>" /></td>
            <td><input type="submit" name="static" value="设置为固定IP" /></td>
          </tr>
        </table>
      </form></td>
    </tr>
    <tr>
      <td align="right"><font color="red"><b><?php echo isset($reboot_mesg)?$reboot_mesg:"" ;?></b>&nbsp;&nbsp;</font></td>
    </tr>
    <tr>
      <td><form action="" method="post">
<?php
$g=(object)$_GET;
//if(isset($g->d)): // gpu
?>
<!--setup for gpu setting-->
<table  cellpadding="4" cellspacing="2" style="margin-left: 30px;">
<tr>
<td colspan="2"><b>显卡参数设置</b></td>
</tr>

<tr>
<td width="15%" align="right">最大功耗</td>
<td><input type="text" id="pow" name="pow" size="6">(W)</td>
</tr>

<tr>
<td align="right">最高温度</td>
<td><input type="text" id="temp" name="temp" size="6">(C)</td>
</tr>

<tr>
<td align="right">风扇转速</td>
<td><input type="text" id="fan" name="fan" size="6">(%)</td>
</tr>

<tr>
<td align="right">核心频率</td>
<td><input type="text" id="core" name="core" size="60">(MHz)[ 每参数用空格隔开]</td>
</tr>

<tr>
<td align="right">内存频率</td>
<td><input type="text" id="mem" name="mem" size="60">(MHz)[每参数用空格隔开]</td>
</tr>

</table>

<!--end setup gpu -->
<?php 
//endif; // end gpu
?>

        <table width="100%" cellpadding="4" cellspacing="2">
          <tr align="center">
            <td colspan="2" align="left"><strong>矿池设置:</strong>&nbsp;&nbsp;<span style='color: red; font-weight: bold; float:right; text-align: center;'><?php if(isset($errmsg) && $errmsg!="") echo $errmsg ?>&nbsp;</span></td>
          </tr>
  
          <tr>
            <td width="10%" align="right" nowrap="nowrap"><strong>主挖：</strong></td>
            <td nowrap="nowrap"><select name="mine_type" size="1" id="mine_type">
              <option value="eth">1 ETH</option>
              <option value="etc">2 ETC</option>
              <!-- <option value="zec">③ ZEC</option>-->
              <option value="zec_n">3 ZEC(N卡)</option>
              <option value="btg">4 BTG</option>

			  </select>
              <strong> 矿工号：</strong>
              <input name="worker" type="text" id="worker" size="16" /></td>
          </tr>
          </table>
          <table width="100%" cellpadding="4" cellspacing="2">
          <tr>
            <td width="20%" align="right" nowrap="nowrap"><strong>1 ETH钱包地址：</strong></td>
            <td><input type="text" name="eth_wallet" id="eth_wallet" size="50" /></td>
          </tr>
          <tr>
            <td width="20%" align="right" valign="top" nowrap="nowrap"><strong>ETH矿池地址：</strong></td>
            <td><input type="text" name="eth_pool_host" id="eth_pool_host" size="40" />
              <strong>矿池端口:</strong>
              <input type="text" name="eth_pool_port" id="eth_pool_port" size="5" />
              <div id="eth_pools"></div></td>
          </tr>

          </table>
          <table width="100%" border="0" cellpadding="4" cellspacing="2">
          
          <tr>
            <td width="20%" align="right" nowrap="nowrap"><strong>2 ETC钱包地址：</strong></td>
            <td><input type="text" name="etc_wallet" size="50" id="etc_wallet" /></td>
          </tr>
          <tr>
            <td width="20%" align="right" valign="top" nowrap="nowrap"><strong><span opt="main_name"></span>ETC矿池地址：</strong></td>
            <td><input name="etc_pool_host" type="text" id="etc_pool_host" size="40" />
              <strong>矿池端口:</strong>
              <input name="etc_pool_port" type="text" id="etc_pool_port" size="5" />
              <div id="etc_pools"></div></td>
          </tr>
          </table>
          <table width="100%" cellpadding="4" cellspacing="2">

          <tr>
            <td width="20%" align="right" nowrap="nowrap"><strong>3 ZEC钱包地址：</strong></td>
            <td><input name="zec_wallet" type="text" id="zec_wallet" size="50" /></td>
          </tr>
          <tr>
            <td width="20%" align="right" valign="top" nowrap="nowrap"><strong><span opt="main_name"></span>ZEC矿池地址：</strong></td>
            <td><input name="zec_pool_host" type="text" id="zec_pool_host" size="40" />
              <strong>矿池端口:</strong>
              <input name="zec_pool_port" type="text" id="zec_pool_port" size="5" />
              <div id="zec_pools"></div></td>
          </tr>
          </table>
          <table width="100%" cellpadding="4" cellspacing="2">

 
           <tr>
            <td width="20%" align="right" nowrap="nowrap"><strong>4 BTG钱包地址：</strong></td>
            <td><input name="btg_wallet" type="text" id="btg_wallet" size="50" /></td>
          </tr>
          <tr>
            <td width="20%" align="right" valign="top" nowrap="nowrap"><strong><span opt="main_name"></span>BTG矿池地址：</strong></td>
            <td><input name="btg_pool_host" type="text" id="btg_pool_host" size="40" />
              <strong>矿池端口:</strong>
              <input name="btg_pool_port" type="text" id="btg_pool_port" size="5" />
              <div id="btg_pools"></div></td>
          </tr>
          </table>
          <table width="100%" cellpadding="4" cellspacing="2">


		  <tr>
		    <td width="20%" align="right" nowrap="nowrap"><strong>设置完成马上重启:</strong></td>
		    <td><input name="restartnow" type="checkbox" id="restartnow" value="1" /></td>
		    </tr>
          <tr>
		  <td width="20%" align="right" nowrap="nowrap"><strong>算力监控:</strong></td>
        <td><input name="monitor" type="text" id="monitor" value="" /></td>
        </tr>
            <td width="20%" align="right" nowrap="nowrap"><input name="lang" type="hidden" id="lang" value="en" />
              <input name="act" type="hidden" id="act" value="save" /></td>
            <td><input type="submit" value=" 保 存 " /></td>
          </tr>
        </table>
      </form></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><table width="100%" align="left">
        <tr>
          <td align="left">&nbsp;</td>
          </tr>
        <tr>
            <p>&nbsp;</p></td>
          </tr>
        <tr>
          <td align="center"><span style="font-size: 10px; color: #ccc;">说明:请联系你的经销商获取超频参数，过度超频伤卡！！！ </span></td>
          </tr>
		    <tr>
		   <td align="center"><span style="font-size: 10px; color: #FFFAF0;">本程序会收取些许费用，介意无用！ </span></td>
          </tr>
      </table></td>
    </tr>
  </table>
  <div style="clear:both"></div>
</div>
</body>
</html>

<script>
var pool_name = eval ('[<?php echo json_encode($POOL_NAME);?>]');

var pool_list = eval ('[<?php echo json_encode($POOL_LIST);?>]');	

$(function(){
	var config_data = eval('[<?php unset($CONFIG->lang,$CONFIG->act); echo json_encode($CONFIG) ?>]')
	//console.log(config_data[0])
	$.each(config_data[0],function(k,v){
		$("#"+k).val(v)
	})

})
</script>
<script src="pool.js?<?php echo rand(100,999) ?>"></script>
<?php
//echo $reboot_mesg;
if(isset($reboot_mesg) && $reboot_mesg!="")
{
	//echo 'set up reboot ';
//	system("sudo /sbin/reboot -f > /dev/null 2>&1 & ");
	system("sudo /sbin/reboot > /dev/null 2>&1 & ");
}
