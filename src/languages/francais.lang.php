<?php 
/*
 * b1gMail
 * Copyright (c) 2021 Patrick Schlangen et al
 * French language by Roger (only User-Frontend, not admin)
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

// b1gMailLang::Francais::Roger::roger@guigma.com::http://::UTF-8::fr.UTF-8|fr|fr_FR.UTF-8|fr_FR|french|fr
// Translation: Roger / roger@guigma.com

/**
 * Client phrases
 */
$lang_client['certmailwarn']	= "Vous essayez d'envoyer un e-mail certifié.\n\nLes e-mails certifiés ne prennent pas en charge les options suivantes\nchoisies.\n\nCes options seront désactivées si vous décidez de continuer.\n\n";
$lang_client['certmailsign']	= 'Signer';
$lang_client['certmailencrypt']	= 'Chiffrer';
$lang_client['certmailconfirm'] = 'Demander un reçu';
$lang_client['addravailable']	= 'Adresse disponible!';
$lang_client['addrtaken']		= 'Adresse indisponible!';
$lang_client['addrinvalid']		= 'Adresse invalide!';
$lang_client['fillin']			= 'Veuillez remplir tous les champs obligatoire!';
$lang_client['selecttext']		= 'Sélectionnez d\'abord un texte!';
$lang_client['fillinname']		= 'Remplissez au moins le prénom et le nom de famille du contact!';
$lang_client['reallyreset']		= 'Êtes-vous sûr de vouloir réinitialiser le formulaire? Les données peuvent être perdues!';
$lang_client['addressbook']		= 'Carnet d\'adresses';
$lang_client['browse']			= 'Parcourir';
$lang_client['importvcf']		= 'Importer VCF';
$lang_client['import']			= 'Importer';
$lang_client['userpicture']		= 'Photo utilisateur';
$lang_client['addattach']		= 'Ajouter une pièce jointe';
$lang_client['saveattach']		= 'Enregistrer la pièce jointe';
$lang_client['attachments']		= 'Pièces jointes';
$lang_client['date']			= 'Date';
$lang_client['pricewarning']	= 'Pour envoyer ce SMS, vous devez disposer d\'un solde d\'au moins %1 Credit(s). Votre solde actuel de %2 Credit(s) est insuffisant. Veuillez Recharger votre compte';
$lang_client['switchwarning']	= 'En passant du mode HTML au mode texte, tout le formatage sera perdu. Êtes-vous sur de vouloir continuer?';

$lang_client['folderprompt']	= 'Veuillez indiquer le nom du dossier à créer:';
$lang_client['newfolder']		= 'Nouveau dossier';
$lang_client['foldererror']		= 'Le dossier ne peut être créé.';
$lang_client['attendees']		= 'Participants';
$lang_client['addattendee']		= 'Ajouter des participants';
$lang_client['newpatches']		= 'De nouveaux correctifs sont disponibles via le système de mise à jour automatique.';
$lang_client['protectedfolder']	= 'Dossier protégé';
$lang_client['source']			= 'Source';
$lang_client['sendwosubject']	= 'Vous n\'avez pas saisi d\'objet pour votre email. Cliquez sur "Annuler" pour ajouter un objet ou cliquez sur "OK" pour envoyer le mail sans sujet.';

$lang_client['movemail']		= 'Déplacer email';
$lang_client['certificate']		= 'Certificat';
$lang_client['addcert']			= 'Importer certificat';
$lang_client['exportcert']		= 'Exporter certificat';
$lang_client['unknown']			= 'Inconnu';
$lang_client['version']			= 'Version';
$lang_client['prefs']			= 'Préférences';
$lang_client['realdel']			= 'Voulez-vous vraiment supprimer ces jeux de données?';
$lang_client['nomailsselected']	= 'Aucun email sélectionné';
$lang_client['mailsselected']	= 'e-mails sélectionnés';
$lang_client['attwarning']		= 'Le texte de votre courrier électronique indique que vous vouliez ajouter une pièce jointe, mais votre courrier électronique ne contient aucune pièce jointe.
				Si vous avez oublié d\'ajouter la pièce jointe, cliquez sur \ "Annuler \" et ajoutez les pièces jointes.
				Sinon, cliquez sur \ "Envoyer \" pour continuer l\'envoi de votre e-mail.';
				
				
$lang_client['viewoptions']		= 'Afficher les options';
$lang_client['compose']			= 'Composer';
$lang_client['uploading']		= 'Télécharger';
$lang_client['export']			= 'Exporter';
$lang_client['groups']			= 'Groupes';
$lang_client['nwslttrtplwarn']	= 'Êtes-vous sûr de vouloir appliquer un nouveau modèle? Toutes vos données saisies seront perdues!';



$lang_client['items']			= 'Articles';
$lang_client['cancel']			= 'Annuler';
$lang_client['nocontactselected']	= 'Aucun contact sélectionné';
$lang_client['contactsselected']	= 'contacts sélectionnés';
$lang_client['checkingaddr']	= 'Vérifier la disponibilité...';
$lang_client['showsuggestions']	= 'Afficher les suggestions';
$lang_client['pleasewait']		= 'Veuillez attendez...';
$lang_client['deliverystatus']	= 'Statut de livraison';
$lang_client['taxnote']			= 'incl. %1% TVA';
$lang_client['decsep']			= ',';
$lang_client['lastsavedat']		= 'Dernière sauvegarde à %1:%2.';
$lang_client['statement']		= 'Relevé de compte';

/**
 * Customizable phrases
 */
$lang_custom['welcome_sub']		= 'Merci pour votre inscription!';
$lang_custom['welcome_text']	= 'Cher %%vorname%% %%nachname%%,' . "\n\n"
									. 'Merci de vous inscrire à notre service.' . "\n"
									. 'Should you have any further questions, please do not hesitate to contact us. A list of frequently asked questions and answers is available by clicking on the"?" icon in the top right-hand corner.'. "\n\n"
									. '(This email has been generated automatically)';
$lang_custom['tos']				= 'I. Conditions d\'utilisation' . "\n"
									. '1. By signing up and/or clicking on "accept" and/or registering with our service, you signify your agreement to our terms of use and commit to observe them.' . "\n"
									. '2. The provider reserves the right to revise the terms of use at any time without notification.' . "\n\n"
									. 'II. Description of our service' . "\n"
									. '1. Any internet costs the user may have while using this webmail are the user\'s responsibility.' . "\n"
									. '2. The software necessary to access our service(common web browser) must be provided by the user.' . "\n"
									. '3. The provider does not check on messages or any other content stored on b1gMail unless bound by law to do so.' . "\n\n"
									. 'III. User\'s Obligations' . "\n"
									. '1. By purchasing this product you agree not to use it for spamming purposes of any kind and not to use it to offend against German law.' . "\n"
									. '2. The user and only the user is responsible for any operations carried out with the help of this application.' . "\n"
									. '3. It is the user\'s responsibility to keep his/her password safe and secret and to prevent his/her account from being accessed by any third party. If, however, a third pary should unauthorizedly gain access to the user\'s account and perform illegal actions(such as offend against thes terms of use or German law), only the registered user of the account can and will be held liable.' . "\n"
									. '4. It is explicitly prohibited to use this service for the purpose of sending spam emails and mass emails. The user will be liable to compensate for any damage or loss.' . "\n"
									. '5. Any contravention of III. 1-4 or any other section of the terms of use will authorize the provider to delete the contravening user\'s account and all of his/ data without requiring his/her consent.' . "\n\n"
									. 'IV. Data Protection' . "\n"
									. '1. The provider is obliged not to give registration or address data to any third party unless bound by law to do so.' . "\n"
									. '2. The provider is authorized to retain the user\'s address data for an unlimited period of time as documentary support in case of a legal dispute.' . "\n\n"
									. 'V. Final Provisions' . "\n"
									. '1. It is the operator named in the imprint that is responsible for this website and its contents. The producer of the software is not involved.';
$lang_custom['imprint']			= 'Please customize the imprint.<br /><br />You will find it in the <a href="./admin/">administration panel</a> under <i>"Settings" - "Languages" - "Customizable Texts"</i>. This can be done for any language installed.';
$lang_custom['maintenance']		= 'We are currently undergoing some scheduled maintanance to our system in order to improve our service. Unfortunately we are currently not available for that reason. We apologize for any inconvenience.';
$lang_custom['selfcomp_n_sub']	= 'Addressbook entry completed';
$lang_custom['selfcomp_n_text']	= 'Cher Monsieur ou Madame,' . "\n\n"
								.	'%%vorname%% %%nachname%% has just accepted your invitation to complete his/her addressbook entry himself/herself. The updated contact details have been copied into your addressbook.' . "\n\n"
								.	'(This message has been generated automatically.)';
$lang_custom['selfcomp_sub']	= 'Your entry in %%vorname%% %%nachname%%\'s addressbook';
$lang_custom['selfcomp_text']	= 'Cher Monsieur ou Madame,' . "\n\n"
								.	'%%vorname%% %%nachname%% added you to his/her addressbook and is asking you to complete your contact details.' . "\n\n"
								.	'Please click the following link to confirm and complete your contact details in %%vorname%% %%nachname%%\'s addressbook.' . "\n\n"
								.	'%%link%%' . "\n\n"
								.	'Merci d\'avance!!' . "\n\n"
								.	'(Ce message a été généré automatiquement au nom de %%vorname%% %%nachname%%)';
$lang_custom['passmail_sub']	= 'Mot de passe oublié';
$lang_custom['passmail_text']	= 'Dear %%vorname%% %%nachname%%,' . "\n\n" 
								.	'a password request has been requested for your account %%mail%%.' . "\n\n"
								. 	'Your new password is: %%passwort%%' . "\n\n"
								.	'Please click the following link to activate your new password:' . "\n\n"
								. 	'%%link%%' . "\n\n"
								.	'After clicking the link you can log in using the password given above.' . "\n\n"
								.	'CAUTION: When resetting your password, all saved private key passwords will become invalid. You will have to re-import all your private certificates!' . "\n\n"
								.	'(This message has been generated automatically)';
$lang_custom['certmail']		= 'Cher Monsieur ou Madame,' . "\n\n" 
								.	'%%user_name%% (%%user_mail%%) sent you a certified message. To get the message and forward it to your email account, please click the following link or paste it into your browser.' . "\n\n" 
								.	'%%url%%' . "\n\n" 
								.	'Please not that this message will only be stored until %%date%% after which time it will expire.' . "\n\n" 
								.	'(This message has been generated automatically)';
$lang_custom['mail2sms']		= 'Nouveau courriel de %%abs%%: %%betreff%%';
$lang_custom['cs_text']			= 'Cher Monsieur ou Madame,' . "\n\n" 
								. 	'the certified message you sent to %%an%% with the subject %%subject%% has just been read (%%date%%).' . "\n\n" 
								.	'(This message has been generated automatically)';
$lang_custom['clndr_subject']	= 'Calendar reminder: %%title%%';
$lang_custom['clndr_date_msg']	= 'Dear Sir or Madam,' . "\n\n" 
								.	'we would like to remind you of the following event: "%%title%%".' . "\n" 
								.	'It is scheduled for %%date%% at %%time%% o\'clock.' . "\n" 
								.	'Notification: %%message%%' . "\n\n" 
								.	'(This message has been generated automatically)';
$lang_custom['clndr_sms']		= '%%date%% %%time%% - %%subtitle%%';
$lang_custom['receipt_text']	= 'Cher Monsieur ou Madame,' . "\n\n" 
								.	'I have just read your message with the subject heading "%%subject%%" (%%date%%).' . "\n\n" 
								.	'(This message has been generated automatically)';
$lang_custom['alias_sub']		= 'Confirm alias setup';
$lang_custom['alias_text']		= 'Cher Monsieur ou Madame,' . "\n\n"
								.	'%%email%% has just added your email address %%aliasemail%% as sender in his email account.' . "\n"
								.	'The setup must be confirmed by clicking the following link. After clicking the link the you will be able to use your email address as the sender\'s address of the following account: %%email%% ' . "\n"
								.	'If you do not want to use your email address as the sender\'s address, DO NOT click the link and erase this message.' . "\n\n"
								.	'Confirmation Link:' . "\n"
								.	'	%%link%%' . "\n\n"
								.	'(This message has been generated automatically)';
$lang_custom['snotify_sub']		= 'Nouvelle inscription Yoopya Mail (%%datum%%)';
$lang_custom['snotify_text']	= 'Quelqu\'un vient de s\'inscrire à votre service de messagerie Yoopya VIP Mail:' . "\n\n"
								. 	'Email: %%email%%' . "\n"
								.	'Domaine: %%domain%%' . "\n"
								.	'Nom: %%name%%' . "\n"
								.	'Adresse de rue: %%strasse%%' . "\n"
								.	'Code postal/Ville: %%plzort%%' . "\n"
								.	'Pays: %%land%%' . "\n\n"
								.	'Téléphone: %%tel%%' . "\n"
								.	'Fax: %%fax%%' . "\n"
								.	'Email alternatif: %%altmail%%' . "\n\n"
								.	'Details: %%link%%';
$lang_custom['validationsms']	= 'Merci de vous être inscrit! Votre code de déverrouillage: %%code%% - Il suffit de le saisir sur demande!';
$lang_custom['validationsms2']	= 'Veuillez saisir le code de déverrouillage suivant dans "SMS" Afin de terminer le changement de votre numéro: %%code%%';
$lang_custom['activationmail_sub']	= 'Votre inscription (%%email%%)';
$lang_custom['activationmail_text']	= 'Cher Monsieur ou Madame,' . "\n\n" 
								.	'Merci de vous inscrire à notre service. Pour activer votre nouvelle adresse e-mail %%email%%, cliquez simplement sur le lien suivant ou saisissez le code d\'activation suivant lors de votre première connexion.' . "\n\n" 
								.	'	Lien: %%url%%' . "\n"
								.	'	Code: %%activationcode%%' . "\n\n" 
								.	'(Ce message a été généré automatiquement)';

$lang_custom['paynotify_sub']	= 'Commande activée';
$lang_custom['paynotify_text']	= 'Une commande vient d\'être activée:' . "\n\n"
								.	'Commande: #%%order_id%%' . "\n"
								.	'Utilisateur: #%%user_id%%' . "\n"
								.	'Facture N°: %%invoice_no%%' . "\n"
								.	'Client N°: %%customer_no%%' . "\n"
								.	'Mode de paiement: %%payment_method%%' . "\n"
								.	'Montant de la commande: %%order_amount%%' . "\n"
								.	'Montant payé: %%paid_amount%%'. "\n"
								.	'Code de la Transaction: %%txn_id%%';
$lang_custom['orderconfirm_sub']	= 'Votre commande (%%invoice_no%%)';
$lang_custom['orderconfirm_text']	= 'Cher Monsieur ou Madame,' . "\n\n"
								.	'Nous vous remercions de votre commande. Nous avons bien reçu votre paiement et activé la commande.' . "\n"
								.	'Vous pouvez trouver les détails de votre commande (y compris votre facture, le cas échéant) dans \"Preferences\" -> \"Commandes\".' . "\n"
								.	'(Ce message a été généré automatiquement)';
$lang_custom['share_sub']		= 'Partager YooDoc';















$lang_custom['share_text']		= 'Bonjour,' . "\n\n"
									. 'Vous pouvez trouver mon partage YooDoc à l\'adresse suivante:' . "\n"
									. "\t" . '%%url%%' . "\n\n"
									. 'Meilleures salutations,' . "\n\n"
									. '%%firstname%% %%lastname%%';
$lang_custom['ap_autolock_sub']	= 'L\'utilisateur de %%email%% est verrouillé en raison d\'une activité suspecte';
$lang_custom['ap_autolock_text']	= 'L\'utilisateur de %%email%% (#%%id%%) a été verrouillé automatiquement car la limite du point de protection anti-abus a été dépassée.' . "\n\n"
				. 'Email: %%email%% (#%%id%%)' . "\n"
				. 'Points: %%pointsum%%' . "\n\n"
				. 'Déclaration de point:' . "\n"
				. '------------------------------------------------------------------------' . "\n"
				. '%%points%%' . "\n"
				. '------------------------------------------------------------------------' . "\n\n"
				. 'Details: %%link%%';
$lang_custom['contact_subjects']	= 'Question sur l\'offre' . "\n"
				. 'Question sur la procedure d\'inscription' . "\n"
				. 'Question sur l\'ouverture de session' . "\n"
				. 'Mot de passe perdu' . "\n"
				
				
				
				. 'Autres questions';
$lang_custom['notify_date']		= 'Rendez-vous: <strong>%s</strong>';
$lang_custom['notify_newemail']	= '<strong>%d</strong> nouveau email(s): %s';
$lang_custom['notify_email']	= 'Email reçu de <strong>%s</strong>: %s';
$lang_custom['notify_birthday']	= '<strong>%s</strong> a <strong>%d years</strong> aujourd\'hui!';

/**
 * User phrases
 */
$lang_user['weekdays']			= 'SMTWTFS';			// sunday through saturday
$lang_user['weekdays_long']		= array('Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa');			// sunday through saturday
$lang_user['full_weekdays']		= array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');

$lang_user['pleasechose']		= 'Veuillez choisir';
$lang_user['suggestions']		= 'Suggestions';
$lang_user['nosuggestions']		= 'Nous n\'avons trouvé aucune suggestion.';
$lang_user['suggestions_desc']	= 'Nous avons déterminé les suggestions suivantes pour votre nouvelle adresse e-mail. Toutes les adresses affichées sont toujours disponibles:';
$lang_user['choose']			= 'Choisir';
$lang_user['nothanks']			= 'Non merci';
$lang_user['contactform']		= 'Formulaire de contact';
$lang_user['message']			= 'Message';
$lang_user['cform_senderror']	= 'Votre message n\'a pas pu être envoyé. Veuillez réessayer plus tard.';
$lang_user['cform_sent']		= 'Nous avons reçu votre demande et vous répondrons dès que possible.';
$lang_user['nonotifications']	= 'Aucune notification en cours';
$lang_user['notifications']		= 'Notifications';
$lang_user['relevance']			= 'Pertinence';
$lang_user['minchars']			= 'min. %d caracteres';
$lang_user['deliverystatus']	= 'Statut d\'envoi';
$lang_user['mds_delivered']		= 'Envoyé a <strong>%d</strong> destinataire(s).';
$lang_user['mds_deferred']		= 'Envoyé a <strong>%d</strong> destinataire(s) différé.';
$lang_user['mds_failed']		= 'Envoyé a <strong>%d</strong> destinataire(s) échoué.';
$lang_user['mds_processing']	= 'Transmis';
$lang_user['mds_recp_processing']	= 'Transmis';
$lang_user['mds_recp_delivered']	= 'Transmis';
$lang_user['mds_recp_deferred']	= 'Transmission (différée)';
$lang_user['mds_recp_failed']	= 'Transmission échouée';
$lang_user['recipient']			= 'Destinataire';
$lang_user['newgroup']			= 'Nouveau groupe';
$lang_user['associatewith']		= 'Associer avec';
$lang_user['mails_del']			= 'Supprimer e-mails';
$lang_user['auto_save_drafts']	= 'Enregistrer automatiquement les brouillons';
$lang_user['notify_sound']		= 'Jouer son';
$lang_user['notify_types']		= 'Me prévenir de';
$lang_user['notify_email']		= 'nouveaux emails';
$lang_user['notify_birthday']	= 'Anniversaires de mes contacts';
$lang_user['auto']				= 'Automatique';
$lang_user['details_default']	= 'Afficher directement les résultats détaillés';
$lang_user['statement']			= 'Relevé de compte';
$lang_user['description']		= 'Description';
$lang_user['current']			= 'courant';
$lang_user['balance']			= 'Solde';
$lang_user['dynamicbalance']	= 'Crédits mensuels restants';
$lang_user['startingbalance']	= 'Solde de départ';
$lang_user['tx_charge']			= 'Frais de compte (%s)';
$lang_user['tx_coupon']			= 'Coupon (%s)';
$lang_user['tx_sms']			= 'SMS';
$lang_user['langCode_editor']	= 'en';
$lang_user['sendnotify']		= 'Montrer les notifications';
$lang_user['readonly']			= 'Lecture seulement';
$lang_user['sharedfolders']		= 'Dossiers partagés';
$lang_user['years']				= 'année(s)';
$lang_user['loaddraft']			= 'Charger le brouillon';
$lang_user['drafttext']			= 'Voulez-vous charger le brouillon enregistré automatiquement de votre dernier courrier électronique?';
$lang_user['exceededsendlimit']	= 'L\'envoi de cet email dépasserait votre limite de %d email(s) tous les %d minute(s). Votre email n\'a pas pu être envoyé.';
$lang_user['bynotify']			= 'par notification';

