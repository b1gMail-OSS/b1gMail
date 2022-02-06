<?php 
/*
 * b1gMail7 server test
 * (c) 2002-2018 B1G Software
 *
 */

error_reporting(0);

// returns numeric PHP version
function PHPNumVersion()
{
	$ver = phpversion();
	if(strlen($ver) > strlen('0.0.0'))
		$ver = substr($ver, 0, strlen('0.0.0'));
	return((int)str_replace('.', '', $ver));
}

// embedded files (stylesheet / images)
$embeddedFiles = array(
	'style.css'			=> array(
		'type'			=> 'text/css',
		'data'			=> 'PCEtLQoKQk9EWQp7CgliYWNrZ3JvdW5kLWNvbG9yOiAjNjY2NjY2OwoJbWFyZ2luOiAwcHg7Cn0KCiNoZWFkZXIKewoJd2lkdGg6IDY0N3B4OwoJaGVpZ2h0OiA5MXB4OwoJYmFja2dyb3VuZC1pbWFnZTogdXJsKHNlcnZlcnRlc3QucGhwP2ZpbGU9aGVhZC5wbmcpOwp9CgojZ3JhZGllbnQKewoJd2lkdGg6IDY0N3B4OwoJaGVpZ2h0OiAyOHB4OwoJYmFja2dyb3VuZC1pbWFnZTogdXJsKHNlcnZlcnRlc3QucGhwP2ZpbGU9Z3JhZGllbnQucG5nKTsKCWJhY2tncm91bmQtcG9zaXRpb246IHJpZ2h0OwoJYmFja2dyb3VuZC1yZXBlYXQ6IG5vLXJlcGVhdDsKCWNvbG9yOiAjRkZGRkZGOwoJZm9udC1zaXplOiAyMnB4OwoJZm9udC1mYW1pbHk6IGFyaWFsOwoJZm9udC13ZWlnaHQ6IGJvbGQ7Cgl0ZXh0LWFsaWduOiByaWdodDsKfQoKI2xlZnRzaGFkZQp7Cgl3aWR0aDogMjlweDsKCWJhY2tncm91bmQtaW1hZ2U6IHVybChzZXJ2ZXJ0ZXN0LnBocD9maWxlPXNoYWRlX2xlZnQucG5nKTsJCn0KCiNyaWdodHNoYWRlCnsKCXdpZHRoOiAyOXB4OwoJYmFja2dyb3VuZC1pbWFnZTogdXJsKHNlcnZlcnRlc3QucGhwP2ZpbGU9c2hhZGVfcmlnaHQucG5nKTsJCn0KCiNtYWluCnsKCXdpZHRoOiA2NDdweDsKCXRleHQtYWxpZ246IGxlZnQ7Cn0KCiogaHRtbCBibG9ja3F1b3RlIHsgaGVpZ2h0OiAwOyBiYWNrZ3JvdW5kLWNvbG9yOiAjRkZGRkZGIWltcG9ydGFudDsgfQoKI2NvbnRlbnQKewoJYmFja2dyb3VuZC1jb2xvcjogI0ZGRkZGRjsKCWZvbnQtZmFtaWx5OiBhcmlhbDsKCWZvbnQtc2l6ZTogMTJweDsKCWNvbG9yOiAjMzIzMjMyOwoJbGluZS1oZWlnaHQ6IDE3cHg7CgltYXJnaW46IDhweDsKfQoKLmxpc3QKewoJd2lkdGg6IDEwMCU7Cn0KCi5saXN0IFRICnsKCWJhY2tncm91bmQtY29sb3I6ICNFRkVGRUY7CglwYWRkaW5nOiA1cHg7Cgl0ZXh0LWFsaWduOiBsZWZ0Owp9CgoubGlzdCBURAp7CglwYWRkaW5nOiA1cHg7Cn0KCkxBQkVMCnsKCWZvbnQtd2VpZ2h0OiBib2xkOwp9CgpIMQp7Cglmb250LWZhbWlseTogdGFob21hLCBhcmlhbDsKCWZvbnQtc2l6ZTogMTRweDsKCWZvbnQtd2VpZ2h0OiBib2xkOwoJY29sb3I6ICMzMzMzMzM7CgltYXJnaW46IDBweDsKCW1hcmdpbi10b3A6IDRweDsKCW1hcmdpbi1ib3R0b206IDEwcHg7Cglib3JkZXItYm90dG9tOiAxcHggc29saWQgI0RERERERDsKfQpIMwp7Cglmb250LWZhbWlseTogYXJpYWw7Cglmb250LXNpemU6IDEycHg7Cglmb250LXdlaWdodDogbm9ybWFsOwoJY29sb3I6ICMwMDAwMDA7CQoJbWFyZ2luOiAwcHg7CgltYXJnaW4tdG9wOiAycHg7CgltYXJnaW4tYm90dG9tOiAycHg7Cn0KCi5nbG9zc2FyeQp7Cglib3JkZXItYm90dG9tOiAxcHggZGFzaGVkICM2NjY2NjY7CgljdXJzb3I6IGhlbHA7Cn0KCi8vLS0+'
	),
	'shade_top.png'		=> array(
		'type'			=> 'image/png',
		'data'			=> 'iVBORw0KGgoAAAANSUhEUgAAAsEAAAAiCAIAAAAxnJMmAAAAA3NCSVQICAjb4U/gAAAACXBIWXMAAAsSAAALEgHS3X78AAAAGHRFWHRDcmVhdGlvbiBUaW1lADI3LjA2LjIwMDeQvzzuAAAAH3RFWHRTb2Z0d2FyZQBNYWNyb21lZGlhIEZpcmV3b3JrcyA4tWjSeAAAAqZJREFUeJzt3UuOozAUQFFSqrVm/zugByUh4s/zsyGJWjpn0KITRDHzlW3I4/l8bgAAk36+fQMAwH9JQwAAKzQEALBCQwAAKzQEALBCQwAAKzQEALBCQwAAK36vX2Lf9+sXAQA+6fF4XLzCekPMpoPUAIAPSMbBMS4vx8RKQ2RqQDEAwFfUQ3BcCX/nL5TEdEMEcWBmAgA+LDP2nwfc3vn7vs9mxERDrNWDUACA9wnG2WYTBLMOsxMS2Ybo3WLz82Q3yAsAuCgY8oPph7gkkhmRaohkKFzpCQBgQXIeormDslcSyYwYN0SmFYpP4m5QFQBwu2YK1N/W3dAsiUxGDBpiKheSMxMAwO2aY26RDscnzZKYzYioIeImCOpBTADAF/XSYXuth6IkZjOi2xALAZFc0dATAHC75u6H7TUXzv9tpsNURqw8l1FHw9ScRHBlACCp+ahFfUJvFaOYkAgyoqfdEJlND/mSUAkAcLt4eD0qYWutXByhUB9sVUb0qqLREJkXRv0d3DshAQBcV08/1MfFQZ0RheZXE892xgEhIwDg687FsIUBce6GeFqip2yI3qifCYhMRjQ/AQAuKqYftlFAzGZEXRVzv7k1DIhkRvSuDAAMBb92cT5nGBC9jEhKvR+iOcD3AmL2UU8AYMpwN+X5nDggmt2QXNF4aYj4nuJoyOyyHP4JAGBZ/CqIXkAMk+JQfPsTnFccFF8F/xYHx7GAAID3KUbbekQeTgGcL1Uc1ObeMTXcCWE5AwC+aGEhY3lXxOAdU8MwWVjO6F0QAMiLd1ZOLWSczzxfKt4VkZqHiOPAPAQAfF5zVK1/WyuzoXJtKqK7HyK+3WFGFAc2QwDAB9RbIs4HV9YNauNnO4e7LeqTgxtSEgDwJs1XQsVzDL0ljMyExD9uKvuju4r2egAAAABJRU5ErkJggg=='
	),
	'shade_bottom.png'	=> array(
		'type'			=> 'image/png',
		'data'			=> 'iVBORw0KGgoAAAANSUhEUgAAAsEAAAAiCAIAAAAxnJMmAAAAA3NCSVQICAjb4U/gAAAACXBIWXMAAAsSAAALEgHS3X78AAAAGHRFWHRDcmVhdGlvbiBUaW1lADI3LjA2LjIwMDeQvzzuAAAAH3RFWHRTb2Z0d2FyZQBNYWNyb21lZGlhIEZpcmV3b3JrcyA4tWjSeAAAAtVJREFUeJzt3U2OozAQBlDTmrPm/jfILKKOaP+Uyw7JTEvvLZAJBFA2/lI2cNxut/Ltfr+fG8HyofqkPcK50V0FAK5yHEd3tWocx/FsPFerT7rL6hR/4ku53+/nZbu1u/N5a7tb/rcAAPYEAaK7W/VJd+fK14UXNIoq54ADALxP1eeu9tdL54rqEOcLCmoM3WUp5dwILs7oBgBsiLv8V2JEMkz0M8RoJKLdNFpWez6+240L6hMAcJV4SkRyokP3u61UHaIMShHJOkT5Tg/iAgB8wLnDXY0R+c56OB9iGkyCZdV4tmUIAHifdjJE2zUHy+pQVaP1ow7Rjly0WzPL86GUIgDgk/aGMzJFiGrr/N7OMsgW1w5kmFkJAEmZP+RLwxmj78Ynys6HeB5rOiuiNAGiugIzKwHgcsF4RAljRLuaVGeIquTQHZsIYkQJA4ThDAB4q9FARhknhu5qfKiHeR0iHyPKLEBIDwDwbvkYUdIBoquTIYKZlXFuGAUI5QcA+LBudChhXAh66u6myTOm2tVRWeJ5gvZ2DLMpAeBy0z/nGwWJ6RfP1p4xVbXbOkT5mR66J24ffJm8BgBgZNSfxrd6xu3YMEO0IxrdGFGaSFF66cGrOwHgY+J7NMpKgAh67dTzIbqftLWH9t1a8du2AIC3St7wmdm5NRnLiGNE6SWJMosOJkAAwOXi/n4jLkz//y/c21kddJQkyngUI3lNAMDruh1uMits3tvZPdAoCnQnOiRnPyhIAMCLkv/Ml4JC8pg792W0pwlutYhf4pU8OwCwavV5D9NNlYX3ZXTjQnJTlzoEALxo9Q/5JenhYe2dW2X2FMtnO5MP1CEA4AMyHe5Gp7ycIUpYdQguRdUBAD7gwspEbCdDVKdMhgNVBwD4f7zeL+9niAsvAgD4db7+9QUAAL+SDAEA7JAhAIAdMgQAsEOGAAB2yBAAwA4ZAgDYIUMAADv+AigV0hEqLvqgAAAAAElFTkSuQmCC'
	),
	'shade_left.png'	=> array(
		'type'			=> 'image/png',
		'data'			=> 'iVBORw0KGgoAAAANSUhEUgAAAB0AAAACCAIAAAArcUAWAAAAA3NCSVQICAjb4U/gAAAACXBIWXMAAAsSAAALEgHS3X78AAAAGHRFWHRDcmVhdGlvbiBUaW1lADI3LjA2LjIwMDeQvzzuAAAAH3RFWHRTb2Z0d2FyZQBNYWNyb21lZGlhIEZpcmV3b3JrcyA4tWjSeAAAACJJREFUCJljTEtLY4CB////////H85GFkFjYBVHJpkYaAMAW9QxBmuj9kEAAAAASUVORK5CYII='
	),
	'shade_right.png'	=> array(
		'type'			=> 'image/png',
		'data'			=> 'iVBORw0KGgoAAAANSUhEUgAAAB0AAAACCAIAAAArcUAWAAAAA3NCSVQICAjb4U/gAAAACXBIWXMAAAsSAAALEgHS3X78AAAAGHRFWHRDcmVhdGlvbiBUaW1lADI3LjA2LjIwMDeQvzzuAAAAH3RFWHRTb2Z0d2FyZQBNYWNyb21lZGlhIEZpcmV3b3JrcyA4tWjSeAAAAClJREFUCJljDAsLY2RkZGBgYGBgYGRkhLDhJBwgc5EVoClmgAEmBtoAAMqPATaK0yPBAAAAAElFTkSuQmCC'
	),
	'gradient.png'		=> array(
		'type'			=> 'image/png',
		'data'			=> 'iVBORw0KGgoAAAANSUhEUgAAAkQAAAAcCAIAAAAPwDe+AAAAA3NCSVQICAjb4U/gAAAACXBIWXMAAAsSAAALEgHS3X78AAAAGHRFWHRDcmVhdGlvbiBUaW1lADI3LjA2LjIwMDeQvzzuAAAAH3RFWHRTb2Z0d2FyZQBNYWNyb21lZGlhIEZpcmV3b3JrcyA4tWjSeAAAALBJREFUeJzt1TEOg0AMAMGQ///ZPCAEpeAUFs2UPiTjareZeT3d4Y3fDv/94xVDu87n1z59DtdNFg3vsOtkfvnT34+16567ZuZ9+AcAECJmAOSJGQB5YgZAnpgBkCdmAOSJGQB5YgZAnpgBkCdmAOSJGQB5YgZAnpgBkCdmAOSJGQB5YgZAnpgBkCdmAOSJGQB5YgZAnpgBkCdmAOSJGQB5YgZAnpgBkCdmAOSJGQB5O1oj1GPrRiO/AAAAAElFTkSuQmCC'
	),
	'head.png'			=> array(
		'type'			=> 'image/png',
		'data'			=> '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'
	),
	'ok.png'			=> array(
		'type'			=> 'image/png',
		'data'			=> 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAANcSURBVHjaTFNbbFRFGP7m3HfZW8923bYqpEW2BmwF2qRGLUrUVPQBjSSNTxiNoBENkQcT45s+CCEYeaF98cHExAStiaAmRg20fWgI3dKmXlpqS+mVbtu9dHfPnnPmzDh7VtCZZObMP+f7/ts3RDr1KKpDJsA2GdBlaO0R52RYd1/0XCtJGVBmxtrtovFDxlM+tzkqrrCB+zCQ/xO0GPT1febGmVwxH58nDM59kv+jt8Sg5sW3Et3MyPEPC1zpv0ug4N+xJ2ifbY4svz8qK6T90AGcTPSgU3sSHijGnGu4uvUrhn4eMeXphT410JRyZf1UzfHjDWjW6Wspc+nT3xt10tHdhaejz6JN2Y8G+X7IYqpEQ0w1Ub8rDElTsTg785gnh5dE/GmpStIa3zg7EXKJ0gYw4bPEt5DjOWTYqr/b3MbK6hIa/m7BwbpnUN+pEuZsnBZYTdqu0LfyyMeLeyksbmHdy2CRzmOWTmOGTmGezuLGchp1Wwl80PMRyqSIcLOBQKhsgjpvy82Hoh8XovmHnBYqvLt+bZhYHeG1xAqYXZ3BTimF490nasWSGX4sDIC5FuxsUJU4s5P6jhCqU7cCKFWKwvtNTLrjSLvX4VHgaMebPnZqagoXvuwTbQFCjToUr5JQbHGISxGod1SUvQLajS6k7Fb0hz5Dcm47+g5+BUlUanBoEO/deAOVlzdhkACkogim2sY81e8Yt8sipxA+2fcFnkv1wGMeLn//Ex7R2/BgdAdGro/g6M1XYR1YRoSYCJIQsis5uETNyJWOxmgps/JCcq+JXy7/hrpsPVpTrdiZ2IVje45jYW4Rh4cO41bngi+2iBRETDKxcCWLLRo7L+OJplG7RE8oRiVoPb+Gr6e/hZLW0NvVC8/xcOTiEUx2TgrNinhFKhERvjMpY37M3UQk1lsVEodiLOem118iTRbJPWxjMDcI7ZqGS39ewsDugVqyAiyJfdtaAHPfFDmvb3hHGNK+EkWVJqAFUR4tPwVQwnZ7GN4cxnhsDDTs+WCfZEJC9iLnMB84B00/c0/Ktf4qV0kwcovP8G784QUZp4JK9HBd3I0LhmEN/K+6DSnZ9K4An777hu69xhqJryIZrn2MOfYrsJyEbzfUdUk1voOu94tauGD/Qf4RYADWXF8vjJnROgAAAABJRU5ErkJggg=='
	),
	'error.png'			=> array(
		'type'			=> 'image/png',
		'data'			=> 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAMISURBVHjaZJPdaxxVGIefPfOx2dmkGJpsTEuzm9xFvYgKsXgjVPzACooKXviFXhRSvPDG/gdFEHrZoHhVkHghgsGKovRCEfohYj+MUlLTTdM2ye4m2Z3Z3ZmTmZ3xndm0FDzMcIY55/e8v/e878npD+iPnDyGzCZG1GFOu7y863EoiTBVwp1CzKIRMB81CJM6hO092YOAOOL1WpXTayuM+Z7CcfajDIOut0OCZsyhdtDmI6PBV/cA5p6csMPHS3/yiVcfNCqPzBCXJ9gyZTmnGO50GKnVWL5ypXRrq/nl44opG05mcf13JHLIi5cv813kjpmzr7zGdrnMPzdXePazzzP4uePHmT5wgNHNTf5YXOTuxlpvNuRVBWdVouH2Lea364Pm4SPPMTAywvbODmaS3DNHMpDHl9kSyOzRo+SLDxlLcDqUfypyefd6lcnpqWns1HIQMJofQPvBfUCkDIpZniHKsnlyZoYqTLRgznRrHAvkQMpTU5JLDJKvnc5BHxDLdy/wsf0B6EVCCylVKgxZivUwfl91XCpDZgFzcFBORErR7UKzSVTfzAC+75Nrt2nXaxk8DaJsm9F9+3AlrinViZCApADPSxUM93oMmXYW3XVdCr34vhgRk6aaiSQ7O+Baq+MTRlHfgeSJRBwX4dryMqurqxQl+sTuLqSv7IlzMZttF0H9pQpbnAxCkpWrV/vkFCBOnE6baydOUD11iorXprG+3l8TwJ3rS9zVJEPSCyqMuVCB3365dIluo9HfJGlsjI9z5MwZ3lhYYG2ywkgKF5e7XpMfz1+kBBek839VYoqD8J6htfvtuZ/ptlpZBYrPv4DpOARa03z0sQwcdFt888P3NFzfOwRvp52Su7FX6518/qXftf46sZTzTGk/hlPk37kPCQoOpS/mKcl9+Ol2jbYfdp/I59/Ma32WBwFeoYC4KVejaOFGGD6ds8Ax+ze0k2YlHTtpWefLSr0lLXwTcfY/QNqa2rKksjy1rfWnG3F8OO3WYaX+ftiyjonwokrPKB17gP8EGADKf21ggiXwiwAAAABJRU5ErkJggg=='
	),
	'warning.png'		=> array(
		'type'			=> 'image/png',
		'data'			=> 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAJSSURBVHjapJPLaxNRFMa/yWQymcmTvNoaKbE1NRJ8Ioa6UBfJQmurImghFERoaxG6KogP7KJZuelKxE113Y3u3PhvBHTjuyHqJCmhmcxkJjOeO9RAsBGhB86FOcz5zXfufIezbRv7CTc78vl8X9GyLDCwYRg3y+VyodlszrPaXuFiRzgc7mUwGIQsy+B5HqahPhg94J6jV7KDFDgAj8fjpCAI4DgOuq5DVdXCoaR68m4xKRK49E9AIBBw0ufzOSAWmqauXTwThNcfRSaduETwsYGAUCjkJJPOghTcL+TcuaZ7GjPFTeTPJ8VMZuK1i2KgAtbM5GuaJhi68rB0L4parQ5RFCH5R3Fjavx4IpGY3xPg9XrB4NQMRVEeT2a7QZ7jwbs4tNtt7LQFFK/FkU6Pr/zp6QPsfhmtVmvI6tRXVucCdLMSeLfbqfslA2MHvZi9Mnw4lUqV/gJQI8mtoVLZerF01ZTlSIgAAegdDa82nkPvRgG7i1uXBSST8SX61eE+I9XrdVSr1dlU5NP09QsizeSBzXVxZ6YBGypGYjosm0NQ3sHasiu88Cjylsw12VNAcwcayvtnq8Wmq0v9JqfhyzcFG29C2HyXwNOXEqzOLzJWDWez2zh3qpOTJOl2D0Dy16dy1Yg/atLcGmyjDh41srOFhcVlyGILXeMnLFOBbW7jyWKDi8esdTYBxzx/4tiR6nCkMiQILufibIsH27HP3w0oDeB0VqCaQePoVDdpVwx8+Aj76xYmHEAsFhshFUf/Y/nY6uqUzd3nH9x+1/m3AAMA+N7sajU3ZucAAAAASUVORK5CYII='
	)
);

