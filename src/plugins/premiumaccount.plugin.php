<?php
/*
 * b1gMail PremiumAccount plugin
 * (c) 2021 Patrick Schlangen et al
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

define('PACC_ACCENT_NONE',			0);
define('PACC_ACCENT_FREE',			1);
define('PACC_ACCENT_BESTVALUE',		2);
define('PACC_ACCENT_RECOMMENDED',	3);

/**
 * plugin interface
 *
 */
class PremiumAccountPlugin extends BMPlugin
{
	/**
	 * Plugin prefs
	 *
	 * @var array
	 */
	var $prefs;

	/**
	 * constructor
	 *
	 * @return PremiumAccountPlugin
	 */
	function __construct()
	{
		global $thisUser;

		// plugin info
		$this->type					= BMPLUGIN_DEFAULT;
		$this->name					= 'b1gMail PremiumAccount PlugIn';
		$this->author				= 'b1gMail Project';
		$this->version				= '2.51';
		$this->website				= 'https://www.b1gmail.org/';
		$this->update_url			= 'https://service.b1gmail.org/plugin_updates/';

		// admin pages
		$this->admin_pages			= true;
		$this->admin_page_title		= 'PremiumAccount';
		$this->admin_page_icon		= 'pacc_logo16.png';
	}

	/**
	 * onload
	 *
	 */
	function OnLoad()
	{
		$this->prefs = $this->_getPrefs();
	}