// phrases for new nli layout
$lang_user['home']				= 'Accueil';
$lang_user['plans']				= 'Plans';
$lang_user['required']			= 'Obligatoire';
$lang_user['street']			= 'Rue';
$lang_user['nr']				= 'N°';
$lang_user['wishaddressandpw']	= 'Nom, adresse e-mail préférée et mot de passe';
$lang_user['accepttos']			= 'je suis d\'accord avec le';
$lang_user['completesignup']	= 'Inscription complète';
$lang_user['next']				= 'Suivant';
$lang_user['pleasewait']		= 'Veuillez attendre...';
$lang_user['readcertmail']		= 'Lire email certifié';

// misc
$lang_user['menu']				= 'Menu';
$lang_user['fetching']			= 'Récupération';
$lang_user['nocontactselected']	= 'Aucun contact sélectionné';
$lang_user['showmore']			= 'Afficher plus';
$lang_user['paused']			= 'En pause';
$lang_user['langCode']			= 'EN';
$lang_user['skrill']			= 'Skrill (Moneybookers)';
$lang_user['pn_skrill']			= 'The invoice amount (%.02f %s) has been gratefully received using Skrill (Moneybookers).';
$lang_user['pn_custom']			= 'The invoice amount (%.02f %s) will be paid using %s.';
$lang_user['pn_customtext']		= 'Thank you for your order. You have chosen to pay using %s. After our team has reviewed your order, it will be activated immediately.';
$lang_user['pop3server']		= 'Serveur boîte de réception(POP3)';
$lang_user['undonetasks']		= 'Tâches en attente';
$lang_user['donetasks']			= 'Tâches effectuées';
$lang_user['tasklist']			= 'Liste de tâches';
$lang_user['tasklists']			= 'Listes de tâches';
$lang_user['nodatesin31d']		= 'Pas de rendez-vous dans les 31 prochains jours.';
$lang_user['desktopversion']	= 'Version Ordinateur';
$lang_user['new']				= 'Nouveau';
$lang_user['right']				= 'A droite';
$lang_user['bottom']			= 'En bas';
$lang_user['notice']			= 'Notification';
$lang_user['from2']				= 'de';
$lang_user['note']				= 'Note';
$lang_user['task']				= 'Tâche';
$lang_user['addraddtext']		= 'Au moins l\'un des destinataires du courrier électronique n\'est pas dans votre carnet d\'adresses. Vous pouvez facilement ajouter ces destinataires à votre carnet d\'adresses. Il suffit de sélectionner les destinataires que vous souhaitez ajouter à votre carnet d\'adresses, de remplir leur nom et de cliquer sur &quot;Enregistrer&quot.';
$lang_user['addradddone']		= 'L\'adresse (es) a / ont été ajoutée (s) à votre carnet d\'adresses avec succès.';
$lang_user['nomailsselected']	= 'Aucun email sélectionné';
$lang_user['markdone']			= 'Terminé';
$lang_user['unmarkdone']		= 'En cours';
$lang_user['marked']			= 'Marqué';
$lang_user['unread']			= 'Non lu';
$lang_user['markallasread']		= 'Tout marquer comme lu';
$lang_user['markallasunread']	= 'Tout marquer comme non lu';
$lang_user['downloadall']		= 'Tout télécharger';
$lang_user['folderactions']		= 'Actions de dossier';
$lang_user['mailsfromab']		= 'Expéditeur du mail dans le carnet d\'adresses';
$lang_user['att_keywords']		= 'Joint,pièce jointe';
$lang_user['attcheck']			= 'Notification de pièce jointe';
$lang_user['attcheck_desc']		= 'Afficher une notification en cas de pièces jointes oubliées';
$lang_user['sendmail3']			= 'Envoyer';
$lang_user['maintenance']		= 'Maintenance';
$lang_user['search']			= 'Rechercher';
$lang_user['nothingfound']		= 'Rien de semblable.';
$lang_user['jswarning']			= 'Veuillez activer JavaScript. Sinon, vous ne pourrez pas utiliser ce service!';
$lang_user['imprint']			= 'Imprimer';
$lang_user['tos']				= 'Conditions d\'utilisation';
$lang_user['mobilepda']			= 'Version Mobile';
$lang_user['mobiledenied']		= 'Vous n\'êtes pas autorisé à utiliser l\'interface mobile.';
$lang_user['workgroup']			= 'Groupe de travail';
$lang_user['workgroups']		= 'Groupes de travail';
$lang_user['groups']			= 'Groupes';
$lang_user['search2']			= 'Chercher';
$lang_user['searchfor']			= 'Rechercher';
$lang_user['searchin']			= 'Rechercher dans';
$lang_user['datefrom']			= 'from';
$lang_user['dateto']			= 'de';
$lang_user['details']			= 'Details';
$lang_user['sess_expired']		= 'Session a expiré';
$lang_user['sess_expired_desc']	= 'Votre session a été fermée pour raison d\'inactivité pour des raisons de sécurité. Veuillez vous reconnecter.';
$lang_user['doublealtmail']		= 'Il ya un compte qui utilise déjà cette adresse e-mail alternative!';
$lang_user['doublecellphone']	= 'Il ya un compte qui utilise déjà ce numéro de téléphone cellulaire!';
$lang_user['realdel_order']		= 'Voulez-vous vraiment annuler la commande?  Si vous avez déjà payé, nous ne pourrons pas vous attribuer votre paiement. Annueler si vous n\'avez pas encore payé!\n\nSi vous souhaitez annuler une commande que vous avez déjà payée, veuillez contacter notre équipe de support.\n\nCliquez sur OK si vous voulez vraiment annuler la commande ou Annuler pour conserver la commande..';





$lang_user['hiddenelements']	= 'Éléments cachés';
$lang_user['hide']				= 'Cacher';
$lang_user['atreply']			= 'A la réponse';
$lang_user['insertquote']		= 'Citer l\'email original';
$lang_user['invoiceaddress']	= 'Adresse de facturation';
$lang_user['paymentmethod']		= 'Mode de paiement';
$lang_user['finalamount']		= 'Montant du paiement';
$lang_user['placeorder']		= 'Soumettre la commande';
$lang_user['banktransfer']		= 'Virement bancaire';
$lang_user['taxnote']			= 'incl. TVA %.02f%%';
$lang_user['pn_paypal']			= 'Le montant de la facture (%.02f %s) a été accepté par PayPal.';
$lang_user['pn_banktransfer']	= 'Veuillez transférer le montant de la facture (%.02f %s) à notre compte bancaire (voir ci-dessous). Veuillez utiliser le code suivant dans l\objet  - sinon nous ne pouvons pas traiter votre paiement: <b>VK-%s</b>.<br />Dès que nous recevons votre paiement, votre commande sera activée.';

$lang_user['pn_sofortueberweisung']	= 'Le montant de la facture (%.02f %s) has been gratefully received using sofortueberweisung.de.';
$lang_user['orders']			= 'Commandes';
$lang_user['order']				= 'Commande';
$lang_user['thankyou']			= 'Nous vous remercions';

$lang_user['paymentreturn_txt']	= '<p>Merci pour votre paiement.</p><p>Dans le cas où votre paiement a été un succès, votre commande sera activée dès que le paiement est confirmé. Dans la plupart des cas, cela ne prend que quelques secondes.</p><p>Vous pouvez trouver votre état actuel de commande à l\'adresse \"<a href=\"prefs.php?action=orders&sid=%s\">Commandes</a>\" dans \"Préférences\" à tout moment.';
$lang_user['prefs_d_orders']	= 'Consultez vos commandes et téléchargez ou imprimez vos factures.';
$lang_user['orderno']			= 'Commande N°';
$lang_user['amount']			= 'Montant';
$lang_user['invoice']			= 'Facture';
$lang_user['printinvoice']		= 'Imprimer Facture';
$lang_user['orderalreadypaid']	= 'Cette commande a déjà été payée ou a été annulée.';
$lang_user['yourinvoice']		= 'Votre facture';
$lang_user['dearsirormadam']	= 'Cher Monsieur ou Madame';
$lang_user['pos']				= 'Position';
$lang_user['descr']				= 'Description';
$lang_user['ep']				= 'Prix unit';
$lang_user['gp']				= 'Prix';
$lang_user['gb']				= 'Montant total';
$lang_user['vat']				= 'TVA';
$lang_user['net']				= 'net';
$lang_user['gross']				= 'brut';
$lang_user['kindregards']		= 'Cordialement';
$lang_user['invtext']			= 'Veuillez trouver votre facture ci-dessous';
$lang_user['invoiceno']			= 'Facture N°';
$lang_user['customerno']		= 'Client N°';
$lang_user['bankacc']			= 'Compte bancaire';
$lang_user['invfooter']			= 'Cette facture a été générée automatiquement et est valable sans signature.';
$lang_user['kto_inh']			= 'Propriétaire du compte';
$lang_user['kto_nr']			= 'Compte n°';
$lang_user['kto_blz']			= 'Code banque';
$lang_user['kto_inst']			= 'Nom de la banque';
$lang_user['kto_iban']			= 'IBAN';
$lang_user['kto_bic']			= 'BIC/code SWIFT';
$lang_user['kto_subject']		= 'Objet';
$lang_user['pay']				= 'Payer';
$lang_user['completed']			= 'Terminé';
$lang_user['setmailcolor']		= 'Définir couleur';
$lang_user['hotkeys']			= 'Touches de raccourci';
$lang_user['to3']				= 'à';
$lang_user['with']				= 'avec';
$lang_user['selectdraft']		= 'Sélectionner brouillon';
$lang_user['pop3ownerror']		= 'Vous ne pouvez pas récupérer la boîte mails en elle-même.';

$lang_user['hidecustomfolders']	= 'Masquer les dossiers définis par l\'utilisateur';
$lang_user['hideintellifolders']= 'Masquer les dossiers intelligents';
$lang_user['hidesystemfolders']	= 'Masquer les dossiers système';
$lang_user['sendmail2']			= 'Envoyer';
$lang_user['color_0']			= 'Aucun';
$lang_user['color_1']			= 'Blue';
$lang_user['color_2']			= 'Vert';
$lang_user['color_3']			= 'Rouge';
$lang_user['color_4']			= 'Orange';
$lang_user['color_5']			= 'Pourpre';
$lang_user['color_6']			= 'Violet';
$lang_user['colors']			= 'Couleurs';
$lang_user['sendsms2']			= 'Envoyer SMS';
$lang_user['targetfolder']		= 'Dossier de destination';
$lang_user['existingfiles']		= 'Fichiers existants';
$lang_user['zipfile']			= 'fichier zip';
$lang_user['deleteafterextract']= 'Supprimer après extraction';
$lang_user['keep']				= 'Conserver';
$lang_user['overwrite']			= 'Écraser';
$lang_user['extract']			= 'Extrait';
$lang_user['altmaillocked']     = 'L\'adresse e-mail que vous avez saisie n\'est pas autorisée à vous abonner à notre service. Veuillez utiliser une autre adresse e-mail.';

// webdisk share
$lang_user['badshare']			= 'Chemin de partage introuvable.';
$lang_user['protected_desc']	= 'Le dossier est protégé par mot de passe. Entrez le mot de passe et cliquez &quot;OK&quot; pour continuer.';
$lang_user['folder_wrongpw']	= 'Le mot de passe saisi n\'est pas correct.';



// faq
$lang_user['faq']				= 'FAQ';
$lang_user['faqtxt']			= 'Voici les réponses aux questions les plus fréquemment posées. Souvent, c\'est un moyen pratique de trouver des réponses à vos questions. Si voous ne trouvez pas réponse à votre question, <a href="index.php?action=imprint"> n\'hésitez pas à nous contacter</a>.';

// lost password
$lang_user['lostpw']			= 'Mot de passe oublié';
$lang_user['requestpw']			= 'Demander mot de passe';
$lang_user['pwresetfailed']		= 'L\'utilisateur de cette adresse e-mail n\'a pas été trouvé ou n\'a pas d\'email alternatif nécessaire pour recevoir un nouveau mot de passe. Veuillez vérifier votre adresse email saisi et essayez à nouveau.<br /><br /> En cas de doute, <a href="index.php?action=imprint"> nous contacter pour la restauration de votre  mot de passe.';
$lang_user['pwresetsuccess']	= 'Un nouveau mot de passe pour votre compte a été généré et envoyé à l\'adresse e-mail alternative que vous avez indiquée dans votre profil. Pour compléter l\'activation de votre nouveau mot de passe, cliquez sur le lien dans le message. Après avoir cliqué sur le lien, vous pourrez utiliser le nouveau mot de passe.';
$lang_user['pwresetfailed2']	= 'Le nouveau mot de passe n\'a pas pu être activé car le lien de confirmation n\'a pas été ouvert correctement ou parce que le nouveau mot de passe avait déjà été activé. Veuillez ouvrir le lien exactement comme vous l\'avez reçu dans le message.<br /><br />En cas de doute <a href="index.php?action=imprint"> veuillez nous contacter pour la restauration de votre mot de passe </a>.';
$lang_user['pwresetsuccess2']	= 'Votre nouveau mot de passe a été activé avec succès. Vous pouvez maintenant vous connecter avec votre adresse e-mail et votre nouveau mot de passe <a href="index.php">Connexion</a>.';




// sign up
$lang_user['wishaddress']		= 'Adresse préférée';
$lang_user['signup']			= 'Inscription';
$lang_user['signuptxt']			= 'Veuilez remplir le formulaire ci-dessous pour créer votre adresse e-mail gratuitement.';
$lang_user['signuptxt_code']	= 'Si vous avez un code coupon, saisissez-le dans le champ - sinon, laissez le champ vide.';


$lang_user['notmember']			= 'Pas encore membre';
$lang_user['notmembertxt']		= 'Créez votre adresse e-mail gratuitement et profitez de nos merveilleuses fonctionnalités. <a href="index.php?action=signup"><b>Cliquez ici</b></a> pour vous inscrire!';

$lang_user['contactinfo']		= 'Détails du contact';
$lang_user['firstname']			= 'Prénom';
$lang_user['surname']			= 'Nom de famille';
$lang_user['streetnr']			= 'Rue / no';
$lang_user['zipcity']			= 'Zip / Ville';
$lang_user['zip']				= 'Zip';
$lang_user['city']				= 'Ville';
$lang_user['phone']				= 'Téléphone';
$lang_user['fax']				= 'Fax';
$lang_user['altmail']			= 'Email';
$lang_user['altmail2']			= 'Email alternatif';
$lang_user['repeat']			= 'Répéter';
$lang_user['security']			= 'Securité';
$lang_user['code']				= 'Code coupon';
$lang_user['submit']			= 'Envoyer';
$lang_user['iprecord']			= 'Votre adresse IP sera enregistrée pour empêcher les inscriptions frauduleuses.';

$lang_user['safecode']			= 'Code de sécurité';
$lang_user['notreadable']		= 'Si code illisible? Cliquez sur le code de sécurité pour en générer un nouveau.';

$lang_user['misc']				= 'Divers';
$lang_user['tosaccept']			= 'J\'accepte les conditions d\'utilisation';
$lang_user['tosnaccept']		= 'Je refuse les conditions d\'utilisation';
$lang_user['country']			= 'Pays';

$lang_user['checkfields']		= 'Veuillez vérifier les champs marqués en rouge et réessayer..';
$lang_user['pwerror']			= 'Votre mot de passe est trop court (&lt; 4 caractères), est trop similaire à votre nom d\'utilisateur ou ne correspond pas à la répétition du mot de passe.';
$lang_user['plzerror']			= 'Le code postal ne correspond pas à la ville.';
$lang_user['toserror']			= 'Vous devez accepter les conditions d\'utilisation avant de pouvoir vous inscrire.';
$lang_user['regerror']			= 'L\'enregistrement a échoué pour des raisons inconnues. Veuillez réessayer plus tard.';
$lang_user['regdone']			= 'Félicitations, vous vous êtes inscrit avec succès! Vous pouvez maintenant vous connecter avec votre nouvelle adresse e-mail <i>%s</i> et votre mot de passe <a href="index.php"> </a> et utiliser votre compte.';
$lang_user['regdonelocked']		= 'Félicitations, vous vous êtes inscrit avec succès! Votre compte avec l\'adresse e-mail <i>%s</i> doit maintenant être activé par un administrateur avant de pouvoir vous <a href="index.php"> vous connecter </a> et utiliser votre compte. Cela peut prendre un ou deux jours ouvrables.';
$lang_user['reglock']			= 'Nous sommes désolés, mais vous ne pouvez pas vous inscrire pour le moment car votre adresse IP a été enregistrée il y a peu de temps. Veuillez réessayer plus tard.';
$lang_user['reglockdnsbl']		= 'Nous sommes désolés, mais vous ne pouvez pas vous inscrire pour le moment car votre adresse IP est bloquée. Veuillez réessayer ultérieurement ou contacter notre équipe d\'assistance.';
$lang_user['regdisabled']		= 'Nous sommes désolés, mais nous ne pouvons pas accepter de nouvelles inscriptions pour le moment. Veuillez réessayer plus tard.';
$lang_user['signupcouponerror']	= 'Le code de Coupon d\'achat est invalide ou a expiré. Veuillez corriger votre saisie ou laisser le champ vide.';

// address book completion
$lang_user['addrselfcomplete']	= 'Completer le carnet d\'adresses';
$lang_user['completeerr']		= 'Vous avez déjà rempli votre carnet d\'adresses ou le lien n\'est pas valide.';
$lang_user['completeintro']		= 'Merci de prendre le temps de vérifier les informations suivantes et de les remplir (si nécessaire). Une fois que vous avez terminé, cliquez sur le bouton &quot;Enregistrer&quot; pour enregistrer les informations dans le carnet d\'adresses de l\'utilisateur.<br /><i>Remarque:</i> Changer / compléter les informations ultérieurement Le temps sera seulement possilbe après une nouvelle invitation de l\'utilisateur.';
$lang_user['completeok']		= 'Merci d\'avoir complété les informations! L\'utilisateur a été informé de la mise à jour.';
// cert mails
$lang_user['certmailerror']		= 'Le lien pour le message certifié n\'est pas valide ou le message certifié a expiré ou a été supprimé.';

