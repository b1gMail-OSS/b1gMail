var BM_ACP_THEME_KEY = 'bm-acp-theme';

function bmAcpSetTheme(theme)
{
	if (theme !== 'dark' && theme !== 'light')
		return;

	try {
		localStorage.setItem(BM_ACP_THEME_KEY, theme);
	} catch (e) {}

	document.documentElement.setAttribute('data-bs-theme', theme);
	document.documentElement.classList.toggle('theme-dark', theme === 'dark');
}

function bmPadTime(time) {
    var match = String(time || '').match(/^(\d{1,2}):(\d{2})/);
    if (!match)
        return '00:00';
    var hour = match[1].length === 1 ? '0' + match[1] : match[1];
    return hour + ':' + match[2];
}

function bmDatetimeParts(value) {
    var match = String(value).trim().match(/^(\d{1,2}\.\d{1,2}\.\d{4})(?:\s+(\d{1,2}:\d{2}))?$/);
    if (!match)
        return null;
    return { date: match[1], time: match[2] ? bmPadTime(match[2]) : '' };
}

function bmApplyDatetime(field) {
    var time = field.getAttribute('data-bm-time') || '00:00';
    var date = String(field.value).trim().split(/\s+/)[0];
    if (/^\d{1,2}\.\d{1,2}\.\d{4}$/.test(date))
        field.value = date + ' ' + time;
}

var bmDatetimePickedAt = new WeakMap();

function bmDatetimeTime(field) {
    var parts = bmDatetimeParts(field.value);
    return (parts && parts.time) || field.getAttribute('data-bm-time') || '00:00';
}

function bmEnableDatetime(field) {
    var Datepicker = window.tabler && window.tabler.Datepicker;
    var picker = Datepicker && Datepicker.getInstance(field);
    if (!picker || !picker.calendar || field._bmDatetimeEnabling)
        return;

    field._bmDatetimeEnabling = true;
    var calendar = picker.calendar;
    var time = bmDatetimeTime(field);
    field.setAttribute('data-bm-time', time);

    var popup = calendar.context && calendar.context.mainElement;
    var hasTime = popup && popup !== field && popup.querySelector('[data-vc="time"]');
    if (calendar.selectionTimeMode !== 24 || (popup && popup !== field && !hasTime)) {
        calendar.set({
            selectionTimeMode: 24,
            selectedTime: time,
            timeControls: 'all'
        });
    }

    calendar.onChangeTime = function (self) {
        var selected = self.context && self.context.selectedTime;
        if (!selected)
            return;
        field.setAttribute('data-bm-time', bmPadTime(String(selected).slice(0, 5)));
        bmApplyDatetime(field);
    };
    field._bmDatetimeEnabling = false;
}

function bmPrepareDatetimePopup(calendar, field) {
    var context = calendar && calendar.context;
    if (!calendar || !context || !calendar.inputMode || context.inputModeInit || context.mainElement !== field)
        return;

    // currentType ist schon gesetzt, das Popup aber noch nicht erzeugt.
    // show() würde sonst das Eingabefeld selbst verschieben.
    context.currentType = '';
}

(function () {
    var Datepicker = window.tabler && window.tabler.Datepicker;
    if (!Datepicker || !Datepicker.prototype || Datepicker.prototype._bmShowWrapped)
        return;

    var origShow = Datepicker.prototype.show;
    Datepicker.prototype.show = function () {
        if (this._bmShowing)
            return;
        this._bmShowing = true;
        try {
            bmPrepareDatetimePopup(this.calendar, this._element);
            return origShow.apply(this, arguments);
        } finally {
            this._bmShowing = false;
        }
    };
    Datepicker.prototype._bmShowWrapped = true;
})();

function bmEnsureDatetimePopup(field) {
    var Datepicker = window.tabler && window.tabler.Datepicker;
    if (!Datepicker)
        return null;

    var picker = Datepicker.getOrCreateInstance(field);
    bmPrepareDatetimePopup(picker && picker.calendar, field);
    return picker;
}

document.addEventListener('focusin', function (event) {
    var field = event.target;
    if (!field.matches || !field.matches('[data-bm-datetime="1"]'))
        return;

    var parts = bmDatetimeParts(field.value);
    var time = (parts && parts.time) || field.getAttribute('data-bm-time') || '00:00';
    field.setAttribute('data-bm-time', time);
    if (parts)
        field.value = parts.date;

    var Datepicker = window.tabler && window.tabler.Datepicker;
    if (!Datepicker || !Datepicker.getInstance(field)) {
        field.setAttribute('data-bs-vcp-options', JSON.stringify({
            selectionTimeMode: 24,
            selectedTime: time,
            timeControls: 'all'
        }));
    }

    bmEnsureDatetimePopup(field);
    bmResetMonthField(field);

    window.setTimeout(function () {
        bmApplyDatetime(field);
        bmEnableDatetime(field);
        bmWatchMonthPopup(field);
        bmPlacePicker(field);
    }, 0);
}, true);

