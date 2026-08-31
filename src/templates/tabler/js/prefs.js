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

var currentSignature = 0;

function showStatement()
{
	openOverlay(bmAppendSession('prefs.php?action=membership&do=statement'),
		lang['statement'],
		700,
		500,
		true);
}

function startSignatureEditor()
{
	var editor = new htmlEditor('html');
	editor.height = '100%';
	editor.onReady = function()
	{
		editor.start();
		editor.switchMode('html', true);
	}
	editor.init();
}

function showSignature(id)
{
	var elems = EBID('signaturesList').getElementsByTagName('LI');
	for(var i=0; i<elems.length; ++i)
	{
		if(elems[i].dataset.sigId == id)
			elems[i].className = 'sel';
		else
			elems[i].className = '';
	}

	EBID('sigItem').innerHTML = '';
	EBID('removeButton').disabled = true;

	if(id <= 0)
		return;

	MakeXMLRequest(bmAppendSession('prefs.php?action=signatures&do=edit&id='+id), function(http)
			{
				if(http.readyState == 4 && http.responseText)
				{
					EBID('sigItem').innerHTML = http.responseText;
					EBID('removeButton').disabled = false;
					currentSignature = id;
					startSignatureEditor();
				}
			});
}

function addSignature()
{
	EBID('sigItem').innerHTML = '';
	MakeXMLRequest(bmAppendSession('prefs.php?action=signatures&do=add'), function(http)
			{
				if(http.readyState == 4 && http.responseText)
				{
					EBID('sigItem').innerHTML = http.responseText;
					EBID('removeButton').disabled = true;
					EBID('titel').focus();
					startSignatureEditor();
				}
			});
}

function updateAliasForm()
{
	var typ = EBID('typ_1').checked ? 1 : 3;

	EBID('tbody_1').style.display = typ == 1 ? '' : 'none';
	EBID('tbody_3').style.display = typ == 3 ? '' : 'none';
}
function checkPOP3AccountForm(form)
{
	if(form.elements['p_host'].value.length < 2
		|| form.elements['p_user'].value.length < 2
		|| form.elements['p_port'].value.length < 1)
	{
		alert(lang['fillin']);
		return(false);
	}
	return(true);
}
function checkFilterForm(form)
{
	if(form.elements['title'].value.length < 2)
	{
		alert(lang['fillin']);
		return(false);
	}
	return(true);
}
function checkSignatureForm(form)
{
	if(form.elements['titel'].value.length < 2)
	{
		alert(lang['fillin']);
		return(false);
	}
	return(true);
}
function addPublicCert()
{
	openOverlay(bmAppendSession('prefs.php?action=keyring&do=importPublicCertificate'),
		lang['addcert'],
		520,
		140,
		true);
}
function addPrivateCert(pkcs12Support)
{
	openOverlay(bmAppendSession('prefs.php?action=keyring&do=importPrivateCertificate'),
		lang['addcert'],
		520,
		pkcs12Support ? 170 : 230,
		true);
}
function exportPrivateCert(hash)
{
	openOverlay(bmAppendSession('prefs.php?action=keyring&do=exportPrivateCertificate&hash=' + hash),
		lang['exportcert'],
		520,
		140,
		true);
}