// output embedded file
if(isset($_REQUEST['file']) && isset($embeddedFiles[$_REQUEST['file']]))
{
	$data = base64_decode($embeddedFiles[$_REQUEST['file']]['data']);
	
	header('Content-Type: ' 	. $embeddedFiles[$_REQUEST['file']]['type']);
	header('Content-Length: ' 	. strlen($data));
	echo($data);
	
	exit();
}

// status variables
$testOK 	= true;
$gdError 	= false;
$smimeError	= false;
$phpBelow54 = false;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>b1gMail Server-Test</title>
	<link type="text/css" href="servertest.php?file=style.css" rel="stylesheet" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>
<center>

	<br />
	<img src="servertest.php?file=shade_top.png" border="0" alt="" />
	<table cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
		<tr>
			<td id="leftshade"></td>
			<td id="main">
				<div id="header">&nbsp;</div>
				<div id="gradient"><i>Server-Test</i>&nbsp;&nbsp;</div>

				<div id="content">

					<h1>Server-Test</h1>
					
					Ihr System wurde auf Kompatibilit&auml;t mit b1gMail &uuml;berpr&uuml;ft.
					Im Folgenden finden Sie das Ergebnis der &Uuml;berpr&uuml;fung.
					
					<br /><br />
					<table class="list">
						<tr>
							<td width="180">&nbsp;</td>
							<th>Ben&ouml;tigt</th>
							<th>Verf&uuml;gbar</th>
							<th width="60">&nbsp;</th>
						</tr>
						<tr>
							<th>PHP-Version</th>
							<td>5.4,7.4 empfohlen</td>
							<td><?php echo(phpversion()); ?></td>
							<td><img src="servertest.php?file=<?php if(PHPNumVersion() >= 540) { echo 'ok'; } else { echo 'error'; $testOK = false; } ?>.png" border="0" alt="" width="16" height="16" /></td>
						</tr>
						<tr>
							<th>MySQL-Unterst&uuml;tzung</th>
							<td>Ja</td>
							<td><?php echo(function_exists('mysqli_connect') ? 'Ja' : 'Nein'); ?></td>
							<td><img src="servertest.php?file=<?php if(function_exists('mysql_connect') || function_exists('mysqli_connect')) echo 'ok'; else { echo 'error'; $testOK = false; } ?>.png" border="0" alt="" width="16" height="16" /></td>
						</tr>
						<tr>
							<th>GD-Bibliothek</th>
							<td>Ja / Nein</td>
							<td><?php echo(function_exists('imagecreate') ? 'Ja' : 'Nein'); ?></td>
							<td><img src="servertest.php?file=<?php if(function_exists('imagecreate')) echo 'ok'; else { echo 'warning'; $gdError = true; } ?>.png" border="0" alt="" width="16" height="16" /></td>
						</tr>
						<tr>
							<th>OpenSSL-Bibliothek</th>
							<td>Ja / Nein</td>
							<td><?php echo(function_exists('openssl_pkcs7_verify') ? 'Ja' : 'Nein'); ?></td>
							<td><img src="servertest.php?file=<?php if(function_exists('openssl_pkcs7_verify')) echo 'ok'; else { echo 'warning'; $smimeError = true; } ?>.png" border="0" alt="" width="16" height="16" /></td>
						</tr>
						<tr>
							<th>Server-Interface</th>
							<td>Modul / CGI</td>
							<td><?php echo(function_exists('apache_get_version') ? 'Modul' : 'CGI'); ?></td>
							<td><img src="servertest.php?file=ok.png" border="0" alt="" width="16" height="16" /></td>
						</tr>
					</table>
					
					<br />
					
					<h1>Ergebnis</h1>
					<ul>
						<li>b1gMail ist auf diesem Server<br />
							<strong><img src="servertest.php?file=<?php echo($testOK ? 'ok' : 'error'); ?>.png" border="0" alt="" width="16" height="16" align="absmiddle" />
							<?php echo($testOK
									? ($gdError ? 'mit Ausnahme der grafischen Statistiken und Sicherheits-Code-Grafiken'
												: 'voll')
										. ' funktionsf&auml;hig'
									: 'nicht oder nur eingeschr&auml;nkt funktionsf&auml;hig'); ?></strong>
							<br /><br /></li>
						<?php if($testOK) { ?><li>Die optionalen Funktionen
							<a title="Schnittstellen zum Zugriff auf die virtuelle Festplatte (Webdisk) aus dem Explorer bzw. Finder sowie Synchronisierung von Adressbuch und Kalender" class="glossary">WebDAV/CalDAV/CardDAV</a><br />
							<strong><img src="servertest.php?file=<?php echo(function_exists('apache_get_version') && !$phpBelow54 ? 'ok' : 'warning'); ?>.png" border="0" alt="" width="16" height="16" align="absmiddle" />
							<?php echo(function_exists('apache_get_version') && !$phpBelow54
									? 'stehen zur Verf&uuml;gung'
									: 'stehen nicht zur Verf&uuml;gung'); ?></strong>
							<br /><br /></li>
						<li>Die optionalen <a title="Zur Verschl&uuml;sselung/Entschl&uuml;sselung/Signierung von E-Mails" class="glossary">S/MIME</a>-Funktionen<br />
							<strong><img src="servertest.php?file=<?php echo(!$smimeError ? 'ok' : 'warning'); ?>.png" border="0" alt="" width="16" height="16" align="absmiddle" />
							stehen <?php echo(!$smimeError
									? 'zur Verf&uuml;gung'
									: 'nicht zur Verf&uuml;gung (ben&ouml;tigen PHP ab Version 5.2.0 mit OpenSSL-Erweiterung)'); ?></strong>
							<br /></li><?php } ?>
					</ul>
					
					<small>
						<b>Hinweis:</b> Dieses Server-Test-Script kann nicht &uuml;berpr&uuml;fen, ob Sie &uuml;ber
										gen&uuml;gend freien Webspace, ein POP3-CatchAll-Postfach und eine MySQL-Datenbank
										verf&uuml;gen. Dies k&ouml;nnen Sie bei Ihrem Provider in Erfahrung bringen.
										Beachten Sie bitte die offiziellen
										<a href="https://www.b1gmail.com/de/faq/#faq-10" target="_blank">System-Anforderungen</a>.
					</small>
					
				</div>
			</td>
			<td id="rightshade"></td>
		</tr>

	</table>
	<img src="servertest.php?file=shade_bottom.png" border="0" alt="" />

</center>
</body>
</html>
