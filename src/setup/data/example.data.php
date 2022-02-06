<?php
/*
 * b1gMail
 * Copyright (c) 2021 Patrick Schlangen et al
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 */

$exampleData = array();
$exampleData[] = 'INSERT INTO bm60_faq(`id`,`typ`,`required`,`frage`,`antwort`,`lang`) VALUES(\'1\',\'nli\',\'\',\'Was hat es mit dem Captcha-Code auf sich?\',\'Mit dem Captcha-Code stellen wir sicher, dass keine Massen-Registrierungen durchgeführt werden, z.B. durch speziell dazu entwickelten Programmen. Die jeweiligen Porgramme können den Captcha-Code, der durch Bilder angezeigt wird, nicht einlesen und somit keine Registrierungen durchführen. So schützen wir unseren Dienst vor Spam-Anmeldungen.\',\'deutsch\')';
$exampleData[] = 'INSERT INTO bm60_faq(`id`,`typ`,`required`,`frage`,`antwort`,`lang`) VALUES(\'2\',\'nli\',\'\',\'Das Land in dem ich lebe ist nicht in der Liste aufgef&uuml;hrt. Was soll ich tun?\',\'F&uuml;r diesen Zweck haben wir einen Extra-Eintrag hinzugef&uuml;gt: \"Anderes Land\". W&auml;hlen Sie bitte diesen Eintrag, wenn ihr Land nicht aufgef&uuml;hrt sein sollte.\',\':all:\')';
$exampleData[] = 'INSERT INTO bm60_faq(`id`,`typ`,`required`,`frage`,`antwort`,`lang`) VALUES(\'3\',\'nli\',\'\',\'Ich habe mein Passwort vergessen, was soll ich tun?\',\'Klicken Sie dazu bitte im Men&uuml; auf Passwort. Geben Sie dort bitte die E-Mail - Adresse ein, die Sie bei und registriert haben. Nach einem Klick auf \"Okay\" wird Ihnen das Passwort an die E-Mail - Adresse gesandt, die Sie als Alternativ - Adresse angegeben haben. Sollten Sie keine Alternativ - Adresse angegeben haben, k&ouml;nnen wir Ihnen leider das Passwort nicht automatisch zusenden lassen. Kontaktieren Sie uns bitte direkt, wir helfen Ihnen dann gerne weiter.\',\':all:\')';
$exampleData[] = 'INSERT INTO bm60_faq(`id`,`typ`,`required`,`frage`,`antwort`,`lang`) VALUES(\'4\',\'nli\',\'\',\'Wo finde ich Kontaktinformationen zu Ihnen?\',\'Unter dem Men&uuml;punkt \"Impressum\" finden Sie Kontaktdaten des Verantwortlichen f&uuml;r diesen Dienst.\',\':all:\')';
$exampleData[] = 'INSERT INTO bm60_faq(`id`,`typ`,`required`,`frage`,`antwort`,`lang`) VALUES(\'5\',\'nli\',\'\',\'Ich stimme Ihren AGB nicht zu, kann ich mich trotzdem anmelden?\',\'Eine Registrierung bei uns mit der Zustimmung und Einhaltung unserer Allgemeinen Gesch&auml;ftsbedingungen (AGB) verbunden. Ein Verstoß gegen die Bedingungen kann eine sofortige, fristlose L&ouml;schung Ihres Accounts zur Folge haben. Wenn Sie unseren AGB nicht zustimmen, k&ouml;nnen und d&uuml;rfen Sie sich bei uns leider nicht registrieren.\',\':all:\')';
$exampleData[] = 'INSERT INTO bm60_faq(`id`,`typ`,`required`,`frage`,`antwort`,`lang`) VALUES(\'6\',\'nli\',\'\',\'Was bedeutet die Option \"Merken\" beim Login?\',\'Wenn Sie \"Merken\" aktivieren, werden Ihre Login-Daten gespeichert und beim n&auml;chsten Besuch unseres Dienstes automatisch eingef&uuml;gt. Somit k&ouml;nnen Sie sich nur durch einen Klick auf \"Login\" einloggen, andere Daten werden wie gesagt automatisch eingef&uuml;gt.\',\':all:\')';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'1\',\'Ägypten\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'2\',\'Albanien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'3\',\'Algerien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'4\',\'Andorra\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'5\',\'Angola\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'6\',\'Argentinien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'7\',\'Armenien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'8\',\'Aserbaidschan\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'9\',\'Äthiopien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'10\',\'Australien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'11\',\'Azoren\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'12\',\'Bahrein\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'13\',\'Bangladesch\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'14\',\'Belgien\',\'yes\',21)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'15\',\'Bolivien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'16\',\'Botswana\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'17\',\'Brasilien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'18\',\'Brunei\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'19\',\'Bulgarien\',\'yes\',20)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'20\',\'Burkina Faso\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'21\',\'Chile\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'22\',\'China\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'23\',\'Costa Rica\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'24\',\'Dänemark\',\'yes\',25)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'25\',\'Deutschland\',\'yes\',19)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'26\',\'Dominikanische Republik\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'27\',\'Ecuador\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'28\',\'El Salvador\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'29\',\'Estland\',\'yes\',20)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'30\',\'Fidschi\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'31\',\'Finnland\',\'yes\',24)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'32\',\'Frankreich\',\'yes\',20)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'33\',\'Französisch Polynesien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'34\',\'Gabun\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'35\',\'Georgien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'36\',\'Griechenland\',\'yes\',23)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'37\',\'Gro&szlig;britannien\',\'yes\',20)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'38\',\'Guatemala\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'39\',\'Honduras\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'40\',\'Hongkong\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'41\',\'Indien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'42\',\'Indonesien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'43\',\'Iran\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'44\',\'Irland\',\'yes\',23)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'45\',\'Island\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'46\',\'Israel\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'47\',\'Italien\',\'yes\',22)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'48\',\'Japan\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'49\',\'Jemen\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'50\',\'Jordanien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'51\',\'Jugoslawien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'52\',\'Kambodscha\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'53\',\'Kamerun\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'54\',\'Kanada\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'55\',\'Kanarische Inseln\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'56\',\'Kasachstan\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'57\',\'Kenia\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'58\',\'Kirgisien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'59\',\'Kolumbien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'60\',\'Kroatien\',\'yes\',25)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'61\',\'Kuba\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'62\',\'Kuwait\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'63\',\'Lesotho\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'64\',\'Lettland\',\'yes\',21)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'65\',\'Libanon\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'66\',\'Libyen\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'67\',\'Liechtenstein\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'68\',\'Litauen\',\'yes\',21)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'69\',\'Luxemburg\',\'yes\',17)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'70\',\'Madagaskar\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'71\',\'Makao\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'72\',\'Malawi\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'73\',\'Malaysia\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'74\',\'Mali\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'75\',\'Malta\',\'yes\',18)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'76\',\'Marokko\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'77\',\'Mauritius\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'78\',\'Mexico\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'79\',\'Mikronesien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'80\',\'Moldawien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'81\',\'Mongolei\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'82\',\'Namibia\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'83\',\'Neuseeland\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'84\',\'Nicaragua\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'85\',\'Niederlande\',\'yes\',21)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'86\',\'Nordkorea\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'87\',\'Norwegen\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'88\',\'Oman\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'89\',\'Österreich\',\'yes\',20)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'90\',\'Pakistan\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'91\',\'Panama\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'92\',\'Paraguay\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'93\',\'Peru\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'94\',\'Philippinen\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'95\',\'Polen\',\'yes\',23)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'96\',\'Portugal\',\'yes\',23)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'97\',\'Puerto Rico\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'98\',\'Qatar\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'99\',\'R&eacute;union\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'100\',\'Rumänien\',\'yes\',24)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'101\',\'Russland\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'102\',\'Sambia\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'103\',\'Saudi-Arabien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'104\',\'Schweden\',\'yes\',25)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'105\',\'Schweiz\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'106\',\'Senegal\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'107\',\'Serbien\',\'yes\',25)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'108\',\'Simbabwe\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'109\',\'Singapur\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'110\',\'Slowakische Republik\',\'yes\',20)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'111\',\'Slowenien\',\'yes\',22)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'112\',\'Spanien\',\'yes\',21)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'113\',\'Sri Lanka\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'114\',\'Südafrika\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'115\',\'Sudan\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'116\',\'Südkorea\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'117\',\'Eswatini\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'118\',\'Syrien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'119\',\'Tadschikistan\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'120\',\'Taiwan\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'121\',\'Tansania\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'122\',\'Thailand\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'123\',\'Togo\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'124\',\'Trinidad & Tobago\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'125\',\'Tschechische Republik\',\'yes\',21)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'126\',\'Tunesien\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'127\',\'Türkei\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'128\',\'Turkmenistan\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'129\',\'Uganda\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'130\',\'Ukraine\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'131\',\'Ungarn\',\'yes\',27)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'132\',\'Uruguay\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'133\',\'USA\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'134\',\'Usbekistan\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'135\',\'Venezuela\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'136\',\'Vereinigte Arabische Emirate\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'137\',\'Vietnam\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'145\',\'Anderes Land\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'144\',\'Zypern\',\'yes\',19)';
$exampleData[] = 'INSERT INTO bm60_staaten(`id`,`land`,`is_eu`,`vat`) VALUES(\'143\',\'Wei&szlig;russland\',\'no\',0)';
$exampleData[] = 'INSERT INTO bm60_mods(`modname`,`installed`,`pos`,`packageName`,`signature`,`files`) VALUES(\'B1GMailSearchProvider\',\'1\',\'0\',\'\',\'\',\'\')';
$exampleData[] = 'INSERT INTO bm60_mods(`modname`,`installed`,`pos`,`packageName`,`signature`,`files`) VALUES(\'BMPlugin_Widget_Notes\',\'1\',\'0\',\'\',\'\',\'\')';
$exampleData[] = 'INSERT INTO bm60_mods(`modname`,`installed`,`pos`,`packageName`,`signature`,`files`) VALUES(\'BMPlugin_Widget_WebdiskDND\',\'1\',\'0\',\'\',\'\',\'\')';
$exampleData[] = 'INSERT INTO bm60_mods(`modname`,`installed`,`pos`,`packageName`,`signature`,`files`) VALUES(\'BMPlugin_Widget_Mailspace\',\'1\',\'0\',\'\',\'\',\'\')';
$exampleData[] = 'INSERT INTO bm60_mods(`modname`,`installed`,`pos`,`packageName`,`signature`,`files`) VALUES(\'BMPlugin_Widget_Quicklinks\',\'1\',\'0\',\'\',\'\',\'\')';
$exampleData[] = 'INSERT INTO bm60_mods(`modname`,`installed`,`pos`,`packageName`,`signature`,`files`) VALUES(\'BMPlugin_Widget_Calendar\',\'1\',\'0\',\'\',\'\',\'\')';
$exampleData[] = 'INSERT INTO bm60_mods(`modname`,`installed`,`pos`,`packageName`,`signature`,`files`) VALUES(\'BMPlugin_Widget_Tasks\',\'1\',\'0\',\'\',\'\',\'\')';
$exampleData[] = 'INSERT INTO bm60_mods(`modname`,`installed`,`pos`,`packageName`,`signature`,`files`) VALUES(\'BMPlugin_Widget_Welcome\',\'1\',\'0\',\'\',\'\',\'\')';
$exampleData[] = 'INSERT INTO bm60_mods(`modname`,`installed`,`pos`,`packageName`,`signature`,`files`) VALUES(\'BMPlugin_Widget_EMail\',\'1\',\'0\',\'\',\'\',\'\')';
$exampleData[] = 'INSERT INTO bm60_mods(`modname`,`installed`,`pos`,`packageName`,`signature`,`files`) VALUES(\'BMPlugin_Widget_Webdiskspace\',\'1\',\'0\',\'\',\'\',\'\')';
$exampleData[] = 'INSERT INTO bm60_mods(`modname`,`installed`,`pos`,`packageName`,`signature`,`files`) VALUES(\'BMPlugin_Widget_Websearch\',\'1\',\'0\',\'\',\'\',\'\')';
$exampleData[] = 'INSERT INTO bm60_abuse_points_config(`type`,`points`,`prefs`) VALUES(1,5,\'\')';
$exampleData[] = 'INSERT INTO bm60_abuse_points_config(`type`,`points`,`prefs`) VALUES(2,25,\'\')';
$exampleData[] = 'INSERT INTO bm60_abuse_points_config(`type`,`points`,`prefs`) VALUES(3,15,\'\')';
$exampleData[] = 'INSERT INTO bm60_abuse_points_config(`type`,`points`,`prefs`) VALUES(4,10,\'\')';
$exampleData[] = 'INSERT INTO bm60_abuse_points_config(`type`,`points`,`prefs`) VALUES(5,10,\'\')';
$exampleData[] = 'INSERT INTO bm60_abuse_points_config(`type`,`points`,`prefs`) VALUES(6,20,\'interval=60\')';
$exampleData[] = 'INSERT INTO bm60_abuse_points_config(`type`,`points`,`prefs`) VALUES(7,20,\'interval=5\')';
$exampleData[] = 'INSERT INTO bm60_abuse_points_config(`type`,`points`,`prefs`) VALUES(21,5,\'amount=50;interval=5\')';
$exampleData[] = 'INSERT INTO bm60_abuse_points_config(`type`,`points`,`prefs`) VALUES(22,5,\'amount=100;interval=5\')';