	/**
	 * initialize language-dependent phrases
	 *
	 */
	function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
	{
		$_lang_custom = $_lang_user = $_lang_client = $_lang_admin = array();

		if($lang == 'deutsch')
		{
			//
			// german
			//

			// custom
			$_lang_custom['pacc_nm_subject']	= 'Ihr Account-Abo läuft in Kürze ab';
			$_lang_custom['pacc_nm_text']		= 'Sehr geehrte Damen und Herren,' . "\n\n"
												.	'Ihr Account-Abo des Pakets "%%paketname%%" bei uns läuft in Kürze ab.' . "\n\n"
												.	'Damit Sie Ihren Account weiter in vollem Umfang nutzen können, bitten wir Sie, Ihr Abo zu erneuern. '
												.	'Für %%betrag%% können Sie Ihren Account z.B. für %%zeit%% verlängern.' . "\n\n"
												.	'Sollte Ihr Abo ohne eine Erneuerung ablaufen, wird Ihr Benutzerkonto entweder eingeschränkt oder gesperrt werden.' . "\n\n"
												.	'(Diese E-Mail wurde automatisch erstellt)';

			// user
			$_lang_user['pacc_sendlimit']			= '%d E-Mail(s) in %d %s';
			$_lang_user['pacc_minutes']				= 'Minute(n)';
			$_lang_user['pacc_hours']				= 'Stunde(n)';
			$_lang_user['pacc_accent_1']			= 'Kostenlos';
			$_lang_user['pacc_accent_2']			= 'Preis-Tipp';
			$_lang_user['pacc_accent_3']			= 'Unsere Empfehlung';
			$_lang_user['pacc_signup_aborted']		= 'Die Registrierung wurde abgebrochen und Ihre Benutzerdaten wurden verworfen. Wir danken Ihnen dennoch f&uuml;r Ihr Interesse an unseren Dienstleistungen.<br />Sie k&ouml;nnen sich gerne jederzeit erneut anmelden. Sollten Sie Fragen haben, z&ouml;gern Sie nicht, uns zu kontaktieren.';
			$_lang_user['pacc_locktext']			= 'Ihr Account ist nicht mehr aktiv. Sie k&ouml;nnen Ihren Account reaktivieren und verl&auml;ngern, indem Sie sich f&uuml;r eines der folgenden Leistungs-Pakete entscheiden. Bitte w&auml;hlen Sie nun das Leistungs-Paket aus, das Sie erwerben m&ouml;chten. Informationen zu verf&uuml;gbaren Paketen und deren Preisen finden Sie in der folgenden Tabelle.';
			$_lang_user['pacc_forcetext']			= 'Bitte w&auml;hlen Sie nun das Leistungs-Paket aus, das Sie erwerben m&ouml;chten. Informationen zu verf&uuml;gbaren Paketen und deren Preisen finden Sie in der folgenden Tabelle.';
			$_lang_user['pacc_ordertext']			= 'Neben unserem kostenlosen Angebot bieten wir auch Pakete mit mehr Funktionen und Leistungen an. Im Folgenden finden Sie einen &Uuml;berblick &uuml;ber verf&uuml;gbare Pakete und deren Preise.<br />Sie k&ouml;nnen ein Paket auf Wunsch direkt hier bestellen. Selbstverst&auml;ndlich k&ouml;nnen Sie auch zuerst unser kostenloses Angebot nutzen und auf Wunsch sp&auml;ter ein Paket mit zus&auml;tzlichen Leistungen erwerben.';
			$_lang_user['pacc_free']				= 'kostenlos';
			$_lang_user['pacc_doorder']				= 'Ausgew&auml;hltes Paket bestellen';
			$_lang_user['pacc_dontorder']			= 'Ohne Bestellung fortfahren';
			$_lang_user['pacc_abort']				= 'Registrierung abbrechen';
			$_lang_user['pacc_infos']				= 'Infos';
			$_lang_user['pacc_price']				= 'Preis';
			$_lang_user['pacc_features']			= 'Leistungen';
			$_lang_user['pacc_selection']			= 'Auswahl';
			$_lang_user['pacc_or']					= 'oder';
			$_lang_user['pacc_incl']				= 'inkl.';
			$_lang_user['pacc_excl']				= 'exkl.';
			$_lang_user['pacc_vat']					= 'MwSt.';
			$_lang_user['pacc_jede']				= 'jede';
			$_lang_user['pacc_jeden']				= 'jeden';
			$_lang_user['pacc_jedes']				= 'jedes';
			$_lang_user['pacc_alle']				= 'alle';
			$_lang_user['pacc_einmalig']			= 'einmalig';
			$_lang_user['pacc_woche']				= 'Woche';
			$_lang_user['pacc_monat']				= 'Monat';
			$_lang_user['pacc_jahr']				= 'Jahr';
			$_lang_user['pacc_wochen']				= 'Wochen';
			$_lang_user['pacc_monate']				= 'Monate';
			$_lang_user['pacc_jahre']				= 'Jahre';
			$_lang_user['pacc_wochen2']				= 'Woche(n)';
			$_lang_user['pacc_monate2']				= 'Monat(e)';
			$_lang_user['pacc_jahre2']				= 'Jahr(e)';
			$_lang_user['pacc_mod']					= 'Account-Upgrade';
			$_lang_user['prefs_d_pacc_mod']			= 'Verbessern Sie die Leistung Ihres Accounts und verwalten Sie Ihre Account-Upgrades.';
			$_lang_user['pacc_prefs_intro']			= 'Hier k&ouml;nnen Sie den Funktions- und Leistungs-Umfang Ihres E-Mail-Kontos erh&ouml;hen, indem Sie ein Paket-Upgrade erwerben. Im folgenden sehen Sie Ihr aktuell abonniertes Paket und die verbleibende Laufzeit und haben die M&ouml;glichkeit, ein Paket aus unserem Angebot zu bestellen.';
			$_lang_user['pacc_activesubscription']	= 'Aktuelles Abonnement';
			$_lang_user['pacc_noactivesubscription'] = 'Im Moment ist kein Abonnement aktiv.';
			$_lang_user['pacc_package']				= 'Paket';
			$_lang_user['pacc_lastpayment']			= 'Letzte Zahlung';
			$_lang_user['pacc_validuntil']			= 'G&uuml;ltig bis';
			$_lang_user['pacc_renew']				= 'Abonnement verl&auml;ngern';
			$_lang_user['pacc_order']				= 'Paket bestellen';
			$_lang_user['pacc_recentorders']		= 'Letzte Bestellungen';
			$_lang_user['pacc_norecentorders']		= 'Es wurden keine Bestellungen in Ihrem Account gefunden.';
			$_lang_user['pacc_orderno']				= 'Best.-Nr.';
			$_lang_user['pacc_invoice']				= 'Rechnung';
			$_lang_user['pacc_completed']			= 'Abgeschlossen?';
			$_lang_user['pacc_amount']				= 'Betrag';
			$_lang_user['pacc_printinvoice']		= 'Rechnung drucken';
			$_lang_user['pacc_packagedetails']		= 'Paket-Details';
			$_lang_user['pacc_description']			= 'Beschreibung';
			$_lang_user['pacc_deletedpackage']		= 'nicht mehr im Angebot';
			$_lang_user['pacc_runtime']				= 'Laufzeit';
			$_lang_user['pacc_invoiceaddress']		= 'Rechnungs-Adresse';
			$_lang_user['pacc_paymentmethod']		= 'Zahlungs-Methode';
			$_lang_user['pacc_pay']					= 'Zahlen';
			$_lang_user['pacc_subscription']		= 'Abonnement';
			$_lang_user['pacc_otherpackwarning']	= 'Es besteht derzeit noch ein aktives Abonnement f&uuml;r ein anderes Paket. Wenn Sie die Bestellung durchf&uuml;hren und abschlie&szlig;en, erlischt Ihr aktuelles Abonnement.';
			$_lang_user['pacc_finalamount']			= 'End-Betrag';
			$_lang_user['pacc_unlimited']			= 'unbegrenzt';
			$_lang_user['pacc_runtimenote']			= 'Die Laufzeit muss ein ganzzahliges Vielfaches von %d sein (%d, %d, %d, ...).';
			$_lang_user['pacc_taxnote']				= 'inkl. %.02f%% MwSt.';
			$_lang_user['pacc_banktransfer']		= '&Uuml;berweisung (Vorkasse)';
			$_lang_user['pacc_paypal']				= 'PayPal';
			$_lang_user['pacc_sofortueberweisung']	= 'sofortueberweisung.de';
			$_lang_user['pacc_placeorder']			= 'Bestellung zahlungspflichtig abschicken';
			$_lang_user['pacc_placeorderfree']		= 'Kostenlos bestellen';
			$_lang_user['pacc_accunit']				= 'Abrechnungseinheit';
			$_lang_user['pacc_pn_paypal']			= 'Den Rechnungsbetrag (%.02f %s) haben wir dankend per PayPal erhalten.';
			$_lang_user['pacc_pn_banktransfer']		= 'Bitte &uuml;berweisen Sie den Rechnungsbetrag (%.02f %s) auf unser unten auf dieser Rechnung angegebenes Bankkonto. Achten Sie <b>unbedingt</b> darauf, im Verwendungszweck exakt folgenden Code anzugeben, damit wir Ihre &Uuml;berweisung identifizieren k&ouml;nnen - andernfalls kann eine Freischaltung nicht oder nur mit Verz&ouml;gerung durchgef&uuml;hrt werden: <b>VK-%s</b>.<br />Ihr Account-Upgrade wird sofort nach Geldeingang freigeschaltet.';
			$_lang_user['pacc_pn_sofortueberweisung']= 'Den Rechnungsbetrag (%.02f %s) haben wir dankend per sofortueberweisung.de erhalten.';
			$_lang_user['pacc_thanks']				= 'Vielen Dank f&uuml;r Ihr Bestellung.';
			$_lang_user['pacc_thanks_paypal']		= 'Bitte klicken Sie nun auf den folgenden Button, um zur PayPal-Zahlungsseite zu gelangen, falls Sie nicht automatisch weitergeleitet werden. Nach abgeschlossener Zahlung wird Ihre Bestellung umgehend freigeschaltet.';
			$_lang_user['pacc_thanks_banktransfer']	= 'Bitte &uuml;berweisen Sie nun den f&auml;lligen Rechnungsbetrag (<b>%.02f %s</b>) auf unser nachfolgendes Bankkonto. Achten Sie dabei <i>unbedingt</i> auf die Verwendung des angegebenen Verwendungszwecks!';
			$_lang_user['pacc_thanks_banktransfer2']= 'Sobald der Betrag auf unserem Konto eingegangen ist, wird Ihre Bestellung freigeschaltet.';
			$_lang_user['pacc_thanks_sofortueberweisung'] = 'Bitte klicken Sie nun auf den folgenden Button, um zur Zahlungsseite von sofortueberweisung.de zu gelangen, falls Sie nicht automatisch weitergeleitet werden. Nach abgeschlossener Zahlung wird Ihre Bestellung umgehend freigeschaltet.';
			$_lang_user['pacc_paysu']				= 'Mit sofortueberweisung.de bezahlen';
			$_lang_user['pacc_kto_inh']				= 'Konto-Inhaber';
			$_lang_user['pacc_kto_nr']				= 'Konto-Nummer';
			$_lang_user['pacc_kto_blz']				= 'BLZ';
			$_lang_user['pacc_kto_inst']			= 'Institut';
			$_lang_user['pacc_kto_iban']			= 'IBAN';
			$_lang_user['pacc_kto_bic']				= 'BIC/SWIFT-Code';
			$_lang_user['pacc_subject']				= 'Verwendungszweck';
			$_lang_user['pacc_showinvoice']			= 'Rechnung anzeigen';
			$_lang_user['pacc_paymentreturn']		= 'Vielen Dank f&uuml;r Ihre Bestellung und das Ausf&uuml;hren der Zahlung. Sofern die Zahlung erfolgreich war, wird diese umgehend freigeschaltet. Sie k&ouml;nnen den Status jederzeit unter &quot;Account-Upgrade&quot; im Bereich &quot;Einstellungen&quot; einsehen.';
			$_lang_user['pacc_paymentreturn2']		= 'Vielen Dank f&uuml;r Ihre Bestellung und das Ausf&uuml;hren der Zahlung. Sofern die Zahlung erfolgreich war, wird diese umgehend freigeschaltet. Sobald Ihr Account freigeschaltet ist, k&ouml;nnen Sie sich auf der <a href="index.php">Startseite</a> einloggen - probieren Sie es doch einfach schon einmal!';
			$_lang_user['pacc_paymentreturn3']		= 'Vielen Dank f&uuml;r Ihre Anmeldung f&uuml;r eines unserer kostenlosen Pakete. Sie k&ouml;nnen sich nun auf unserer <a href="index.php">Startseite</a> einloggen.';
			$_lang_user['pacc_invoiceno']			= 'Rechnungsnummer';
			$_lang_user['pacc_customerno']			= 'Kundennummer';
			$_lang_user['pacc_yourinvoice']			= 'Ihre Rechnung';
			$_lang_user['pacc_dearsirormadam']		= 'Sehr geehrte Damen und Herren';
			$_lang_user['pacc_invtext']				= 'hiermit berechnen wir Ihnen die folgenden Produkte bzw. Leistungen';
			$_lang_user['pacc_pos']					= 'Position';
			$_lang_user['pacc_count']				= 'Anzahl';
			$_lang_user['pacc_descr']				= 'Bezeichnung';
			$_lang_user['pacc_ep']					= 'Einzelpreis';
			$_lang_user['pacc_gp']					= 'Gesamtpreis';
			$_lang_user['pacc_gb']					= 'Gesamtbetrag';
			$_lang_user['pacc_net']					= 'netto';
			$_lang_user['pacc_gross']				= 'brutto';
			$_lang_user['pacc_kindregards']			= 'Mit freundlichen Gr&uuml;&szlig;en';
			$_lang_user['pacc_bankacc']				= 'Bankverbindung';
			$_lang_user['pacc_invfooter']			= 'Diese Rechnung wurde maschinell erstellt und ist auch ohne Unterschrift g&uuml;ltig.';
			$_lang_user['pacc_activated']			= 'Ihre Bestellung wurde freigeschaltet.';
			$_lang_user['pacc_next']				= 'Weiter';
			$_lang_user['pacc_cancelwarning']		= 'Wenn Sie Ihre Mitgliedschaft k&uuml;ndigen, gehen auch Ihr aktuelles Account-Upgrade-Abonnement sowie Ihr gesamtes Credit-Guthaben unwiderruflich verloren. Hier finden Sie noch einemal Infos zu Ihrem aktuellen Abonnement. Klicken Sie auf &quot;Weiter &gt;&gt;&quot;, wenn Sie einverstanden sind, dass Ihr Abonnement und Ihr Guthaben verloren gehen.';
			$_lang_user['pacc_maxerror']			= 'Dieses Paket ist f&uuml;r maximal <b>%d %s</b> buchbar. Nat&uuml;rlich k&ouml;nnen Sie Ihre Buchung sp&auml;ter vor Ablauf verl&auml;ngern, jedoch darf die Laufzeit die angegebene maximale Laufzeit nicht &uuml;berschreiten.';
			$_lang_user['pacc_packages']			= 'Pakete';
			$_lang_user['pacc_signuptext']			= 'F&uuml;llen Sie einfach das folgende Formular aus, um das gew&auml;hlte Paket zu bestellen.';

			// admin
			$_lang_admin['pacc_accentuation']		= 'Hervorhebung';
			$_lang_admin['pacc_accent_1']			= 'Kostenlos';
			$_lang_admin['pacc_accent_2']			= 'Preis-Tipp';
			$_lang_admin['pacc_accent_3']			= 'Unsere Empfehlung';
			$_lang_admin['pacc_packages']			= 'Pakete';
			$_lang_admin['pacc_subscriptions']		= 'Abonnements';
			$_lang_admin['pacc_payments']			= 'Zahlungen';
			$_lang_admin['pacc_activatepayment']	= 'Zahlung freischalten';
			$_lang_admin['pacc_paymentid']			= 'Zahlungs-ID';
			$_lang_admin['pacc_amount']				= 'Betrag';
			$_lang_admin['pacc_subscribers']		= 'Abonnenten';
			$_lang_admin['pacc_revenue']			= 'Einnahmen';
			$_lang_admin['pacc_thismonth']			= 'dieser Monat';
			$_lang_admin['pacc_outstandingpayments']= 'Ausstehende Zahlungen';
			$_lang_admin['pacc_periodprice']		= 'Preis pro Zahlungsperiode';
			$_lang_admin['pacc_paymentperiod']		= 'Zahlungsperiode';
			$_lang_admin['pacc_addpackage']			= 'Paket hinzuf&uuml;gen';
			$_lang_admin['pacc_once']				= 'Einmalig';
			$_lang_admin['pacc_period_jahre']		= 'Jahr(e)';
			$_lang_admin['pacc_period_wochen']		= 'Woche(n)';
			$_lang_admin['pacc_period_monate']		= 'Monat(e)';
			$_lang_admin['pacc_period_tage']		= 'Tag(e)';
			$_lang_admin['pacc_fallbackgroup']		= 'R&uuml;ckfall-Gruppe';
			$_lang_admin['pacc_lockaccount']		= 'Account sperren';
			$_lang_admin['pacc_every']				= 'Alle';
			$_lang_admin['pacc_deletepackage']		= 'Paket l&ouml;schen';
			$_lang_admin['pacc_deletepackagedesc']	= 'Zu diesem Paket existieren ein oder mehrere Abonnements. Was soll mit diesen Abonnements geschehen?';
			$_lang_admin['pacc_delcontinue']		= 'Zum Laufzeitende auslaufen lassen';
			$_lang_admin['pacc_delfallback']		= 'Jetzt sofort auslaufen lassen';
			$_lang_admin['pacc_package']			= 'Paket';
			$_lang_admin['pacc_lastpayment']		= 'Letzte Zahlung';
			$_lang_admin['pacc_expiration']			= 'Ablauf';
			$_lang_admin['pacc_cancelsubscr']		= 'Abo beenden';
			$_lang_admin['pacc_extendsubscr']		= 'Abo verl&auml;ngern';
			$_lang_admin['pacc_realcancel']			= 'Wollen Sie das Abonnement wirklich beenden?';
			$_lang_admin['pacc_locked_at_signup']	= '[%s] Dieser Benutzer wurde nach seiner Anmeldung durch das PremiumAccount-Modul gesperrt und wird nach erfolgter Bestellung eines Pakets automatisch aktiviert.';
			$_lang_admin['pacc_moved_note']			= '[%s] Dieser Benutzer wurde durch das PremiumAccount-Modul in die Gruppe <%d> verschoben und aktiviert, nachdem die Bestellung <%d> bestaetigt wurde.';
			$_lang_admin['pacc_expire_moved_note']	= '[%s] Dieser Benutzer wurde durch das PremiumAccount-Modul in die Gruppe <%d> verschoben, nachdem das Abonnement <%d> abgelaufen ist.';
			$_lang_admin['pacc_expire_notmoved_note']='[%s] Dieser Benutzer wurde durch das PremiumAccount-Modul NICHT in die Gruppe <%d> verschoben, nachdem das Abonnement <%d> abgelaufen ist, da er manuell in eine nicht dem Paket zugehoerige Gruppe verschoben wurde.';
			$_lang_admin['pacc_expire_locked_note']	= '[%s] Dieser Benutzer wurde durch das PremiumAccount-Modul gesperrt, nachdem das Abonnement <%d> abgelaufen ist.';
			$_lang_admin['pacc_afterdays']			= 'Tagen';
			$_lang_admin['pacc_after']				= 'nach';
			$_lang_admin['pacc_delete_order']		= 'Offene Bestellungen l&ouml;schen';
			$_lang_admin['pacc_pay_notification']	= 'Benachrichtigung bei Zahlung';
			$_lang_admin['pacc_update_notification']= 'Abo-Ablauf-Benachrichtigung';
			$_lang_admin['pacc_before_expiration']	= 'Tage vor Ablauf';
			$_lang_admin['pacc_vat']				= 'MwSt.';
			$_lang_admin['pacc_vat_enthalten']		= 'in Preisen enthalten';
			$_lang_admin['pacc_vat_add']			= 'zu Preisen hinzurechnen';
			$_lang_admin['pacc_vat_nomwst']			= 'nicht berechnen';
			$_lang_admin['pacc_vatrate']			= 'Steuersatz';
			$_lang_admin['pacc_paymentmethod']		= 'Zahlungs-Methode';
			$_lang_admin['pacc_paymentmethods']		= 'Zahlungs-Methoden';
			$_lang_admin['pacc_banktransfer']		= '&Uuml;berweisung (Vorkasse)';
			$_lang_admin['pacc_paypal']				= 'PayPal';
			$_lang_admin['pacc_sofortueberweisung']	= 'sofortueberweisung.de';
			$_lang_admin['pacc_kto_inh']			= 'Konto-Inhaber';
			$_lang_admin['pacc_kto_nr']				= 'Konto-Nummer';
			$_lang_admin['pacc_kto_blz']			= 'BLZ';
			$_lang_admin['pacc_kto_inst']			= 'Institut';
			$_lang_admin['pacc_kto_iban']			= 'IBAN';
			$_lang_admin['pacc_kto_bic']			= 'BIC/SWIFT-Code';
			$_lang_admin['pacc_su_kdnr']			= 'Kunden-Nummer';
			$_lang_admin['pacc_su_prjnr']			= 'Projekt-Nummer';
			$_lang_admin['pacc_su_prjpass']			= 'Projekt-Passwort';
			$_lang_admin['pacc_su_createnew']		= 'Projekt bei sofortueberweisung.de anlegen';
			$_lang_admin['pacc_signup_order']		= 'Bestell-Seite nach Registrierung';
			$_lang_admin['pacc_signup_order_force']	= 'Bestellung obligatorisch';
			$_lang_admin['pacc_invoices']			= 'Rechnungen';
			$_lang_admin['pacc_sendrg']				= 'Rechnungen ausstellen';
			$_lang_admin['pacc_rgnrfmt']			= 'Rg.-Nr.-Format';
			$_lang_admin['pacc_kdnrfmt']			= 'Kd.-Nr.-Format';
			$_lang_admin['pacc_rgtemplate']			= 'Rechnungs-Vorlage';
			$_lang_admin['pacc_viewedit']			= 'Anzeigen / Bearbeiten';
			$_lang_admin['pacc_fields']				= 'Feature-Kategorien';
			$_lang_admin['pacc_sucurrencyerror']	= 'Die Zahlung per sofortueberweisung.de erfordert, dass die Zahlungs-W&auml;hrung EUR oder CHF ist!';
			$_lang_admin['text_pacc_pn_subject']	= 'PremiumAccount: Zahlungs-Nachricht-Betreff';
			$_lang_admin['text_pacc_pn_text']		= 'PremiumAccount: Zahlungs-Nachricht-Text';
			$_lang_admin['text_pacc_nm_subject']	= 'PremiumAccount: Ablauf-Hinweis-Betreff';
			$_lang_admin['text_pacc_nm_text']		= 'PremiumAccount: Ablauf-Hinweis-Text';
			$_lang_admin['pacc_extendsubscrdesc']	= 'Gew&auml;hlte(s) Abonnement(s) verl&auml;ngern...';
			$_lang_admin['pacc_extenddynamic']		= '...um jeweils';
			$_lang_admin['pacc_extendstatic']		= '...bis einschl. zum';
			$_lang_admin['pacc_showinvoice']		= 'Rechnung anzeigen';
			$_lang_admin['pacc_downloadinvoices']	= 'Rechnungen herunterladen';
			$_lang_admin['pacc_act_notfound']		= 'Die angegebene Bestellung wurde nicht gefunden oder ist bereits abgeschlossen.';
			$_lang_admin['pacc_act_success']		= 'Die Zahlung wurde erfolgreich freigeschaltet!';
			$_lang_admin['pacc_act_error']			= 'Die Zahlung konnte nicht freigeschaltet werden. M&ouml;glicherweise ist der angegebene Betrag zu gering.';
			$_lang_admin['pacc_adfree']				= 'Werbefrei';
			$_lang_admin['pacc_fieldpos']			= 'Position';
			$_lang_admin['pacc_defaulttpl']			= 'Standard-Template';
			$_lang_admin['pacc_periods_all']		= 'Frei w&auml;hlbar';
			$_lang_admin['pacc_periods']			= 'Bestellbare Laufzeiten';
			$_lang_admin['pacc_sepbycomma']			= 'mehrere durch Komma trennen';
			$_lang_admin['pacc_period_limit']		= 'Maximale Laufzeit begrenzen:';
			$_lang_admin['pacc_update_notification_altmail']	= 'Auch an Alternativ-E-Mail-Adresse senden';
			$_lang_admin['pacc_send_limit_count']	= 'Versand-Limit (Anzahl)';
			$_lang_admin['pacc_send_limit_time']	= 'Versand-Limit (Zeitraum)';
			$_lang_admin['pacc_nlipackages']		= '&quot;Pakete&quot;-Seite';
			$_lang_admin['pacc_nlipack_no']			= 'Deaktivieren';
			$_lang_admin['pacc_nlipack_yes']		= 'Zus&auml;tzlich anzeigen';
			$_lang_admin['pacc_nlipack_replace']	= 'Vor Registrierung anzeigen';
			$_lang_admin['pacc_alllanguages']		= 'alle Sprachen';
		}
		else
		{
			//
			// english
			//

			// custom
			$_lang_custom['pacc_nm_subject']		= 'Your subscription expires within a short time';
			$_lang_custom['pacc_nm_text']		= 'Dear Sir or Madam,' . "\n\n"
												.	'your "%%paketname%%"-subscription expires within a short time.' . "\n\n"
												.	'If you like to continue to use your account to the full extent, we kindly ask you to renew your subscription. '
												.	'For just %%betrag%%, you can renew your subscription for %%zeit%%, for example.' . "\n\n"
												.	'If you do not renew your subscription and the subscription expires, your account may get limited or locked.' . "\n\n"
												.	'(This e-mail has been generated automaticaly)';

			// user
			$_lang_user['pacc_sendlimit']			= '%d email(s) in %d %s';
			$_lang_user['pacc_minutes']				= 'minute(s)';
			$_lang_user['pacc_hours']				= 'hour(s)';
			$_lang_user['pacc_accent_1']			= 'Free';
			$_lang_user['pacc_accent_2']			= 'Best Value';
			$_lang_user['pacc_accent_3']			= 'Our Recommendation';
			$_lang_user['pacc_signup_aborted']		= 'The sign up process has been aborted and your user data has been discarded. Thank you for your interest in our services.<br />You are welcome to sign up again anytime. If you encounter questions, please do not hesitate to contact us.';
			$_lang_user['pacc_locktext']			= 'Your account is not active anymore. You can re-activate your account by renewing your account subscription. We invite you to choose betweem the following offers. You can find detailed information below.';
			$_lang_user['pacc_forcetext']			= 'Please choose the offer you would like to subscribe to. You can find detailed information in the table below.';
			$_lang_user['pacc_ordertext']			= 'Besides our free services, we also offer subscriptions with enhanced services and features. You can find detailed information and prices below.<br />You can subscribe to an offer just here, if you like to. Of course you can use our free service first and subscribe later, if you are satisfied.';
			$_lang_user['pacc_free']				= 'free';
			$_lang_user['pacc_doorder']				= 'Subscribe to selected offer';
			$_lang_user['pacc_dontorder']			= 'Proceed without ordering';
			$_lang_user['pacc_abort']				= 'Cancel sign up';
			$_lang_user['pacc_infos']				= 'Information';
			$_lang_user['pacc_price']				= 'Price';
			$_lang_user['pacc_features']			= 'Features';
			$_lang_user['pacc_selection']			= 'Selection';
			$_lang_user['pacc_or']					= 'or';
			$_lang_user['pacc_incl']				= 'incl.';
			$_lang_user['pacc_excl']				= 'excl.';
			$_lang_user['pacc_vat']					= 'VAT';
			$_lang_user['pacc_jede']				= 'every';
			$_lang_user['pacc_jeden']				= 'every';
			$_lang_user['pacc_jedes']				= 'every';
			$_lang_user['pacc_alle']				= 'every';
			$_lang_user['pacc_einmalig']			= 'once';
			$_lang_user['pacc_woche']				= 'week';
			$_lang_user['pacc_monat']				= 'month';
			$_lang_user['pacc_jahr']				= 'year';
			$_lang_user['pacc_wochen']				= 'weeks';
			$_lang_user['pacc_monate']				= 'months';
			$_lang_user['pacc_jahre']				= 'years';
			$_lang_user['pacc_wochen2']				= 'week(s)';
			$_lang_user['pacc_monate2']				= 'month(s)';
			$_lang_user['pacc_jahre2']				= 'year(s)';
			$_lang_user['pacc_mod']					= 'Account Upgrade';
			$_lang_user['prefs_d_pacc_mod']			= 'Enhance the features of your account and manage your active subscriptions.';
			$_lang_user['pacc_prefs_intro']			= 'You can enhance the features and services of your account by ordering a subscription. You can find your active subscription and the expiration date below.';
			$_lang_user['pacc_activesubscription']	= 'Active subscription';
			$_lang_user['pacc_noactivesubscription'] = 'No active subscriptions found.';
			$_lang_user['pacc_package']				= 'Package';
			$_lang_user['pacc_lastpayment']			= 'Last payment';
			$_lang_user['pacc_validuntil']			= 'Valid until';
			$_lang_user['pacc_renew']				= 'Renew subscription';
			$_lang_user['pacc_order']				= 'Order package';
			$_lang_user['pacc_recentorders']		= 'Latest orders';
			$_lang_user['pacc_norecentorders']		= 'No orders found.';
			$_lang_user['pacc_orderno']				= 'Order no.';
			$_lang_user['pacc_invoice']				= 'Invoice';
			$_lang_user['pacc_completed']			= 'Completed?';
			$_lang_user['pacc_amount']				= 'Amount';
			$_lang_user['pacc_printinvoice']		= 'Print invoice';
			$_lang_user['pacc_packagedetails']		= 'Package details';
			$_lang_user['pacc_description']			= 'Description';
			$_lang_user['pacc_deletedpackage']		= 'not for sale anymore';
			$_lang_user['pacc_runtime']				= 'Runtime';
			$_lang_user['pacc_invoiceaddress']		= 'Invoice address';
			$_lang_user['pacc_paymentmethod']		= 'Payment method';
			$_lang_user['pacc_pay']					= 'Pay';
			$_lang_user['pacc_subscription']		= 'Subscription';
			$_lang_user['pacc_otherpackwarning']	= 'You still have an active subscription for another package. Your old subscription will be cancelled, if you complete this order.';
			$_lang_user['pacc_finalamount']			= 'Final amount';
			$_lang_user['pacc_unlimited']			= 'unlimited';
			$_lang_user['pacc_runtimenote']			= 'The runtime must be a multiple of %d (%d, %d, %d, ...).';
			$_lang_user['pacc_taxnote']				= 'incl. %.02f%% VAT';
			$_lang_user['pacc_banktransfer']		= 'Bank transfer (prepayment)';
			$_lang_user['pacc_paypal']				= 'PayPal';
			$_lang_user['pacc_sofortueberweisung']	= 'sofortueberweisung.de';
			$_lang_user['pacc_placeorder']			= 'Submit order';
			$_lang_user['pacc_placeorderfree']		= 'Submit order for free';
			$_lang_user['pacc_accunit']				= 'billing unit';
			$_lang_user['pacc_pn_paypal']			= 'The invoiced amount (%.02f %s) has been paid using PayPal.';
			$_lang_user['pacc_pn_banktransfer']		= 'Please transfer the invoiced amount (%.02f %s) to our bank account stated below. <b>Necessarily</a> use the following code as the payment subject: <b>VK-%s</b>.<br />Your subscription will be activated as soon as the payment is visible in our bank account.';
			$_lang_user['pacc_pn_sofortueberweisung']= 'The invoided amount (%.02f %s) has been paid using sofortueberweisung.de.';
			$_lang_user['pacc_thanks']				= 'Thank you for your order.';
			$_lang_user['pacc_thanks_paypal']		= 'Please click at the following button to visit the PayPal payment page if you are not redirected automaticaly. After completing the payment, your subscription will be activated.';
			$_lang_user['pacc_thanks_banktransfer']	= 'Please transfer the invoiced amount (<b>%.02f %s</b>) to our bank account stated below. <i>Necessarily</i> use the stated payment subject!';
			$_lang_user['pacc_thanks_banktransfer2']		= 'As soon as the payment is visible in our bank account your subscription will be activated.';
			$_lang_user['pacc_thanks_sofortueberweisung'] 	= 'Please click at the following button to visit the sofortueberweisung.de z payment page if you are not redirected automaticaly. After completing the payment, your subscription will be activated.';
			$_lang_user['pacc_paysu']				= 'Pay using sofortueberweisung.de';
			$_lang_user['pacc_kto_inh']				= 'Account owner';
			$_lang_user['pacc_kto_nr']				= 'Account no.';
			$_lang_user['pacc_kto_blz']				= 'Bank code';
			$_lang_user['pacc_kto_inst']			= 'Bank name';
			$_lang_user['pacc_kto_iban']			= 'IBAN';
			$_lang_user['pacc_kto_bic']				= 'BIC/SWIFT-Code';
			$_lang_user['pacc_subject']				= 'Payment subject';
			$_lang_user['pacc_showinvoice']			= 'Show invoice';
			$_lang_user['pacc_paymentreturn']		= 'Thank you for your order. If your payment has been successful, your subscription will be unlocked immediately. You can view the order status at the &quot;Account Upgrade&quot; page in the &quot;Preferences&quot; section.';
			$_lang_user['pacc_paymentreturn2']		= 'Thank you for your order. If your payment has been successful, your subscription will be unlocked immediately. As soon as your account is activated, you can log in at the <a href="index.php">start page</a> - just try it now!';
			$_lang_user['pacc_paymentreturn3']		= 'Thank you for chosing one of our free plans. You can now log in to you account on our <a href="index.php">start page</a>!';
			$_lang_user['pacc_invoiceno']			= 'Invoice no.';
			$_lang_user['pacc_customerno']			= 'Customer no.';
			$_lang_user['pacc_yourinvoice']			= 'Your invoice';
			$_lang_user['pacc_dearsirormadam']		= 'Dear Sir or Madam';
			$_lang_user['pacc_invtext']				= 'please find your invoice below';
			$_lang_user['pacc_pos']					= 'Position';
			$_lang_user['pacc_count']				= 'Count';
			$_lang_user['pacc_descr']				= 'Description';
			$_lang_user['pacc_ep']					= 'Unit price';
			$_lang_user['pacc_gp']					= 'Sum price';
			$_lang_user['pacc_gb']					= 'Final amount';
			$_lang_user['pacc_net']					= 'net';
			$_lang_user['pacc_gross']				= 'gross';
			$_lang_user['pacc_kindregards']			= 'Kind regards';
			$_lang_user['pacc_bankacc']				= 'Bank connection';
			$_lang_user['pacc_invfooter']			= 'This invoice has been generated automaticaly and is valid without a signature.';
			$_lang_user['pacc_activated']			= 'Your order has been activated.';
			$_lang_user['pacc_next']				= 'Next';
			$_lang_user['pacc_cancelwarning']		= 'When you cancel your accont, your account subscription and all your credits will be lost. You can find your current subscription below. Click at &quot;Next &gt;&gt;&quot; in case you accept that your subscription and your credits will be void.';
			$_lang_user['pacc_maxerror']			= 'This package can be booked for a maximum time frame of <b>%d %s</b>. Of course you can renew/extend your subscription when it expires.';
			$_lang_user['pacc_packages']			= 'Packages';
			$_lang_user['pacc_signuptext']			= 'Please fill in the following form to order the selected package.';

			// admin
			$_lang_admin['pacc_accentuation']		= 'Accentuation';
			$_lang_admin['pacc_accent_1']			= 'Free';
			$_lang_admin['pacc_accent_2']			= 'Best Value';
			$_lang_admin['pacc_accent_3']			= 'Our Recommendation';
			$_lang_admin['pacc_packages']			= 'Packages';
			$_lang_admin['pacc_subscriptions']		= 'Subscriptions';
			$_lang_admin['pacc_payments']			= 'Payments';
			$_lang_admin['pacc_activatepayment']	= 'Activate payments';
			$_lang_admin['pacc_paymentid']			= 'Payment ID';
			$_lang_admin['pacc_amount']				= 'Amount';
			$_lang_admin['pacc_subscribers']		= 'Subscriptions';
			$_lang_admin['pacc_revenue']			= 'Revenues';
			$_lang_admin['pacc_thismonth']			= 'current month';
			$_lang_admin['pacc_outstandingpayments']= 'Outstanding amounts';
			$_lang_admin['pacc_periodprice']		= 'Price per payment period';
			$_lang_admin['pacc_paymentperiod']		= 'Payment period';
			$_lang_admin['pacc_addpackage']			= 'Add package';
			$_lang_admin['pacc_once']				= 'Once';
			$_lang_admin['pacc_period_jahre']		= 'Year(s)';
			$_lang_admin['pacc_period_wochen']		= 'Week(s)';
			$_lang_admin['pacc_period_monate']		= 'Month(s)';
			$_lang_admin['pacc_period_tage']		= 'Day(s)';
			$_lang_admin['pacc_fallbackgroup']		= 'Fallback group';
			$_lang_admin['pacc_lockaccount']		= 'lock account';
			$_lang_admin['pacc_every']				= 'Every';
			$_lang_admin['pacc_deletepackage']		= 'Delete package';
			$_lang_admin['pacc_deletepackagedesc']	= 'One or more users subscribe to this package. What shall be done with the active subscriptions?';
			$_lang_admin['pacc_delcontinue']		= 'Continue subscriptions until they expire';
			$_lang_admin['pacc_delfallback']		= 'Cancel subscriptions';
			$_lang_admin['pacc_package']			= 'Package';
			$_lang_admin['pacc_lastpayment']		= 'Last payment';
			$_lang_admin['pacc_expiration']			= 'Expiration';
			$_lang_admin['pacc_cancelsubscr']		= 'Cancel subscription';
			$_lang_admin['pacc_extendsubscr']		= 'Renew subscription';
			$_lang_admin['pacc_realcancel']			= 'Do you really want to cancel the subscription?';
			$_lang_admin['pacc_locked_at_signup']	= '[%s] This user has been locked by the PremiumAccount plugin after signing up and will be activated as soon as he completes an order.';
			$_lang_admin['pacc_moved_note']			= '[%s] This user has been activated and moved to the group <%d> by the PremiumAccount plugin after the order <%d> has been activated.';
			$_lang_admin['pacc_expire_moved_note']	= '[%s] This user has been moved to the group <%d> by the PremiumAccount plugin after the subscription <%d> has expired.';
			$_lang_admin['pacc_expire_notmoved_note']='[%s] This user has NOT been moved to the group <%d> by the PremiumAccount plugin after the subscription <%d> has expired because he was manually moved to a group not belonging to this package.';
			$_lang_admin['pacc_expire_locked_note']	= '[%s] This user has been locked by the PremiumAccount plugin after the subscription <%d> has expired.';
			$_lang_admin['pacc_afterdays']			= 'days';
			$_lang_admin['pacc_after']				= 'after';
			$_lang_admin['pacc_delete_order']		= 'Delete incomplete orders';
			$_lang_admin['pacc_pay_notification']	= 'Payment notification';
			$_lang_admin['pacc_update_notification']= 'Expiration notification';
			$_lang_admin['pacc_before_expiration']	= 'days prior to expiration';
			$_lang_admin['pacc_vat']				= 'VAT';
			$_lang_admin['pacc_vat_enthalten']		= 'included in prices';
			$_lang_admin['pacc_vat_add']			= 'add to prices';
			$_lang_admin['pacc_vat_nomwst']			= 'no VAT';
			$_lang_admin['pacc_vatrate']			= 'Tax rate';
			$_lang_admin['pacc_paymentmethod']		= 'Payment method';
			$_lang_admin['pacc_paymentmethods']		= 'Payment methods';
			$_lang_admin['pacc_banktransfer']		= 'Bank transfer (prepayment)';
			$_lang_admin['pacc_paypal']				= 'PayPal';
			$_lang_admin['pacc_sofortueberweisung']	= 'sofortueberweisung.de';
			$_lang_admin['pacc_kto_inh']			= 'Account owner';
			$_lang_admin['pacc_kto_nr']				= 'Account no.';
			$_lang_admin['pacc_kto_blz']			= 'Bank code';
			$_lang_admin['pacc_kto_inst']			= 'Bank name';
			$_lang_admin['pacc_kto_iban']			= 'IBAN';
			$_lang_admin['pacc_kto_bic']			= 'BIC/SWIFT-Code';
			$_lang_admin['pacc_su_kdnr']			= 'Customer no.';
			$_lang_admin['pacc_su_prjnr']			= 'Project no.';
			$_lang_admin['pacc_su_prjpass']			= 'Project password';
			$_lang_admin['pacc_su_createnew']		= 'Create sofortueberweisung.de-project';
			$_lang_admin['pacc_signup_order']		= 'Order page after sign up';
			$_lang_admin['pacc_signup_order_force']	= 'Force order';
			$_lang_admin['pacc_invoices']			= 'Invoices';
			$_lang_admin['pacc_sendrg']				= 'Generate invoices';
			$_lang_admin['pacc_rgnrfmt']			= 'Invoice no. format';
			$_lang_admin['pacc_kdnrfmt']			= 'Customer no. format';
			$_lang_admin['pacc_rgtemplate']			= 'Invoice template';
			$_lang_admin['pacc_viewedit']			= 'View / edit';
			$_lang_admin['pacc_fields']				= 'Feature categories';
			$_lang_admin['pacc_sucurrencyerror']	= 'The sofortueberweisung.de payment method required EUR or CHF as currency!';
			$_lang_admin['text_pacc_pn_subject']	= 'PremiumAccount: Payment notification subject';
			$_lang_admin['text_pacc_pn_text']		= 'PremiumAccount: Payment notification text';
			$_lang_admin['text_pacc_nm_subject']	= 'PremiumAccount: Expiration notification subject';
			$_lang_admin['text_pacc_nm_text']		= 'PremiumAccount: Expiration notification text';
			$_lang_admin['pacc_extendsubscrdesc']	= 'Renews subscriptions...';
			$_lang_admin['pacc_extenddynamic']		= '...for';
			$_lang_admin['pacc_extendstatic']		= '...until';
			$_lang_admin['pacc_showinvoice']		= 'Show invoice';
			$_lang_admin['pacc_downloadinvoices']	= 'Download invoices';
			$_lang_admin['pacc_act_notfound']		= 'The order cannot be found or is already completed.';
			$_lang_admin['pacc_act_success']		= 'The payment has been activated successfuly!';
			$_lang_admin['pacc_act_error']			= 'The payment cannot be unlocked. Probably the payment amount is too low.';
			$_lang_admin['pacc_adfree']				= 'Free of ads';
			$_lang_admin['pacc_defaulttpl']			= 'Default template';
			$_lang_admin['pacc_periods_all']		= 'Arbitrary';
			$_lang_admin['pacc_periods']			= 'Orderable run times';
			$_lang_admin['pacc_sepbycomma']			= 'separate multiple by comma';
			$_lang_admin['pacc_period_limit']		= 'Maximum run time:';
			$_lang_admin['pacc_update_notification_altmail']	= 'Also send to alternative email address';
			$_lang_admin['pacc_fieldpos']			= 'Position';
			$_lang_admin['pacc_send_limit_count']	= 'Send limit (count)';
			$_lang_admin['pacc_send_limit_time']	= 'Send limit (time)';
			$_lang_admin['pacc_nlipackages']		= '&quot;Packages&quot; page';
			$_lang_admin['pacc_nlipack_no']			= 'Disable';
			$_lang_admin['pacc_nlipack_yes']		= 'Show additionally';
			$_lang_admin['pacc_nlipack_replace']	= 'Show before signup';
			$_lang_admin['pacc_alllanguages']		= 'all languages';
		}

		// convert charset
		global $currentCharset;
		$arrays = array('admin', 'client', 'user', 'custom');
		foreach($arrays as $array)
		{
		   $destArray = sprintf('lang_%s', $array);
		   $srcArray  = '_' . $destArray;

		   if(!isset($$srcArray))
			  continue;

		   foreach($$srcArray as $key=>$val)
		   {
			  if(function_exists('CharsetDecode') && !in_array(strtolower($currentCharset), array('iso-8859-1', 'iso-8859-15')))
				 $val = CharsetDecode($val, 'iso-8859-15');
			  ${$destArray}[$key] = $val;
		   }
		}
	}