// login
$lang_user['pwcrypted']			= 'La transmission de votre mot de passe est encodée en toute sécurité.';
$lang_user['welcomeback']		= 'Bienvenue, <i>%s</i>.';
$lang_user['otheruser']			= 'Changer de compte';
$lang_user['loginblocked']		= 'En raison d\'un trop grand nombre de tentatives de connexion infructueuses, votre compte restera verrouillé jusqu\'à <i>%s</i>. Si vous avez des questions, s\'il vous plaît contactez-nous.';
$lang_user['badlogin']			= 'Le mot de passe que vous avez saisi ne correspond pas au mot de passe enregistré dans notre système. Veuillez réessayer. Il s\'agit de votre <b>%d.</b> tentative de connexion infructueuse. Après que <b>5</b> tentatives de connexion aient échoué, votre compte sera verrouillé pendant une courte période pour des raisons de sécurité.';
$lang_user['baduser']			= 'L\'adresse e-mail que vous avez Saisie n\'est pas connue de notre système. Veuillez réessayer et assurez-vous que l\'orthographe de l\'adresse e-mail est correcte.';
$lang_user['userlocked']		= 'Votre adresse e-mail a été verrouillée ou n\'a pas encore été activée. Si vous avez des questions, s\'il vous plaît contactez-nous.';
$lang_user['login']				= 'Connexion';
$lang_user['email']				= 'Email';
$lang_user['password']			= 'Mot de passe';
$lang_user['language']			= 'Langue';
$lang_user['savelogin']			= 'Rester connecter';
$lang_user['ssl']				= 'Connexion SSL sécurisée';
$lang_user['smsvalidation'] 	= 'Activer le compte';
$lang_user['smsvalidation_text']= 'Bienvenue! Nous avons envoyé un e-mail ou un SMS contenant votre code d\'activation à l\'adresse e-mail ou le numéro de cellulaire que vous avez entré lors de l\'inscription. Veuillez entrer le code d\'activation pour continuer.';



$lang_user['validationcode']	= 'Code d\'activation';
$lang_user['validation_ok']	    = 'Votre compte a été activé avec succès. Vous pouvez maintenant vous<a href="index.php">connectez</a>.';
$lang_user['validation_err']	= 'Nous n\'avons pas pu activer votre compte. Le compte est déjà activé ou le lien d\'activation est correctement.';

$lang_user['didnotgetcode']		= 'Vous n\'avez pas reçu votre code d\'activation?';
$lang_user['resendcode']		= 'Renvoyer le code';
$lang_user['coderesent']		= 'Votre code d\'activation a été renvoyé.';
$lang_user['validation_resend_text']		= 'Il vous reste %d code d\'activation.';
$lang_user['validation_count_limit']		= 'Vous avez déjà demandé le renvoi de votre code d\'activation %d fois. Veuillez contacter notre équipe d\'assistance si vous rencontrez des problèmes lors de l\'activation du compte.';
$lang_user['validation_time_limit']			= 'Vous pouvez demander un renvoi de votre code d\'activation dans %d:%02d minutes.';

// folders
$lang_user['folders']			= 'Nouveaux Dossiers';
$lang_user['inbox']				= 'Boîte de réception';
$lang_user['outbox']			= 'Boîte d\'envoi';
$lang_user['drafts']			= 'Brouillons';
$lang_user['spam']				= 'Spam';
$lang_user['trash']				= 'Corbeille';

// modes
$lang_user['email']				= 'Email';
$lang_user['organizer']			= 'Agenda';
$lang_user['webdisk']			= 'YooDoc';
$lang_user['prefs']				= 'Paramètres';
$lang_user['start']				= 'Tableau de bord';

// mail th
$lang_user['mails']				= 'Emails';
$lang_user['from']				= 'De';
$lang_user['subject']			= 'Objet';
$lang_user['date']				= 'Date';
$lang_user['size']				= 'Taille';

$lang_user['unknown']			= 'Inconnu';
$lang_user['back']				= 'Retour';
$lang_user['customize']			= 'Personnaliser';
$lang_user['today']				= 'Aujourd\'hui';
$lang_user['yesterday']			= 'Hier';
$lang_user['lastweek']			= 'Semaine dernière';
$lang_user['later']				= 'Anterieur';
$lang_user['ok']				= 'OK';
$lang_user['error']				= 'Erreur';

// webdisk
$lang_user['dragfileshere']		= 'Glisser-déposer simplement vos fichiers dans cette zone.';
$lang_user['list']				= 'Liste';
$lang_user['icons']				= 'Icons';
$lang_user['viewmode']			= 'Affichage';
$lang_user['dnd_upload']		= 'Glisser-déposer';
$lang_user['foldererror']		= 'Impossible de créer le dossier. Un dossier de ce nom existe déjà ou le nom est invalide (minimum de 1 caractère).';

$lang_user['createfolder']		= 'Créer dossier';
$lang_user['uploadfiles']		= 'Télécharger des fichiers';
$lang_user['iteminfo']			= 'Détails';
$lang_user['pleaseselectitem']	= 'Sélectionner un dossier ou un fichier.';
$lang_user['actions']			= 'Action';
$lang_user['count']				= 'Nombre';
$lang_user['droptext']			= 'Pour télécharger des fichiers, faites-les glisser dans le gestionnaire de fichiers et déposez-les dans le dossier.';

$lang_user['filename']			= 'Nom de fichier';
$lang_user['size']				= 'Taille';
$lang_user['created']			= 'Créé';
$lang_user['internalerror']		= 'Erreur interne - Veuillez réessayer plus tard.';
$lang_user['success']			= 'Réussi';
$lang_user['fileexists']		= 'Un fichier de ce nom existe déjà ou ce type de fichier n\'est pas valide.';

$lang_user['nospace']			= 'Espace insuffisant';
$lang_user['nospace2']			= 'Espace Web insuffisant ou erreur interne - le dossier peut etre copié complètement';

$lang_user['space']				= 'Espace';
$lang_user['used']				= 'utilisé';
$lang_user['unlimited']			= 'illimité';
$lang_user['copy']				= 'Copier';
$lang_user['rename']			= 'Renommer';
$lang_user['download']			= 'Télécharger';
$lang_user['move']				= 'Déplacer';
$lang_user['cut']				= 'Couper';
$lang_user['open']				= 'Ouvrir';
$lang_user['delete']			= 'Supprimer';
$lang_user['paste']				= 'Coller';
$lang_user['realdel']			= 'Supprimer irrévocablement?';
$lang_user['realempty']			= 'Voulez-vous vraiment vider le dossier?';
$lang_user['sourcenex']			= 'La source n\'a pas été trouvée.';
$lang_user['notraffic']			= 'Pas assez de trafic pour cette opération';
$lang_user['traffic']			= 'Traffic';
$lang_user['sharing']			= 'Partager';
$lang_user['shared']			= 'Partagé';
$lang_user['share']				= 'Partager';
$lang_user['folder']			= 'Dossier';
$lang_user['save']				= 'Enrégistrer';
$lang_user['saveas']			= 'Enregistrer sous';
$lang_user['cancel']			= 'Annuler';
$lang_user['modified']			= 'Modifié';
$lang_user['sharednote']		= 'Vous partagez actuellement ce dossier. Il est accessible à l\'adresse suivante:';
// organizer
$lang_user['calendar']			= 'Calendrier';
$lang_user['overview']			= 'Aperçu';
$lang_user['todolist']			= 'A Faire';
$lang_user['addressbook']		= 'Carnet d\'adresses';
$lang_user['notes']				= 'Notes';

// notes
$lang_user['edit']				= 'Editer';
$lang_user['text']				= 'Texte';
$lang_user['priority']			= 'Priorité';
$lang_user['prio_-1']			= 'Basse';
$lang_user['prio_0']			= 'Normale';
$lang_user['prio_1']			= 'Haute';
$lang_user['clicknote']			= 'Cliquez sur l\'aperçu du texte d\'une note dans la liste des notes pour afficher le texte complet.';
$lang_user['selaction']			= 'Action';
$lang_user['addnote']			= 'Ajouter une note';
$lang_user['editnote']			= 'Editer une note';
$lang_user['reset']				= 'Réinitialiser';
$lang_user['markasdone']		= 'Marque comme achevé';

// todo list
$lang_user['more']				= 'Plus';
$lang_user['tasks']				= 'Les tâches';
$lang_user['addtask']			= 'Ajouter une tâche';
$lang_user['edittask']			= 'Modifier la tâche';
$lang_user['begin']				= 'Début';
$lang_user['due']				= 'Dû';
$lang_user['status']			= 'Statut';
$lang_user['done']				= 'Terminé';
$lang_user['title']				= 'Titre';
$lang_user['taskst_16']			= 'Pas commencé';
$lang_user['taskst_32']			= 'En cours';
$lang_user['taskst_64']			= 'Terminé';
$lang_user['taskst_128']		= 'Différé';
$lang_user['comment']			= 'Commentaire';

// addressbook
$lang_user['send_anyhow']		= 'Envoyer quand même';
$lang_user['convfolder']		= 'Créer dossier de conversation';
$lang_user['addtogroup']		= 'Ajouter groupe';
$lang_user['addcontact']		= 'Ajouter contact';
$lang_user['editcontact']		= 'Modifier contact';
$lang_user['company']			= 'Compagnie';
$lang_user['addcontact'] 		= 'Ajouter contact';
$lang_user['editcontact']		= 'Modifier contact';
$lang_user['all']				= 'Tout';
$lang_user['group']				= 'Groupe';
$lang_user['export_csv']		= 'Exporter (CSV)';
$lang_user['web']				= 'Web';
$lang_user['userpicture']		= 'Photo utilisateur';
$lang_user['userpicturetext']	= 'Veuillez sélectionner le fichier (JPG, PNG ou GIF) que vous souhaitez utiliser comme photo utilisateur. Notez que si vous avez déjà enregistré une image dans notre système, cette image sera remplacée.';
$lang_user['changepicbyclick']	= 'Vous pouvez changer votre image en cliquant dessus.';
$lang_user['groupmember']		= 'Appartenance à un groupe';
$lang_user['nogroups']			= 'Aucun groupe existant.';
$lang_user['mobile']			= 'Mobile';
$lang_user['priv']				= 'Privé';
$lang_user['work']				= 'Profession';
$lang_user['address']			= 'Adresse';
$lang_user['common']			= 'Options de compte';
$lang_user['default']			= 'Par défaut';
$lang_user['salutation']		= 'Civilité';
$lang_user['mrs']				= 'Mme';
$lang_user['mr']				= 'M.';
$lang_user['position']			= 'Position';
$lang_user['orderpos']			= 'Position';
$lang_user['features']			= 'Fonctionnalités';
$lang_user['birthday']			= 'Date de naissance';
$lang_user['importvcf']			= 'Importer VCF';
$lang_user['exportvcf']			= 'Exporter VCF';
$lang_user['complete']			= 'Auto Completer';
$lang_user['importvcftext']		= 'Veuillez sélectionner la vCard (fichier VCF) que vous souhaitez importer. Veuillez noter qu\'en important le fichier, toutes les données que vous auriez saisies dans le champ  &quot;appuyez pour ajouter&uuml;gen&quot; seront remplacées.';
$lang_user['localfile']			= 'fichier local';
$lang_user['webdiskfile']		= 'fichier YooDoc';
$lang_user['completetext']		= 'Grâce à cette fonction, votre contact peut remplir ses coordonnées lui ou elle-même. Il/elle recevra un message contenant un lien qui lui/elle conduira à un site Web où il/elle pourra saisir ses coordonnées. L\'information sera enregistrée dans votre carnet d\'adresses.<br /><br /> Veuillez sélectionner l\'adresse e-mail que vous souhaitez envoyer le lien vers.';
$lang_user['complete_noemail']	= 'Pour l\'information de ce carnet d\'adresse, il n\'y a pas d\'adresse électronique stockée dans notre système. Veuillez donner au moins une adresse e-mail valide pour utiliser cette fonctionnalité.';
$lang_user['complete_invited']	= 'Pour ce contact une mise à jour des données de contact a déjà été demandée. Vous ne pouvez pas envoyer une nouvelle demande avant que l\'ancienne ne soit acceptée.';
$lang_user['complete_error']	= 'La demande n\'a pas pu être envoyé. Veuillez vérifier si l\'adresse e-mail est valide et réessayer plus tard.';
$lang_user['complete_ok']		= 'La demande a été envoyée. L\'utilisateur peut désormais corriger ou compléter ses données du carnet d\'adresse lui / elle-même. Vous recevrez un message dès que l\'utilisateur a répondu à votre demande.';



$lang_user['members']			= 'Membres';
$lang_user['add']				= 'Ajouter';
$lang_user['import']			= 'Importer';
$lang_user['export']			= 'Exporter';
$lang_user['groupexists']		= 'Il y a un groupe avec ce nom déjà.';
$lang_user['editgroup']			= 'Modifier groupe';
$lang_user['doexport']			= 'Exporter';
$lang_user['invalidpicture']	= 'L\'image que vous avez sélectionné est invalide. Veuillez utiliser une image JPG, PNG, GIF dont la taille ne depasse pas %.02f KB.';
$lang_user['semicolon']			= 'point-virgule';
$lang_user['comma']				= 'Virgule';
$lang_user['tab']				= 'Onglet';
$lang_user['double']			= 'Double';
$lang_user['single']			= 'Unique';
$lang_user['linebreakchar']		= 'Saut de ligne';
$lang_user['sepchar']			= 'Separateur';
$lang_user['quotechar']			= 'Guillemets';
$lang_user['advanced']			= 'Avancée';
$lang_user['type']				= 'Type';
$lang_user['csvfile']			= 'Fichier CSV';
$lang_user['encoding']			= 'Codage';
$lang_user['vcfzipfile']		= 'Fichier ZIP avec fichiers VCF';
$lang_user['addrimporttext']	= 'Veuillez sélectionner le fichier que vous souhaitez importer dans votre carnet d\'adresses. Assurez-vous que le fichier est du même format que celui que vous avez sélectionné sous&quot;Type&quot;.';
$lang_user['invalidformat']		= 'Le format du fichier est inconnu ou le fichier est trop volumineux. Veuillez réessayer.';
$lang_user['file']				= 'Fichier';
$lang_user['association']		= 'Affectation';
$lang_user['existingdatasets']	= 'Données existantes';
$lang_user['update']			= 'Mettre à jour';
$lang_user['ignore']			= 'Ignorer';
$lang_user['datasets']			= 'Jeux de données';
$lang_user['putingroups']		= 'Ajouter de nouveaux contacts aux groupes suivants';
$lang_user['importdone']		= 'Importation terminée. %d données ont été importés.';
$lang_user['pages']				= 'Pages';
$lang_user['contacts']			= 'Contacts';

// email
$lang_user['confirmationsent']	= 'Une confirmation de lecture a été envoyé à cet e-mail.';
$lang_user['thisisadraft']		= 'Cet e-mail est un brouillon.';
$lang_user['editsend']			= 'Modifier/envoyer &raquo;';
$lang_user['conversation']		= 'Conversation';
$lang_user['conversationview']	= 'Afficher la conversation';
$lang_user['unknownmessage']	= 'Message inconnu';
$lang_user['bodyskipped']		= '(Le corps du message ne peut pas être affiché car la taille du message est plus que 64 KB)';
$lang_user['showsource']		= 'Afficher la source';
$lang_user['moveto']			= 'Déplacer dans';
$lang_user['sendmail']			= 'Envoyer';
$lang_user['folderadmin']		= 'Dossier';
$lang_user['previewpane']		= 'Affichage';
$lang_user['preview']			= 'Affichage';
$lang_user['mail_read']			= 'Lire Email';
$lang_user['mail_del']			= 'Supprimer Email';
$lang_user['mail_menu']			= 'Options Email';
$lang_user['print']				= 'Imprimer';
$lang_user['mail_menu']			= 'Options Email';
$lang_user['reply']				= 'Répondre';
$lang_user['replyall']			= 'Répondre à tous';
$lang_user['forward']			= 'Transférer';
$lang_user['redirect']			= 'Redirectionner';
$lang_user['flags']				= 'Drapeaux';
$lang_user['markspam']			= 'Marquer comme spam';
$lang_user['marknonspam']		= 'Marquer comme non-spam';
$lang_user['markread']			= 'Marquer comme lu';
$lang_user['markunread']		= 'Marquer comme non lu';
$lang_user['mark']				= 'Selectionner';
$lang_user['unmark']			= 'Désélectionner';
$lang_user['to']				= 'À';
$lang_user['cc']				= 'Cc';
$lang_user['bcc']				= 'Cci';
$lang_user['replyto']			= 'Répondre à';
$lang_user['quotesel']			= 'Citer le texte sélectionné';
$lang_user['searchsel']			= 'Recherche sur Internet le texte sélectionné';
$lang_user['attachments']		= 'Pièces jointes';
$lang_user['approx']			= 'approx.';
$lang_user['savetowebdisk']		= 'Enregistrer dans YooDoc';
$lang_user['view']				= 'Afficher';
$lang_user['toaddr']			= 'Ajouter au carnet d\'adresses';
$lang_user['read']				= 'Lu';
$lang_user['flagged']			= 'Marqué';
$lang_user['answered']			= 'Répondu';
$lang_user['forwarded']			= 'Transféré';
$lang_user['attachment']		= 'Pièces jointes';
$lang_user['group_mode']		= 'Mode groupe';
$lang_user['yes']				= 'Oui';
$lang_user['no']				= 'Non';
$lang_user['props']				= 'Propriétés';
$lang_user['viewoptions']		= 'Option d\'affichage';
$lang_user['mails_per_page']	= 'Messages par page';
$lang_user['htmlavailable']		= 'Une version HTML de ce message est disponible.';
$lang_user['noexternal']		= 'Le chargement du contenu externe / actif de ce message a été bloqué pour des raisons de sécurité.';
$lang_user['showexternal']		= 'Activer le contenu externe';
$lang_user['emptyfolder']		= 'Vider Dossier';
$lang_user['refresh']			= 'Mettre à jour';
$lang_user['spamtext']			= 'Ce message a été identifié comme du spam.';
$lang_user['spamquestion']		= 'Ce message est-il un spam?';
$lang_user['isnotspam']			= 'Le message n\'est pas du spam &raquo;';
$lang_user['infectedtext']		= 'Le virus suivant a été détecté dans cet e-mail';
$lang_user['elapsed_seconds']	= ' (il y a%d seconds)';
$lang_user['elapsed_minutes']	= ' (il y a %d minutes)';
$lang_user['elapsed_hours']		= ' (il y a %d heures)';
$lang_user['elapsed_days']		= ' (il y a %d jours)';
$lang_user['elapsed_second']	= ' (il y a%d seconds)';
$lang_user['elapsed_minute']	= ' (il y a %d minute)';
$lang_user['elapsed_hour']		= ' (il y a %d heure)';
$lang_user['elapsed_day']		= ' (il y a %d jour)';
$lang_user['sendconfirmation']	= 'Envoyer une confirmation';
$lang_user['senderconfirmto']	= 'L\'expéditeur a demandé une confirmation de réception.';
$lang_user['nomails']			= 'Pas de messages.';
$lang_user['mailsent']			= 'Messages envoyés avec succès.';
$lang_user['certmailinfo']		= 'C\'est un message certifié. Si vous le supprimez, il ne sera plus lisible!';
$lang_user['signed']			= 'Signé numériquement';
$lang_user['badsigned']			= 'Signature numérique non valide';
$lang_user['noverifysigned']	= 'Signature non confiante';
$lang_user['encrypted']			= 'Crypté';
$lang_user['decryptionfailed']	= 'Echec criptage';
$lang_user['movemailto']		= 'Déplacer e-mail vers';



