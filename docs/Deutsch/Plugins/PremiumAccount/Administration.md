# Administration

## Konzept 

Mit dem PremiumAccount-Plugin können Sie Ihren Benutzern Leistungs-Upgrades anbieten - entweder zu einem einmaligen Festpreis oder zu einem periodisch zu zahlenden Preis im Abo.

Sie können verschiedene Leistungsupgrades anbieten, indem Sie mehrere Pakete (man könnte diese auch _Angebote_ nennen) anlegen, und so können Sie auch verschiedene Leistungen zu verschiedenen Preisen anbieten.

Die Leistungen eines bestimmten Pakets definieren Sie als **Benutzergruppe in b1gMail**. Wenn Sie ein Leistungspaket anbieten möchten, richten Sie zuerst eine spezielle Benutzergruppe in b1gMail ein (im Administrationsbereich unter 'Gruppen'), in der Sie alle Leistungen genau festlegen.

Danach legen Sie im PremiumAccount-Plugin ein Paket an. Dieses Paket verbinden Sie mit der angelegten Gruppe, welche die Leistungen definiert, die das Paket bieten soll. Zum Paket können Sie einen Preis, eine Zahlungsperiode und eine sog. Rückfall-Gruppe festlegen und eine Beschreibung hinterlegen.

Abonniert einer Ihrer Benutzer nun ein von Ihnen angebotenes Paket, wird er nach erfolgreicher Aktivierung seiner Bestellung (durch Begleichen des Rechnungsbetrags) in die neue Benutzergruppe verschoben.

Sollte das Abo des Benutzers auslaufen (indem es nicht mehr verlängert wird), wird der Benutzer entsprechend der eingestellten Rückfall-Gruppe behandelt: Er wird entweder gesperrt oder in die eingestellte Gruppe verschoben.

## Administrations-Bereich 

Der Administrationsbereich des PremiumAccount-Plugins ist im Adminbereich unter 'Plugins' -> 'PremiumAccount' zu finden und gliedert sich in die folgenden Bereiche. Klicken Sie auf einen Bereichsnamen, um zur Dokumentation des entsprechenden Bereichs zu gelangen.

* **[Übersicht](Administration/Übersicht.md)**  
  Zeigt eine Übersicht mit Kurzstatistiken und Statusinformationen an.
* **[Pakete](Administration/Pakete.md)**  
  Erlaubt die Einrichtung und Verwaltung von angebotenen Upgrade-Paketen.
* **[Abonnements](Administration/Abonnements.md)**  
  Erlaubt die Verwaltung bestehender Abonnements.
* **[Einstellungen](Administration/Einstellungen.md)**  
  Erlaubt das Konfigurieren des PremiumAccount-Plugins.

## Verwaltung von Zahlungen 

Die Zahlungsschnittstelle und -administration ist fester Bestandteil von b1gMail:

* Die Konfiguration der Zahlungsschnittstelle erfolgt im Adminbereich unter 'Einstellungen' -> 'Zahlungen'.
* Die Verwaltung eingegangener Bestellungen und erzeugter Rechnungen erfolgt unter 'Benutzer & Gruppen' -> 'Zahlungen'.