	/**
	 * installer
	 *
	 */
	function Install()
	{
		global $db, $currentLanguage;

		// db struct
		$databaseStructure =                      // checksum: 4eab06ece647cda6068ee3a4f678eaff
			  'YTo0OntzOjI1OiJibTYwX21vZF9wcmVtaXVtX3BhY2thZ2VzIjthOjI6e3M6NjoiZmllbGRzIjt'
			. 'hOjE2OntpOjA7YTo2OntpOjA7czoyOiJpZCI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk'
			. '8iO2k6MztzOjM6IlBSSSI7aTo0O047aTo1O3M6MTQ6ImF1dG9faW5jcmVtZW50Ijt9aToxO2E6N'
			. 'jp7aTowO3M6NjoiZ3J1cHBlIjtpOjE7czo3OiJpbnQoMTEpIjtpOjI7czoyOiJOTyI7aTozO3M6'
			. 'MDoiIjtpOjQ7TjtpOjU7czowOiIiO31pOjI7YTo2OntpOjA7czoxMDoicHJlaXNfY2VudCI7aTo'
			. 'xO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MzoiMjk5IjtpOj'
			. 'U7czowOiIiO31pOjM7YTo2OntpOjA7czoxMDoiYWJyZWNobnVuZyI7aToxO3M6NDI6ImVudW0oJ'
			. '3dvY2hlbicsJ21vbmF0ZScsJ2phaHJlJywnZWlubWFsaWcnKSI7aToyO3M6MjoiTk8iO2k6Mztz'
			. 'OjA6IiI7aTo0O3M6NjoibW9uYXRlIjtpOjU7czowOiIiO31pOjQ7YTo2OntpOjA7czoxMjoiYWJ'
			. 'yZWNobnVuZ190IjtpOjE7czoxMDoidGlueWludCg0KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6Ii'
			. 'I7aTo0O047aTo1O3M6MDoiIjt9aTo1O2E6Njp7aTowO3M6MTA6ImxhdWZ6ZWl0ZW4iO2k6MTtzO'
			. 'jEyOiJ2YXJjaGFyKDI1NSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IioiO2k6'
			. 'NTtzOjA6IiI7fWk6NjthOjY6e2k6MDtzOjEyOiJtYXhfbGF1ZnplaXQiO2k6MTtzOjc6ImludCg'
			. 'xMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6Nz'
			. 'thOjY6e2k6MDtzOjEyOiJmYWxsYmFja19ncnAiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6I'
			. 'k5PIjtpOjM7czowOiIiO2k6NDtzOjI6Ii0xIjtpOjU7czowOiIiO31pOjg7YTo2OntpOjA7czo1'
			. 'OiJ0aXRlbCI7aToxO3M6MTI6InZhcmNoYXIoMjU1KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI'
			. '7aTo0O047aTo1O3M6MDoiIjt9aTo5O2E6Njp7aTowO3M6MTI6ImJlc2NocmVpYnVuZyI7aToxO3'
			. 'M6NDoidGV4dCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9aToxM'
			. 'DthOjY6e2k6MDtzOjk6ImdlbG9lc2NodCI7aToxO3M6MTA6InRpbnlpbnQoNCkiO2k6MjtzOjI6'
			. 'Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6MTE7YTo2OntpOjA7czo'
			. '4OiJ0ZW1wbGF0ZSI7aToxO3M6MTI6InZhcmNoYXIoMjU1KSI7aToyO3M6MjoiTk8iO2k6MztzOj'
			. 'A6IiI7aTo0O047aTo1O3M6MDoiIjt9aToxMjthOjY6e2k6MDtzOjEyOiJhY2NlbnR1YXRpb24iO'
			. '2k6MTtzOjEwOiJ0aW55aW50KDQpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIw'
			. 'IjtpOjU7czowOiIiO31pOjEzO2E6Njp7aTowO3M6NToib3JkZXIiO2k6MTtzOjc6ImludCgxMSk'
			. 'iO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6MTQ7YT'
			. 'o2OntpOjA7czoxMDoiYWx0X3RpdGxlcyI7aToxO3M6NDoidGV4dCI7aToyO3M6MjoiTk8iO2k6M'
			. 'ztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9aToxNTthOjY6e2k6MDtzOjE2OiJhbHRfZGVzY3Jp'
			. 'cHRpb25zIjtpOjE7czo0OiJ0ZXh0IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7TjtpOjU'
			. '7czowOiIiO319czo3OiJpbmRleGVzIjthOjE6e3M6NzoiUFJJTUFSWSI7YToxOntpOjA7czoyOi'
			. 'JpZCI7fX19czoyMjoiYm02MF9tb2RfcHJlbWl1bV9wcmVmcyI7YToyOntzOjY6ImZpZWxkcyI7Y'
			. 'ToxMTp7aTowO2E6Njp7aTowO3M6MjQ6InNlbmRfdXBkYXRlX25vdGlmaWNhdGlvbiI7aToxO3M6'
			. 'MTY6ImVudW0oJ3llcycsJ25vJykiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjM6Inl'
			. 'lcyI7aTo1O3M6MDoiIjt9aToxO2E6Njp7aTowO3M6MjQ6InVwZGF0ZV9ub3RpZmljYXRpb25fZG'
			. 'F5cyI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiN'
			. 'yI7aTo1O3M6MDoiIjt9aToyO2E6Njp7aTowO3M6Mjc6InVwZGF0ZV9ub3RpZmljYXRpb25fYWx0'
			. 'bWFpbCI7aToxO3M6MTY6ImVudW0oJ3llcycsJ25vJykiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiI'
			. 'iO2k6NDtzOjI6Im5vIjtpOjU7czowOiIiO31pOjM7YTo2OntpOjA7czo2OiJmaWVsZHMiO2k6MT'
			. 'tzOjQ6InRleHQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7fWk6N'
			. 'DthOjY6e2k6MDtzOjg6ImN1cnJlbmN5IjtpOjE7czoxMToidmFyY2hhcigzMikiO2k6MjtzOjI6'
			. 'Ik5PIjtpOjM7czowOiIiO2k6NDtzOjM6IkVVUiI7aTo1O3M6MDoiIjt9aTo1O2E6Njp7aTowO3M'
			. '6MTc6InNpZ251cF9vcmRlcl9wYWdlIjtpOjE7czoxNjoiZW51bSgneWVzJywnbm8nKSI7aToyO3'
			. 'M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MzoieWVzIjtpOjU7czowOiIiO31pOjY7YTo2OntpO'
			. 'jA7czoxODoic2lnbnVwX29yZGVyX2ZvcmNlIjtpOjE7czoxNjoiZW51bSgneWVzJywnbm8nKSI7'
			. 'aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6Mjoibm8iO2k6NTtzOjA6IiI7fWk6NzthOjY'
			. '6e2k6MDtzOjE4OiJkZWxldGVfb3JkZXJfYWZ0ZXIiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOj'
			. 'I6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjc6IjI2Nzg0MDAiO2k6NTtzOjA6IiI7fWk6ODthOjY6e'
			. '2k6MDtzOjEyOiJkZWxldGVfb3JkZXIiO2k6MTtzOjE2OiJlbnVtKCd5ZXMnLCdubycpIjtpOjI7'
			. 'czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czozOiJ5ZXMiO2k6NTtzOjA6IiI7fWk6OTthOjY6e2k'
			. '6MDtzOjEyOiJmaWVsZHNfb3JkZXIiO2k6MTtzOjQ6InRleHQiO2k6MjtzOjI6Ik5PIjtpOjM7cz'
			. 'owOiIiO2k6NDtOO2k6NTtzOjA6IiI7fWk6MTA7YTo2OntpOjA7czoxNzoibmxpX3BhY2thZ2VzX'
			. '3BhZ2UiO2k6MTtzOjI2OiJlbnVtKCd5ZXMnLCdubycsJ3JlcGxhY2UnKSI7aToyO3M6MjoiTk8i'
			. 'O2k6MztzOjA6IiI7aTo0O3M6MzoieWVzIjtpOjU7czowOiIiO319czo3OiJpbmRleGVzIjthOjA'
			. '6e319czoyODoiYm02MF9tb2RfcHJlbWl1bV9zdWJzY3JpYmVycyI7YToyOntzOjY6ImZpZWxkcy'
			. 'I7YTo2OntpOjA7YTo2OntpOjA7czoyOiJpZCI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiT'
			. 'k8iO2k6MztzOjM6IlBSSSI7aTo0O047aTo1O3M6MTQ6ImF1dG9faW5jcmVtZW50Ijt9aToxO2E6'
			. 'Njp7aTowO3M6ODoiYmVudXR6ZXIiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM'
			. '7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7fWk6MjthOjY6e2k6MDtzOjU6InBha2V0IjtpOjE7cz'
			. 'o3OiJpbnQoMTEpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7TjtpOjU7czowOiIiO31pO'
			. 'jM7YTo2OntpOjA7czoxNDoibGV0enRlX3phaGx1bmciO2k6MTtzOjc6ImludCgxMSkiO2k6Mjtz'
			. 'OjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7fWk6NDthOjY6e2k6MDtzOjY6ImF'
			. 'ibGF1ZiI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aT'
			. 'o1O3M6MDoiIjt9aTo1O2E6Njp7aTowO3M6MjQ6Imxhc3RfdXBkYXRlX25vdGlmaWNhdGlvbiI7a'
			. 'ToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoi'
			. 'Ijt9fXM6NzoiaW5kZXhlcyI7YToxOntzOjc6IlBSSU1BUlkiO2E6MTp7aTowO3M6MjoiaWQiO31'
			. '9fXM6MjU6ImJtNjBfbW9kX3ByZW1pdW1fdXNlcmF1dGgiO2E6Mjp7czo2OiJmaWVsZHMiO2E6Mz'
			. 'p7aTowO2E6Njp7aTowO3M6NjoidXNlcmlkIjtpOjE7czo3OiJpbnQoMTEpIjtpOjI7czoyOiJOT'
			. 'yI7aTozO3M6MzoiUFJJIjtpOjQ7TjtpOjU7czowOiIiO31pOjE7YTo2OntpOjA7czo1OiJ0b2tl'
			. 'biI7aToxO3M6MTE6InZhcmNoYXIoMzIpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7Tjt'
			. 'pOjU7czowOiIiO31pOjI7YTo2OntpOjA7czo0OiJkYXRlIjtpOjE7czo3OiJpbnQoMTEpIjtpOj'
			. 'I7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7TjtpOjU7czowOiIiO319czo3OiJpbmRleGVzIjthO'
			. 'jE6e3M6NzoiUFJJTUFSWSI7YToxOntpOjA7czo2OiJ1c2VyaWQiO319fX0=';
		$databaseStructure = unserialize(base64_decode($databaseStructure));

		// sync struct
		SyncDBStruct($databaseStructure);

		// prefs row?
		$res = $db->Query('SELECT COUNT(*) FROM {pre}mod_premium_prefs');
		list($rowCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		if($rowCount < 1)
		{
			// insert prefs row
			$db->Query('INSERT INTO {pre}mod_premium_prefs(`fields`) VALUES(?)',
				'a:13:{i:0;s:7:"storage";i:1;s:7:"maxsize";i:2;s:4:"pop3";i:3;s:7:"anlagen";i:4;s:7:"webdisk";i:5;s:8:"mail2sms";i:7;s:3:"wap";i:8;s:6:"aliase";i:9;s:10:"send_limit";i:10;s:3:"ads";i:11;s:4:"imap";i:12;s:6:"webdav";i:13;s:4:"smtp";}');
		}
		else
		{
			// remove deprecated fields
			$res = $db->Query('SELECT `fields` FROM {pre}mod_premium_prefs');
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$fields = @unserialize($row['fields']);
				if(!is_array($fields))
					continue;
				$fields = array_diff($fields, array('httpmail'));
				$db->Query('UPDATE {pre}mod_premium_prefs SET `fields`=?',
					serialize($fields));
			}
			$res->Free();
		}

		// log
		PutLog(sprintf('%s v%s installed',
			$this->name,
			$this->version),
			PRIO_PLUGIN,
			__FILE__,
			__LINE__);

		return(true);
	}

