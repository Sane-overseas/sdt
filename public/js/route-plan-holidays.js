(function () {
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
            var notes = [];

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

            if (dailyMaxHours !== null) {
                if (daily - dailyMaxHours > 0.001) {
                    notes.push('<span class="text-danger">Daily ' + daily + ' hrs exceeds max ' + dailyMaxHours + ' hrs/day.</span>');
                } else {
                    notes.push('<span class="text-success">Daily ' + daily + ' hrs ≤ max ' + dailyMaxHours + ' hrs/day.</span>');
                }
            }

            if (minWorkingDays !== null && days) {
                if (days < minWorkingDays) {
                    notes.push('<span class="text-danger">Working days (' + days + ') below minimum ' + minWorkingDays + '.</span>');
                } else {
                    notes.push('<span class="text-success">Working days (' + days + ') meet minimum ' + minWorkingDays + '.</span>');
                }
            }

            if (requiredHours !== null && days && mins !== null) {
                var plannedCheck = Math.round((days * daily) * 100) / 100;
                if (plannedCheck + 0.001 < requiredHours) {
                    notes.push('<span class="text-danger">Planned ' + plannedCheck + ' hrs is less than required total ' + requiredHours + ' hrs.</span>');
                } else {
                    notes.push('<span class="text-success">Planned hours cover required total ' + requiredHours + ' hrs.</span>');
                }
            }

            $hoursNote.html(notes.join('<br>'));
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
                $holidayNote.html('<span class="text-danger">Start date is a holiday (' + holidayLabel(start, holidayMap) + '). Choose a working day.</span>');
                $endDisplay.val('');
                updateHours();
                return;
            }

            var end = calculateEndDate(start, days, holidayDates, workingDates);
            $endDisplay.val(formatDisplayDate(end));

            var excluded = excludedHolidaysBetween(start, end, holidayDates, workingDates, holidayMap);
            if (excluded.length > 0) {
                $holidayNote.text(days + ' working days — ' + excluded.length + ' holiday(s) excluded: ' + excluded.join(', ') + '.');
            } else {
                $holidayNote.text(days + ' working days — no holidays in this range.');
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
                alert('Please enter start date and working days.');
                return;
            }

            if (isHoliday(start, holidayDates, workingDates)) {
                e.preventDefault();
                alert('Start date cannot be a holiday (' + holidayLabel(start, holidayMap) + '). Please choose a working day.');
                return;
            }

            if (!$intime.val() || !$outtime.val()) {
                e.preventDefault();
                alert('Please enter Intime and Outtime.');
                return;
            }

            var mins = minutesBetween($intime.val(), $outtime.val());
            if (mins === null) {
                e.preventDefault();
                alert('Outtime must be after Intime.');
                return;
            }

            var daily = Math.round((mins / 60) * 100) / 100;
            if (dailyMaxHours !== null && daily - dailyMaxHours > 0.001) {
                e.preventDefault();
                alert('Daily training hours (' + daily + ') cannot exceed max ' + dailyMaxHours + ' hrs/day.');
                return;
            }

            if (minWorkingDays !== null && days < minWorkingDays) {
                e.preventDefault();
                alert('Working days must be at least ' + minWorkingDays + ' (total hours ÷ hours/day). You may take more days.');
                return;
            }

            if (requiredHours !== null) {
                var planned = Math.round((days * daily) * 100) / 100;
                if (planned + 0.001 < requiredHours) {
                    e.preventDefault();
                    alert('Planned hours (' + planned + ') must cover required total ' + requiredHours + ' hrs. Increase working days or daily hours.');
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
