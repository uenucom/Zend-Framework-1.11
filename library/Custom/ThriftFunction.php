<?php
function Thrift_Select_Server($Thrift_Host)
{
	$Maxnum = $Thrift_Host["Maxnum"];
	if($Maxnum==0)	{
		$num = 0;
	}else{
		$num = rand(0,$Maxnum);
	}
	return $Thrift_Host;
}