	/**
	 * admin handler
	 *
	 */
	function AdminHandler()
	{
		global $tpl, $plugins, $lang_admin, $bm_prefs;

		if(!isset($_REQUEST['action']))
			$_REQUEST['action'] = 'overview';

		$tabs = array(
			0 => array(
				'title'		=> $lang_admin['overview'],
				'icon'		=> '../plugins/templates/images/pacc_logo.png',
				'link'		=> $this->_adminLink() . '&',
				'active'	=> $_REQUEST['action'] == 'overview'
			),
			1 => array(
				'title'		=> $lang_admin['pacc_packages'],
				'icon'		=> '../plugins/templates/images/pacc_packages32.png',
				'link'		=> $this->_adminLink() . '&action=packages&',
				'active'	=> $_REQUEST['action'] == 'packages'
			),
			2 => array(
				'title'		=> $lang_admin['pacc_subscriptions'],
				'icon'		=> '../plugins/templates/images/pacc_subscriptions.png',
				'link'		=> $this->_adminLink() . '&action=subscriptions&',
				'active'	=> $_REQUEST['action'] == 'subscriptions'
			),
			3 => array(
				'title'		=> $lang_admin['prefs'],
				'icon'		=> '../plugins/templates/images/pacc_prefs32.png',
				'link'		=> $this->_adminLink() . '&action=prefs&',
				'active'	=> $_REQUEST['action'] == 'prefs'
			)
		);

		$tpl->assign('tabHeaderText',	'PremiumAccount');
		$tpl->assign('tabs', 			$tabs);
		$tpl->assign('pageURL', 		$this->_adminLink());
		$tpl->assign('pacc_prefs', 		$this->prefs);
		$tpl->assign('usertpldir', 		B1GMAIL_REL . 'templates/' . $bm_prefs['template'] . '/');

		if($_REQUEST['action'] == 'overview')
			$this->_overviewPage();
		else if($_REQUEST['action'] == 'packages')
			$this->_packagesPage();
		else if($_REQUEST['action'] == 'subscriptions')
			$this->_subscriptionsPage();
		else if($_REQUEST['action'] == 'prefs')
			$this->_prefsPage();
	}

	/**
	 * cron tasks
	 *
	 */
	function OnCron()
	{
		global $db, $lang_custom, $lang_admin, $bm_prefs;

		// clean up user auth tokens
		$db->Query('DELETE FROM {pre}mod_premium_userauth WHERE date<?',
			time()-TIME_ONE_DAY);

		// delete unfinished orders
		if($this->prefs['delete_order'] == 'yes')
		{
			$res = $db->Query('SELECT `orderid` FROM {pre}orders WHERE `status`=0 AND `created`<' . (time()-$this->prefs['delete_order_after']));
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$db->Query('DELETE FROM {pre}orders WHERE `orderid`=?',
						   $row['orderid']);
				$db->Query('DELETE FROM {pre}invoices WHERE `orderid`=?',
						   $row['orderid']);
			}
			$res->Free();
		}

		// send notifications
		if($this->prefs['send_update_notification'] == 'yes')
		{
			$expireTimeframe = $this->prefs['update_notification_days'] * TIME_ONE_DAY;
			$expireDate = time() + $expireTimeframe;
			$sentIDs = array();

			// search for subscriptions
			$res = $db->Query('SELECT id,benutzer,paket FROM {pre}mod_premium_subscribers WHERE ablauf<' . $expireDate . ' AND last_update_notification<(ablauf-' . $expireTimeframe . ') AND ablauf>1');
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				// get user details
				$userRes = $db->Query('SELECT email,vorname,nachname,altmail,`language` FROM {pre}users WHERE id=? AND gesperrt=\'no\'',
					$row['benutzer']);
				if($userRes->RowCount() != 1)
					continue;
				$userRow = $userRes->FetchArray(MYSQLI_ASSOC);
				$userRes->Free();
				if(trim($userRow['language']) == '')
					$userRow['language'] = $bm_prefs['language'];

				// get package details
				$packageRes = $db->Query('SELECT titel,preis_cent,abrechnung,abrechnung_t,`alt_titles` FROM {pre}mod_premium_packages WHERE id=?',
					$row['paket']);
				$package = $packageRes->FetchArray(MYSQLI_ASSOC);
				$packageRes->Free();

				// prepare mail
				$vars = array(
					'email'			=> function_exists('DecodeEMail') ? DecodeEMail($userRow['email']) : $userRow['email'],
					'vorname'		=> $userRow['vorname'],
					'nachname'		=> $userRow['nachname'],
					'paketname'		=> $this->_getTranslatedText($package['titel'], $package['alt_titles'], $userRow['language']),
					'betrag'		=> sprintf('%.02f %s', $this->_calcOrderAmount($row['paket'], $package['abrechnung_t'])/100, $bm_prefs['currency']),
					'zeit'			=> $this->_intervalStr($package['abrechnung'], $package['abrechnung_t'], true)
				);

				// send mail to account email address
				if(SystemMail(sprintf('"%s" <%s>', $bm_prefs['pay_emailfrom'], $bm_prefs['pay_emailfromemail']),
					$userRow['email'],
					GetPhraseForUser($row['benutzer'], 'lang_custom', 'pacc_nm_subject'),
					'pacc_nm_text',
					$vars,
					$row['benutzer']))
				{
					$sentIDs[] = $row['id'];
				}

				// also send to alternative email address, if setting enabled
				if(!empty($userRow['altmail']) && $this->prefs['update_notification_altmail'] == 'yes')
				{
					SystemMail(sprintf('"%s" <%s>', $bm_prefs['pay_emailfrom'], $bm_prefs['pay_emailfromemail']),
						$userRow['altmail'],
						GetPhraseForUser($row['benutzer'], 'lang_custom', 'pacc_nm_subject'),
						'pacc_nm_text',
						$vars,
						$row['benutzer']);
				}
			}
			$res->Free();