// prefs
$lang_user['autosend']			= 'Envoyer automatiquement';
$lang_user['software_intro']	= 'Ici, vous pouvez télécharger %s &mdash; notre logiciel pour votre PC ou votre Mac. %s contient un vérificateur de mail et plusieurs autres outils pour intégrer nos services dans votre bureau.';
$lang_user['software_win']		= 'Ici vous pouvez télécharger notre logiciel pour votre PC Windows. Windows XP ou supérieur est requis. Après le téléchargement, vous pouvez lancer l\'installation en double-cliquant sur le fichier téléchargé.';
$lang_user['software_mac']		= 'Ici, vous pouvez télécharger notre logiciel pour votre Mac. OS X 10.7 ou plus récent est requis. Après le téléchargement, il suffit d\'extraire le fichier téléchargé et de déplacer le programme dans votre dossier Applications.';



$lang_user['defaults']			= 'Par défaut';
$lang_user['options']			= 'Options';
$lang_user['nospamoverride']	= 'Remplacer le filtre anti-spam';
$lang_user['name']				= 'Nom';
$lang_user['validto']			= 'Valable jusque';
$lang_user['contact']			= 'Contact';
$lang_user['antivirus']			= 'Anti-Virus';
$lang_user['antispam']			= 'Anti-Spam';
$lang_user['filters']			= 'Filtres';
$lang_user['signatures']		= 'Signatures';
$lang_user['aliases']			= 'Aliases';
$lang_user['autoresponder']		= 'Auto Répondeur';
$lang_user['extpop3']			= 'Comptes POP3';
$lang_user['coupons']			= 'Coupons';
$lang_user['software']			= 'Logiciel';
$lang_user['membership']		= 'Adhésion';
$lang_user['validity']			= 'Validité';
$lang_user['certificate']		= 'Certificat';
$lang_user['chaincerts']		= 'Certificat de chaîne';
$lang_user['cert_subject']		= 'Propriétaire';
$lang_user['cert_issuer']		= 'Émetteur';
$lang_user['organization']		= 'Organisation';
$lang_user['organizationunit']	= 'Unité d\'organisation';
$lang_user['commonname']		= 'Nom commun';
$lang_user['state']				= 'État/Province';
$lang_user['version']			= 'Version';
$lang_user['serial']			= 'Numéro de série';
$lang_user['publickey']			= 'Clé publique';
$lang_user['bits']				= 'Bits';
$lang_user['keyring']			= 'Porte-clés';
$lang_user['prefs_d_keyring']	= 'Gérez vos clés et certificats pour la signature et le cryptage des e-mails.';
$lang_user['prefs_d_faq']		= 'Voici les réponses aux questions les plus fréquemment posées.';
$lang_user['prefs_d_common']	= 'Modifiez les paramètres généraux de votre compte (par exemple, les options de lecture et de citation).';



$lang_user['prefs_d_contact']	= 'Modifiez vos coordonnées, qui seront également utilisées pour votre vCard.';
$lang_user['prefs_d_filters']	= 'Trier les messages entrants à l\'aide de règles de filtrage auto-définies.';
$lang_user['prefs_d_signatures']= 'Modifiez vos signatures électroniques ou ajoutez de nouvelles signatures.';
$lang_user['prefs_d_antivirus']	= 'Protégez votre boîte aux lettres et vos fichiers à l\'aide du système antivirus intégré.';

$lang_user['prefs_d_antispam']	= 'Autoriser uniquement les messages que vous êtes sûr de vouloir recevoir.';
$lang_user['prefs_d_aliases']	= 'Obtenir plus d\'adresses e-mail pour ce compte.';
$lang_user['prefs_d_autoresponder'] = 'Votre adresse électronique a-t-elle répondu automatiquement, par ex. Lorsque vous n\'êtes pas disponible.';
$lang_user['prefs_d_extpop3']	= 'Vous pouvez avoir des comptes POP3 externes pour récupérer vos messages dans votre compte de messagerie.';


$lang_user['prefs_d_software']	= 'Téléchargez le logiciel client pour votre ordinateur, Ex. Un vérificateur de courrier.';
$lang_user['prefs_d_membership'] = 'Affichez des informations sur votre espace membre ou résiliez-les si vous le souhaitez.';
$lang_user['prefs_d_coupons']	= 'Échangez les bons que vous avez obtenus des promotions spéciales.';

$lang_user['alias']				= 'Alias';
$lang_user['addalias']			= 'Ajouter alias';
$lang_user['aliastype_1']		= 'Expéditeur';
$lang_user['aliastype_2']		= 'Récepteur';
$lang_user['aliastype_4']		= 'Pas encore activé';
$lang_user['typ_1_desc']		= 'Une fois que vous avez créé votre alias, nous enverrons un message de confirmation à l\'adresse. Dès que le lien dans ce message a été cliqué, l\'alias peut être utilisé.';
$lang_user['aliasusage']		= '<b>%d</b> de <b>%d</b> alias(es) configuré.';
$lang_user['addresstaken']		= 'L\'adresse e-mail que vous avez donnée est prise ou non valide. Veuillez réessayer.';
$lang_user['toomanyaliases']	= 'Vous avez atteint le nombre maximal d\'alias. Vous devez supprimer un alias avant de pouvoir configurer un nouveau..';
$lang_user['addressinvalid']	= 'L\'adresse e-mail n\'est pas valide. Veuillez réessayer.';
$lang_user['addressmustexist']	= 'L\'adresse e-mail n\'existe pas. Les adresses qui ont été configurées comme alias doivent être valides. Veuillez réessayer.';
$lang_user['confirmalias']		= 'L\'alias a été configuré et un message de confirmation a été envoyé à<b>%s</b>. Veuillez ouvrir le lien contenu dans le message dans un delais de 7 jours afin d\'activer l\'alias.';
$lang_user['confirmaliastitle']	= 'Confirmer l\'alias';
$lang_user['confirmaliasok']	= 'L\'alias a été confirmé avec succès et peut maintenant être utilisé.';
$lang_user['confirmaliaserr']	= 'L\'alias a déjà été confirmé, introuvable ou non valide.';
$lang_user['credits']			= 'Crédits';
$lang_user['charge']			= 'Chargez le compte';
$lang_user['buynow']			= 'Acheter maintenant';
$lang_user['pay_using']			= 'Mode de paiement';
$lang_user['paypal']			= 'PayPal';
$lang_user['su']				= 'Virement bancaire';
$lang_user['charge_return']		= 'Merci de compléter votre compte utilisateur. Dès que le paiement a été confirmé par le fournisseur de services de paiement, les crédits que vous avez achetés seront ajoutés à votre solde. Habituellement, cela ne prendra que quelques secondes. À de rares occasions, cependant, il peut prendre plus de temps (par exemple, lorsque votre compte bancaire est insuffisamment financé).';
$lang_user['charge_min']		= 'Le montant minimum de crédit requis pour être sélectionné pour recharger votre compte est de <b>%.02f %s</b>.';
$lang_user['charge_min_err']	= 'Le montant minimum de crédit requis pour être sélectionné pour recharger votre compte est de <b>%.02f %s</b>. Le montant sélectionné n\'est que <b>%.02f %s</b>. Sélectionnez au moins <b>%d</b>.';
$lang_user['charge_desc']		= 'Ici, vous pouvez recharger votre compte de crédit. Dans le champ suivant, indiquez le nombre de crédits que vous souhaitez acheter. Cliquez sur &quot;OK&quot;. Une fois que vous avez vérifié le prix total, cliquez sur &quot;Acheter maintenant&quot; pour payer. Les crédits seront ajoutés à votre solde peu après votre paiement.';

$lang_user['creditseach']		= 'Crédits chacun <b>%.02f %s</b>';
$lang_user['charge2']			= 'Recharger';
$lang_user['chargeitemname']	= 'Recharger votre compte (%d Credits)';
$lang_user['wgmembership']		= 'Membres de groupes de travail';
$lang_user['membersince']		= 'Membre depuis';
$lang_user['cancelmembership']	= 'Supprimer adhésion';
$lang_user['canceltext']		= 'Voulez-vous vraiment supprimer votre adhesion? Toutes les données stockées dans notre système seront perdues et votre adresse electronique ne sera plus valide! Cette étape est irréversible!';
$lang_user['cancelledtext']		= 'Vous avez annulé mon adhésion. Votre compte a été désactivé. Nous vous remercions de l\'intérêt que vous portez à notre service. Nous espérons néanmoins vous revoir très bientôt..';
$lang_user['enable']			= 'Activer';
$lang_user['enablebydefault']	= 'Activer par défaut';
$lang_user['spamfilter']		= 'Filtre anti-spam';
$lang_user['defensive']			= 'Défensif';
$lang_user['aggressive']		= 'Agressif';
$lang_user['bayesborder']		= 'Politique de filtrage';
$lang_user['spamaction']		= 'Action spam';
$lang_user['block']				= 'Bloquer';
$lang_user['spamindex']			= 'Base de données de formation';
$lang_user['entries']			= 'Entrées';
$lang_user['resetindex']		= 'Réinitialiser la base de données';
$lang_user['resetindextext']	= 'Réinitialisez la base de données si le filtre anti-spam ne bloque pas facilement les messages entrants ou s\'il bloque trop de messages non-spam. Après cela, vous pouvez ré-former le filtre anti-spam en marquant les messages entrants comme spam ou non-spam.';
$lang_user['unspamme']			= 'Courriels auto-envoyés';
$lang_user['applied']			= 'Appliqué';
$lang_user['addfilter']			= 'Ajouter un filtre';
$lang_user['active']			= 'Actif';
$lang_user['filterrequiredis']	= 'Appliquer un filtre à la rencontre des e-mails';
$lang_user['editfilter']		= 'Modifier le filtre';
$lang_user['stoprules']			= 'Passer autres filtres';
$lang_user['attachmentlist']	= 'Liste des pièces jointes';
$lang_user['inboxrefresh']		= 'Actualiser';
$lang_user['every']				= 'chaque';
$lang_user['seconds']			= 'Secondes';
$lang_user['insthtmlview']		= 'Préfére HTML';
$lang_user['weekstart']			= 'Début de la semaine';
$lang_user['dateformat']		= 'Format de date';
$lang_user['composeprefs']		= 'Options d\'envoi';
$lang_user['retext']			= 'Préfixe de Réponse ';
$lang_user['fwdtext']			= 'Préfixe de transfert';
$lang_user['defaultsender']		= 'Experditeur par défault';
$lang_user['sendername']		= 'Nom expéditeur';
$lang_user['receiveprefs']		= 'Option de réception';
$lang_user['forwarding']		= 'Envoi';
$lang_user['to2']				= 'à';
$lang_user['deleteforwarded']	= 'Supprimer les messages transférés';
$lang_user['mail2sms']			= 'Mail à SMS';
$lang_user['redeemcoupon']		= 'Échange de coupon';
$lang_user['couponerror']		= 'Le coupon ne peut pas être échangé. Le code coupon peut être incorrect ou déjà utilisé.';
$lang_user['couponok']			= 'Le Coupon a été échangé avec succès.';
$lang_user['virusfilter']		= 'Filtre de virus';
$lang_user['virusaction']		= 'Action de virus';
$lang_user['changepw']			= 'Changer mot de passe';
$lang_user['addsignature']		= 'Ajouter signature';
$lang_user['editsignature']		= 'Modifier signature';
$lang_user['question']			= 'Question';
$lang_user['addpop3']			= 'Ajouter compte POP3';
$lang_user['editpop3']			= 'Modifier compte POP3';
$lang_user['toomanypop3']		= 'Vous avez atteint le nombre maximal de comptes POP3. Vous devez supprimer un compte avant de pouvoir en créer un nouveau.';
$lang_user['pop3usage']			= '<b>%d</b> sur <b>%d</b> Compte (s) créé.';
$lang_user['username']			= 'Nom d\'utilisateur';
$lang_user['host']				= 'Nom hôte';
$lang_user['lastfetch']			= 'Dernière récupération';
$lang_user['port']				= 'Port';
$lang_user['pop3target']		= 'Dossier cible POP3';
$lang_user['never']				= 'jamais';
$lang_user['keepmails']			= 'Conserver les messages sur le serveur';
$lang_user['pop3loginerror']	= 'Avec ses données de connexion, aucune connexion au serveur POP3 n\'a pu être établie.';
$lang_user['sendsmsnotify']		= 'Envoyer alerte SMS';
$lang_user['plaintextcourier']	= 'Email en texte brut';
$lang_user['usecourier']		= 'Utiliser police à largeur fixe';
$lang_user['subscribe']			= 'S\'abonner';
$lang_user['newsletter']		= 'Newsletter';
$lang_user['val24error']		= 'Vous pouvez changer votre numéro de téléphone cellulaire une fois toutes les 24 heures seulement!';
$lang_user['addcert']			= 'Importer certificate';
$lang_user['publiccerts']		= 'Certificats publics';
$lang_user['owncerts']			= 'Certificats personnels';
$lang_user['addcerttext']		= 'Veuillez choisir le certificat public (format PEM) que vous souhaitez importer. Vous pouvez télécharger un fichier à partir de votre ordinateur local ou choisir un fichier à partir de YooDoc (si disponible).';
$lang_user['certstoreerr']		= 'Le certificat ne peut pas être importé. Il est non valide, pas au format PEM / PKCS12, ne convient pas à des fins de courrier électronique ou existe déjà dans votre porte-clés.';
$lang_user['requestcert']		= 'demande de certificat';
$lang_user['addprivcerttext']	= 'Choisissez le certificat privé (format PEM) que vous souhaitez importer et la clé privée appropriée et entrez le mot de passe de la clé privée au cas où la clé est cryptée (recommandé).';
$lang_user['addprivcert12text']	= 'Choisissez le certificat privé / paquet de clés privées (format PKCS12; * .p12 / * .pfx) et tapez le mot de passe d\'importation du fichier.';
$lang_user['exportprivcerttext']= 'Tapez un mot de passe que vous souhaitez chiffrer les données exportées avec.';
$lang_user['pkcs12file']		= 'Fichier PKCS12';
$lang_user['certexportpwerror']	= 'Le mot de passe est trop court (<4 caractères) ou la répétition ne correspond pas au mot de passe.';
$lang_user['certexporterror']	= 'Le certificat ne peut pas être exporté. Veuillez réessayer plus tard.';
$lang_user['key']				= 'Clé';
$lang_user['privcertstoreerr'] 	= 'Le certificat ne peut pas être importé. Le mot de passe est incorrect ou le certificat / clé est invalide, ne correspond pas, ne sont pas au format PEM ou ne conviennent pas à des fins de courrier électronique ou existent déjà dans votre porte-clés.';
$lang_user['issuecert_noaddr']	= 'Des certificats pour toutes vos adresses e-mail / alias sont déjà disponibles. Nous ne pouvons délivrer de certificats que pour les adresses auxquelles vous n\'avez déjà pas de certificat.';
$lang_user['issuecert_addrdesc']= 'Veuillez choisir l\'adresse électronique pour laquelle le certificat doit être délivré. Le certificat n\'est utilisable qu\'avec l\'adresse électronique choisie. Vous ne pouvez pas choisir les adresses e-mail pour lesquelles vous avez déjà un certificat.';
$lang_user['issuecert_passdesc']= 'Revoyez votre demande de certificat et saisissez votre mot de passe pour continuer.';
$lang_user['issuecert_wrongpw'] = 'Le mot de passe que vous avez saisi est incorrect. Utilisez le même mot de passe que vous utilisez pour vous connecter.';
$lang_user['issuecert_err'] 	= 'Désolé, nous ne pouvons pas vous délivrer un certificat pour le moment, pour des raisons inconnues. Réessayez plus tard et contactez-nous au cas où le problème persistera.';





// start
$lang_user['welcome']			= 'Bienvenue';
$lang_user['welcometext']		= '<b>Bienvenue</b> à %s, %s!';
$lang_user['newmailtext']		= '<b>%d</b> messages non lus';
$lang_user['datetext']			= '<b>%d</b> Agenda';
$lang_user['tasktext']			= '<b>%d</b> Notes annulées';
$lang_user['newmailtext1']		= '<b>%d</b> message non lu';
$lang_user['datetext1']			= '<b>%d</b> Agenda';
$lang_user['tasktext1']			= '<b>%d</b> note annulée';
$lang_user['websearch']			= 'Recherche Web';
$lang_user['activewidgets']		= 'Widgets actifs';
$lang_user['quicklinks']		= 'Raccourcis';
$lang_user['logout']			= 'Déconnexion';
$lang_user['logoutquestion']	= 'Êtes-vous sûr de vouloir vous déconnecter?';

// folders
$lang_user['sysfolders']		= 'Dossiers système';
$lang_user['ownfolders']		= 'Dossiers personnels';
$lang_user['parentfolder']		= 'Dossier Parent';
$lang_user['subscribed']		= 'Souscrit';
$lang_user['storetime']			= 'Période de conservation des données';
$lang_user['intelligent']		= 'Intelligent';
$lang_user['addfolder']			= 'Ajouter un nouveau dossier';
$lang_user['editfolder']		= 'Modifier le dossier';
$lang_user['days']				= 'jour(s)';
$lang_user['weeks']				= 'semaine(s)';
$lang_user['months']			= 'mois(s)';
$lang_user['conditions']		= 'Condition(s)';
$lang_user['requiredis']		= 'Afficher l\'email si il rencontre';
$lang_user['ofatleastone']		= 'au moins un';
$lang_user['ofevery']			= 'chaque';
$lang_user['oftheseconditions']	= 'condition(s).';
$lang_user['isequal']			= 'est égal à';
$lang_user['isnotequal']		= 'n\'est pas égal à';
$lang_user['contains']			= 'contient';
$lang_user['notcontains']		= 'ne contient pas';
$lang_user['startswith']		= 'commence par';
$lang_user['endswith']			= 'se termine par';

