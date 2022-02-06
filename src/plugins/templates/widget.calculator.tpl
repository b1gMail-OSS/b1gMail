<div class="innerWidget"><div style="padding:3px;">
	<div style="width:98%;text-align:center;">
		<div style="border:1px solid #999;background-color:#EFEFEF;border-radius:8px;padding:4px;">
			<input type="text" value="0" id="calcDisplay" readonly="readonly" style="font-size:20px;text-align:right;color:#333;width:100%;border:0px;box-shadow:none;background:none;" />
		</div>
	</div>
	
	<table style="width:98%;border-collapse:collapse;margin-top:4px;text-align:center;">
		<tr>
			<td width="25%"><button style="padding:6px;width:95%;height:31px;" onclick="calcButton('C')">C</button></td>
			<td width="25%"><button style="padding:6px;width:95%;height:31px;" onclick="calcButton('+-')">&plusmn;</button></td>
			<td width="25%"><button style="padding:6px;width:95%;height:31px;" onclick="calcButton('/')" id="calcOp/">&divide;</button></td>
			<td width="25%"><button style="padding:6px;width:95%;height:31px;" onclick="calcButton('*')" id="calcOp*">x</button></td>
		</tr>
		<tr>
			<td><button style="padding:6px;width:95%;height:31px;" onclick="calcButton('7')">7</button></td>
			<td><button style="padding:6px;width:95%;height:31px;" onclick="calcButton('8')">8</button></td>
			<td><button style="padding:6px;width:95%;height:31px;" onclick="calcButton('9')">9</button></td>
			<td><button style="padding:6px;width:95%;height:31px;" onclick="calcButton('-')" id="calcOp-">-</button></td>
		</tr>
		<tr>
			<td><button style="padding:6px;width:95%;height:31px;" onclick="calcButton('4')">4</button></td>
			<td><button style="padding:6px;width:95%;height:31px;" onclick="calcButton('5')">5</button></td>
			<td><button style="padding:6px;width:95%;height:31px;" onclick="calcButton('6')">6</button></td>
			<td><button style="padding:6px;width:95%;height:31px;" onclick="calcButton('+')" id="calcOp+">+</button></td>
		</tr>
		<tr>
			<td><button style="padding:6px;width:95%;height:31px;" onclick="calcButton('1')">1</button></td>
			<td><button style="padding:6px;width:95%;height:31px;" onclick="calcButton('2')">2</button></td>
			<td><button style="padding:6px;width:95%;height:31px;" onclick="calcButton('3')">3</button></td>
			<td rowspan="2"><button style="padding:6px;width:95%;height:68px;" onclick="calcButton('=')">=</button></td>
		</tr>
		<tr>
			<td colspan="2"><button style="padding:6px;width:95%;height:31px;" onclick="calcButton('0')">0</button></td>
			<td><button style="padding:6px;width:95%;height:31px;" onclick="calcButton('.')">,</button></td>
		</tr>
	</table>
</div></div>

{literal}<script>
<!--
	
	var _calcOperator = '', _calcClearNext = false, _calcLastNum = null;
	
	function calcButton(c)
	{
		var display = EBID('calcDisplay'), floatVal = 0;
		
		if(display.value.substr(-1) == '.')
			floatVal = parseFloat(display.value.substr(0, display.value.length-1));
		else
			floatVal = parseFloat(display.value);
		
		switch(c)
		{
		case '0':
		case '1':
		case '2':
		case '3':
		case '4':
		case '5':
		case '6':
		case '7':
		case '8':
		case '9':
			if(_calcClearNext)
			{
				_calcLastNum = floatVal;
				_calcClearNext = false;
				display.value = '';
			}
			
			if(display.value.length < 12)
			{
				var val = display.value;
				
				if(val == '0')
				{
					val = c;
				}
				else
				{
					val += c;
				}
				
				display.value = val;
			}
			break;
			
		case 'C':
			display.value = '0';
			_calcOperator = '';
			_calcClearNext = false;
			_calcLastNum = null;
			EBID('calcOp+').style.border = '';
			EBID('calcOp-').style.border = '';
			EBID('calcOp*').style.border = '';
			EBID('calcOp/').style.border = '';
			break;
			
		case '+-':
			if(display.value[0] == '-')
				display.value = display.value.substr(1);
			else
				display.value = '-' + display.value;
			break;
		
		case '.':
			if(display.value.indexOf('.') == -1)
				display.value += '.';
			break;

		case '=':
		case '+':
		case '-':
		case '*':
		case '/':
			var result = floatVal;
		
			if(_calcOperator != '' && typeof(_calcLastNum) != 'object')
			{
				switch(_calcOperator)
				{
				case '+':
					result = _calcLastNum + floatVal;
					break;
					
				case '-':
					result = _calcLastNum - floatVal;
					break;
					
				case '*':
					result = _calcLastNum * floatVal;
					break;
					
				case '/':
					result = _calcLastNum / floatVal;
					break;
				}
			}
		
			display.value = result;
			_calcClearNext = true;

			EBID('calcOp+').style.border = '';
			EBID('calcOp-').style.border = '';
			EBID('calcOp*').style.border = '';
			EBID('calcOp/').style.border = '';
			
			if(c == '=')
			{
				_calcLastNum = null;
				_calcOperator = '';
			}
			else
			{
				_calcOperator = c;
				EBID('calcOp'+c).style.border = '1px solid #FFCC00';
			}
			
			break;
		}
	}

//-->
</script>{/literal}