			// update last notification dates
			if(count($sentIDs) > 0)
				$db->Query('UPDATE {pre}mod_premium_subscribers SET last_update_notification=? WHERE id IN ?',
					time(),
					$sentIDs);
		}

		// delete expired subscriptions
		$deleteSubscriptions = array();
		$res = $db->Query('SELECT id,benutzer,paket FROM {pre}mod_premium_subscribers WHERE ablauf>1 AND ablauf<' . time());
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			// get package details
			$packageRes = $db->Query('SELECT fallback_grp,`gruppe` FROM {pre}mod_premium_packages WHERE id=?',
				$row['paket']);
			$package = $packageRes->FetchArray(MYSQLI_ASSOC);
			$packageRes->Free();

			// get current user group
			$userGroup = -1;
			$userRes = $db->Query('SELECT `gruppe` FROM {pre}users WHERE `id`=?',
				$row['benutzer']);
			if($userRes->RowCount() == 1)
			{
				$userRow = $userRes->FetchArray(MYSQLI_ASSOC);
				$userGroup = $userRow['gruppe'];
			}
			$userRes->Free();

			if($package['fallback_grp'] == -1)
				$db->Query('UPDATE {pre}users SET gesperrt=?,notes=CONCAT(notes,?) WHERE id=?',
					'locked',
					sprintf($lang_admin['pacc_expire_locked_note'], date('r'), $row['id']),
					$row['benutzer']);
			else if($userGroup == -1 || $userGroup == $package['gruppe'])
				$db->Query('UPDATE {pre}users SET gruppe=?,notes=CONCAT(notes,?) WHERE id=?',
					$package['fallback_grp'],
					sprintf($lang_admin['pacc_expire_moved_note'], date('r'), $package['fallback_grp'], $row['id']),
					$row['benutzer']);
			else
				$db->Query('UPDATE {pre}users SET notes=CONCAT(notes,?) WHERE id=?',
					sprintf($lang_admin['pacc_expire_notmoved_note'], date('r'), $package['fallback_grp'], $row['id']),
					$row['benutzer']);

			PutLog(sprintf('PremiumAccount: Subscription #%d of user #%d has expired',
				$row['id'],
				$row['benutzer']),
				PRIO_NOTE,
				__FILE__,
				__LINE__);

			$deleteSubscriptions[] = $row['id'];
		}
		$res->Free();
		if(count($deleteSubscriptions) > 0)
			$db->Query('DELETE FROM {pre}mod_premium_subscribers WHERE id IN ?',
				$deleteSubscriptions);

		// delete packages marked for deletion, if possible
		$deletePackages = array();
		$res = $db->Query('SELECT id FROM {pre}mod_premium_packages WHERE geloescht=1');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$subRes = $db->Query('SELECT COUNT(*) FROM {pre}mod_premium_subscribers WHERE paket=?',
				$row['id']);
			list($subCount) = $subRes->FetchArray(MYSQLI_NUM);
			$subRes->Free();

			if($subCount == 0)
				$deletePackages[] = $row['id'];
		}
		$res->Free();
		if(count($deletePackages) > 0)
			$db->Query('DELETE FROM {pre}mod_premium_packages WHERE id IN ?',
				$deletePackages);
	}

	/**
	 * BeforePageTabsAssign, abused as Template::display hook
	 *
	 * @param array $tabs Tabs (not needed here)
	 */
	function BeforePageTabsAssign(&$tabs)
	{
		global $userRow, $tpl;

		if(ADMIN_MODE || !isset($tpl) || !isset($userRow) || !is_array($userRow))
			return;

		if(count($subscriptions = $this->_getUserSubscriptions($userRow['id'])) > 0)
		{
			$activeSubscription = array_pop($subscriptions);

			if($activeSubscription['package']['template'] != '')
			{
				$template = $activeSubscription['package']['template'];
				if(file_exists(B1GMAIL_DIR . 'templates/' . $template . '/'))
				{
					$tpl->template_dir 	= B1GMAIL_DIR . 'templates/' . $template . '/';
					$tpl->compile_dir 	= B1GMAIL_DIR . 'templates/' . $template . '/cache/';
					$tpl->assign('tpldir', 	B1GMAIL_REL . 'templates/' . $template . '/');
					$tpl->assign('_tpldir',	'templates/' . $template . '/');
				}
			}
		}
	}

	/**
	 * user pages
	 *
	 * @param bool $loggedin
	 * @return array
	 */
	function getUserPages($loggedin)
	{
		global $lang_user;

		if($loggedin || $this->prefs['nli_packages_page'] != 'yes' || !$this->_packagesAvailable())
			return array();

		return(array(
			'paccPackages' => array(
				'text'	=> $lang_user['pacc_packages'],
				'link'	=> 'index.php?action=paccPackages',
				'top'	=> true,
				'active'=> isset($_REQUEST['action']) && $_REQUEST['action'] == 'paccPackages',
				'after'	=> 'login'
			)
		));
	}

	/**
	 * packages page for NLI area
	 *
	 */
	function _paccNLIPackages($signupMode = false)
	{
		global $tpl, $bm_prefs, $lang_user;

		if($this->prefs['nli_packages_page'] == 'no' || !$this->_packagesAvailable())
			return;

		$countries = CountryList(true);
		$vatRate = $countries[$bm_prefs['std_land']]['vat'];

		$versionComponents = explode('.', B1GMAIL_VERSION);
		$b1gMail74orLater = ($versionComponents[0] > 7 || ($versionComponents[0] == 7 && $versionComponents[1] >= 4));

		$tpl->register_function('paccFormatField', array(&$this, '_smartyPaccFormatField'));
		$tpl->assign('signUp',		$signupMode);
		$tpl->assign('nliPackages',	true);
		$tpl->assign('regEnabled', 	$bm_prefs['regenabled'] == 'yes' && $b1gMail74orLater);
		$tpl->assign('page', 		$this->_templatePath('pacc.nli.packages.tpl'));
		$tpl->assign('matrix', 		$this->_packageMatrix($vatRate));
		$tpl->assign('pageTitle', 	$signupMode ? $lang_user['signup'] : $lang_user['pacc_packages']);
		$tpl->display('nli/index.tpl');
	}

	/**
	 * get a translated text using the fallback text and an array of alt texts (lang => val)
	 *
	 * @param $fallback Fallback text
	 * @param $alt Alt array
	 * @param $lang Language (if false, the current language is used)
	 * @return string
	 */
	function _getTranslatedText($fallback, $alt, $lang = false)
	{
		global $currentLanguage;
		if($lang === false) $lang = $currentLanguage;

		$alt = @unserialize($alt);
		if(is_array($alt) && isset($alt[$lang]))
			return $alt[$lang];

		return $fallback;
	}

	/**
	 * get package with given ID
	 *
	 * @param int $packageID
	 * @return array
	 */
	function _getPackage($packageID)
	{
		global $db, $bm_prefs;

		$package = false;

		$res = $db->Query('SELECT id,titel,beschreibung,abrechnung,abrechnung_t,preis_cent,laufzeiten,max_laufzeit,`alt_titles`,`alt_descriptions` FROM {pre}mod_premium_packages WHERE id=? AND geloescht=0',
			$packageID);
		if($res->RowCount() == 1)
		{
			$row = $res->FetchArray(MYSQLI_ASSOC);

			$countries = CountryList(true);
			$vatRate = $countries[$bm_prefs['std_land']]['vat'];

			// build price string
			$price = sprintf('%.02f %s',
				$this->_taxedPrice($row['preis_cent']/100, $vatRate),
				$bm_prefs['currency']);

			$package = array(
				'id'			=> $row['id'],
				'title'			=> $this->_getTranslatedText($row['titel'], $row['alt_titles']),
				'description'	=> $this->_getTranslatedText($row['beschreibung'], $row['alt_descriptions']),
				'price'			=> $price,
				'priceTax'		=> $this->_taxStr($vatRate),
				'priceInterval'	=> $this->_intervalStr($row['abrechnung'], $row['abrechnung_t']),
				'isFree'		=> $row['preis_cent'] == 0
			);

			$res->Free();
		}

		return $package;
	}

	/**
	 * file handler
	 *
	 * @param string $file
	 * @param string $action
	 */
	function FileHandler($file, $action)
	{
		global $tpl, $userRow, $lang_user;

		if($file=='index.php' && $action=='signup')
		{
			if(isset($_REQUEST['paccPackage'])
				&& ($package = $this->_getPackage($_REQUEST['paccPackage'])) !== false)
			{
				$tpl->registerHook('nli:signup.tpl:panelGroupStart',
					B1GMAIL_DIR . 'plugins/templates/pacc.nli.hook.signup.tpl');
				$tpl->assign('paccPackage',	$package);
				$tpl->assign('signupText', 	$lang_user['pacc_signuptext']);
			}
			else if($this->prefs['nli_packages_page'] == 'replace')
			{
				$this->_paccNLIPackages(true);
				exit();
			}
		}

		if($file=='index.php' && $action=='paccOrder')
		{
			$this->_paccOrder();
			exit();
		}
		if($file=='index.php' && $action=='paccPlaceOrder')
		{
			$this->_paccPlaceOrder();
			exit();
		}
		if($file=='index.php' && $action=='paccPaymentReturn')
		{
			$tpl->assign('msg', 	$lang_user['pacc_paymentreturn2']);
			$tpl->assign('page', 	'nli/regdone.tpl');
			$tpl->display('nli/index.tpl');
			exit();
		}
		else if($file=='index.php' && $action=='paccPackageDetails'
			&& isset($_REQUEST['id']))
		{
			$this->_showPackageDetails($_REQUEST['id']);
			exit();
		}
		else if($file=='index.php' && $action=='paccPackages')
		{
			$this->_paccNLIPackages();
			exit();
		}
		else if($file=='prefs.php'
			&& isset($userRow) && is_array($userRow))
		{
			if($action=='paccPackageDetails'
				&& isset($_REQUEST['id']))
			{
				$this->_showPackageDetails($_REQUEST['id']);
				exit();
			}

			if($this->_packagesAvailable()
				|| count($this->_getUserSubscriptions($userRow['id'])) > 0)
			{
				$GLOBALS['prefsItems']['pacc_mod'] = true;
				$GLOBALS['prefsImages']['pacc_mod'] = 'plugins/templates/images/pacc_subscriptions_user.png';
				$GLOBALS['prefsIcons']['pacc_mod'] = 'plugins/templates/images/pacc_subscriptions_user.png';
			}

			if($action == 'membership'
				&& isset($_REQUEST['do'])
				&& $_REQUEST['do'] == 'cancelAccount'
				&& !isset($_REQUEST['paccContinue']))
			{
				if(count($subscriptions = $this->_getUserSubscriptions($userRow['id'])) > 0)
				{
					$_REQUEST['action'] = 'pacc_mod';
					$_REQUEST['do']		= 'cancelAccountWarning';
				}
			}
		}
	}

	/**
	 * user account prefs page handler
	 *
	 * @param string $action Action
	 * @return bool
	 */
	function UserPrefsPageHandler($action)
	{
		global $db, $tpl, $userRow, $lang_user, $bm_prefs;
		if($action != 'pacc_mod')
			return(false);

		$tpl->assign('pacc_prefs',		$this->prefs);

		// no action => overview page
		if(!isset($_REQUEST['do']))
		{
			// page output
			$tpl->register_function('paccFormatField', array(&$this, '_smartyPaccFormatField'));
			if(count($subscriptions = $this->_getUserSubscriptions($userRow['id'])) > 0)
				$tpl->assign('activeSubscription',	array_pop($subscriptions));
			$tpl->assign('poHeight',		150+25*count(unserialize($this->prefs['fields'])));
			$tpl->assign('matrix', 			$this->_packageMatrix($this->_vatRateForUser($userRow['id'])));
			$tpl->assign('pageContent', 	$this->_templatePath('pacc.user.overview.tpl'));
			$tpl->display('li/index.tpl');
		}

		// cancel account warning
		else if($_REQUEST['do'] == 'cancelAccountWarning')
		{
			if(count($subscriptions = $this->_getUserSubscriptions($userRow['id'])) < 1)
				exit();

			$tpl->assign('activeSubscription',	array_pop($subscriptions));
			$tpl->assign('pageContent',			$this->_templatePath('pacc.user.cancelwarning.tpl'));
			$tpl->display('li/index.tpl');
		}

		// order page
		else if($_REQUEST['do'] == 'order'
			&& isset($_REQUEST['id']))
		{
			$this->_prepareOrderPage($userRow['id'], (int)$_REQUEST['id'], $userRow);
			$tpl->assign('pageContent', 	$this->_templatePath('pacc.user.order.tpl'));
			$tpl->display('li/index.tpl');
		}

		// place order
		else if($_REQUEST['do'] == 'placeOrder'
			&& isset($_REQUEST['id']))
		{
			$packageID = (int)$_REQUEST['id'];

			if(!$this->_initiateOrderPlacement($userRow['id'], $packageID, $userRow))
				$tpl->assign('pageContent', 	$this->_templatePath('pacc.user.order.tpl'));
			$tpl->display('li/index.tpl');
		}

		return(true);
	}

	/**
	 * signup handler
	 *
	 * @param int $userID
	 * @param string $userMail
	 */
	function AfterSuccessfulSignup($userID, $userMail)
	{
		global $db, $lang_admin;

		if($this->prefs['signup_order_page'] == 'yes'
			|| (isset($_REQUEST['paccPackage']) && $this->prefs['nli_packages_page'] != 'no'))
		{
			// force order? => lock user until order is completed
			if($this->prefs['signup_order_force'] == 'yes')
			{
				$db->Query('UPDATE {pre}users SET gesperrt=?,notes=CONCAT(notes,?) WHERE id=?',
					'locked',
					sprintf($lang_admin['pacc_locked_at_signup'], date('r')) . "\n",
					$userID);
			}

			if(isset($_REQUEST['paccPackage']))
			{
				$packageID = (int)$_REQUEST['paccPackage'];
				if($this->_getPackage($packageID) !== false)
				{
					$db->Query('REPLACE INTO {pre}mod_premium_userauth(`userid`,`token`,`date`) VALUES(?,?,?)',
						$userID,
						$userToken = GenerateRandomKey('PAccUserToken'),
						time());
					$_REQUEST['userID'] 	= $userID;
					$_REQUEST['userToken'] 	= $userToken;
					$_REQUEST['signUp']		= true;
					$_REQUEST['doOrder']	= true;
					$_REQUEST['package']	= $packageID;
					$this->_paccOrder();
					exit();
				}
			}

			$this->_nliPackagesPage($userID, true);
		}
	}

	/**
	 * failed login handler
	 *
	 * @param string $userMail
	 * @param string $password
	 * @param int $reason
	 */
	function OnLoginFailed($userMail, $password, $reason)
	{
		if($this->prefs['signup_order_force'] == 'yes'
			&& $this->prefs['signup_order_page'] == 'yes'
			&& $reason == BM_LOCKED)
		{
			$user = _new('BMUser', array(BMUser::GetID($userMail, true)));
			if($user->_row['gesperrt'] == 'locked')
			{
				$this->_nliPackagesPage($user->_row['id'], false);
			}
		}
	}

	/**
	 * return title for field
	 *
	 * @param string $name Field name
	 * @return string
	 */
	function _fieldTitle($name)
	{
		global $lang_admin, $plugins;

		$aliases = array(
			'maxsize'				=> 'emailin',
			'soforthtml'			=> 'htmlview',
			'anlagen'				=> 'emailout',
			'sms_sig'				=> 'smssig',
			'sms_monat'				=> 'monthasset',
			'responder'				=> 'autoresponder',
			'titel'					=> 'title',
			'signatur'				=> 'mailsig',
			'wap'					=> 'mobileaccess',
			'aliase'				=> 'aliases',
			'send_limit'			=> 'sendlimit',
			'checker'				=> 'mailchecker',
			'sms_from'				=> 'smsfrom',
			'sms_ownfrom'			=> 'ownfrom',
			'sms_pre'				=> 'smspre',
			'traffic'				=> 'wdtraffic',
			'saliase'				=> 'aliasdomains',
			'sms_price_per_credit'	=> 'creditprice',
			'share'					=> 'wdshare',
			'wd_open_kbs'			=> 'sharespeed',
			'wd_member_kbs'			=> 'wdspeed',
			'ownpop3_interval'		=> 'ownpop3interval',
			'ads'					=> 'pacc_adfree',
			'max_recps'				=> 'maxrecps',
			'maildeliverystatus'	=> 'deliverystatus',
			'send_limit_count'		=> 'pacc_send_limit_count',
			'send_limit_time'		=> 'pacc_send_limit_time'
		);

		$groupOptions = $plugins->GetGroupOptions();

		if(isset($aliases[$name]))
			$name = $aliases[$name];
		else if(isset($groupOptions[$name]))
			return(preg_replace('/[\:\?]$/', '', $groupOptions[$name]['desc']));

		if(isset($lang_admin[$name]))
			return($lang_admin[$name]);
		else
			return('#UNKNOWN_FIELD(' . $name . ')#');
	}

	/**
	 * fetch DB prefs
	 *
	 * @return array
	 */
	function _getPrefs()
	{
		global $db;

		$res = $db->Query('SELECT * FROM {pre}mod_premium_prefs LIMIT 1');
		$prefs = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		return($prefs);
	}

	/**
	 * input price to cent price
	 *
	 * @param string $in Input
	 * @return int
	 */
	function _formCentPrice($in)
	{
		$result = (float)str_replace(',', '.', $in);
		$result = (int)((float)$result * 100.0);
		return($result);
	}

	/**
	 * build interval string
	 *
	 * @param string $abrechnung
	 * @param int $abrechnung_t
	 * @param bool $withoutPrefix
	 * @return string
	 */
	function _intervalStr($abrechnung, $abrechnung_t, $withoutPrefix = false)
	{
		global $lang_user;

		if($abrechnung_t == 1)
		{
			switch($abrechnung)
			{
			case 'wochen':
				return ($withoutPrefix ? '1 ' : $lang_user['pacc_jede'] . ' ') . $lang_user['pacc_woche'];
				break;
			case 'monate':
				return ($withoutPrefix ? '1 ' : $lang_user['pacc_jeden'] . ' ') . $lang_user['pacc_monat'];
				break;
			case 'jahre':
				return ($withoutPrefix ? '1 ' : $lang_user['pacc_jedes'] . ' ') . $lang_user['pacc_jahr'];
				break;
			case 'einmalig':
				return $lang_user['pacc_einmalig'];
				break;
			}
		}
		else
		{
			switch($abrechnung)
			{
			case 'wochen':
				return ($withoutPrefix ? '' : $lang_user['pacc_alle'] . ' ') . $abrechnung_t . ' ' . $lang_user['pacc_wochen'];
				break;
			case 'monate':
				return ($withoutPrefix ? '' : $lang_user['pacc_alle'] . ' ') . $abrechnung_t . ' ' . $lang_user['pacc_monate'];
				break;
			case 'jahre':
				return ($withoutPrefix ? '' : $lang_user['pacc_alle'] . ' ') . $abrechnung_t . ' ' . $lang_user['pacc_jahre'];
				break;
			case 'einmalig':
				return $lang_user['pacc_einmalig'];
				break;
			}
		}
	}

	/**
	 * build tax string
	 *
	 * @return string
	 */
	function _taxStr($vatRate)
	{
		global $lang_user, $bm_prefs;

		if($bm_prefs['mwst'] == 'nomwst')
			return('');
		else
			return(sprintf('%s %.02f%% %s',
				$lang_user['pacc_incl'],
				$vatRate,
				$lang_user['pacc_vat']));
	}

	/**
	 * build taxed price
	 *
	 * @return double
	 */
	function _taxedPrice($price, $vatRate)
	{
		global $lang_user, $bm_prefs;

		if($bm_prefs['mwst'] == 'nomwst' || $bm_prefs['mwst'] == 'enthalten')
			return($price);
		else
			return(round($price * (1 + $vatRate/100), 2));
	}

	/**
	 * generate package matrix
	 *
	 * @param int $id Package ID or 0 for all packages
	 * @return array
	 */
	function _packageMatrix($vatRate, $id=0)
	{
		global $db, $lang_admin, $lang_user, $plugins, $bm_prefs;

		$result = array('fields' => array(), 'packages' => array());

		// unserialize fields
		$fields = array();
		$_fields = @unserialize($this->prefs['fields']);
		$fieldsOrder = @unserialize($this->prefs['fields_order']);
		if(!is_array($_fields))
			$_fields = array();
		if(!is_array($fieldsOrder))
		{
			$i = 0;
			$fieldsOrder = array();
			foreach($_fields as $val)
				$fieldsOrder[$val] = ++$i;
		}
		asort($fieldsOrder);
		foreach($fieldsOrder as $key=>$val)
			if(in_array($key, $_fields))
				$fields[] = $key;

		// add fields
		foreach($fields as $fieldKey)
			$result['fields'][$fieldKey] = $this->_fieldTitle($fieldKey);

		// get packages
		$res = $db->Query('SELECT id,titel,abrechnung,abrechnung_t,preis_cent,beschreibung,gruppe,accentuation,`alt_titles`,`alt_descriptions` FROM {pre}mod_premium_packages WHERE geloescht=0 '.($id!=0?'AND id='.(int)$id.' ':'').'ORDER BY `order` ASC,preis_cent ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			// get fields
			$fields = array();
			$groupRes = $db->Query('SELECT * FROM {pre}gruppen WHERE id=?',
				$row['gruppe']);
			if($groupRes->RowCount() == 0)
				continue;
			while($groupRow = $groupRes->FetchArray(MYSQLI_ASSOC))
			{
				$timeUnit = $lang_user['pacc_minutes'];
				if($groupRow['send_limit_time'] >= 60 && $groupRow['send_limit_time']%60 == 0)
				{
					$timeUnit = $lang_user['pacc_hours'];
					$groupRow['send_limit_time'] /= 60;
				}
				$groupRow['send_limit'] = sprintf($lang_user['pacc_sendlimit'],
					$groupRow['send_limit_count'],
					$groupRow['send_limit_time'],
					$timeUnit);
				$fields = $groupRow;
			}
			$groupRes->Free();

			// group options
			$groupOptions = $plugins->GetGroupOptions($row['gruppe']);
			foreach($groupOptions as $optionKey=>$optionInfo)
				$fields[$optionKey] = $optionInfo['value'];

			// build price string
			$price = sprintf('%.02f %s',
				$this->_taxedPrice($row['preis_cent']/100, $vatRate),
				$bm_prefs['currency']);

			// push to array
			$result['packages'][$row['id']] = array(
				'id'			=> $row['id'],
				'title'			=> $this->_getTranslatedText($row['titel'], $row['alt_titles']),
				'description'	=> $this->_getTranslatedText($row['beschreibung'], $row['alt_descriptions']),
				'price'			=> $price,
				'priceTax'		=> $this->_taxStr($vatRate),
				'priceInterval'	=> $this->_intervalStr($row['abrechnung'], $row['abrechnung_t']),
				'isFree'		=> $row['preis_cent'] == 0,
				'fields'		=> $fields,
				'accentuation'	=> $row['accentuation']
			);
		}
		$res->Free();

		return($result);
	}

	/**
	 * show package details page
	 *
	 * @param int $id Package ID
	 */
	function _showPackageDetails($id)
	{
		global $tpl, $db, $userRow, $bm_prefs;

		$vatRate = 0;
		if(isset($userRow))
		{
			$vatRate = $this->_vatRateForUser($userRow['id']);
		}
		else
		{
			$countries = CountryList(true);
			$vatRate = $countries[$bm_prefs['std_land']]['vat'];
		}

		$res = $db->Query('SELECT id,titel,beschreibung,geloescht,`alt_titles`,`alt_descriptions` FROM {pre}mod_premium_packages WHERE id=? AND geloescht=0',
			$id);
		if($res->RowCount() == 1)
		{
			$row = $res->FetchArray(MYSQLI_ASSOC);
			$res->Free();

			$row['titel'] 			= $this->_getTranslatedText($row['titel'], $row['alt_titles']);
			$row['beschreibung']	= $this->_getTranslatedText($row['beschreibung'], $row['alt_descriptions']);

			$tpl->register_function('paccFormatField', array(&$this, '_smartyPaccFormatField'));
			$tpl->assign('package', 	$row);
			$tpl->assign('matrix', 		$this->_packageMatrix($vatRate, (int)$id));
			$tpl->display($this->_templatePath('pacc.user.package.tpl'));
		}
		else
			die('Invalid package');
	}

	/**
	 * activate order
	 *
	 * @param int $packageID Package ID
	 * @param int $count Paid intervals
	 * @param int $orderID Order ID
	 * @param int $userID User ID
	 * @return bool Success
	 */
	function _activateOrder($packageID, $count, $orderID, $userID)
	{
		global $db, $lang_admin;

		// fetch package
		$res = $db->Query('SELECT gruppe,abrechnung,abrechnung_t FROM {pre}mod_premium_packages WHERE id=?',
			$packageID);
		if($res->RowCount() == 0)
		{
			PutLog(sprintf('PremiumAccount: ActivateOrderItem(%d, %d): Associated package not found',
				$orderID,
				$userID),
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return(false);
		}
		$package = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// calculate expiration date
		$expire = -1;
		if($package['abrechnung'] != 'einmalig')
		{
			$intervals = array('wochen' => TIME_ONE_WEEK, 'monate' => TIME_ONE_MONTH, 'jahre' => TIME_ONE_YEAR);
			$expire = time() + ($count * $package['abrechnung_t'] * $intervals[$package['abrechnung']]);
		}

		// active subscription?
		$deleteSubscriptions = array();
		$res = $db->Query('SELECT id,paket,ablauf FROM {pre}mod_premium_subscribers WHERE benutzer=?',
			$userID);
		while($subscription = $res->FetchArray(MYSQLI_ASSOC))
		{
			if($subscription['paket'] == $packageID
				&& $expire != -1)
				$expire += max(0, $subscription['ablauf'] - time());
			$deleteSubscriptions[] = $subscription['id'];
		}
		$res->Free();

		// delete former active subscriptions
		if(count($deleteSubscriptions) > 0)
		{
			$db->Query('DELETE FROM {pre}mod_premium_subscribers WHERE id IN(?) AND benutzer=?',
				$deleteSubscriptions,
				$userID);
		}

		// create subscription
		$db->Query('INSERT INTO {pre}mod_premium_subscribers(benutzer,paket,letzte_zahlung,ablauf,last_update_notification) '
			. 'VALUES(?,?,?,?,?)',
			$userID,
			$packageID,
			time(),
			$expire,
			0);

		// move user to the group he paid for
		$db->Query('UPDATE {pre}users SET gruppe=?,notes=CONCAT(notes,?),gesperrt=? WHERE id=?',
			$package['gruppe'],
			sprintf($lang_admin['pacc_moved_note'], date('r'), $package['gruppe'], $orderID) . "\n",
			'no',
			$userID);

		// log
		if($orderID == -1)
		{
			PutLog('PremiumAccount: Order of free package activated successfuly',
				PRIO_NOTE,
				__FILE__,
				__LINE__);
		}
		else
		{
			PutLog(sprintf('PremiumAccount: Order #%d activated successfuly',
				$orderID),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
		}

		return(true);
	}

	/**
	 * order activation callback
	 *
	 * @param int $orderID Order ID
	 * @param int $userID User ID
	 * @param array $cartItem Cart item
	 * @return bool Handled?
	 */
	function ActivateOrderItem($orderID, $userID, $cartItem)
	{
		if(substr($cartItem['key'], 0, 10) != 'PAcc.order')
			return(false);

		list(,, $packageID) = explode('.', $cartItem['key']);
		$count = $cartItem['count'];

		return($this->_activateOrder($packageID, $count, $orderID, $userID));
	}

	/**
	 * prepare customer order page
	 *
	 * @param int $userID User ID
	 * @param int $packageID Package ID
	 * @param bool $nli Not logged in?
	 * @param array $userRow User row
	 */
	function _prepareOrderPage($userID, $packageID, $userRow, $nli=false)
	{
		global $lang_user, $db, $tpl, $bm_prefs;

		$subscriptions = $this->_getUserSubscriptions($userID);

		// get package
		$res = $db->Query('SELECT id,titel,beschreibung,abrechnung,abrechnung_t,preis_cent,laufzeiten,max_laufzeit,`alt_titles`,`alt_descriptions` FROM {pre}mod_premium_packages WHERE id=? AND geloescht=0',
			$packageID);
		if($res->RowCount() == 1)
		{
			$package = $res->FetchArray(MYSQLI_ASSOC);

			if($package['laufzeiten'] != '*')
				$package['laufzeiten'] = explode(',', $package['laufzeiten']);

			$package['isFree'] 			= $package['preis_cent'] == 0;
			$package['titel'] 			= $this->_getTranslatedText($package['titel'], $package['alt_titles']);
			$package['beschreibung']	= $this->_getTranslatedText($package['beschreibung'], $package['alt_descriptions']);

			$res->Free();
		}
		else
			die('Invalid package');

		if($package['isFree'] && $nli)
		{
			if($this->_activateOrder($package['id'], 1, -1, $userID))
			{
				// delete auth row
				$db->Query('DELETE FROM {pre}mod_premium_userauth WHERE userid=?',
					$userID);
				$tpl->assign('msg', 	$lang_user['pacc_paymentreturn3']);
				$tpl->assign('page', 	'nli/regdone.tpl');
				$tpl->display('nli/index.tpl');
				exit;
			}
			else
				die('Failed to activate order');
		}

		// check for other subscriptions
		$otherPackage = false;
		foreach($subscriptions as $subscription)
			if($subscription['package']['id'] != $packageID)
				$otherPackage = true;

		// page output
		$tpl->assign('currency',		$bm_prefs['currency']);
		$tpl->assign('pacc_prefs',		$this->prefs);
		$tpl->assign('packageAmount',	sprintf('%.02f', $package['preis_cent']/100));
		$tpl->assign('runtimeNote',		sprintf($lang_user['pacc_runtimenote'], $package['abrechnung_t'], $package['abrechnung_t'], $package['abrechnung_t']*2, $package['abrechnung_t']*3));
		$tpl->assign('taxNote',			sprintf($lang_user['pacc_taxnote'], $this->_vatRateForUser($userID)));
		$tpl->assign('package',			$package);
		$tpl->assign('otherPackage',	$otherPackage);
		$tpl->assign('abrechnung_t',	$package['abrechnung_t']);

		if($package['abrechnung'] != 'einmalig')
			$tpl->assign('intervalStr',	$lang_user['pacc_'.$package['abrechnung'].'2']);

		if(!class_exists('BMPayment'))
			include(B1GMAIL_DIR . 'serverlib/payment.class.php');
		BMPayment::PreparePaymentForm($tpl, '', 0, $userRow);
	}

	/**
	 * initiate order placement
	 *
	 * @param int $userID User ID
	 * @param int $packageID Package ID
	 * @param array $userRow User row
	 * @param bool $nli Not logged in?
	 * @return bool
	 */
	function _initiateOrderPlacement($userID, $packageID, $userRow, $nli=false)
	{
		global $db, $tpl, $lang_user, $bm_prefs;

		if(!class_exists('BMPayment'))
			include(B1GMAIL_DIR . 'serverlib/payment.class.php');
		BMPayment::PreparePaymentForm($tpl, '', 0, $userRow);

		$invalidFields = array();
		$tpl->assign('pacc_prefs',		$this->prefs);

		// get package
		$res = $db->Query('SELECT id,titel,beschreibung,abrechnung,abrechnung_t,preis_cent,laufzeiten,max_laufzeit,`alt_titles`,`alt_descriptions` FROM {pre}mod_premium_packages WHERE id=? AND geloescht=0',
			$packageID);
		if($res->RowCount() == 1)
		{
			$package = $res->FetchArray(MYSQLI_ASSOC);
			$package['isFree'] 			= $package['preis_cent'] == 0;
			$package['titel'] 			= $this->_getTranslatedText($package['titel'], $package['alt_titles']);
			$package['beschreibung']	= $this->_getTranslatedText($package['beschreibung'], $package['alt_descriptions']);
			$res->Free();
		}
		else
			die('Invalid package');

		// check runtime
		if($package['abrechnung'] == 'einmalig')
		{
			$abrechnung_t = -1;
			$amount = $package['preis_cent'];
		}
		else
		{
			$abrechnung_t = (int)$_POST['abrechnung_t'];

			if(($abrechnung_t < 1)
				|| ($abrechnung_t % $package['abrechnung_t'] != 0)
				|| ($package['laufzeiten'] != '*' && !in_array($abrechnung_t, explode(',', $package['laufzeiten']))))
			{
				$invalidFields[] = 'abrechnung_t';
			}
			else
			{
				$amount = ($abrechnung_t/$package['abrechnung_t']) * $package['preis_cent'];
			}
		}

		// check max runtime
		if($package['max_laufzeit'] != 0)
		{
			// calculate expiration date
			$expire = -1;
			$maxExpire = -1;
			if($package['abrechnung'] != 'einmalig')
			{
				$intervals = array('wochen' => TIME_ONE_WEEK, 'monate' => TIME_ONE_MONTH, 'jahre' => TIME_ONE_YEAR);
				$expire = time() + ($abrechnung_t * $intervals[$package['abrechnung']]);
				$maxExpire = time() + ($package['max_laufzeit'] * $intervals[$package['abrechnung']]);
			}

			// active subscription?
			if($expire != -1)
			{
				$res = $db->Query('SELECT id,paket,ablauf FROM {pre}mod_premium_subscribers WHERE benutzer=?',
					$userID);
				while($subscription = $res->FetchArray(MYSQLI_ASSOC))
				{
					if($subscription['paket'] == $packageID
						&& $subscription['ablauf'] > 0
						&& $expire != -1)
					{
						$expire += max(0, $subscription['ablauf'] - time());
					}
				}
				$res->Free();
			}

			// over limit?
			if($expire != -1 && $maxExpire != -1 && $expire > $maxExpire)
			{
				$invalidFields[] = 'abrechnung_t';
				$tpl->assign('errorMsg', sprintf($lang_user['pacc_maxerror'],
					$package['max_laufzeit'],
					$lang_user['pacc_'.$package['abrechnung'].($package['max_laufzeit']>1?'2':'')]));
			}
		}

		// prepare cart
		$orderID = -1;
		$cart = array();
		$cart[] = array(
			'key'		=> 'PAcc.order.' . $packageID,
			'count'		=> ($package['preis_cent'] == 0 ? 0 : $amount/$package['preis_cent']),
			'amount'	=> $package['preis_cent'],
			'total'		=> $amount,
			'text'		=> $package['titel']
							. ' (' . $lang_user['pacc_accunit'] . ': '
							. $this->_intervalStr($package['abrechnung'], $package['abrechnung_t'], true)
							. ')'
		);

		// error?
		$orderID = -1;
		if(count($invalidFields) > 0 || (!$package['isFree'] && ($orderID = BMPayment::ProcessPaymentForm($tpl, $cart, false, $userID)) == 0))
		{
			if($orderID == -1)
				BMPayment::ProcessPaymentForm($tpl, $cart, true, $userID);

			if($package['laufzeiten'] != '*')
				$package['laufzeiten'] = explode(',', $package['laufzeiten']);

			$tpl->assign('invalidFields', 	$invalidFields);
			$tpl->assign('runtimeNote',		sprintf($lang_user['pacc_runtimenote'], $package['abrechnung_t'], $package['abrechnung_t'], $package['abrechnung_t']*2, $package['abrechnung_t']*3));
			$tpl->assign('taxNote',			sprintf($lang_user['pacc_taxnote'], $bm_prefs['steuersatz']));
			$tpl->assign('package',			$package);
			$tpl->assign('paymentMethod',	$_POST['paymentMethod']);

			if(isset($_POST['abrechnung_t']))
				$tpl->assign('abrechnung_t',	$_POST['abrechnung_t']);
			else
				$tpl->assign('abrechnung_t',	$package['abrechnung_t']);

			if($package['abrechnung'] != 'einmalig')
				$tpl->assign('intervalStr',	$lang_user['pacc_'.$package['abrechnung'].'2']);

			return(false);
		}

		// ok => place order!
		else if(isset($amount))
		{
			if($amount == 0 && $orderID == -1)
			{
				if($this->_activateOrder($package['id'], 1, -1, $userID))
				{
					$tpl->assign('title', $lang_user['order']);
					$tpl->assign('msg', $lang_user['pacc_activated']);
					$tpl->assign('pageContent', 'li/msg.tpl');
					$tpl->assign('backLink', 'prefs.php?action=pacc_mod&sid='.session_id());
				}
				else
					die('Failed to activate order');
			}
			else
			{
				BMPayment::InitiatePayment($tpl, $orderID, $nli ? $bm_prefs['selfurl'] . 'index.php?action=paccPaymentReturn' : '', 'pageContent', $userID);
			}

			return(true);
		}

		return(false);
	}

	/**
	 * calculate order amount
	 *
	 * @param int $packageID Package ID
	 * @param int $abrechnungT Runtime (unit according to package settings)
	 * @return int Price in cents
	 */
	function _calcOrderAmount($packageID, $abrechnungT = -1)
	{
		global $db, $bm_prefs;

		$res = $db->Query('SELECT abrechnung,abrechnung_t,preis_cent FROM {pre}mod_premium_packages WHERE id=?',
			$packageID);
		if($res->RowCount() == 1)
		{
			$package = $res->FetchArray(MYSQLI_ASSOC);

			if($bm_prefs['mwst'] == 'add')
				$package['preis_cent'] = round($package['preis_cent'] * (1 + $bm_prefs['steuersatz']/100), 0);
			$res->Free();

			if($package['abrechnung'] == 'einmalig')
			{
				$abrechnung_t = -1;
				return(round($package['preis_cent'], 0));
			}
			else
			{
				if($abrechnungT % $package['abrechnung_t'] != 0
					|| $abrechnungT < 1)
					return(-1);
				else
					return(round(($abrechnungT/$package['abrechnung_t']) * $package['preis_cent'], 0));
			}
		}
		else
			return(-1);
	}

	/**
	 * check if (undeleted) packages are available
	 *
	 */
	function _packagesAvailable()
	{
		global $db;

		$res = $db->Query('SELECT COUNT(*) FROM {pre}mod_premium_packages WHERE geloescht=0');
		list($packageCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return($packageCount);
	}

	/**
	 * get subscriptions for user
	 *
	 * @param int $userID User ID
	 * @return array
	 */
	function _getUserSubscriptions($userID)
	{
		global $db;

		// get/cache packages
		$packages = array();
		$res = $db->Query('SELECT * FROM {pre}mod_premium_packages');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$row['titel'] 			= $this->_getTranslatedText($row['titel'], $row['alt_titles']);
			$row['beschreibung'] 	= $this->_getTranslatedText($row['beschreibung'], $row['alt_descriptions']);
			$packages[$row['id']] = $row;
		}
		$res->Free();

		// get subscriptions
		$result = array();
		$res = $db->Query('SELECT id,benutzer,paket,letzte_zahlung,ablauf,last_update_notification FROM {pre}mod_premium_subscribers WHERE benutzer=?',
			$userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$row['package'] = $packages[$row['paket']];
			$result[$row['id']] = $row;
		}
		$res->Free();

		return($result);
	}

	/**
	 * check user auth token
	 *
	 */
	function _checkUserToken()
	{
		global $db;

		// check required input
		if(!isset($_REQUEST['userID'])
			|| !isset($_REQUEST['userToken'])
			|| strlen($_REQUEST['userToken']) != 32)
			die('Error: One or more missing input variables.');
		$userID 	= (int)$_REQUEST['userID'];
		$userToken 	= $_REQUEST['userToken'];
		$signUp 	= isset($_REQUEST['signUp']);

		// check token
		$res = $db->Query('SELECT COUNT(*) FROM {pre}mod_premium_userauth WHERE userid=? AND token=?',
			$userID,
			$userToken);
		list($rowCount) = $res->FetchArray();
		$res->Free();
		if($rowCount != 1)
			die('Invalid authentication token');
	}

	/**
	 * place order
	 *
	 */
	function _paccPlaceOrder()
	{
		global $db, $tpl;

		// check required input
		if(!isset($_REQUEST['id'])
			|| (!isset($_REQUEST['dontOrder']) && !isset($_REQUEST['doOrder'])))
			die('Error: One or more missing input variables.');
		$userID 	= (int)$_REQUEST['userID'];
		$userToken 	= $_REQUEST['userToken'];
		$signUp 	= isset($_REQUEST['signUp']);

		// check user auth token
		$this->_checkUserToken();

		// get user
		$user = _new('BMUser', array($userID));

		// abort
		if(isset($_REQUEST['dontOrder']) && $signUp)
		{
			$this->_abortNLIOrder($userID, $user->_row);
		}

		// display order page
		else
		{
			$packageID = (int)$_REQUEST['id'];

			if($this->_initiateOrderPlacement($userID, $packageID, $user->Fetch(), true))
			{
				// delete auth row
				$db->Query('DELETE FROM {pre}mod_premium_userauth WHERE userid=?',
					$userID);
				$tpl->assign('page', 	$this->_templatePath('pacc.nli.order.done.tpl'));
				$tpl->assign('omitTable',true);
			}
			else
				$tpl->assign('page', 	$this->_templatePath('pacc.nli.order.tpl'));

			$tpl->assign('force',		$this->prefs['signup_order_force'] == 'yes');
			$tpl->assign('userID',		$userID);
			$tpl->assign('userToken',	$userToken);
			$tpl->assign('signUp',		$signUp);
			$tpl->display('nli/index.tpl');

			exit();
		}
	}

	/**
	 * abort not logged in order
	 *
	 * @param int $userID User ID
	 * @param array $userRow User row
	 */
	function _abortNLIOrder($userID, $userRow)
	{
		global $db, $lang_user, $tpl, $bm_prefs;

		if($this->prefs['signup_order_force'] == 'yes')
		{
			// delete user
			$db->Query('UPDATE {pre}users SET gesperrt=? WHERE id=?',
				'delete',
				$userID);
			PutLog(sprintf('User <%s> (%d) marked for deletion (signup aborted at order page)',
				$userRow['email'],
				$userID),
				PRIO_NOTE,
				__FILE__,
				__LINE__);

			// display reg aborted message
			$tpl->assign('msg',		$lang_user['pacc_signup_aborted']);
		}
		else
		{
			// display reg done message
			$tpl->assign('msg', 	sprintf($bm_prefs['usr_status'] == 'locked'
										? $lang_user['regdonelocked']
										: $lang_user['regdone'], $userRow['email']));
		}

		// delete auth row
		$db->Query('DELETE FROM {pre}mod_premium_userauth WHERE userid=?',
			$userID);

		// page output
		$tpl->assign('page',	'nli/regdone.tpl');
		$tpl->display('nli/index.tpl');
		exit();
	}

	/**
	 * show not logged in order page
	 *
	 */
	function _paccOrder()
	{
		global $db, $tpl, $lang_user, $bm_prefs;

		// check required input
		if((!isset($_REQUEST['dontOrder']) && !isset($_REQUEST['doOrder']))
			|| (isset($_REQUEST['doOrder']) && !isset($_REQUEST['package'])))
			die('Error: One or more missing input variables.');
		$userID 	= (int)$_REQUEST['userID'];
		$userToken 	= $_REQUEST['userToken'];
		$signUp 	= isset($_REQUEST['signUp']);

		// check user auth token
		$this->_checkUserToken();

		// get user
		$user = _new('BMUser', array($userID));

		// abort
		if(isset($_REQUEST['dontOrder']))
		{
			$this->_abortNLIOrder($userID, $user->Fetch());
		}

		// display order page
		else
		{
			$this->_prepareOrderPage($userID, (int)$_REQUEST['package'], $user->Fetch(), true);
			$tpl->assign('force',		$this->prefs['signup_order_force'] == 'yes');
			$tpl->assign('userID',		$userID);
			$tpl->assign('userToken',	$userToken);
			$tpl->assign('signUp',		$signUp);
			$tpl->assign('page', 		$this->_templatePath('pacc.nli.order.tpl'));
			$tpl->display('nli/index.tpl');
		}
	}

	/**
	 * returns the default VAT rate for a certain user
	 *
	 * @param int $userID User ID
	 * @return double VAT rate
	 */
	function _vatRateForUser($userID)
	{
		global $db;

		$vatRate = 0;
		$countries = CountryList(true);
		$res = $db->Query('SELECT `land` FROM {pre}users WHERE `id`=?', $userID);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if(isset($countries[$row['land']]))
				$vatRate = $countries[$row['land']]['vat'];
		}
		$res->Free();

		return $vatRate;
	}

	/**
	 * display packages page for not logged in users
	 *
	 * @param int $userID User ID
	 * @param bool $signUp Called from signup?
	 */
	function _nliPackagesPage($userID, $signUp=false)
	{
		global $tpl, $db, $lang_user;

		// create auth row
		$db->Query('REPLACE INTO {pre}mod_premium_userauth(`userid`,`token`,`date`) VALUES(?,?,?)',
			$userID,
			$userToken = GenerateRandomKey('PAccUserToken'),
			time());

		// find VAT rate for user
		$vatRate = $this->_vatRateForUser($userID);

		// show order page
		$tpl->register_function('paccFormatField', array(&$this, '_smartyPaccFormatField'));
		$tpl->assign('userID',		$userID);
		$tpl->assign('userToken',	$userToken);
		$tpl->assign('signUp',		$signUp);
		$tpl->assign('force',		$this->prefs['signup_order_force'] == 'yes');
		$tpl->assign('orderText',	$this->prefs['signup_order_force'] == 'yes' ?  ($signUp ? $lang_user['pacc_forcetext'] : $lang_user['pacc_locktext']) : $lang_user['pacc_ordertext']);
		$tpl->assign('page', 		$this->_templatePath('pacc.nli.packages.tpl'));
		$tpl->assign('matrix', 		$this->_packageMatrix($vatRate));
		$tpl->display('nli/index.tpl');
		exit();
	}

	/**
	 * subscriptions
	 *
	 */
	function _subscriptionsPage()
	{
		global $db, $tpl, $lang_admin, $bm_prefs;

		if(!isset($_REQUEST['do']))
			$_REQUEST['do'] = 'list';

		//
		// list
		//
		if($_REQUEST['do'] == 'list')
		{
			// single action
			if(isset($_REQUEST['singleAction'])
				&& in_array($_REQUEST['singleAction'], array('extend', 'cancel')))
			{
				$_REQUEST['executeMassAction'] = true;
				$_REQUEST['massAction'] = $_REQUEST['singleAction'];
				$_POST['subscriber'] = array((int)$_REQUEST['singleID'] => true);
			}

			// mass action
			if(isset($_REQUEST['executeMassAction']))
			{
				// get subscriber IDs
				$subscriberIDs = isset($_POST['subscriber']) ? $_POST['subscriber'] : array();
				if(!is_array($subscriberIDs))
					$subscriberIDs = array();
				else
					$subscriberIDs = array_keys($subscriberIDs);

				if(count($subscriberIDs) > 0)
				{
					// cancel subscriptions
					if($_REQUEST['massAction'] == 'cancel')
					{
						$res = $db->Query('SELECT id,benutzer,paket FROM {pre}mod_premium_subscribers WHERE id IN ?',
							$subscriberIDs);
						while($row = $res->FetchArray(MYSQLI_ASSOC))
						{
							// get package details
							$packageRes = $db->Query('SELECT fallback_grp,gruppe FROM {pre}mod_premium_packages WHERE id=?',
								$row['paket']);
							$package = $packageRes->FetchArray(MYSQLI_ASSOC);
							$packageRes->Free();

							// get current user group
							$userGroup = -1;
							$userRes = $db->Query('SELECT `gruppe` FROM {pre}users WHERE `id`=?',
								$row['benutzer']);
							if($userRes->RowCount() == 1)
							{
								$userRow = $userRes->FetchArray(MYSQLI_ASSOC);
								$userGroup = $userRow['gruppe'];
							}
							$userRes->Free();

							if($package['fallback_grp'] == -1)
								$db->Query('UPDATE {pre}users SET gesperrt=?,notes=CONCAT(notes,?) WHERE id=?',
									'locked',
									sprintf($lang_admin['pacc_expire_locked_note'], date('r'), $row['id']),
									$row['benutzer']);
							else if($userGroup == -1 || $userGroup == $package['gruppe'])
								$db->Query('UPDATE {pre}users SET gruppe=?,notes=CONCAT(notes,?) WHERE id=?',
									$package['fallback_grp'],
									sprintf($lang_admin['pacc_expire_moved_note'], date('r'), $package['fallback_grp'], $row['id']),
									$row['benutzer']);
							else
								$db->Query('UPDATE {pre}users SET notes=CONCAT(notes,?) WHERE id=?',
									sprintf($lang_admin['pacc_expire_notmoved_note'], date('r'), $package['fallback_grp'], $row['id']),
									$row['benutzer']);

							PutLog(sprintf('PremiumAccount: Subscription #%d of user #%d cancelled by admin',
								$row['id'],
								$row['benutzer']),
								PRIO_NOTE,
								__FILE__,
								__LINE__);
						}
						$res->Free();

						$db->Query('DELETE FROM {pre}mod_premium_subscribers WHERE id IN ?',
							$subscriberIDs);
					}

					// extend subscriptions
					else if($_REQUEST['massAction'] == 'extend')
					{
						// assign
						$tpl->assign('ids',		implode(',', $subscriberIDs));
						$tpl->assign('page', 	$this->_templatePath('pacc.admin.subscriptions.extend.tpl'));
						return;
					}
				}
			}

			// extend subscriptions
			if(isset($_REQUEST['extend'])
				&& trim($_REQUEST['extend']) != ''
				&& isset($_REQUEST['mode']))
			{
				$subscriberIDs = explode(',', $_REQUEST['extend']);

				if($_REQUEST['mode'] == 'dynamic')
				{
					$time = (int)$_REQUEST['dynamicValue']
								* min(365, max(1, (int)$_REQUEST['dynamicFactor']))
								* TIME_ONE_DAY;
					$db->Query('UPDATE {pre}mod_premium_subscribers SET ablauf=ablauf+? WHERE id IN ? AND ablauf>1',
						$time,
						$subscriberIDs);
				}

				else if($_REQUEST['mode'] == 'static')
				{
					$_REQUEST['staticValueHour'] = 23;
					$_REQUEST['staticValueMinute'] = $_REQUEST['staticValueSecond'] = 59;
					$time = SmartyDateTime('staticValue');
					$db->Query('UPDATE {pre}mod_premium_subscribers SET ablauf=? WHERE id IN ?',
						$time,
						$subscriberIDs);
				}
			}

			// sort options
			$sortBy = isset($_REQUEST['sortBy'])
						? $_REQUEST['sortBy']
						: 'ablauf';
			$sortOrder = isset($_REQUEST['sortOrder'])
							? strtolower($_REQUEST['sortOrder'])
							: 'asc';
			$perPage = max(1, isset($_REQUEST['perPage'])
							? (int)$_REQUEST['perPage']
							: 50);

			// filter stuff
			$queryAdd = '';
			if(isset($_REQUEST['filter']))
			{
				$packageIDs = array_keys($_REQUEST['packages']);
				$queryPackages = count($packageIDs) > 0 ? implode(',', $packageIDs) : '0';
				$queryAdd = 'WHERE paket IN(' . $queryPackages . ') ';
			}

			// page calculation
			$res = $db->Query('SELECT COUNT(*) FROM {pre}mod_premium_subscribers ' . $queryAdd);
			list($subscriptionCount) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();
			$pageCount = ceil($subscriptionCount / $perPage);
			$pageNo = isset($_REQUEST['page'])
						? max(1, min($pageCount, (int)$_REQUEST['page']))
						: 1;
			$startPos = max(0, min($perPage*($pageNo-1), $subscriptionCount));

			// get packages
			$packages = array();
			$res = $db->Query('SELECT id,titel,geloescht FROM {pre}mod_premium_packages WHERE geloescht=0 ORDER BY titel ASC');
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$packages[$row['id']] = array(
					'id'		=> $row['id'],
					'title'		=> $row['titel'],
					'checked'	=> !isset($_REQUEST['packages']) || isset($_REQUEST['packages'][$row['id']]),
					'deleted'	=> $row['geloescht'] == 1
				);
			}
			$res->Free();

			// do the query!
			$subscriptions = array();
			$res = $db->Query('SELECT id,benutzer,paket,letzte_zahlung,ablauf FROM {pre}mod_premium_subscribers ' . $queryAdd
						. 'ORDER BY ' . $sortBy . ' '
						. $sortOrder . ' '
						. 'LIMIT ' . $startPos . ',' . $perPage);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$user = _new('BMUser', array($row['benutzer']));
				$user = $user->Fetch();

				$subscriptions[$row['id']] = array(
					'id'			=> $row['id'],
					'package'		=> $packages[$row['paket']],
					'lastPayment'	=> $row['letzte_zahlung'],
					'expiration'	=> $row['ablauf'],
					'user'			=> $user
				);
			}
			$res->Free();

			// assign
			$tpl->assign('pageNo', 			$pageNo);
			$tpl->assign('pageCount', 		$pageCount);
			$tpl->assign('sortBy', 			$sortBy);
			$tpl->assign('sortOrder', 		$sortOrder);
			$tpl->assign('sortOrderInv', 	$sortOrder == 'asc' ? 'desc' : 'asc');
			$tpl->assign('perPage', 		$perPage);
			$tpl->assign('packages',		$packages);
			$tpl->assign('subscriptions',	$subscriptions);
			$tpl->assign('page', 			$this->_templatePath('pacc.admin.subscriptions.tpl'));
		}
	}

	/**
	 * prefs
	 *
	 */
	function _prefsPage()
	{
		global $db, $tpl, $bm_prefs, $lang_admin, $plugins;

		if(!isset($_REQUEST['do']))
			$_REQUEST['do'] = 'common';

		//
		// common prefs
		//
		if($_REQUEST['do'] == 'common')
		{
			// save?
			if(isset($_REQUEST['save']))
			{
				$db->Query('UPDATE {pre}mod_premium_prefs SET delete_order=?, delete_order_after=?, send_update_notification=?, update_notification_days=?, update_notification_altmail=?, signup_order_page=?, signup_order_force=?, nli_packages_page=?',
					isset($_REQUEST['delete_order']) ? 'yes' : 'no',
					(int)$_REQUEST['delete_order_after']*86400,
					isset($_REQUEST['send_update_notification']) ? 'yes' : 'no',
					$_REQUEST['update_notification_days'],
					isset($_REQUEST['update_notification_altmail']) ? 'yes' : 'no',
					isset($_REQUEST['signup_order_page']) ? 'yes' : 'no',
					isset($_REQUEST['signup_order_force']) ? 'yes' : 'no',
					$_REQUEST['nli_packages_page']);
				$this->prefs = $this->_getPrefs();
			}

			// assign
			$tpl->assign('bmURL',			$bm_prefs['selfurl']);
			$tpl->assign('pacc_prefs',		$this->prefs);
			$tpl->assign('page', 			$this->_templatePath('pacc.admin.prefs.tpl'));
		}

		//
		// feature fields
		//
		else if($_REQUEST['do'] == 'featureFields')
		{
			// save?
			if(isset($_REQUEST['save'])
				&& is_array($_POST['fields']))
			{
				$db->Query('UPDATE {pre}mod_premium_prefs SET `fields`=?,`fields_order`=?',
					serialize($_POST['fields']),
					serialize($_POST['positions']));
				$fieldValues = $_POST['fields'];
				$fieldPositions = $_POST['positions'];
			}
			else
			{
				if(!is_array($fieldValues = @unserialize($this->prefs['fields'])))
					$fieldValues = array();
				if(!is_array($fieldPositions = @unserialize($this->prefs['fields_order'])))
					$fieldPositions = array();
			}

			// fields
			$fields = $fieldTitles = array();
			$res = $db->Query('SHOW FIELDS FROM {pre}gruppen');
			while($row = $res->FetchArray(MYSQLI_NUM))
			{
				list($fieldName) = $row;
				if($fieldName == 'id' || $fieldName == 'send_limit_time')
					continue;
				if($fieldName == 'send_limit_count')
					$fieldName = 'send_limit';
				$fieldTitles[$fieldName] = $this->_fieldTitle($fieldName);
				$fields[$fieldName] = in_array($fieldName, $fieldValues);
			}
			$res->Free();

			// group fields
			$options = $plugins->GetGroupOptions();
			foreach($options as $optionKey=>$optionInfo)
			{
				$fieldTitles[$optionKey] = $this->_fieldTitle($optionKey);
				$fields[$optionKey] = in_array($optionKey, $fieldValues);
			}

			// remove old positions
			foreach($fieldPositions as $key=>$val)
				if(!isset($fieldTitles[$key]))
					unset($fieldPositions[$key]);

			// add missing positions
			$maxPos = 0;
			foreach($fieldPositions as $val)
				if($maxPos < $val)
					$maxPos = $val;
			foreach($fields as $key=>$val)
			{
				if(!isset($fieldPositions[$key]))
					$fieldPositions[$key] = ($maxPos += 10);
			}

			// sort
			asort($fieldPositions, SORT_NUMERIC);

			// assign
			$tpl->assign('fields',			$fields);
			$tpl->assign('fieldTitles',		$fieldTitles);
			$tpl->assign('fieldPositions',	$fieldPositions);
			$tpl->assign('page', 			$this->_templatePath('pacc.admin.prefs.featurefields.tpl'));
		}
	}

	/**
	 * packages page
	 *
	 */
	function _packagesPage()
	{
		global $db, $tpl, $lang_admin, $bm_prefs;

		if(!isset($_REQUEST['do']))
			$_REQUEST['do'] = 'list';

		//
		// list
		//
		if($_REQUEST['do'] == 'list')
		{
			// delete
			if(isset($_REQUEST['delete']))
			{
				$id = (int)$_REQUEST['delete'];

				// fetch subscriber count
				$res = $db->Query('SELECT COUNT(*) FROM {pre}mod_premium_subscribers WHERE paket=?',
					$id);
				list($subscriberCount) = $res->FetchArray(MYSQLI_NUM);
				$res->Free();

				// subscribers?
				if($subscriberCount > 0 && !isset($_REQUEST['subscriptionAction']))
				{
					// fetch title
					$res = $db->Query('SELECT titel FROM {pre}mod_premium_packages WHERE id=?',
						$id);
					list($packageTitle) = $res->FetchArray(MYSQLI_NUM);
					$res->Free();

					// assign
					$tpl->assign('id', 				$id);
					$tpl->assign('packageTitle', 	$packageTitle);
					$tpl->assign('page',			$this->_templatePath('pacc.admin.packages.delete.tpl'));
					return;
				}
				else if($subscriberCount > 0 && isset($_REQUEST['subscriptionAction']))
				{
					if($_REQUEST['subscriptionAction'] == 'continue')
					{
						// mark for deletion
						$db->Query('UPDATE {pre}mod_premium_packages SET geloescht=1 WHERE id=?',
							$id);

						// log
						PutLog(sprintf('Premium package <%d> marked for deletion',
							$id),
							PRIO_NOTE,
							__FILE__,
							__LINE__);
					}
					else if($_REQUEST['subscriptionAction'] == 'delete')
					{
						// get fallback group
						$res = $db->Query('SELECT fallback_grp FROM {pre}mod_premium_packages WHERE id=?',
							$id);
						list($fallbackGroup) = $res->FetchArray(MYSQLI_NUM);
						$res->Free();

						// process users
						$res = $db->Query('SELECT id,benutzer FROM {pre}mod_premium_subscribers WHERE paket=?',
							$id);
						while($row = $res->FetchArray(MYSQLI_ASSOC))
						{
							if($fallbackGroup == -1)
								$db->Query('UPDATE {pre}users SET gesperrt=?,notes=CONCAT(notes,?) WHERE id=?',
									'locked',
									sprintf($lang_admin['pacc_expire_locked_note'], date('r'), $row['id']),
									$row['benutzer']);
							else
								$db->Query('UPDATE {pre}users SET gruppe=? WHERE id=?',
									$fallbackGroup,
									sprintf($lang_admin['pacc_expire_moved_note'], date('r'), $fallbackGroup, $row['id']),
									$row['benutzer']);
						}
						$res->Free();

						// delete subscriptions
						$db->Query('DELETE FROM {pre}mod_premium_subscribers WHERE paket=?',
							$id);

						// delete package
						$db->Query('DELETE FROM {pre}mod_premium_packages WHERE id=?',
							$id);

						// log
						PutLog(sprintf('Premium package <%d> deleted (subscriptions deleted; fallback group: %d)',
							$id,
							$fallbackGroup),
							PRIO_NOTE,
							__FILE__,
							__LINE__);
					}
				}
				else
				{
					$db->Query('DELETE FROM {pre}mod_premium_packages WHERE id=?',
						$id);
					PutLog(sprintf('Premium package <%d> deleted (no subscriptions)',
						$id),
						PRIO_NOTE,
						__FILE__,
						__LINE__);
				}
			}

			// add
			if(isset($_REQUEST['add']))
			{
				if($_REQUEST['laufzeiten_all'] == 'true')
					$laufzeiten = '*';
				else
					$laufzeiten = implode(',', array_map('trim', explode(',', $_REQUEST['laufzeiten'])));

				if(isset($_REQUEST['max_laufzeit_enable']))
					$max_laufzeit = max(1, $_REQUEST['max_laufzeit']);
				else
					$max_laufzeit = 0;

				$price = $this->_formCentPrice($_REQUEST['preis']);

				$altTitles = array();
				foreach($_REQUEST['titles'] as $titleEntryKey=>$titleEntry)
				{
					if($titleEntryKey == 0 || empty($titleEntry['title']))
						continue;
					$altTitles[$titleEntry['lang']] = $titleEntry['title'];
				}

				$altDescriptions = array();
				foreach($_REQUEST['descriptions'] as $descriptionEntryKey=>$descriptionEntry)
				{
					if($descriptionEntryKey == 0 || empty($descriptionEntry['description']))
						continue;
					$altDescriptions[$descriptionEntry['lang']] = $descriptionEntry['description'];
				}

				$db->Query('INSERT INTO {pre}mod_premium_packages(titel,gruppe,fallback_grp,abrechnung_t,abrechnung,laufzeiten,max_laufzeit,preis_cent,beschreibung,template,accentuation,`order`,`alt_titles`,`alt_descriptions`) '
							. 'VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
							$_REQUEST['titles'][0]['title'],
							$_REQUEST['gruppe'],
							$_REQUEST['fallback_grp'],
							$_REQUEST['abrechnung_t'],
							$price==0 ? 'einmalig' : $_REQUEST['abrechnung'],
							$laufzeiten,
							$max_laufzeit,
							$price,
							$_REQUEST['descriptions'][0]['description'],
							$_REQUEST['template'],
							$_REQUEST['accentuation'],
							$_REQUEST['order'],
							serialize($altTitles),
							serialize($altDescriptions));
			}

			// fetch
			$maxOrder = 0;
			$packages = array();
			$res = $db->Query('SELECT {pre}mod_premium_packages.id AS id,'
									.	'{pre}gruppen.titel AS `group`,'
									.	'{pre}mod_premium_packages.fallback_grp AS fallback_grp,'
									.	'{pre}mod_premium_packages.preis_cent AS preis_cent,'
									.	'{pre}mod_premium_packages.abrechnung AS abrechnung,'
									.	'{pre}mod_premium_packages.abrechnung_t AS abrechnung_t,'
									.	'{pre}mod_premium_packages.titel AS title, '
									.	'{pre}mod_premium_packages.template AS template, '
									.	'{pre}mod_premium_packages.`order` AS `order` '
									.	'FROM {pre}mod_premium_packages '
									. 	'LEFT JOIN {pre}gruppen ON {pre}gruppen.id={pre}mod_premium_packages.gruppe '
									.	'WHERE {pre}mod_premium_packages.geloescht=0 ORDER BY {pre}mod_premium_packages.`order` ASC,{pre}mod_premium_packages.titel ASC');
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				// get subscriber count
				$res2 = $db->Query('SELECT COUNT(*) FROM {pre}mod_premium_subscribers WHERE paket=?',
					$row['id']);
				list($subscribers) = $res2->FetchArray(MYSQLI_NUM);
				$res2->Free();

				// fallback group
				if($row['fallback_grp'] == -1)
					$fallbackGroup = '(' . $lang_admin['pacc_lockaccount'] . ')';
				else
				{
					$groupRes = $db->Query('SELECT titel FROM {pre}gruppen WHERE id=?',
						$row['fallback_grp']);
					list($fallbackGroup) = $groupRes->FetchArray(MYSQLI_NUM);
					$groupRes->Free();
				}

				// generate texts
				$periodPrice = sprintf('%.02f %s', $row['preis_cent']/100, $bm_prefs['currency']);
				$paymentPeriod = $this->_intervalStr($row['abrechnung'], $row['abrechnung_t']);

				// add to array
				$packages[] = array(
					'id'				=> $row['id'],
					'group'				=> $row['group'],
					'fallback_group'	=> $fallbackGroup,
					'title'				=> $row['title'],
					'periodPrice'		=> $periodPrice,
					'paymentPeriod'		=> $paymentPeriod,
					'subscribers'		=> $subscribers,
					'order'				=> $row['order']
				);

				$maxOrder = max($maxORder, $row['order']);
			}
			$res->Free();

			// assign
			$tpl->assign('nextOrder',		$maxOrder + 10);
			$tpl->assign('templates',		GetAvailableTemplates());
			$tpl->assign('packages',		$packages);
			$tpl->assign('groups',			BMGroup::GetSimpleGroupList());
			$tpl->assign('defaultGroup',	$bm_prefs['std_gruppe']);
			$tpl->assign('languages',		GetAvailableLanguages());
			$tpl->assign('page', 			$this->_templatePath('pacc.admin.packages.tpl'));
		}

		//
		// edit
		//
		else if($_REQUEST['do'] == 'edit'
				&& isset($_REQUEST['id']))
		{
			// save
			if(isset($_REQUEST['save']))
			{
				if($_REQUEST['laufzeiten_all'] == 'true')
					$laufzeiten = '*';
				else
					$laufzeiten = implode(',', array_map('trim', explode(',', $_REQUEST['laufzeiten'])));

				if(isset($_REQUEST['max_laufzeit_enable']))
					$max_laufzeit = max(1, $_REQUEST['max_laufzeit']);
				else
					$max_laufzeit = 0;

				$price = $this->_formCentPrice($_REQUEST['preis']);

				$altTitles = array();
				foreach($_REQUEST['titles'] as $titleEntryKey=>$titleEntry)
				{
					if($titleEntryKey == 0 || empty($titleEntry['title']))
						continue;
					$altTitles[$titleEntry['lang']] = $titleEntry['title'];
				}

				$altDescriptions = array();
				foreach($_REQUEST['descriptions'] as $descriptionEntryKey=>$descriptionEntry)
				{
					if($descriptionEntryKey == 0 || empty($descriptionEntry['description']))
						continue;
					$altDescriptions[$descriptionEntry['lang']] = $descriptionEntry['description'];
				}

				// update db
				$db->Query('UPDATE {pre}mod_premium_packages SET titel=?,gruppe=?,fallback_grp=?,abrechnung_t=?,abrechnung=?,laufzeiten=?,max_laufzeit=?,preis_cent=?,beschreibung=?,template=?,accentuation=?,`order`=?,`alt_titles`=?,`alt_descriptions`=? WHERE id=?',
					$_REQUEST['titles'][0]['title'],
					$_REQUEST['gruppe'],
					$_REQUEST['fallback_grp'],
					$_REQUEST['abrechnung_t'],
					$price == 0 ? 'einmalig' : $_REQUEST['abrechnung'],
					$laufzeiten,
					$max_laufzeit,
					$price,
					$_REQUEST['descriptions'][0]['description'],
					$_REQUEST['template'],
					$_REQUEST['accentuation'],
					$_REQUEST['order'],
					serialize($altTitles),
					serialize($altDescriptions),
					$_REQUEST['id']);

				// redirect
				header('Location: ' . $this->_adminLink(true) . '&action=packages');
				exit();
			}

			// fetch
			$res = $db->Query('SELECT id,titel,gruppe,fallback_grp,abrechnung_t,abrechnung,laufzeiten,max_laufzeit,preis_cent,beschreibung,template,accentuation,`order`,`alt_titles`,`alt_descriptions` FROM {pre}mod_premium_packages WHERE id=?',
				(int)$_REQUEST['id']);
			$package = $res->FetchArray(MYSQLI_ASSOC);
			$res->Free();

			// prepare
			$package['preis'] = sprintf('%.02f', $package['preis_cent']/100);
			$altTitles = @unserialize($package['alt_titles']);
			if(!is_array($altTitles)) $altTitles = array();
			$altDescriptions = @unserialize($package['alt_descriptions']);
			if(!is_array($altDescriptions)) $altDescriptions = array();

			// assign
			$tpl->assign('templates',		GetAvailableTemplates());
			$tpl->assign('languages',		GetAvailableLanguages());
			$tpl->assign('package',			$package);
			$tpl->assign('altTitles',		$altTitles);
			$tpl->assign('altDescriptions',	$altDescriptions);
			$tpl->assign('groups',			BMGroup::GetSimpleGroupList());
			$tpl->assign('page',			$this->_templatePath('pacc.admin.packages.edit.tpl'));
		}
	}

	/**
	 * overview page
	 *
	 */
	function _overviewPage()
	{
		global $db, $tpl, $lang_admin, $currentLanguage, $bm_prefs;

		//
		// get stats
		//

		// package count
		$res = $db->Query('SELECT COUNT(*) FROM {pre}mod_premium_packages WHERE geloescht=0');
		list($packageCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// subscriber count
		$res = $db->Query('SELECT COUNT(*) FROM {pre}mod_premium_subscribers');
		list($subscriberCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// overall revenue
		$res = $db->Query('SELECT SUM(amount) FROM {pre}orders WHERE status=1 AND cart LIKE \'%PAcc.order.%\'');
		list($overallRevenue) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// month revenue
		$res = $db->Query('SELECT SUM(amount) FROM {pre}orders WHERE status=1 AND cart LIKE \'%PAcc.order.%\' AND activated>?',
			mktime(0, 0, 0, date('m'), 1, date('Y')));
		list($monthRevenue) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// outstanding payments (overall)
		$res = $db->Query('SELECT COUNT(*),SUM(amount) FROM {pre}orders WHERE status=0 AND cart LIKE \'%PAcc.order.%\'');
		list($outstandingPaymentsCount, $outstandingPaymentsSum) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// outstanding payments (advance)
		$res = $db->Query('SELECT COUNT(*),SUM(amount) FROM {pre}orders WHERE status=0 AND cart LIKE \'%PAcc.order.%\' AND paymethod=?',
			PAYMENT_METHOD_BANKTRANSFER);
		list($advancePaymentsCount, $advancePaymentsSum) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// outstanding payments (paypal)
		$res = $db->Query('SELECT COUNT(*),SUM(amount) FROM {pre}orders WHERE status=0 AND cart LIKE \'%PAcc.order.%\' AND paymethod=?',
			PAYMENT_METHOD_PAYPAL);
		list($paypalPaymentsCount, $paypalPaymentsSum) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// outstanding payments (sofortueberweisung)
		$res = $db->Query('SELECT COUNT(*),SUM(amount) FROM {pre}orders WHERE status=0 AND cart LIKE \'%PAcc.order.%\' AND paymethod=?',
			PAYMENT_METHOD_SOFORTUEBERWEISUNG);
		list($suPaymentsCount, $suPaymentsSum) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// assign
		$tpl->assign('version',				$this->version);
		$tpl->assign('packageCount',		$packageCount);
		$tpl->assign('subscriberCount',		$subscriberCount);
		$tpl->assign('overallRevenue',		sprintf('%.02f %s', $overallRevenue/100, $bm_prefs['currency']));
		$tpl->assign('monthRevenue',		sprintf('%.02f %s', $monthRevenue/100, $bm_prefs['currency']));
		$tpl->assign('outstandingPayments',	sprintf('%d (%.02f %s)', $outstandingPaymentsCount, $outstandingPaymentsSum/100, $bm_prefs['currency']));
		$tpl->assign('advancePayments',		sprintf('%d (%.02f %s)', $advancePaymentsCount, $advancePaymentsSum/100, $bm_prefs['currency']));
		$tpl->assign('paypalPayments',		sprintf('%d (%.02f %s)', $paypalPaymentsCount, $paypalPaymentsSum/100, $bm_prefs['currency']));
		$tpl->assign('suPayments',			sprintf('%d (%.02f %s)', $suPaymentsCount, $suPaymentsSum/100, $bm_prefs['currency']));
		$tpl->assign('lang',				$currentLanguage);
		$tpl->assign('page', 				$this->_templatePath('pacc.admin.overview.tpl'));
	}

	//
	//
	// smarty callbacks
	//
	//
	function _smartyPaccFormatField($params, &$smarty)
	{
		global $lang_user, $bm_prefs, $plugins, $userRow;

		// params
		$value = $params['value'];
		$key = $params['key'];
		if(isset($params['cut']))
			$cut = $params['cut'];
		else
			$cut = -1;

		// plugin stuff
		$groupOptions = $plugins->getGroupOptions();
		if(isset($groupOptions[$key]))
		{
			// plugin fields
			switch($groupOptions[$key]['type'])
			{
			case FIELD_CHECKBOX:
				$value = $value == 1 ? 'yes' : 'no';
				break;

			case FIELD_RADIO:
			case FIELD_DROPDOWN:
				return($groupOptions[$key]['options'][$value]);

			case FIELD_TEXT:
			case FIELD_TEXTAREA:
				return(TemplateText($params, $smarty));

			default:
				return('#UNKNOWN_FIELD_TYPE(' . $groupOptions[$key]['type'] . ')#');
			}
		}
		else
		{
			// other fields
			if(in_array($key, array('storage','webdisk','maxsize','anlagen','traffic')))
				return(TemplateSize(array('bytes' => $value), $smarty));
			else if(in_array($key, array('wd_member_kbs','wd_open_kbs')))
				return(sprintf('%d KB/s', $value));
			else if($key == 'sms_monat')
				return(sprintf('%d %s', $value, $lang_user['credits']));
			else if($key == 'sms_price_per_credit')
				return(sprintf('%.02f %s', $value/100, $bm_prefs['currency']));
			else if(in_array($key, array('sms_sig', 'signatur')))
				return(trim($value) == '' ? $lang_user['none'] : TemplateText($params, $smarty));
			else if($key == 'saliase' || $key == 'sms_pre')
				return(implode(', ', explode(':', $value)));
			else if($key == 'ads')
				$value = ($value == 'no' ? 'yes' : 'no');
		}

		if($value == 'yes' || $value == 'no')
		{
			if(!isset($userRow))
				return sprintf('<span class="glyphicon glyphicon-%s" style="color:%s;"></span>',
					$value == 'yes' ? 'ok' : 'remove',
					$value == 'yes' ? 'green' : 'red');
			else
				return(sprintf('<span class="fa fa-%s" style="color:%s;" />',
					$value == 'yes' ? 'check' : 'times',
					$value == 'yes' ? 'green' : 'red'));
		}

		// default
		return($params['value']);
	}
}

/**
 * register plugin
 */
$plugins->registerPlugin('PremiumAccountPlugin');