document.addEventListener('shown.bs.datepicker', function (event) {
    var field = event.target;
    if (!field.getAttribute || field.getAttribute('data-bm-datetime') !== '1')
        return;

    bmResetMonthField(field);
    bmEnableDatetime(field);
    bmWatchMonthPopup(field);
    window.setTimeout(function () {
        bmPlacePicker(field);
    }, 0);
});

document.addEventListener('change.bs.datepicker', function (event) {
    var field = event.target;
    if (!field.getAttribute || field.getAttribute('data-bm-datetime') !== '1')
        return;

    bmDatetimePickedAt.set(field, Date.now());
    bmApplyDatetime(field);
});

function bmResetMonthField(field) {
    field.removeAttribute('data-vc-position');
    field.style.setProperty('position', 'static', 'important');
    field.style.setProperty('left', 'auto', 'important');
    field.style.setProperty('top', 'auto', 'important');
    field.style.setProperty('right', 'auto', 'important');
    field.style.setProperty('margin', '0', 'important');
    field.style.setProperty('transform', 'none', 'important');
}

function bmPlacePicker(field) {
    var Datepicker = window.tabler && window.tabler.Datepicker;
    var picker = Datepicker && Datepicker.getInstance(field);
    if (!picker || !picker.calendar || !picker.calendar.context)
        return;

    bmResetMonthField(field);

    var popup = picker.calendar.context.mainElement;
    if (!popup || popup === field || popup.hasAttribute('data-vc-calendar-hidden'))
        return;

    var rect = field.getBoundingClientRect();
    var width = popup.offsetWidth || 280;
    var height = popup.offsetHeight || 240;
    var left = rect.left;
    if (left + width > window.innerWidth - 8)
        left = Math.max(8, window.innerWidth - width - 8);

    var top = rect.bottom + 4;
    if (top + height > window.innerHeight - 8 && rect.top > height + 12)
        top = rect.top - height - 4;

    var nextLeft = Math.round(left) + 'px';
    var nextTop = Math.round(top) + 'px';
    if (popup.style.position === 'fixed' && popup.style.left === nextLeft && popup.style.top === nextTop)
        return;

    popup.style.position = 'fixed';
    popup.style.left = nextLeft;
    popup.style.top = nextTop;
    popup.style.margin = '0';
    popup.style.zIndex = '1055';
}

function bmWatchMonthPopup(field) {
    var Datepicker = window.tabler && window.tabler.Datepicker;
    var picker = Datepicker && Datepicker.getInstance(field);
    var popup = picker && picker.calendar && picker.calendar.context && picker.calendar.context.mainElement;
    if (!popup || popup === field || popup._bmMonthWatch)
        return;

    popup._bmMonthWatch = true;
    var observer = new MutationObserver(function () {
        bmPlacePicker(field);
    });
    observer.observe(popup, { attributes: true, attributeFilter: ['style', 'data-vc-calendar-hidden'] });
}

function bmConfigureMonthPicker(field) {
    var Datepicker = window.tabler && window.tabler.Datepicker;
    var picker = Datepicker && Datepicker.getInstance(field);
    if (!picker || !picker.calendar)
        return;

    var calendar = picker.calendar;
    if (calendar.type !== 'month')
        calendar.set({ type: 'month' });

    calendar.onClickMonth = function (self) {
        var month = Number(self.context.selectedMonth) + 1;
        var year = Number(self.context.selectedYear);
        if (month >= 1 && month <= 12 && year > 1900)
            field.value = (month < 10 ? '0' : '') + month + '.' + year;

        bmResetMonthField(field);
        var current = Datepicker.getInstance(field);
        if (current)
            current.hide();
    };

    bmWatchMonthPopup(field);
    bmPlacePicker(field);
}

document.addEventListener('focusin', function (event) {
    var field = event.target;
    if (!field.getAttribute || field.getAttribute('data-bm-month') !== '1')
        return;

    bmResetMonthField(field);
    window.setTimeout(function () {
        bmResetMonthField(field);
        bmConfigureMonthPicker(field);
    }, 0);
}, true);

document.addEventListener('shown.bs.datepicker', function (event) {
    var field = event.target;
    if (!field.getAttribute || field.getAttribute('data-bm-month') !== '1')
        return;

    bmConfigureMonthPicker(field);
    window.setTimeout(function () {
        bmPlacePicker(field);
    }, 0);
});

function bmPlaceOpenMonthPickers() {
    document.querySelectorAll('[data-bm-month="1"]').forEach(bmPlacePicker);
}