// compose
$lang_user['deletedraft']		= 'Supprimer le brouillon';
$lang_user['linesep']			= 'Ligne de limitation';
$lang_user['linesep_desc']		= 'Afficher la ligne de limitation lors de la composition mails au format texte';
$lang_user['blockedrecipients']	= 'Le/les destinataire(s) suivant est/sont bloqués: <b>%s</b>. Veuillez corriger la liste des destinataires et réessayez.';
$lang_user['invalidcode']		= 'code invalide.';
$lang_user['attachvc']			= 'Carte de visite';
$lang_user['certmail']			= 'Courrier certifié';
$lang_user['mailconfirmation']	= 'Demander confirmation de réception';
$lang_user['savecopy']			= 'Enregistrer une copie dans';
$lang_user['fromaddr']			= 'du carnet d\'adresses';
$lang_user['plaintext']			= 'Plein texte';
$lang_user['htmltext']			= 'HTML';
$lang_user['srcmsg']			= 'message original';
$lang_user['addattach']			= 'ajouter une pièce jointe';
$lang_user['addattachtext']		= 'Sélectionnez le fichier que vous souhaitez joindre à votre courriel et cliquez sur &quot;OK&quot;. Vous pouvez télécharger un fichier à partir de votre ordinateur ou utiliser un fichier de votre webdisc (si disponible)';
$lang_user['toobigattach']		= 'Le fichier que vous avez sélectionné est trop volumineux. Veuillez limiter la taille totale de toutes les pièces jointes en dessous de %.02f KB.';
$lang_user['smartattach']		= 'pièce jointe intelligent';
$lang_user['savedraft']			= 'Enregistrer comme brouillon';
$lang_user['waituntil1']		= 'Veuillez patienter';
$lang_user['waituntil2']		= 'secondes pour envoyer l\'e-mail.';
$lang_user['waituntil3']		= 'Pour envoyer un autre email, veuillez patienter <b>%d</b> secondes.';
$lang_user['norecipients']		= 'Vous n\'avez pas saisi une adresse destinataire valide. Veuillez retourner et essayer à nouveau.';
$lang_user['toomanyrecipients']	= 'Il y a un nombre maximal de destinataires de <b>%d</b> par courrier électronique. En Selectinnant <b>%d</b> destinataires par courrier électronique, vous avez dépassé la limite. Veuillez corriger le nombre de destinataires et réessayer.';
$lang_user['sendfailed']		= 'Le mail n\'a pas pu être envoyé. Une erreur inconnue est survenue. Veuillez réessayer plus tard.';
$lang_user['sign']				= 'Signe';
$lang_user['encrypt']			= 'Chiffrer';
$lang_user['smimeerr0']			= 'Vous n\'avez pas saisi de destinataire valide.';
$lang_user['smimeerr1']			= 'Vous avez choisi de signer ce message, mais vous n\'avez pas de certificat privé pour l\'adresse expéditrice choisie dans votre porte-clés.' . "\n\n" . 'Veuillez ajouter un certificat approprié à votre porte-clés (dans "Préférences") et essayez à nouveau.';
$lang_user['smimeerr2']			= 'Vous avez choisi de chiffrer ce message, mais vous n\'avez pas de certificats publics d\'un ou plusieurs destinataires dans votre porte-clés' . "\n\n" . 'Veuillez ajouter des certificats publics des destinataires suivants à votre porte-clés et essayez de nouveau:';

// calendar
$lang_user['nodatesin6m']		= 'Aucun rendez-vous dans les 6 prochains mois.';
$lang_user['day']				= 'Jour';
$lang_user['week']				= 'Semaine';
$lang_user['month']				= 'Mois';
$lang_user['adddate']			= 'Ajouter une note';
$lang_user['nocalcat']			= '(Aucun groupe)';
$lang_user['date2']				= 'Note';
$lang_user['close']				= 'Fermer';
$lang_user['attendees']			= 'Participants';
$lang_user['none']				= 'aucun';
$lang_user['end']				= 'Fin';
$lang_user['location']			= 'Emplacement';
$lang_user['repeating']			= 'Répéter';
$lang_user['reminder']			= 'Rappel';
$lang_user['editgroups']		= 'Modifier groupes';
$lang_user['mailattendees']		= 'Courriel aux participants';
$lang_user['btr']				= 'Concernant';
$lang_user['wholeday']			= 'toute la journée';
$lang_user['thisevent']			= 'original';
$lang_user['color']				= 'Couleur';
$lang_user['addgroup']			= 'Ajouter un groupe';
$lang_user['dates']				= 'Note(s)';
$lang_user['dates2']			= 'Notes';
$lang_user['duration']			= 'Durée';
$lang_user['hours']				= 'heure(s)';
$lang_user['minutes']			= 'minute(s)';
$lang_user['byemail']			= 'par email';
$lang_user['bysms']				= 'par SMS';
$lang_user['timeframe']			= 'Délai';
$lang_user['timebefore']		= 'avant';
$lang_user['repeatoptions']		= 'Options de répétition';
$lang_user['until']				= 'jusqu\'à';
$lang_user['times']				= 'fois';
$lang_user['endless']			= 'interminable';
$lang_user['repeatcount']		= 'Répéter';
$lang_user['interval']			= 'Intervalle';
$lang_user['besides']			= 'à l\'exception de';
$lang_user['at']				= 'sur';
$lang_user['ofthemonth']		= 'du mois';
$lang_user['first']				= 'premier jour';
$lang_user['second']			= 'deuxième jour';
$lang_user['third']				= 'troisième jour';
$lang_user['fourth']			= 'quatrième jour';
$lang_user['last']				= 'dernier jour';
$lang_user['editdate']			= 'modifier le jour';
$lang_user['cw']				= 'calendrier de la semaine';

// sms
$lang_user['sms']				= 'SMS';
$lang_user['sendsms']			= 'Envoyer SMS';
$lang_user['smsoutbox']			= 'Boîte d\'envoi SMS';
$lang_user['accbalance']		= 'Solde du compte';
$lang_user['price']				= 'Prix (crédits)';
$lang_user['chars']				= 'Caractères';
$lang_user['smssent']			= 'Votre SMS a été envoyé avec succès.';
$lang_user['smssendfailed']		= 'Votre SMS n\'a pas pu être envoyé. Vous pouvez ne pas avoir assez de crédit pour envoyer ce message ou il peut y avoir une erreur interne temporaire. Veuillez réessayer plus tard.';

$lang_user['smsvalidation2'] 	= 'Activer les fonctions SMS';
$lang_user['smsvalidation2_text'] = 'Veuillez saisir le code d\'activation que nous avons envoyé dans votre téléphone portable.';
$lang_user['pleasevalidate']	= 'Veuillez saisir votre numéro de téléphone portable dans vos coordonnées (dans "Préférences") afin d\'activer les fonctions SMS';

$lang_user['invalidsmscode']	= 'Le code d\'activation n\'est pas valide. Veuillez vérifier votre saisie et réessayer.';

// Added in 7.4.1
$lang_user['taxid']     		= 'TVA';
$lang_user['yourtaxid'] 		= 'Votre TVA';
$lang_user['redirect_note']     = 'Avis de redirection';
$lang_user['login_with_alias']  = 'Autoriser la connexion avec un alias';
$lang_user['editalias'] 		= 'Editer alias';

$lang_custom['cs_subject']      = 'Accusé de réception';
$lang_custom['deref']   		= 'La page sur laquelle vous étiez essaie de vous envoyer sur<br /><br /> %s. <br /><br /> Si vous ne souhaitez pas visiter cette page, vous pouvez fermer cette fenêtre.';
/**
 * Admin phrases (not translated)
 */
