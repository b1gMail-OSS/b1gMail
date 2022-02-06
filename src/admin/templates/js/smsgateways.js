function insGate(titel, get, ok, get74)
{
	var v74OrLater = (window.self == window.top);
	document.getElementById('titel').value = titel;
	document.getElementById('getstring').value = v74OrLater && get74.length > 0 ? get74 : get;
	document.getElementById('success').value = ok;
}

document.write('<ul>');
document.write(' <li><a href="#" onclick="insGate(\'Yomega.info\',\'http://gateway.yomega.info/textsms2.py?user=%%user%%&pass=%%passwort%%&from=%%from%%&to=%%to%%&text=%%msg%%&what=textsms\',\'5002\',\'\');">Yomega.info</a></li>');
document.write(' <li><a href="#" onclick="insGate(\'SMSkaufen.de\',\'http://gateway.smskaufen.de/?id=%%user%%&pw=%%passwort%%&type=%%typ%%&empfaenger=%%to%%&absender=%%from%%&text=%%msg%%&reply_email=%%usermail%%&reply=1\',\'100\',\'\');alert(\'Wichtig: Nach dem Anlegen des Gateways muss mindestens ein SMS-Typ eingereichtet werden (unter Typen). Als Typ muss dabei eine SMS-Typ-Nummer von SMSkaufen angegeben werden (z.B. 2).\');">SMSkaufen.de</a></li>');
document.write(' <li><a href="#" onclick="insGate(\'smstrade.de\',\'http://gateway.smstrade.de/?key=%%passwort%%&message=%%msg%%&to=%%to%%&from=%%from%%&route=%%typ%%\',\'100\',\'\');alert(\'Wichtig: Geben Sie Ihren smstrade.de-Schnittstellen-Key bitte als Passwort ein und lassen Sie das Benutzer-Feld leer. Nach dem Anlegen des Gateways muss mindestens ein SMS-Typ eingerichtet werden (unter Typen). Als Typ kann dabei einer der folgenden Werte verwendet werden: basic, economy, gold, direct (entsprechen den Routen bei smstrade.de).\');">smstrade.de</a></li>');
document.write(' <li><a href="#" onclick="insGate(\'CM Telecom\',\'https://sgw01.cm.nl/gateway.ashx?producttoken=%%passwort%%&body=%%msg%%&to=%%to%%&from=%%from%%&reference=\',\'\',\'https://sgw01.cm.nl/gateway.ashx?producttoken=%%passwort%%&body=%%msg_utf8%%&to=%%to%%&from=%%from%%&reference=\');alert(\'Bitte geben Sie Ihren Product Token im Feld Passwort ein. Benutzer sowie Rückgabe-Wert können frei gelassen werden. Es muss weiterhin mindestens ein SMS-Typ angelegt werden. Das Typ-Feld kann dabei frei gelassen werden.\');">CM Telecom</a></li>');
document.write('</ul>');