window.addEventListener('resize', bmPlaceOpenMonthPickers);
window.addEventListener('scroll', bmPlaceOpenMonthPickers, true);

document.addEventListener('hide.bs.datepicker', function (event) {
    var field = event.target;
    if (!field.getAttribute || field.getAttribute('data-bm-datetime') !== '1')
        return;

    var pickedAt = bmDatetimePickedAt.get(field) || 0;
    if (Date.now() - pickedAt < 300)
        event.preventDefault();
});

document.addEventListener("DOMContentLoaded", function () {
    if (window.tabler && tabler.OtpInput) {
        document.querySelectorAll('.bm-mfa-otp[data-bs-toggle="otp"]').forEach(function (el) {
            tabler.OtpInput.getOrCreateInstance(el);
        });
    }

    if (window.tabler && tabler.Sparkline) {
        document.querySelectorAll('[data-bs-toggle="sparkline"]').forEach(function (el) {
            tabler.Sparkline.getOrCreateInstance(el);
        });
    }

    document.querySelectorAll("[data-bm-mail-card]").forEach(function (card) {
        card.querySelectorAll("[data-bm-range]").forEach(function (button) {
            button.addEventListener("click", function () {
                var ranges;
                try {
                    ranges = JSON.parse(card.getAttribute("data-bm-ranges") || "{}");
                } catch (e) {
                    return;
                }
                var item = ranges[button.getAttribute("data-bm-range")];
                if (!item)
                    return;
                var sum = card.querySelector("[data-bm-sum]");
                if (sum)
                    sum.textContent = item.sum;
                var pct = card.querySelector("[data-bm-pct]");
                if (pct) {
                    if (item.pct === null) {
                        pct.classList.add("d-none");
                    } else {
                        var up = item.pct >= 0;
                        pct.classList.remove("d-none", "text-green", "text-red");
                        pct.classList.add(up ? "text-green" : "text-red");
                        pct.innerHTML = (item.pct > 0 ? "+" : "") + item.pct + '% <i class="fa-solid ' + (up ? "fa-arrow-up" : "fa-arrow-down") + ' ms-1"></i>';
                    }
                }
                var label = card.querySelector("[data-bm-range-label]");
                if (label)
                    label.textContent = button.textContent.trim();
                card.querySelectorAll("[data-bm-range]").forEach(function (other) {
                    other.classList.remove("active");
                    other.removeAttribute("aria-current");
                });
                button.classList.add("active");
                button.setAttribute("aria-current", "true");
                var logTotal = (item.info || 0) + (item.warning || 0) + (item.error || 0);
                ["info", "warning", "error"].forEach(function (key) {
                    if (item[key] == null)
                        return;
                    var count = card.querySelector('[data-bm-count="' + key + '"]');
                    if (count)
                        count.textContent = " " + item[key];
                    var bar = card.querySelector('[data-bm-bar="' + key + '"]');
                    if (bar) {
                        bar.style.width = (logTotal > 0 ? (item[key] / logTotal * 100) : 0) + "%";
                        bar.classList.toggle("d-none", item[key] <= 0);
                    }
                    var link = card.querySelector('[data-bm-log="' + key + '"]');
                    if (link && item.start) {
                        var url = new URL(link.getAttribute("href"), window.location.href);
                        url.searchParams.set("start", item.start);
                        url.searchParams.set("end", item.end);
                        link.setAttribute("href", url.pathname + url.search);
                    }
                });
                var spark = card.querySelector('[data-bs-toggle="sparkline"]');
                if (spark && window.tabler && tabler.Sparkline) {
                    var inst = tabler.Sparkline.getInstance(spark) || tabler.Sparkline.getOrCreateInstance(spark);
                    inst.update(item.values);
                }
            });
        });
    });

    if (window.innerWidth >= 992) {
        document.querySelectorAll(".nav-item.dropdown").forEach(function (dropdown) {
            dropdown.addEventListener("mouseenter", function () {
                let toggle = dropdown.querySelector(".dropdown-toggle")
                    || dropdown.querySelector('[data-bs-toggle="dropdown"]');
                let menu = dropdown.querySelector(".dropdown-menu");

                if (!toggle || !menu)
                    return;

                toggle.classList.add("show");
                menu.classList.add("show");
                toggle.setAttribute("aria-expanded", "true");
            });

            dropdown.addEventListener("mouseleave", function () {
                let toggle = dropdown.querySelector(".dropdown-toggle")
                    || dropdown.querySelector('[data-bs-toggle="dropdown"]');
                let menu = dropdown.querySelector(".dropdown-menu");

                if (!toggle || !menu)
                    return;

                toggle.classList.remove("show");
                menu.classList.remove("show");
                toggle.setAttribute("aria-expanded", "false");
            });
        });
    }
});