$lang_admin['disabled']			= 'disabled';
$lang_admin['republickey']		= 'Site key';
$lang_admin['reprivatekey']		= 'Secret key';
$lang_admin['splashimage']		= 'Splash image';
$lang_admin['login_bg_1']		= 'Envelope tree';
$lang_admin['addtransaction']	= 'Add transaction';
$lang_admin['edittransaction']	= 'Edit transaction';
$lang_admin['booked']			= 'Booked';
$lang_admin['cancelled']		= 'Cancelled';
$lang_admin['transactions']		= 'Transactions';
$lang_admin['description']		= 'Description';
$lang_admin['cancel']			= 'Cancel';
$lang_admin['uncancel']			= 'Un-cancel';
$lang_admin['mail_groupmode']	= 'Email grouping';
$lang_admin['props']			= 'Properties';
$lang_admin['flags']			= 'Flags';
$lang_admin['read']				= 'Read';
$lang_admin['answered']			= 'Answered';
$lang_admin['forwarded']		= 'Forwarded';
$lang_admin['flagged']			= 'Flagged';
$lang_admin['done']				= 'Done';
$lang_admin['attachment']		= 'Attachment';
$lang_admin['color']			= 'Color';
$lang_admin['min_draft_save']	= 'Min. draft save interval';
$lang_admin['auto_save_drafts']	= 'Automaticaly save drafts';
$lang_admin['mail_send_code']	= 'Captcha code when sending mails';
$lang_admin['sms_send_code']	= 'Captcha code when sending SMS';
$lang_admin['timeframe']		= 'Timeframe';
$lang_admin['last7d']			= 'last 7 days';
$lang_admin['sendstats']		= 'Email send statistics';
$lang_admin['recvstats']		= 'Email receive statistics';
$lang_admin['pfrulenote']		= '(only for text fields (regular expressions) or date fields (e.g. &quot;&gt;= 18y&quot;))';
$lang_admin['vatratenotice']	= 'The VAT rate is configurable per country at';
$lang_admin['eucountry']		= 'EU country';
$lang_admin['registered']		= 'Registered';
$lang_admin['max']				= 'max.';
$lang_admin['onlyfor']			= 'only for';
$lang_admin['deliverystatus']	= 'Delivery status';
$lang_admin['acpiconsfrom']		= 'ACP icons by';
$lang_admin['acpbgfrom']		= 'ACP background by';
$lang_admin['addservices']		= 'Additional services';
$lang_admin['mailspace_add']	= 'Add. email space';
$lang_admin['diskspace_add']	= 'Add. webdisk space';
$lang_admin['traffic_add']		= 'Add. webdisk traffic';
$lang_admin['notifications']	= 'Notifications';
$lang_admin['notifyinterval']	= 'Check for notifications every';
$lang_admin['notifylifetime']	= 'Delete notifications after';
$lang_admin['days2']			= 'day(s)';
$lang_admin['after']			= 'after';
$lang_admin['nosignupautodel']	= 'Automatic deletion when never logged in';
$lang_admin['blobcompress']		= 'Compress user database';
$lang_admin['userdbvacuum']		= 'Optimize blob databases';
$lang_admin['userdbvacuum_desc']= 'When saving multiple objects in one file per user, it can happen that disk space is not released immediately after deleting large numbers of objects.  This feature will compact the database sizes and optimize access speed.';
$lang_admin['rebuildblobstor']	= 'Convert storage format';
$lang_admin['rebuildblobstor_desc']	= 'When changing the storage method for a data type, all objects which have already been stored remain in their format. You can use this feature to convert objects in your old storage method to your new storage method.';
$lang_admin['rbbs_email']		= 'Convert emails';
$lang_admin['rbbs_webdisk']		= 'Convert webdisk files';
$lang_admin['separatefiles']	= 'one file per object';
$lang_admin['userdb']			= 'one file per user';
$lang_admin['nliarea']			= 'Not logged-in area';
$lang_admin['contactform']		= 'Contact form';
$lang_admin['contactform_name']	= 'Name field in contact form';
$lang_admin['contactform_subject']	= 'Subject field in contact form';
$lang_admin['cfs_note']			= 'The subject choices can be customized per language at';
$lang_admin['captchaprovider']	= 'Captcha provider';
$lang_admin['privatekey']		= 'Private key';
$lang_admin['publickey']		= 'Public key';
$lang_admin['theme']			= 'Theme';
$lang_admin['write_xsenderip']	= 'Write X-Sender-IP header';
$lang_admin['fts_bg_indexing']	= 'Automatic background indexing';
$lang_admin['signupsuggestions']= 'Offer address suggestions';
$lang_admin['buildindex']		= 'Build index';
$lang_admin['buildindex_desc']	= 'You can use this feature to add non-indexed mails to the full text search index.<br /><br />This may be necessary when you have just enabled the full text search feature for users who already exist and did not have a search index so far.';
$lang_admin['optimizeindex']	= 'Optimize index';
$lang_admin['optimizeindex_desc']	= 'This feature can be used to optimize the search index databases of your users. Optimization may release unused space, compact the databases and increase the search performance in case databases became fragmented over time.';
$lang_admin['organizerdav']		= 'CalDAV/CardDAV';
$lang_admin['ftsearch']			= 'Full text search';
$lang_admin['ftsindex']			= 'Full text index';
$lang_admin['showlist']			= 'Show list';
$lang_admin['lastactivity']		= 'Last activity';
$lang_admin['never']			= 'never';
$lang_admin['ap_medium_limit']	= 'Warn limit';
$lang_admin['ap_hard_limit']	= 'Lock limit';
$lang_admin['ap_expire_time']	= 'Points expire after';
$lang_admin['ap_expire_mode']	= 'Expiration plan';
$lang_admin['ap_dynamic']		= 'All points expire as soon as the user did not get any new points in the specified timeframe';
$lang_admin['ap_static']		= 'Points expire individually as soon as they reach the specified age';
$lang_admin['ap_autolock']		= 'Automatically lock accounts';
$lang_admin['ap_athardlimit']	= 'When exceeding the lock limit';
$lang_admin['hours']			= 'Hour(s)';
$lang_admin['pointtypes']		= 'Point types';
$lang_admin['ap_warn_medium']	= '<b>%d</b> active user(s) has/have exceeded the abuse protect warn limit.';
$lang_admin['ap_warn_hard']		= '<b>%d</b> active user(s) has/have exceeded the abuse protect lock limit.';
$lang_admin['ap_autolock_log']	=  "\n" . '[%s] User locked by abuse protection system after exceeding the lock limit (%d >= %d).';
$lang_admin['ap_autolock_notify']	= 'Lock notification';
$lang_admin['limit_amount_count']	= 'Max. amount';
$lang_admin['limit_amount_mb']	= 'Max. traffic (MB)';
$lang_admin['limit_interval_m']	= 'Period (minutes)';
$lang_admin['ap_type1']			= 'Attempt to exceed the recipient count limit';
$lang_admin['ap_comment_1']		= 'Compose form, %d recipients';
$lang_admin['ap_comment_1_m']	= 'Mobile compose form, %d recipient';
$lang_admin['ap_type2']			= 'Attempt to exceed the sending frequency limit';
$lang_admin['ap_type3']			= 'Attempt to send to blocked recipients';
$lang_admin['ap_comment_3']		= 'Compose form, to %s';
$lang_admin['ap_comment_3_m']	= 'Mobile compose form, to %s';
$lang_admin['ap_comment_7']		= 'Time between opening form and sending: %d seconds';
$lang_admin['ap_comment_21']	= 'Received %d mails in last %d minutes';
$lang_admin['ap_comment_22']	= 'Received %.02f MB in last %d minutes';
$lang_admin['ap_type4']			= 'Attempt to send to existing domain, but non-existing recipient';
$lang_admin['ap_type5']			= 'Attempt to send to non-existing domain';
$lang_admin['ap_type6']			= 'SMTP submission without prior POP3/IMAP login';
$lang_admin['ap_type7']			= 'Sending of an email too quickly after opening the compose form';
$lang_admin['ap_type21']		= 'High email receiving frequency';
$lang_admin['ap_type22']		= 'High incoming email traffic';
$lang_admin['workgroup']		= 'Workgroup';
$lang_admin['noaccess']			= 'No access';
$lang_admin['readonly']			= 'Read only';
$lang_admin['readwrite']		= 'Read / Write';
$lang_admin['sharedfolders']	= 'Email folders';
$lang_admin['recover']			= 'Recover';
$lang_admin['min_resend_interval_s']	= 'Minimum time (seconds)';
$lang_admin['minpasslength']	= 'Minimum password length';
$lang_admin['text_notify_date']	= 'Appointment notification';
$lang_admin['text_notify_newemail']	= 'Email notification';
$lang_admin['text_notify_email']	= 'Email notification from filter';
$lang_admin['text_notify_birthday']	= 'Birthday notification';
$lang_admin['text_contact_subjects']	= 'Contact form subjects';
$lang_admin['text_ap_autolock_sub']		= 'Abuse protect lock email subject';
$lang_admin['text_ap_autolock_text']	= 'Abuse protect lock email text';
$lang_admin['ssl_signup_enable']	= 'Signup via SSL';
$lang_admin['showcheckboxes']	= 'Multi-select using checkboxes';
$lang_admin['domaindisplay']	= 'Domain display';
$lang_admin['ddisplay_normal']	= '@ char in dropdown';
$lang_admin['ddisplay_separate']= '@ char separated';
$lang_admin['signupdnsbl']		= 'Signup DNSBL filter';
$lang_admin['blocksignup']		= 'Reject signup';
$lang_admin['activatemanually']	= 'Activate manually';
$lang_admin['details']			= 'Details';
$lang_admin['compresspages']	= 'Compress page output';
$lang_admin['comment']			= 'Comment';
$lang_admin['resetstats']		= 'Reset statistics';
$lang_admin['reallyresetstats']	= 'Do you really want to reset the statistics?';
$lang_admin['payment']			= 'Payment';
$lang_admin['waitingorders']	= '<b>%d</b> orders with custom payment methods are awaiting activation.';
$lang_admin['disable']			= 'Disable';
$lang_admin['enablechrgskrill']	= 'Payments by Skrill';
$lang_admin['skrillacc']		= 'Skrill account';
$lang_admin['skrillsecret']		= 'Secret word';
$lang_admin['skrill']			= 'Skrill (Moneybookers)';
$lang_admin['paymentmethod']	= 'Payment method';
$lang_admin['addpaymethod']		= 'Add payment method';
$lang_admin['invoice']			= 'Invoice';
$lang_admin['at_activation']	= 'Create at activation time';
$lang_admin['at_order']			= 'Create at order time';
$lang_admin['sync']				= 'Synchronization';
$lang_admin['syncml']			= 'Synchronization';
$lang_admin['hotkeys']			= 'Hotkeys';
$lang_admin['log_autodelete']	= 'Auto archiving';
$lang_admin['enableolder']		= 'Enable for entries older than';
$lang_admin['week']				= 'Week';
$lang_admin['calendarviewmode']	= 'Calendar view';
$lang_admin['month']			= 'Month';
$lang_admin['points']			= 'Points';
$lang_admin['lastpoint']		= 'Last point';
$lang_admin['abuseprotect']		= 'Abuse protection';
$lang_admin['toolbox']			= 'Toolbox';
$lang_admin['test']				= 'Test';
$lang_admin['release']			= 'Release';
$lang_admin['preparing']		= 'Preparing';
$lang_admin['releasedone']		= 'The version has been released. Your users can download it in their accounts at &quot;Preferences&quot; &raquo; &quot;Software&quot;. Users of older version will receive an update notification.';
$lang_admin['reallyrelease']	= 'Are you sure you want to release this version?';
$lang_admin['releaseversion']	= 'Test and release version';
$lang_admin['toolboxrelease']	= 'You can now test and release the version. Click &quot;Test (Windows/Mac)&quot; to download a test copy of the version. After testing, you can release the version to your users by clicking &quot;Release&quot;.';
$lang_admin['toolboxonlinenote']= 'When clicking &quot;Test&quot; or &quot;Release&quot;, the configuration of this release will be transmitted to the b1gMail project server in order to create the release files. Creating the test or release versions may take a few minutes.';
$lang_admin['keepcurrentimg']	= 'Keep current image';
$lang_admin['toolboxfileerr']	= 'The following images could not be saved. Please ensure that the image matches the required size and is in PNG format.';
$lang_admin['versions']			= 'Versions';
$lang_admin['addversion']		= 'Add version';
$lang_admin['baseversion']		= 'Base version';
$lang_admin['created']			= 'Created';
$lang_admin['released']			= 'Released';
$lang_admin['tbx_welcome1']		= 'The Toolbox is an application that can be installed by your cutomers on their desktop PCs. It contains an email checker, a tool to send SMS, a fax printer, Webdisk synchronization and more.';
$lang_admin['tbx_welcome2']		= 'In order to offer the Toolbox download to your users, you must create a Toolbox version and release it. Just click at &quot;Add&quot; at the bottom of this page, configure your Toolbox version and test and release it. Your users can find the Toolbox downloads in their account at &quot;Preferences&quot; &raquo; &quot;Software&quot; afterwards.';
$lang_admin['branding']			= 'Branding';
$lang_admin['apptitle']			= 'Application title';
$lang_admin['serviceurl']		= 'Service URL';
$lang_admin['servicetitle']		= 'Service title';
$lang_admin['applogo']			= 'Application logo';
$lang_admin['tbbranding']		= 'Toolbar branding';
$lang_admin['wizardleft']		= 'Setup banner left';
$lang_admin['wizardhead']		= 'Setup banner top';
$lang_admin['style']			= 'Style';
$lang_admin['native']			= 'Native';
$lang_admin['stylesheet']		= 'Stylesheet';
$lang_admin['names']			= 'Names';
$lang_admin['bmtoolbox']		= 'b1gMail Toolbox';
$lang_admin['tbx_enable']		= 'Offer Toolbox';
$lang_admin['tbx_webdisk']		= 'Webdisk sync';
$lang_admin['tbx_smsmanager']	= 'SMS Manager';
$lang_admin['notrecommended']	= 'NOT recommended';
$lang_admin['prefslayout']		= 'Preferences overview';
$lang_admin['onecolumn']		= 'One column';
$lang_admin['twocolumns']		= 'Two columns';
$lang_admin['navpos']			= 'Main navigation';
$lang_admin['left']				= 'Left';
$lang_admin['top']				= 'Top';
$lang_admin['deprecated']		= 'deprecated';
$lang_admin['defaultemplate']	= 'Default template';
$lang_admin['colorscheme']		= 'Color scheme';
$lang_admin['orange']			= 'Orange';
$lang_admin['blue']				= 'Blue';
$lang_admin['tabmode']			= 'Tab mode';
$lang_admin['complete']			= 'Complete';
$lang_admin['icons']			= 'Icons';
$lang_admin['hidesignup']		= 'Hide signup when disabled';
$lang_admin['showuseremail']	= 'Show user email address';
$lang_admin['templates']		= 'Templates';
$lang_admin['show_at']			= 'Show at';
$lang_admin['adddomain']		= 'Add domain';
$lang_admin['account']			= 'Account';
$lang_admin['admins']			= 'Administrators';
$lang_admin['download']			= 'Download';
$lang_admin['phpinfo']			= 'PHP info';
$lang_admin['redirectmobile']	= 'Mobile interface redirect';
$lang_admin['maxlength']		= 'Max. length';
$lang_admin['repeat']			= 'repeat';
$lang_admin['admin']			= 'Administrator';
$lang_admin['superadmin']		= 'Super administrator';
$lang_admin['loggedinas']		= 'Logged in as';
$lang_admin['pwerror']			= 'The passwords you entered do not match or have less then 6 characters.';
$lang_admin['addadmin']			= 'Add administrator';
$lang_admin['adminexists']		= 'An administrator with this username already exists. Please chose another username.';
$lang_admin['permissions']		= 'Permissions';
$lang_admin['editadmin']		= 'Edit administrator';
$lang_admin['areas']			= 'Areas';
$lang_admin['lockedaltmails']	= 'Forbidden alt. email addresses';
$lang_admin['altmailsepby']		= '(one entry per line, \'*\' usable as wild card, e.g. \'*@evil-domain.xy\')';
$lang_admin['yourinvoice']		= 'Your invoice';
$lang_admin['dearsirormadam']	= 'Dear Sir or Madam';
$lang_admin['descr']			= 'Description';
$lang_admin['ep']				= 'Unit price';
$lang_admin['gp']				= 'Price';
$lang_admin['gb']				= 'Total amount';
$lang_admin['vat']				= 'VAT';
$lang_admin['net']				= 'net';
$lang_admin['gross']			= 'gross';
$lang_admin['kindregards']		= 'Kind regards';
$lang_admin['invtext']			= 'please find your invoice below';
$lang_admin['invoiceno']		= 'Invoice no';
$lang_admin['customerno']		= 'Customer no';
$lang_admin['bankacc']			= 'Bank account';
$lang_admin['invfooter']		= 'This invoice has been generated automatically and is valid without a signature.';
$lang_admin['kto_inh']			= 'Account owner';
$lang_admin['kto_nr']			= 'Account no';
$lang_admin['kto_blz']			= 'Bank code';
$lang_admin['kto_inst']			= 'Bank name';
$lang_admin['kto_iban']			= 'IBAN';
$lang_admin['kto_bic']			= 'BIC/SWIFT code';
$lang_admin['kto_subject']		= 'Subject';
$lang_admin['sender_aliases']	= 'Sender aliases';
$lang_admin['attachments']		= 'Attachments';
$lang_admin['flexspans'] 		= 'Auto subject length';
$lang_admin['paidonly']			= 'Paid only';
$lang_admin['account_debit']	= 'Debit account';
$lang_admin['account_credit']	= 'Credit account';
$lang_admin['accentries']		= 'Accounting entries';
$lang_admin['orderno']			= 'Order no';
$lang_admin['export2']			= 'Export';
$lang_admin['vkcode']			= 'Wire transfer code';
$lang_admin['activatepayment']	= 'Activate payment';
$lang_admin['amount']			= 'Amount';
$lang_admin['activate_ok']		= 'Payment activated successfully!';
$lang_admin['activate_err']		= 'Not found / already activated / wrong amount!';
$lang_admin['activate_desc']	= 'Here you can activate payments you received on your bank account.';
$lang_admin['returnpathcheck']	= 'Return path check';
$lang_admin['licensedetails']	= 'License details';
$lang_admin['updateaccess']		= 'Update access';
$lang_admin['pleasewait']		= 'Please wait...';
$lang_admin['startwidgets']		= 'Start dashboard';
$lang_admin['defaultlayout']	= 'Default layout';
$lang_admin['default']			= 'Default';
$lang_admin['layout_addremove']	= 'Add/remove widgets';
$lang_admin['layout_resetdesc']	= 'Reset the widget layout of all users belonging to one of the following groups to the default layout:';
$lang_admin['organizerwidgets']	= 'Organizer dashboard';
$lang_admin['pos']				= 'Position';
$lang_admin['widgetlayouts']	= 'Widget layouts';
$lang_admin['taborder']			= 'Tab order';
$lang_admin['payments']			= 'Payments';
$lang_admin['invalidselffolder'] 		= 'The configured absolute path to b1gMail (<code>%s</code>) does not exist. Please correct the path!';
$lang_admin['auto_tz']			= 'Timezone auto detect';
$lang_admin['check_double_altmail']		= 'Check for double alt. mail';
$lang_admin['check_double_cellphone']	= 'Check for double cellphone no';
$lang_admin['orphansfound']		= '%d orphaned email(s) were found (%.02f KB). We recommend to delete the orphaned objects.';
$lang_admin['orphans_desc']		= 'This function deletes all orphaned mails irrevocably.<br /><br />Orphaned mails are mails which do not belong to an user anymore. They can occur when an user deletion process aborts unexpectedly.';
$lang_admin['orphans_done']		= '%d orphaned object(s) have been found and deleted (%.02f KB).';
$lang_admin['orphans']			= 'Orphans';
$lang_admin['diskorphansfound']	= '%d orphaned webdisk file(s) have been found (%.02f KB). We recommend to delete the orphaned objects.';
$lang_admin['diskorphans_desc']	= 'This function deletes all orphaned webdisk files irrevocably.<br /><br />Orphaned webdisk files are files which do not belong to an user anymore. They can occur when an user deletion process aborts unexpectedly.';
$lang_admin['mailorphans']	= 'Orphaned emails';
$lang_admin['diskorphans']	= 'Orphaned webdisk files';
$lang_admin['text_paynotify_sub']		= 'Payment notification subject';
$lang_admin['text_paynotify_text']		= 'Payment notification text';
$lang_admin['text_orderconfirm_sub']	= 'Order confirmation subject';
$lang_admin['text_orderconfirm_text']	= 'Order confirmation text';
$lang_admin['text_share_sub']			= 'Webdisk share mail subject';
$lang_admin['text_share_text']			= 'Webdisk share mail text';
$lang_admin['selffolder']		= 'Abs. path to b1gMail';
$lang_admin['dynnorecvrules']	= 'Recipient detemination is set to &quot;Use receive rules&quot; but no receive rule exists. This way, receiving emails is impossible. Please set the recipient determination method to &quot;Automatic&quot; or add receive rules.';
$lang_admin['disablesender']	= 'Disable sender field';
$lang_admin['salutation']		= 'Salutation';
$lang_admin['mrs']				= 'Mrs';
$lang_admin['mr']				= 'Mr';
$lang_admin['greeting']			= 'Greeting';
$lang_admin['greeting_mr']		= 'Dear Mr %s';
$lang_admin['greeting_mrs']		= 'Dear Mrs %s';
$lang_admin['greeting_none']	= 'Dear Sir or Madam';
$lang_admin['sunotifypass']		= 'Notification password';
$lang_admin['suinputcheck']		= 'Input check';
$lang_admin['invoices']			= 'Invoices';
$lang_admin['pay_notification']	= 'Payment notifications';
$lang_admin['vat']				= 'VAT';
$lang_admin['vatrate']			= 'VAT rate';
$lang_admin['vat_add']			= 'add to prices';
$lang_admin['vat_enthalten']	= 'included in prices';
$lang_admin['vat_nomwst']		= 'no VAT';
$lang_admin['enablebanktransfer']	= 'Wire transfer payment';
$lang_admin['kto_inh']			= 'Account owner';
$lang_admin['kto_nr']			= 'Account no';
$lang_admin['kto_blz']			= 'Bank code';
$lang_admin['kto_inst']			= 'Bank name';
$lang_admin['kto_iban']			= 'IBAN';
$lang_admin['kto_bic']			= 'BIC/SWIFT code';
$lang_admin['banktransfer']		= 'Wire transfer';
$lang_admin['sendrg']			= 'Generate invoices';
$lang_admin['rgnrfmt']			= 'Invoice no format';
$lang_admin['kdnrfmt']			= 'Customer no format';
$lang_admin['rgtemplate']		= 'Invoice template';
$lang_admin['enablesmscharge']	= 'Enable SMS charging';
$lang_admin['paypal']			= 'PayPal';
$lang_admin['su']				= 'sofort&uuml;berweisung.de';
$lang_admin['paymentmethods']	= 'Payment methods';
$lang_admin['orderstatus_0']	= 'Created';
$lang_admin['orderstatus_1']	= 'Activated';
$lang_admin['downloadinvoices']	= 'Download invoices';
$lang_admin['showinvoice']		= 'Show invoice';
$lang_admin['acp']				= 'Administrator Control Panel (ACP)';
$lang_admin['password']			= 'Password';
$lang_admin['login']			= 'Login';
$lang_admin['dattempt']		 	= 'This is your %d. attempt to log in, after 5 attempts log-in will be blocked for 5 minutes.';
$lang_admin['dattempt2']		= 'Due to too many failed login attempts login for this account will be blocked until %s .';
$lang_admin['loginerror']		= 'Invalid password. Please try again.';
$lang_admin['welcome']			= 'Welcome';
$lang_admin['usersgroups']		= 'Users &amp; Groups';
$lang_admin['users']			= 'Users';
$lang_admin['user']				= 'User';
$lang_admin['domain']			= 'Domain';
$lang_admin['groups']			= 'Groups';
$lang_admin['activity']			= 'Activity';
$lang_admin['edittemplate']		= 'Edit template';
$lang_admin['addtemplate']		= 'Add template';
$lang_admin['newsletter']		= 'Newsletter';
$lang_admin['newsletter_done']	= 'The newsletter has been sent successfully to <b>%d</b> users. <b>%d</b> attempts to send the newsletter have failed.';
$lang_admin['prefs']			= 'Preferences';
$lang_admin['common']			= 'Common';
$lang_admin['profilefields']	= 'Profile fields';
$lang_admin['languages']		= 'Languages';
$lang_admin['webdiskicons']		= 'Webdisc icons';
$lang_admin['sms']				= 'SMS';
$lang_admin['ads']				= 'Advertisements';
$lang_admin['faq']				= 'FAQ';
$lang_admin['coupons']			= 'Vouchers';
$lang_admin['tools']			= 'Tools';
$lang_admin['optimize']			= 'Optimize';
$lang_admin['stats']			= 'Statistics';
$lang_admin['backup']			= 'Backup';
$lang_admin['logs']				= 'Log files';
$lang_admin['receivesys']		= 'Receiving';
$lang_admin['updates']			= 'Updates';
$lang_admin['plugins']			= 'Plugins';
$lang_admin['logout']			= 'Logout';
$lang_admin['logoutquestion']	= 'Are you sure you want to log out?';
$lang_admin['license']			= 'License';
$lang_admin['overview']			= 'Overview';
$lang_admin['notes']			= 'Notes';
$lang_admin['notices']			= 'Notifications';
$lang_admin['about']			= 'About b1gMail';
$lang_admin['version']			= 'Version';
$lang_admin['nonotices']		= 'Currently there are no new notifications.';
$lang_admin['save']				= 'Save';
$lang_admin['notactivated']		= 'Not activated';
$lang_admin['locked']			= 'Locked';
$lang_admin['emails']			= 'Emails';
$lang_admin['folders']			= 'Folders';
$lang_admin['disksize']			= 'Webdisc size';
$lang_admin['files']			= 'Files';
$lang_admin['phpversion']		= 'PHP version';
$lang_admin['webserver']		= 'Webserver';
$lang_admin['mysqlversion']		= 'MySQL version';
$lang_admin['dbsize']			= 'Database size';
$lang_admin['load']				= 'Server load';
$lang_admin['db']				= 'Database';
$lang_admin['cache']			= 'Cache';
$lang_admin['filesystem']		= 'File system';
$lang_admin['optimizedb']		= 'Optimize database';
$lang_admin['tables']			= 'Tables';
$lang_admin['action']			= 'Action';
$lang_admin['op_optimize']		= 'Optimize';
$lang_admin['op_optimize_desc']	= 'Will release unused space in the tables and clean them up for quicker access.';
$lang_admin['op_repair']		= 'Repair';
$lang_admin['op_repair_desc']	= 'Will check tables for errors and repair them as good as possible.';
$lang_admin['op_struct']		= 'Check structure';
$lang_admin['op_struct_desc']	= 'Will check the structure of the tables and repair them if requested. This operation is not dependent on the table selection.';
$lang_admin['execute']			= 'Execute';
$lang_admin['back']				= 'Back';
$lang_admin['success']			= 'Success';
$lang_admin['error']			= 'Error';
$lang_admin['couldfree']		= 'An optimization of the database can improve the database performance and free up %.02f MB of space.';
$lang_admin['emailsize']		= 'Email size';
$lang_admin['debugmode']		= 'b1gMail is currently in debug mode. You should disable the debug mode when being in production use.';
$lang_admin['rebuildcaches']	= 'Rebuild caches';
$lang_admin['rebuild_desc']		= 'Here you can rebuild intermediately stored data (caches) to provide the best possible data integrity. However, this will usually only be necessary if you are experiencing any problems with the data in question.';
$lang_admin['heavyop']			= 'This operation may be very heavy and take a long time. You should not cancel the operation.';
$lang_admin['emailsizes_cache']	= 'Recalculate email sizes';
$lang_admin['emailsizes_desc']	= 'Will recalculate the size of every email stored in the system (in practice, this operation is very rarely necessary).';
$lang_admin['usersizes_cache'] 	= 'Recalculate user space usage';
$lang_admin['usersizes_desc']	= 'Will recalculate the space usage of the your users.';
$lang_admin['disksizes_cache']	= 'Recalculate the sizes of webdisc files';
$lang_admin['disksizes_desc']	= 'Will recalculate the size of every webdisc file saved in the system (in practice, this operation is very rarely necessary).';
$lang_admin['opsperpage']		= 'Operations per instance';
$lang_admin['nopostmaster']		= 'The postmaster user (<code>%s</code>) does not exist. You are strongly recommended to create that user to avoid problems with email processing.';
$lang_admin['cachesizesdiffer']	= 'The user space cache seems to be out of sync with the user data. You should rebuild the cache.';
$lang_admin['unknown']			= 'unknown';
$lang_admin['dataperms']		= 'Cannot write to the b1gMail data directory (<code>%s</code>). Please check the access rights!';
$lang_admin['invaliddata']		= 'The b1gMail data directory (<code>%s</code>) could not be found. Please check the path and try again!';
$lang_admin['email']			= 'Email';
$lang_admin['receive']			= 'Receive';
$lang_admin['send']				= 'Send';
$lang_admin['antispam']			= 'Anti spam';
$lang_admin['antivirus']		= 'Anti virus';
$lang_admin['recvmethod']		= 'Receiving method';
$lang_admin['miscprefs']		= 'Miscellaneous preferences';
$lang_admin['rules']			= 'Rules';
$lang_admin['gateways']			= 'Gateways';
$lang_admin['types']			= 'Types';
$lang_admin['texts']			= 'Texts';
$lang_admin['calendar']			= 'Calendar';
$lang_admin['signup']			= 'Sign-up';
$lang_admin['optimizedb']		= 'Optimize database';
$lang_admin['tempfiles']		= 'Temporary files';
$lang_admin['count']			= 'Count';
$lang_admin['size']				= 'Size';
$lang_admin['tempdesc']			= 'Temporary files will usually be deleted by b1gMail if they are no longer required. You may also start the cleanup process manually. Please note that only those files will be deleted that have reached a certain age.';
$lang_admin['cleanup']			= 'Clean up';
$lang_admin['mailstorage']		= 'Email storage';
$lang_admin['storage_desc']		= 'Here you can transfer emails to the current storage.';
$lang_admin['file2db']			= 'Files -&gt; database';
$lang_admin['db2file']			= 'Database -&gt; files';
$lang_admin['file2db_desc']		= 'Will transfer emails that are stored in files to the database.';
$lang_admin['db2file_desc']		= 'Emails stored in the database will be re-stored as files.';
$lang_admin['installedplugins']	= 'Installed plugins';
$lang_admin['installplugin']	= 'Install plugin';
$lang_admin['inactive']			= 'Inactive';
$lang_admin['active']			= 'Active';
$lang_admin['type']				= 'Type';
$lang_admin['title']			= 'Title';
$lang_admin['author']			= 'Author';
$lang_admin['info']				= 'Info';
$lang_admin['status']			= 'Status';
$lang_admin['installed']		= 'Installed';
$lang_admin['notinstalled']		= 'Not installed';
$lang_admin['current']			= 'Latest';
$lang_admin['module']			= 'Module';
$lang_admin['widget']			= 'Widget';
$lang_admin['widgets']			= 'Widgets';
$lang_admin['acdeactivate']		= 'Activate / deactivate';
$lang_admin['reallyplugin']		= 'Are you sure you want to activate / deactivate the plugin? Activating faulty plugins may compromise system integrity; deactivating plugins may permanently erase the data associated with the plugin!';
$lang_admin['plugpackage']		= 'Plugin package (.bmplugin file)';
$lang_admin['install_desc']		= 'You can easily install new plugins if they are available as a .bmplugin package. Just upload the plugin package here and it will be installed automatically.';
$lang_admin['install']			= 'Install';
$lang_admin['plugin_formaterr']	= 'The file you uploaded is damaged or it is not a valid plugin package in .bmplugin format!';
$lang_admin['sourcewarning'] 	= 'Please make sure you install files from trustworthy sources only - they might contain dangerous code!';
$lang_admin['archiving']		= 'Archiving';
$lang_admin['entry']			= 'Entry';
$lang_admin['date']				= 'Date';
$lang_admin['export']			= 'Export';
$lang_admin['filter']			= 'Filter';
$lang_admin['show']				= 'Show';
$lang_admin['from']				= 'From';
$lang_admin['to']				= 'To';
$lang_admin['logarc_desc']		= 'Here you can erase all logs that have been generated before a certain date (e.g. in order to accelerate the system and to save storage space). Optionally you can save a copy in the archive (&quot;logs&quot; folder).';
$lang_admin['savearc']			= 'Save copy to the archive';
$lang_admin['reallynotarc']		= 'Are you sure you want to erase the logs permanently WITHOUT saving them to the archive? There might be a certain minimum retention period required by law!';
$lang_admin['notactnotice']		= '<b>%d</b> user account(s) has/have the status &quot;Not activated&quot; and is/are waiting for you to activate it/them.';
$lang_admin['deletenotice']		= '<b>%d</b> user account(s) has/have the Status &quot;Erased&quot; and is/are waiting for you to erase it/them permanently.';
$lang_admin['maxsizewarning']	= 'The mail size limit (incoming) of the group <b>%s</b> is %d KB and thus bigger than the mail size limit of %d KB configured at Preferences &raquo; Email &raquo; Incoming!';
$lang_admin['manylogs']			= 'The log table contains over 250.000 entries. You should archive your old entries.';
$lang_admin['mbstring']			= 'Neither the <code>mbstring</code> nor the <code>iconv</code> PHP extension is available. In order to achieve optimal results as far as special characters are concerned, you should install one of the extensions if possible.';
$lang_admin['gdlib']			= 'The <code>gd</code> PHP extension is not available. Some of the graphics-related features of b1gMail require this extension. If possible, you should install it.';
$lang_admin['idnlib']			= 'Support for internationalized domain names (IDN) is not available. If you want to use IDN, please install the PECL extension <code>intl</code> or <code>idn</code>.';
$lang_admin['domdocument']		= 'The <code>dom</code>/<code>xml</code> PHP extension is not available. To improve security when reading HTML emails, it is strongly recommended to install these extension(s).';
$lang_admin['create']			= 'Create';
$lang_admin['id']				= 'ID';
$lang_admin['name']				= 'Name';
$lang_admin['deleted']			= 'Deleted';
$lang_admin['apply']			= 'Apply';
$lang_admin['perpage']			= 'Per page';
$lang_admin['search']			= 'Search';
$lang_admin['group']			= 'Group';
$lang_admin['missing']			= '(MISSING)';
$lang_admin['pages']			= 'Pages';
$lang_admin['actions']			= 'Actions';
$lang_admin['move']				= 'Move';
$lang_admin['moveto']			= 'Move to';
$lang_admin['delete']			= 'Delete';
$lang_admin['lock']				= 'Lock';
$lang_admin['unlock']			= 'Unlock';
$lang_admin['restore']			= 'Restore';
$lang_admin['edit']				= 'Edit';
$lang_admin['loginwarning']		= 'Are you sure you want to log on to the account you have selected? Please pay attention to the data privacy regulations applying in your country and respect your user\'s privacy!';
$lang_admin['spaceusage']		= 'Space usage';
$lang_admin['webdisk']			= 'Webdisc';
$lang_admin['used']				= 'used';
$lang_admin['usage']			= 'Usage';
$lang_admin['aliases']			= 'Aliases';
$lang_admin['firstname']		= 'First name';
$lang_admin['lastname']			= 'Last name';
$lang_admin['streetno']			= 'Street / no.';
$lang_admin['zipcity']			= 'Zip / city';
$lang_admin['tel']				= 'Phone';
$lang_admin['fax']				= 'Fax';
$lang_admin['altmail']			= 'Alternative email';
$lang_admin['profile']			= 'Profile';
$lang_admin['country']			= 'Country';
$lang_admin['re']				= 'Re';
$lang_admin['fwd']				= 'Fwd';
$lang_admin['cellphone']		= 'Cellphone';
$lang_admin['misc']				= 'Miscellaneous';
$lang_admin['lastlogin']		= 'Last login';
$lang_admin['ip']				= 'IP address';
$lang_admin['regdate']			= 'Registration date';
$lang_admin['lastpop3']			= 'Last POP3 access';
$lang_admin['lastimap']			= 'Last IMAP access';
$lang_admin['lastsmtp']			= 'Last SMTP access';
$lang_admin['newpassword']		= '(New) password';
$lang_admin['assets']			= 'Assets';
$lang_admin['credits']			= 'Credits';
$lang_admin['alias']			= 'Alias';
$lang_admin['realdel']			= 'Are you sure you want to erase the entry permanently?';
$lang_admin['wdtraffic']		= 'Webdisc traffic';
$lang_admin['used2']			= 'used';
$lang_admin['used3']			= 'Used';
$lang_admin['ok']				= 'OK';
$lang_admin['sendmail']			= 'Send email';
$lang_admin['emptytrash']		= 'Empty trash';
$lang_admin['monthasset']		= 'Assets per month';
$lang_admin['yes']				= 'Yes';
$lang_admin['no']				= 'No';
$lang_admin['notconfirmed']		= 'Unconfirmed';
$lang_admin['mail2sms']			= 'Mail-to-SMS';
$lang_admin['forward']			= 'Forwarding';
$lang_admin['forwardto']		= 'Forward to';
$lang_admin['dateformat']		= 'Date format';
$lang_admin['sendername']		= 'Sender name';
$lang_admin['addressinvalid']	= 'The email address you have entered is invalid. Please try again.';
$lang_admin['addresstaken']		= 'The email address you have entered is no longer available. Please try again.';
$lang_admin['accountcreated']	= 'The account has been created successfuly.<br /><br /><a href="users.php?do=edit&id=%d&sid=%s">&raquo; Edit user</a>';
$lang_admin['enablereg']		= 'Enable sign-up';
$lang_admin['stateafterreg']	= 'Status after sign-up';
$lang_admin['smsvalidation_signup']	= 'SMS signup activation';
$lang_admin['smsvalidation']	= 'SMS sender validation';
$lang_admin['reg_validation']	= 'Signup validation';
$lang_admin['max_resend_times']	= 'Max. re-send requests';
$lang_admin['min_resend_interval']	= 'Minimum interval';
$lang_admin['byemail']		    = 'by email';
$lang_admin['bysms']		    = 'by SMS';
$lang_admin['resend_val_email'] = 'Resend validation email';
$lang_admin['resend_val_sms']   = 'Resend validation SMS';
$lang_admin['val_code_resent']	= 'The validation code has been re-sent.';
$lang_admin['stdgroup']			= 'Default group';
$lang_admin['scsf']				= 'Security code interference factor';
$lang_admin['domain_combobox']	= 'Login domain combo box';
$lang_admin['fields']			= 'Fields';
$lang_admin['field']			= 'Field';
$lang_admin['oblig']			= 'Obligatory';
$lang_admin['available']		= 'Available';
$lang_admin['notavailable']		= 'Not available';
$lang_admin['safecode']			= 'Security code';
$lang_admin['datavalidation']	= 'Data validation';
$lang_admin['sessioniplock']	= 'Session IP lock';
$lang_admin['sessioncookielock']= 'Session cookie lock';
$lang_admin['regiplock']		= 'Sign-up IP lock';
$lang_admin['seconds']			= 'seconds';
$lang_admin['plzcheck']			= 'Check if zip code and city match';
$lang_admin['altcheck']			= 'Check alternative email';
$lang_admin['usercountlimit']	= 'User count limit';
$lang_admin['minaddrlength']	= 'Minimum username length';
$lang_admin['to2']				= 'To';
$lang_admin['regnotify']		= 'Sign-up notification';
$lang_admin['recvrules']		= 'Receive rules';
$lang_admin['autodetection']	= 'Auto detection';
$lang_admin['expression']		= 'Regular expression';
$lang_admin['value']			= 'Value';
$lang_admin['isrecipient']		= 'Set definite recipient';
$lang_admin['setrecipient']		= 'Set exclusive recipient';
$lang_admin['addrecipient']		= 'Add possible recipient(s)';
$lang_admin['receiverule']		= 'Receive rule';
$lang_admin['custom']			= 'Custom';
$lang_admin['addrecvrule']		= 'Add receive rule';
$lang_admin['add']				= 'Add';
$lang_admin['bounce']			= 'Bounce';
$lang_admin['markspam']			= 'Mark as spam';
$lang_admin['markinfected']		= 'Mark as infected';
$lang_admin['setinfection']		= 'Set infection';
$lang_admin['markread']			= 'Mark as read';
$lang_admin['import']			= 'Import';
$lang_admin['ruledesc']			= 'Here you can import a .bmrecvrules-file with receive rules.';
$lang_admin['rulefile']			= 'Rule file (.bmrecvrules file)';
$lang_admin['validityrule']		= 'Validity rule';
$lang_admin['validitytime']		= 'Period of validity';
$lang_admin['checkbox']			= 'Checkbox';
$lang_admin['dropdown']			= 'Dropdown';
$lang_admin['radio']			= 'Radio button';
$lang_admin['text']				= 'Text';
$lang_admin['customfieldsat']	= 'Custom fields can be configured at';
$lang_admin['addprofilefield']	= 'Add profile field';
$lang_admin['options']			= 'Options';
$lang_admin['onlyfortext']		= '(for text fields only)';
$lang_admin['optionsdesc']		= '(only for radio button or dropdown field, use comma to seperate)';
$lang_admin['brokenperms']		= 'The following files and folders are not writeable: <code>%s</code>. Please check the access rights!';
$lang_admin['brokenhtaccess']	= 'The following .htaccess files do not exist: <code>%s</code>. Please upload the files again - without these files some of the data files are not protected from unauthorized access!';
$lang_admin['maintenance']		= 'Maintenance';
$lang_admin['inactiveusers']	= 'Inactive users';
$lang_admin['trash']			= 'Trash';
$lang_admin['pop3gateway']		= 'POP3 gateway';
$lang_admin['pop3fetch_desc']	= 'Here you can check the CatchAll POP3 account of the POP3 gateway manually in case too many emails exist in it. If the process aborts unexpectedly, please check that your mail size limits are not set to a too large value.';
$lang_admin['help']				= 'Help';
$lang_admin['none']				= 'None';
$lang_admin['persistent']		= 'Persistent connections';
$lang_admin['servers']			= 'Server';
$lang_admin['memcachesepby']	= '(one server per line, format: &quot;hostname:port,weight&quot;)';
$lang_admin['parseonly']		= 'Only email objects';
$lang_admin['caching']			= 'Caching';
$lang_admin['cachemanager']		= 'Cache manager';
$lang_admin['ce_disable']		= 'Disable';
$lang_admin['ce_disable_desc']	= 'Will disable the caching of objects (not recommended).';
$lang_admin['ce_b1gmail']		= 'b1gMail cache manager';
$lang_admin['ce_b1gmail_desc']	= 'Will use the b1gMail cache manager to cache objects (recommended if memcached is not available).';
$lang_admin['ce_memcache']		= 'memcached';
$lang_admin['ce_memcache_desc']	= 'will use memcached as cache manager (recommended if available).';
$lang_admin['filecache']		= 'File cache';
$lang_admin['filecachedesc']	= 'If file cache is enabled, b1gMail itself will administrate the caching of objects that are CPU-intensive to generate. The cache will usually be administrated automatically and will also be cleaned up automatically if required. Here you can empty the cache manually if required.';
$lang_admin['emptycache']		= 'Empty cache';
$lang_admin['clamintegration']	= 'ClamAV-/clamd-integration';
$lang_admin['host']				= 'Host';
$lang_admin['port']				= 'Port';
$lang_admin['enable']			= 'Enable';
$lang_admin['clamwarning']		= 'Only enable ClamAV-integration if ClamAV/clamd has been installed on the mentioned server and is enabled.';
$lang_admin['countries']		= 'Countries';
$lang_admin['plzdb']			= 'ZIP DB';
$lang_admin['addcountry']		= 'Add country';
$lang_admin['cachetime']		= 'Cache validity';
$lang_admin['cachesize']		= 'Cache size';
$lang_admin['inactiveonly']		= 'For inactive files only';
$lang_admin['storein']			= 'Store message in';
$lang_admin['language']			= 'Language';
$lang_admin['addlanguage']		= 'Add language';
$lang_admin['addlang_desc']		= 'Here you can install a new language file (.lang.php file) by uploading it using the following form. Please make sure the language file is applicable for your version of b1gMail.';
$lang_admin['langfile']			= 'Language file';
$lang_admin['pipeetc']			= 'Pipe / transportmap gateway';
$lang_admin['pop3host']			= 'POP3 server';
$lang_admin['pop3user']			= 'POP3 user';
$lang_admin['pop3pass']			= 'POP3 password';
$lang_admin['pop3port']			= 'POP3 port';
$lang_admin['fetchcount']		= 'Emails per fetch process';
$lang_admin['mailmax']			= 'Maximum size of messages';
$lang_admin['errormail']		= 'Enable Non-Delivery Notification';
$lang_admin['errormail_soft']	= 'Only for emails without valid recipients';
$lang_admin['failure_forward']	= 'Forward undeliverable messages to postmaster';
$lang_admin['smtphost']			= 'SMTP server';
$lang_admin['smtpport']			= 'SMTP port';
$lang_admin['smtpauth']			= 'Requires authentification';
$lang_admin['smtpuser']			= 'SMTP user';
$lang_admin['smtppass']			= 'SMTP password';
$lang_admin['sendmethod']		= 'Sending method';
$lang_admin['smtp']				= 'SMTP';
$lang_admin['phpmail']			= 'PHP mail';
$lang_admin['sysmailsender']	= 'System mail sender';
$lang_admin['maxrecps']			= 'Maximum number of recipients';
$lang_admin['blockedrecps']		= 'Forbidden recipients';
$lang_admin['sepby']			= '(one entry per line)';
$lang_admin['dnsbl']			= 'DNSBL filter';
$lang_admin['dnsblservers']		= 'DNSBL servers';
$lang_admin['bayes']			= 'Statistical, trainable filter';
$lang_admin['bayesmode']		= 'Filter database mode';
$lang_admin['bayeslocal']		= 'Local (one database for each user)';
$lang_admin['bayesglobal']		= 'Global (one database for all users)';
$lang_admin['customtexts']		= 'Customizable texts';
$lang_admin['sendmail2']		= 'Sendmail';
$lang_admin['sendmailpath']		= 'Sendmail path';
$lang_admin['text_maintenance']			= 'Maintenance mode note';
$lang_admin['text_welcome_sub']			= 'Welcome mail subject';
$lang_admin['text_welcome_text']		= 'Welcome mail text';
$lang_admin['text_tos']					= 'TOS';
$lang_admin['text_imprint']				= 'Impint';
$lang_admin['text_snotify_sub']			= 'Sign-up note subject';
$lang_admin['text_snotify_text']		= 'Sign-up note text';
$lang_admin['text_selfcomp_n_sub']		= 'Addressbook notify subject';
$lang_admin['text_selfcomp_n_text']		= 'Addressbook notify text';
$lang_admin['text_selfcomp_sub']		= 'Addressbook mail subject';
$lang_admin['text_selfcomp_text']		= 'Addressbook mail text';
$lang_admin['text_passmail_sub']		= 'Password mail subject';
$lang_admin['text_passmail_text']		= 'Password mail text';
$lang_admin['text_certmail']			= 'Certified mail text';
$lang_admin['text_mail2sms']			= 'Mail-to-SMS notification';
$lang_admin['text_cs_subject']			= 'Cert. mail receipt subject';
$lang_admin['text_cs_text']				= 'Cert. mail receipt text';
$lang_admin['text_clndr_subject']		= 'Date nofiy subject';
$lang_admin['text_clndr_date_msg']		= 'Date notify text';
$lang_admin['text_clndr_sms']			= 'Date notify SMS';
$lang_admin['text_receipt_text']		= 'Receipt mail text';
$lang_admin['text_validationsms']		= 'Sign up SMS validation';
$lang_admin['text_validationsms2']		= 'SMS sender validation';
$lang_admin['text_alias_sub']	= 'Alias mail subject';
$lang_admin['text_alias_text']	= 'Alias mail text';
$lang_admin['text_activationmail_sub']	= 'Activation mail subject';
$lang_admin['text_activationmail_text']	= 'Activation mail text';
$lang_admin['projecttitle']		= 'Site title';
$lang_admin['selfurl']			= 'b1gMail URL';
$lang_admin['mobile_url']		= 'Mobile b1gMail URL';
$lang_admin['ssl']				= 'SSL';
$lang_admin['ssl_url']			= 'b1gMail SSL URL';
$lang_admin['ssl_login_enable'] = 'Login using SSL by default';
$lang_admin['ssl_login_option'] = 'SSL login checkbox';
$lang_admin['datafolder']		= 'Data directory';
$lang_admin['hostname']			= 'Hostname';
$lang_admin['template']			= 'Template';
$lang_admin['defaults']			= 'Defaults';
$lang_admin['itemsperpage']		= 'Entries per page';
$lang_admin['censorchar']		= 'Censorship character';
$lang_admin['domains']			= 'Domains';
$lang_admin['allownewsoptout'] 	= 'Allow newsletter opt-out';
$lang_admin['allow_newsletter_optout'] 	= 'Allow newsletter opt-out';
$lang_admin['gutregged']		= 'Vouchers for registered users';
$lang_admin['autocancel']		= 'Account deletable by user';
$lang_admin['maintmode']		= 'Maintenance mode';
$lang_admin['whitelist']		= 'Access list';
$lang_admin['maintmodenote']	= 'Maintenance mode is enabled. The b1gMail installation is not accessible for users.';
$lang_admin['dldate']			= 'Package date';
$lang_admin['invalidserial']	= 'The serial number you have entered is invalid. Please try again.';
$lang_admin['members']			= 'Members';
$lang_admin['addmember']		= 'Add member';
$lang_admin['storage']			= 'Storage';
$lang_admin['limits']			= 'Limits';
$lang_admin['emailin']			= 'Email (incoming)';
$lang_admin['emailout']			= 'Email (outgoing)';
$lang_admin['services']			= 'Services';
$lang_admin['pop3']				= 'POP3';
$lang_admin['imap']				= 'IMAP';
$lang_admin['webdav']			= 'WebDAV';
$lang_admin['autoresponder']	= 'Autoresponder';
$lang_admin['mobileaccess']		= 'Mobile access';
$lang_admin['mailchecker']		= 'Mail checker';
$lang_admin['issue_certificates'] 	= 'Issue certificates';
$lang_admin['upload_certificates'] 	= 'Upload certificates';
$lang_admin['ownfrom']			= 'Own SMS sender';
$lang_admin['wdshare']			= 'Webdisc share';
$lang_admin['wdspeed']			= 'Webdisc speed';
$lang_admin['sharespeed']		= 'Share speed';
$lang_admin['htmlview']			= 'HTML mode by default';
$lang_admin['sendlimit']		= 'Send limit';
$lang_admin['emailsin']			= 'email(s) in';
$lang_admin['minutes']			= 'minute(s)';
$lang_admin['ownpop3']			= 'External POP3 accounts';
$lang_admin['ownpop3interval']	= 'POP3 poll interval';
$lang_admin['selfpop3_check']	= 'Protect against fetching own account';
$lang_admin['smspre']			= 'SMS area codes';
$lang_admin['aliasdomains']		= 'Additional alias domains';
$lang_admin['smsfrom']			= 'SMS sender';
$lang_admin['smssig']			= 'SMS signature';
$lang_admin['mailsig']			= 'Email signature';
$lang_admin['creditprice']		= 'Credit price';
$lang_admin['receivedmails']	= 'Received';
$lang_admin['sentmails']		= 'Sent';
$lang_admin['wdtrafficshort']	= 'WD traffic';
$lang_admin['structstate']		= 'missing / invalid';
$lang_admin['exists']			= 'exists';
$lang_admin['table']			= 'Table';
$lang_admin['query']			= 'Query';
$lang_admin['repairstruct']		= 'Repair structure';
$lang_admin['repairdone']		= 'The database structure has been repaired.';
$lang_admin['addgateway']		= 'Add gateway';
$lang_admin['returnvalue']		= 'Return value';
$lang_admin['getstring']		= 'GET URL';
$lang_admin['defaultgateway']	= 'Default gateway';
$lang_admin['defaulttype']		= 'Default SMS type';
$lang_admin['smsvalidation_type']	= 'SMS type for validations';
$lang_admin['clndr_sms_type']	= 'SMS type for calendar SMS';
$lang_admin['mail2sms_type']	= 'SMS type for Mail-to-SMS';
$lang_admin['gateway']			= 'Gateway';
$lang_admin['gateuser']			= 'Gateway user';
$lang_admin['gatepass']			= 'Gateway password';
$lang_admin['clndrsmsabs']		= 'Calendar SMS sender';
$lang_admin['mail2smsabs']		= 'Mail-to-SMS sender';
$lang_admin['smsreplyabs']		= 'SMS-to-mail sender';
$lang_admin['datastorage']		= 'Data storage';
$lang_admin['structstorage']	= 'Structured storage';
$lang_admin['structrec']	 	= 'The PHP safe mode is disabled. Under these circumstances the structured data storage will enhance performance. Enabling the structured data storage is recommended.';
$lang_admin['dnsblreq']			= 'Required positive tests';
$lang_admin['croninterval']		= 'Minimum cronjob interval';
$lang_admin['logouturl']		= 'Logout URL';
$lang_admin['addtype']			= 'Add type';
$lang_admin['price']			= 'Price';
$lang_admin['setdefault']		= 'Set as default';
$lang_admin['deletegroup']		= 'Delete group';
$lang_admin['groupdeletedesc']	= 'Please select the group(s) to which the members of those groups are to be assigned that you want to delete.';
$lang_admin['dbwarn']			= 'It is highly recommended to create a backup before running. Use at own risk.';
$lang_admin['workgroups']		= 'Workgroup';
$lang_admin['collaboration']	= 'Collaboration';
$lang_admin['share_addr']		= 'Shared addresses';
$lang_admin['share_calendar']	= 'Shared calendar';
$lang_admin['share_todo']		= 'Shared todo';
$lang_admin['share_notes']		= 'Shared notes';
$lang_admin['share_webdisk']	= 'Shared webdisc';
$lang_admin['bayesdb']			= 'Filter database';
$lang_admin['reset']			= 'Reset';
$lang_admin['bayesresetq']		= 'Are you sure you want to reset the filter training database? The filter will not be operational again until re-training.';
$lang_admin['entries']			= 'Entries';
$lang_admin['addcoupon']		= 'Add vouchers';
$lang_admin['codes']			= 'Voucher code(s)';
$lang_admin['unlimited']		= 'unlimited';
$lang_admin['or']				= 'or';
$lang_admin['now']				= 'now';
$lang_admin['generate']			= 'Generate';
$lang_admin['chars']			= 'Characters';
$lang_admin['length']			= 'Length';
$lang_admin['benefit']			= 'Benefit';
$lang_admin['movetogroup']		= 'Move to group';
$lang_admin['addcredits']		= 'Add credits';
$lang_admin['code']				= 'Code';
$lang_admin['createbackup']		= 'Create backup';
$lang_admin['backupitems']		= 'Create backup of';
$lang_admin['approx']			= 'approx.';
$lang_admin['userdata']			= 'User data';
$lang_admin['maildata']			= 'Email data';
$lang_admin['webdiskdata']		= 'Webdisc data';
$lang_admin['statsdata']		= 'Statistics data';
$lang_admin['organizerdata']	= 'Organizer data';
$lang_admin['backupwarn']		= 'The integrity of backups cannot be guaranteed. Use at own risk.';
$lang_admin['usagebycategory']	= 'Space use by category';
$lang_admin['category']			= 'Category';
$lang_admin['organizer']		= 'Organizer';
$lang_admin['mails']			= 'Emails';
$lang_admin['overall']			= 'Overall';
$lang_admin['usagebygroup']		= 'Memory usage by group';
$lang_admin['useraverage']		= 'User average';
$lang_admin['withoutmeta']		= 'without metadata';
$lang_admin['commonstats']		= 'Common statistics';
$lang_admin['emailstats']		= 'Email statistics';
$lang_admin['view']				= 'View';
$lang_admin['stat_login']		= 'Logins';
$lang_admin['stat_mobile_login']= 'Mobile logins';
$lang_admin['stat_signup']		= 'Sign-ups';
$lang_admin['stat_sms']			= 'Sent SMS';
$lang_admin['stat_wd']			= 'Webdisc';
$lang_admin['stat_wd_down']		= 'Webdisc (download, MB)';
$lang_admin['stat_wd_up']		= 'Webdisc (upload, MB)';
$lang_admin['stat_receive']		= 'Received emails';
$lang_admin['stat_infected']	= 'Emails identified as infected';
$lang_admin['stat_spam']		= 'Emails identified as spam';
$lang_admin['stat_send']		= 'Sent emails';
$lang_admin['stat_send_intern']	= 'Sent emails (internal)';
$lang_admin['stat_send_extern']	= 'Sent emails (external)';
$lang_admin['stat_sysmail']		= 'System emails';
$lang_admin['day']				= 'Day';
$lang_admin['redeemedby']		= 'Redeemed by...';
$lang_admin['oldcontacts']		= 'Old contact information available.';
$lang_admin['contacthistory']	= 'Contact history';
$lang_admin['savehistory']		= 'Save contact history';
$lang_admin['discarded']		= 'Discarded';
$lang_admin['clearhistory']		= 'Clear history';
$lang_admin['charge']			= 'Charge';
$lang_admin['enablechrgpaypal']	= 'Payments by PayPal';
$lang_admin['enablechrgsu']		= 'Payments by sofort&uuml;berweisung.de';
$lang_admin['sukdnr']			= 'Customer number';
$lang_admin['suprjnr']			= 'Project number';
$lang_admin['suprjpass']		= 'Projekt password';
$lang_admin['su_createnew']		= 'Create sofort&uuml;berweisung.de project';
$lang_admin['enablecharge']		= 'Enable charge';
$lang_admin['currency']			= 'Currency';
$lang_admin['paypalacc']		= 'Paypal account';
$lang_admin['filetypes']		= 'File type(s)';
$lang_admin['addwebdiskicon']	= 'Add webdisc icon';
$lang_admin['icon']				= 'Icon';
$lang_admin['recipients']		= 'Recipients';
$lang_admin['priority']			= 'Priority';
$lang_admin['prio_-1']			= 'Low';
$lang_admin['prio_0']			= 'Normal';
$lang_admin['prio_1']			= 'High';
$lang_admin['subject']			= 'Subject';
$lang_admin['sendletter']		= 'Send newsletter';
$lang_admin['recpdetermined']	= 'Recipient determined';
$lang_admin['sendto']			= 'Send to';
$lang_admin['mailboxes']		= 'Email accounts';
$lang_admin['altmails']			= 'Alternative email addresses';
$lang_admin['mode']				= 'Mode';
$lang_admin['plaintext']		= 'Text';
$lang_admin['htmltext']			= 'HTML';
$lang_admin['team']				= 'Team';
$lang_admin['limitedextensions']= 'Forbidden file extensions';
$lang_admin['limitedmimetypes']	= 'Forbidden MIME types';
$lang_admin['sendwelcomemail']	= 'Send welcome email';
$lang_admin['searchprovider']	= 'b1gMail search provider';
$lang_admin['includeinsearch']	= 'Include the following sections in the user search';
$lang_admin['mailsearchwarn']	= 'Searching a large data stock is very CPU intensive.';
$lang_admin['smsoutbox']		= 'SMS outbox';
$lang_admin['tasks']			= 'Tasks';
$lang_admin['addressbook']		= 'Addressbook';
$lang_admin['all']				= 'All';
$lang_admin['li']				= 'Logged in';
$lang_admin['nli']				= 'Not logged in';
$lang_admin['question']			= 'Question';
$lang_admin['requires']			= 'Requires';
$lang_admin['addfaq']			= 'Add FAQ';
$lang_admin['both']				= 'Both';
$lang_admin['autoperms']		= 'Automatic permission settings';
$lang_admin['autoperms_desc']	= 'If you give the FTP-details from your b1gMail installation here, the update wizard will be able to give permission for files to be updated automatically. That will make the updating process a lot easier for you. You will not need an FTP client or the like.';
$lang_admin['ftphost']			= 'FTP host';
$lang_admin['ftpport']			= 'FTP port';
$lang_admin['ftpuser']			= 'FTP user';
$lang_admin['ftppass']			= 'FTP password';
$lang_admin['ftpdir']			= 'Directory to b1gMail';
$lang_admin['ftpperms']			= 'Default permissions';
$lang_admin['certmaillife']		= 'Certified mail storage time';
$lang_admin['days']				= 'days';
$lang_admin['searchupdatesnow']	= 'Search for new updates now.';
$lang_admin['updatesdesc']		= 'Please click the following button to search for updates (e.g. important security updates) for your b1gMail installation. Please note that in the updating process the license number and serial number of your b1gMail license will be transmitted to our update server.';
$lang_admin['upderrordesc']		= 'An error occurred while trying to update. Please read the following error message.';
$lang_admin['noupdatesfound']	= 'No updates for your b1gMail installation have been found. Please try again later if necessary.';
$lang_admin['updatesfound']		= 'The following updates for your b1gMail installation have been found.';
$lang_admin['clicktoupdate']	= 'Please click &quot;Next &raquo;&quot; to start the update setup process.';
$lang_admin['next']				= 'Next';
$lang_admin['pleasereadme']		= 'Please read the following information on this update carefully.';
$lang_admin['changedfiles']		= 'Installing the update, the following files will be overwritten. Therefore they have to be writable (CHMOD 777). If you have specified your correct FTP details and if you have enabled the automatic permission settings, the update wizard will update the permissions automatically and you will not have to do anything.';
$lang_admin['filename']			= 'Filename';
$lang_admin['writeable']		= 'Writable';
$lang_admin['updating']			= 'Updating...';
$lang_admin['updateinstalled']	= 'The update has been installed.';
$lang_admin['moreupdates']		= 'Further updates are available. Please click &quot;Next &raquo;&quot; to continue.';
$lang_admin['banners']			= 'Banners';
$lang_admin['banner']			= 'Banner';
$lang_admin['weight']			= 'Weight';
$lang_admin['views']			= 'Views';
$lang_admin['paused']			= 'Paused';
$lang_admin['pause']			= 'Pause';
$lang_admin['continue']			= 'Continue';
$lang_admin['addbanner']		= 'Add banner';
$lang_admin['vars']				= 'Variables';
$lang_admin['wddomain']			= 'Webdisc subdomain';
$lang_admin['searchfor']		= 'Search for';
$lang_admin['searchin']			= 'Search in';
$lang_admin['address']			= 'Address';
$lang_admin['searchingfor']		= 'You are searching for';
$lang_admin['detectduplicates']	= 'Detect duplicates';
$lang_admin['activity_desc1']	= 'To all users who';
$lang_admin['notloggedinsince']	= 'have not ben logged in for:';
$lang_admin['activity_desc2']	= 'apply the following operation:';
$lang_admin['undowarn']			= 'This action may perhaps be irreversible.';
$lang_admin['activity_done']	= 'The action has been performed on <b>%d</b> users.';
$lang_admin['trash_desc']		= 'Erase all messages from the trash folders of users who belong to the following groups:';
$lang_admin['trash_only']		= 'Only erase messages which';
$lang_admin['trash_daysonly']	= 'are older than:';
$lang_admin['trash_done']		= 'The action has been completed. <b>%d</b> emails (%.02f MB) have been deleted.';
$lang_admin['trash_sizesonly']	= 'are larger than:';
$lang_admin['whobelongtogrps']	= 'belong to one of the following groups:';
$lang_admin['withoutpackage']	= 'Without package';
$lang_admin['package']			= 'Package';
$lang_admin['realpackage']		= 'Are you sure you want to disable and erase the entire plugin package? Disabling plugins may permanently erase the data connected to the plugin!';
$lang_admin['install_desc2']	= 'In the following you will find a summary of the plugin package meta data. Are you sure you want to install the package you have uploaded? Only install plugin packages from a trustworthy source because plugins may contain malicious code!';
$lang_admin['vendor']			= 'Vendor';
$lang_admin['forb1gmail']		= 'For b1gMail';
$lang_admin['yourversion']		= 'Your version';
$lang_admin['checkingsig']		= 'Checking digital signature...';
$lang_admin['sigfailed']		= 'Signature checking failed';
$lang_admin['sigfailed_desc']	= 'The connection to the signature server for checking the plugin signature failed.';
$lang_admin['sigofficial']		= 'Official plugin package';
$lang_admin['sigofficial_desc']	= 'This plugin package was officially released by the b1gMail project and has a valid signature. It may be installed without concern.';
$lang_admin['sigver']			= 'Verified plugin package';
$lang_admin['sigver_desc']		= 'The source of the plugin-package has been verified. The package is not known to cause any damage or errors.';
$lang_admin['sigunknown']		= 'Unknown plugin package';
$lang_admin['sigunknown_desc']	= 'This plugin and its source are unknown. Only install it if it is from a reliable source.';
$lang_admin['sigmal']			= 'Erroneous/malicious plugin package';
$lang_admin['sigmal_desc']		= 'This plugin package contains serious errors or malicious code and should not be installed under any circumstances.';
$lang_admin['plugin_insterr']	= 'The plugin could not be installed. It may already have been installed, it may be erroneous, or it may be incompatible with other packages.';
$lang_admin['plugin_installed']	= 'The plugin has been installed successfully.';
$lang_admin['sendingletter']	= 'Sending newsletter...';
$lang_admin['lockedusernames']	= 'Blocked user names';
$lang_admin['addlockedusername']= 'Add blocked user name';
$lang_admin['username']			= 'User name';
$lang_admin['startswith']		= 'Starts with';
$lang_admin['endswith']			= 'Ends with';
$lang_admin['contains']			= 'Contains';
$lang_admin['isequal']			= 'Is equal to';
$lang_admin['recpdetection']	= 'Recipient detection';
$lang_admin['rd_static']		= 'Conventional (statically)';
$lang_admin['rd_dynamic']		= 'Use receive rules (dynamically)';
$lang_admin['searchengine']		= 'Search engine';
$lang_admin['activate']			= 'Enable';
$lang_admin['licensekey']		= 'License key';
$lang_admin['features']			= 'Features';
$lang_admin['minamount']		= 'Minimum amount (in the currency stated above)';
$lang_admin['smime']			= 'S/MIME';
$lang_admin['openssl_err']		= 'The PHP extension <code>openssl</code> is not available. S/MIME support required the PHP <code>openssl</code> extension to be installed. Please install the extension in order to use S/MIME in b1gMail.';
$lang_admin['validity']			= 'Validity';
$lang_admin['rootcerts']		= 'Root certificates';
$lang_admin['addrootcert']		= 'Add root certificate';
$lang_admin['certfile']			= 'Certificate (.pem file)';
$lang_admin['cert_err_noca']	= 'This certificate cannot be imported because it is not a root certificate for S/MIME purposes.';
$lang_admin['cert_err_format']	= 'The file is not a valid PEM certificate.';
$lang_admin['cert_err_exists']	= 'The certificate already exists.';
$lang_admin['cert_upload_own']	= 'Upload own certificates';
$lang_admin['cert_generate']	= 'Issue certificates';
$lang_admin['cert_ca']			= 'Certificate authority';
$lang_admin['setedit']			= 'Add / edit';
$lang_admin['cert_pleasesetca'] = 'Please add a certificate authority first.';
$lang_admin['cert_ca_info']		= 'This certificate authority will be used to issue certificates for your users.<br /><br /><b>NOTICE:</b> Both certificate and private key will be stored unencrypted in the database!';
$lang_admin['cert_ca_current']	= 'Current certificate authority';
$lang_admin['cert_noca']		= 'You did not add a certificate authority yet.';
$lang_admin['cert_ca_import']	= 'Import certificate authority';
$lang_admin['cert_ca_file_pem']	= 'CA certificate (.pem file)';
$lang_admin['cert_ca_file_key']	= '<i>and</i> private key (.key file)';
$lang_admin['cert_ca_cert']		= 'CA certificate / private key';
$lang_admin['cert_ca_pass']		= 'Private key password (if necessary)';
$lang_admin['cert_caerr_format']	= 'Certificate or key file are in an invalid format.';
$lang_admin['cert_caerr_purpose']	= 'The certificate is not suitable for issuing S/MIME certificates.';
$lang_admin['cert_caerr_pkcheck']	= 'The private key does not fit to the certificate or the password is wrong.';
$lang_admin['sum']				= 'Sum';
$lang_admin['exturl']			= 'external Link';
$lang_admin['exturl_warning']	= 'Show a Warning site before access an external Link';
$lang_admin['text_deref']		= 'Warning, if external url is visited';
$lang_admin['company']			= 'Company';
$lang_admin['taxid'] 			= 'VAT';
$lang_admin['latinmodenote']    = 'Your database is still in Latin1 format / ISO charset. To get updates in the future you have to convert the database in <a href="../setup/utf8convert.php">UTF-8</a>.';
$lang_admin['bzip2']			= 'The <code>bz2</code> php extension is not available. If you want compress the logfiles you should install this extension soon.';
$lang_admin['use_tls']			= 'Use TLS';
$lang_admin['use_ssl']			= 'Use SSL';
$lang_admin['no_encryption']	= 'No encryption';
$lang_admin['secure_connection'] 	= 'Secure connection';
$lang_admin['follow_mastodon']	= 'Follow us on Mastodon';
$lang_admin['to_startpage']		= 'To Startpage';
// Avoid duplicates
$lang_admin['zip']				= $lang_user['zip'];
$lang_admin['city']				= $lang_user['city'];
$lang_admin['street']			= $lang_user['street'];
$lang_admin['no']				= $lang_user['nr'];