(function () {
	function collectPushTypes() {
		var types = {};
		var rows = document.querySelectorAll('.bm-push-type-row[data-push-type]');
		for (var i = 0; i < rows.length; i++) {
			var typeKey = rows[i].getAttribute('data-push-type');
			var input = rows[i].querySelector('input[type="checkbox"]');
			if (typeKey && input) {
				types[typeKey] = input.checked;
			}
		}
		return types;
	}

	function updatePushButtons(subscribed) {
		var subBtn = document.getElementById('bmPushSubscribeBtn');
		var unsubBtn = document.getElementById('bmPushUnsubscribeBtn');
		var testBtn = document.getElementById('bmPushTestBtn');
		if (subBtn) {
			subBtn.style.display = subscribed ? 'none' : '';
		}
		if (unsubBtn) {
			unsubBtn.style.display = subscribed ? '' : 'none';
		}
		if (testBtn) {
			testBtn.style.display = subscribed ? '' : 'none';
		}
	}

	document.addEventListener('DOMContentLoaded', function () {
		if (typeof bmPush === 'undefined' || !bmPush.isSupported()) {
			return;
		}
		var subBtn = document.getElementById('bmPushSubscribeBtn');
		var unsubBtn = document.getElementById('bmPushUnsubscribeBtn');
		var testBtn = document.getElementById('bmPushTestBtn');
		var testResult = document.getElementById('bmPushTestResult');
		if (!subBtn && !unsubBtn) {
			return;
		}

		function syncPushUi() {
			var browserSub = false;
			var serverSubs = 0;
			var prefsEnabled = false;

			var pBrowser = bmPush.hasSubscription().then(function (v) {
				browserSub = !!v;
			});
			var pServer = fetch(bmAppendSession('push-api.php?action=status'), { credentials: 'same-origin' })
				.then(function (r) { return r.json(); })
				.then(function (data) {
					if (data && data.ok) {
						serverSubs = data.subscriptions || 0;
						prefsEnabled = !!data.prefsEnabled;
					}
				})
				.catch(function () {});

			return Promise.all([pBrowser, pServer]).then(function () {
				updatePushButtons(browserSub && (serverSubs > 0 || prefsEnabled));
				return { browserSub: browserSub, serverSubs: serverSubs, prefsEnabled: prefsEnabled };
			});
		}

		syncPushUi();

		if (subBtn) {
			subBtn.addEventListener('click', function () {
				subBtn.disabled = true;
				bmPush.subscribe(collectPushTypes()).then(function (r) {
					subBtn.disabled = false;
					if (r && r.ok) {
						syncPushUi();
					} else if (testResult) {
						testResult.textContent = lang['push_enable_fail'] || lang['push_enabled_fail'] || '';
					}
				}).catch(function () {
					subBtn.disabled = false;
				});
			});
		}
		if (unsubBtn) {
			unsubBtn.addEventListener('click', function () {
				unsubBtn.disabled = true;
				bmPush.unsubscribe().then(function () {
					unsubBtn.disabled = false;
					syncPushUi();
				}).catch(function () {
					unsubBtn.disabled = false;
				});
			});
		}
		function pushTestFailMessage(data) {
			if (!data) {
				return lang['push_test_fail'] || 'Failed';
			}
			if (data.reason === 'no_subscription' || (data.subscriptions !== undefined && data.subscriptions < 1)) {
				return lang['push_test_fail_nosub'] || lang['push_test_fail'] || 'Failed';
			}
			if (data.reason === 'prefs_blocked' || data.prefsEnabled === false) {
				return lang['push_test_fail_noprefs'] || lang['push_test_fail'] || 'Failed';
			}
			if (data.reason === 'mail_type_disabled') {
				return lang['push_test_fail_mailtype'] || lang['push_test_fail'] || 'Failed';
			}
			if (data.reason === 'delivery_failed' || (data.failed && data.failed > 0)) {
				var msg = lang['push_test_fail_delivery'] || lang['push_test_fail'] || 'Failed';
				if (data.lastError) {
					msg += ' (' + data.lastError + ')';
				}
				return msg;
			}
			return lang['push_test_fail'] || 'Failed';
		}

		if (testBtn) {
			testBtn.addEventListener('click', function () {
				testBtn.disabled = true;
				if (testResult) {
					testResult.textContent = '';
				}
				syncPushUi().then(function (state) {
					if (!state.browserSub) {
						testBtn.disabled = false;
						if (testResult) {
							testResult.textContent = lang['push_test_fail_nosub'] || lang['push_test_fail'] || 'Failed';
						}
						return;
					}
					if (state.serverSubs < 1) {
						return bmPush.subscribe(collectPushTypes()).then(function (r) {
							if (!r || !r.ok) {
								testBtn.disabled = false;
								if (testResult) {
									testResult.textContent = lang['push_test_fail_nosub'] || lang['push_test_fail'] || 'Failed';
								}
								return;
							}
							return fetch(bmAppendSession('start.php?action=testPush'), { credentials: 'same-origin' });
						});
					}
					return fetch(bmAppendSession('start.php?action=testPush'), { credentials: 'same-origin' });
				}).then(function (r) {
					if (!r || !r.json) {
						return;
					}
					return r.json();
				}).then(function (data) {
					if (!data) {
						return;
					}
					testBtn.disabled = false;
					if (testResult) {
						if (data.ok) {
							var okMsg = lang['push_test_ok'] || 'OK';
							testResult.textContent = okMsg.split('%%n%%').join(String(data.sent));
						} else {
							testResult.textContent = pushTestFailMessage(data);
						}
					}
				}).catch(function () {
					testBtn.disabled = false;
					if (testResult) {
						testResult.textContent = lang['push_test_fail'] || 'Failed';
					}
				});
			});
		}
	});
})();
