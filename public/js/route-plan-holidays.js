(function () {
    function showTrainerAlert(en, hi) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Notice / सूचना',
                html: '<p style="margin:0 0 8px;font-weight:600;">' + en + '</p>'
                    + '<p style="margin:0;color:#555;">' + hi + '</p>',
                confirmButtonText: 'OK / ठीक है',
                confirmButtonColor: '#0d6efd',
                allowOutsideClick: true,
            });
            return;
        }
        alert(en + '\n' + hi);
    }

    function toDateStr(date) {
        var y = date.getFullYear();
        var m = String(date.getMonth() + 1).padStart(2, '0');
        var d = String(date.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + d;
    }

    function isSunday(date) {
        return date.getDay() === 0;
    }

    function isSecondSaturday(date) {
        return date.getDay() === 6 && Math.ceil(date.getDate() / 7) === 2;
    }

    function isGlobalHoliday(dateStr) {
        var date = new Date(dateStr + 'T00:00:00');
        return isSunday(date) || isSecondSaturday(date);
    }

    function fetchHolidays(stateId, districtId) {
        var params = {};
        if (stateId) {
            params.state_id = stateId;
        }
        if (districtId) {
            params.district_id = districtId;
        }

        return $.get('/holidays/list', params).then(function (data) {
            var holidayDates = (data.holidays || []).map(function (h) { return h.date; });
            var holidayMap = {};
            (data.holidays || []).forEach(function (h) {
                holidayMap[h.date] = h.title || 'Off';
            });
            var workingDates = data.working_dates || [];
            return {
                holidayDates: holidayDates,
                holidayMap: holidayMap,
                workingDates: workingDates,
            };
        });
    }

    function holidayLabel(dateStr, holidayMap) {
        var date = new Date(dateStr + 'T00:00:00');
        if (isSunday(date)) {
            return 'Sunday';
        }
        if (isSecondSaturday(date)) {
            return '2nd Saturday';
        }
        return holidayMap[dateStr] || 'Off';
    }

    function isHoliday(dateStr, holidayDates, workingDates) {
        if ((workingDates || []).indexOf(dateStr) !== -1) {
            return false;
        }
        if ((holidayDates || []).indexOf(dateStr) !== -1) {
            return true;
        }
        return isGlobalHoliday(dateStr);
    }

    function calculateEndDate(startStr, workingDays, holidayDates, workingDates) {
        if (!startStr || !workingDays) {
            return null;
        }

        var current = new Date(startStr + 'T00:00:00');
        var count = 0;

        while (count < workingDays) {
            var ds = toDateStr(current);
            if (!isHoliday(ds, holidayDates, workingDates)) {
                count++;
            }
            if (count < workingDays) {
                current.setDate(current.getDate() + 1);
            }
        }

        return toDateStr(current);
    }

    function formatDisplayDate(dateStr) {
        if (!dateStr) {
            return '';
        }
        var parts = dateStr.split('-');
        return parts[2] + '/' + parts[1] + '/' + parts[0];
    }

    function countHolidaysBetween(startStr, endStr, holidayDates, workingDates) {
        var count = 0;
        var cur = new Date(startStr + 'T00:00:00');
        var end = new Date(endStr + 'T00:00:00');

        while (cur <= end) {
            if (isHoliday(toDateStr(cur), holidayDates, workingDates)) {
                count++;
            }
            cur.setDate(cur.getDate() + 1);
        }

        return count;
    }

    function excludedHolidaysBetween(startStr, endStr, holidayDates, workingDates, holidayMap) {
        var labels = [];
        var cur = new Date(startStr + 'T00:00:00');
        var end = new Date(endStr + 'T00:00:00');

        while (cur <= end) {
            var ds = toDateStr(cur);
            if (isHoliday(ds, holidayDates, workingDates)) {
                labels.push(formatDisplayDate(ds) + ' (' + holidayLabel(ds, holidayMap) + ')');
            }
            cur.setDate(cur.getDate() + 1);
        }

        return labels;
    }

    function minutesBetween(intime, outtime) {
        if (!intime || !outtime) {
            return null;
        }
        var s = intime.split(':');
        var e = outtime.split(':');
        var startMins = (parseInt(s[0], 10) * 60) + parseInt(s[1], 10);
        var endMins = (parseInt(e[0], 10) * 60) + parseInt(e[1], 10);
        if (isNaN(startMins) || isNaN(endMins) || endMins <= startMins) {
            return null;
        }
        return endMins - startMins;
    }

    function initRoutePlanForm($form, holidayDates, holidayMap, workingDates) {
        var $fields = $form.find('.route-plan-fields');
        var $start = $form.find('.route-plan-start');
        var $workingDays = $form.find('.route-plan-working-days');
        var $endDisplay = $form.find('.route-plan-end-display');
        var $holidayNote = $form.find('.route-plan-holiday-note');
        var $intime = $form.find('.route-plan-intime');
        var $outtime = $form.find('.route-plan-outtime');
        var $dailyDisplay = $form.find('.route-plan-daily-display');
        var $hoursDisplay = $form.find('.route-plan-hours-display');
        var $hoursNote = $form.find('.route-plan-hours-note');

        var requiredHours = parseFloat($fields.data('required-hours'));
        if (isNaN(requiredHours)) requiredHours = null;
        var dailyMaxHours = parseFloat($fields.data('daily-max-hours'));
        if (isNaN(dailyMaxHours)) dailyMaxHours = null;
        var minWorkingDays = parseInt($fields.data('min-working-days'), 10);
        if (isNaN(minWorkingDays)) minWorkingDays = null;

        function updateHours() {
            var days = parseInt($workingDays.val(), 10);
            var mins = minutesBetween($intime.val(), $outtime.val());

            if (mins === null) {
                $dailyDisplay.val('');
                $hoursDisplay.val('');
                $hoursNote.text('');
                return;
            }

            var daily = Math.round((mins / 60) * 100) / 100;
            $dailyDisplay.val(daily + ' hrs/day');

            if (days) {
                var planned = Math.round((days * daily) * 100) / 100;
                $hoursDisplay.val(planned + ' hrs (' + days + ' days × ' + daily + ' hrs/day)');
            } else {
                $hoursDisplay.val('');
            }

            // Keep notes empty — validation popup shows on submit only
            $hoursNote.html('');
        }

        function updateEndDate() {
            var start = $start.val();
            var days = parseInt($workingDays.val(), 10);

            if (!start || !days) {
                $endDisplay.val('');
                $holidayNote.text('');
                updateHours();
                return;
            }

            if (isHoliday(start, holidayDates, workingDates)) {
                $holidayNote.html('<span class="text-danger">This date is a holiday. Choose another day. / यह तारीख छुट्टी है। दूसरी तारीख चुनें।</span>');
                $endDisplay.val('');
                updateHours();
                return;
            }

            var end = calculateEndDate(start, days, holidayDates, workingDates);
            $endDisplay.val(formatDisplayDate(end));

            var excluded = excludedHolidaysBetween(start, end, holidayDates, workingDates, holidayMap);
            if (excluded.length > 0) {
                $holidayNote.text(days + ' working days — ' + excluded.length + ' holiday(s) skipped. / ' + days + ' काम के दिन — ' + excluded.length + ' छुट्टियाँ हटाई गईं।');
            } else {
                $holidayNote.text(days + ' working days — no holiday. / ' + days + ' काम के दिन — कोई छुट्टी नहीं।');
            }

            updateHours();
        }

        $start.on('change input', updateEndDate);
        $workingDays.on('change input', updateEndDate);
        $intime.on('change input', updateHours);
        $outtime.on('change input', updateHours);

        $form.on('submit', function (e) {
            var start = $start.val();
            var days = parseInt($workingDays.val(), 10);

            if (!start || !days) {
                e.preventDefault();
                showTrainerAlert(
                    'Please fill start date and working days.',
                    'कृपया शुरू की तारीख और काम के दिन भरें।'
                );
                return;
            }

            if (isHoliday(start, holidayDates, workingDates)) {
                e.preventDefault();
                showTrainerAlert(
                    'This date is a holiday. Please choose another day.',
                    'यह तारीख छुट्टी है। दूसरी तारीख चुनें।'
                );
                return;
            }

            if (!$intime.val() || !$outtime.val()) {
                e.preventDefault();
                showTrainerAlert(
                    'Please fill Intime and Outtime.',
                    'कृपया आने और जाने का समय भरें।'
                );
                return;
            }

            var mins = minutesBetween($intime.val(), $outtime.val());
            if (mins === null) {
                e.preventDefault();
                showTrainerAlert(
                    'Outtime must be after Intime.',
                    'जाने का समय, आने के समय के बाद होना चाहिए।'
                );
                return;
            }

            var daily = Math.round((mins / 60) * 100) / 100;
            if (dailyMaxHours !== null && daily - dailyMaxHours > 0.001) {
                e.preventDefault();
                showTrainerAlert(
                    'One day max ' + dailyMaxHours + ' hours. You put ' + daily + ' hours. Please put less time.',
                    'एक दिन में ज्यादा से ज्यादा ' + dailyMaxHours + ' घंटे। आपने ' + daily + ' घंटे डाले हैं। कृपया कम समय डालें।'
                );
                return;
            }

            if (minWorkingDays !== null && days < minWorkingDays) {
                e.preventDefault();
                showTrainerAlert(
                    'Please choose at least ' + minWorkingDays + ' working days.',
                    'कृपया कम से कम ' + minWorkingDays + ' काम के दिन चुनें।'
                );
                return;
            }

            if (requiredHours !== null) {
                var planned = Math.round((days * daily) * 100) / 100;
                if (planned + 0.001 < requiredHours) {
                    e.preventDefault();
                    showTrainerAlert(
                        'Total hours are less than needed (' + requiredHours + '). Add more days or hours.',
                        'कुल घंटे पूरे नहीं हो रहे (' + requiredHours + ')। और दिन या घंटे बढ़ाएँ।'
                    );
                    return;
                }
            }
        });

        updateEndDate();
        updateHours();
    }

    $(document).ready(function () {
        $('form[id="uplodeForm"]').each(function () {
            var $form = $(this);
            var $fields = $form.find('.route-plan-fields');
            if (!$fields.length) {
                return;
            }

            var stateId = $fields.data('state-id') || null;
            var districtId = $fields.data('district-id') || null;

            fetchHolidays(stateId, districtId).always(function (result) {
                var holidayDates = (result && result.holidayDates) ? result.holidayDates : [];
                var holidayMap = (result && result.holidayMap) ? result.holidayMap : {};
                var workingDates = (result && result.workingDates) ? result.workingDates : [];
                initRoutePlanForm($form, holidayDates, holidayMap, workingDates);
            });
        });
    });
})();
