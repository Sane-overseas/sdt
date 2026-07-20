/**
 * Excel export helpers for Uploaded Data tables.
 * - Uses UI header row only (ignores filter-row) so columns match the screen
 * - Replaces "View" with actual file/video URLs (plain text, not hyperlinks)
 */
(function (window, $) {
    'use strict';

    function toAbsoluteUrl(href) {
        if (!href) {
            return href;
        }
        if (/^https?:\/\//i.test(href)) {
            return href;
        }
        if (href.indexOf('//') === 0) {
            return window.location.protocol + href;
        }
        if (href.charAt(0) === '/') {
            return window.location.origin + href;
        }
        return window.location.origin + '/' + href.replace(/^\.\//, '');
    }

    function cleanCellText($node) {
        var $clone = $node.clone();
        $clone.find(
            'script, style, .modal, form, input, button, .bi, .checkmark,' +
            ' .deleteVideo, .deleteImage, .deleteAllvideos, .deleteAllImages,' +
            ' .completionVideo, .distributionVideo, .send_btn, a[data-url]'
        ).remove();
        return $.trim($clone.text().replace(/\s+/g, ' ')) || '—';
    }

    function mediaLinksFromCell($node) {
        var parts = [];
        $node.find('a.vid-link, a.img-link, a.file-link').each(function () {
            var href = toAbsoluteUrl(($(this).attr('href') || '').trim());
            if (!href || href === '#' || href.indexOf('javascript:') === 0) {
                return;
            }
            var label = $(this)
                .closest('.video-row, .img-row, .file-row')
                .find('.vid-label, .img-label, .file-label')
                .first()
                .text()
                .trim();
            parts.push(label ? label + ': ' + href : href);
        });
        return parts;
    }

    function noteFromCell($node) {
        if (!$node.hasClass('actions-cell') && !$node.find('textarea').length) {
            return null;
        }
        var note = $.trim($node.find('textarea').first().val() || '');
        if (note) {
            return note;
        }
        if ($node.find('.not-started').length) {
            return 'Rejected';
        }
        return '—';
    }

    /**
     * @param {jQuery} $table
     * @param {string} [filename]
     * @returns {object} DataTables Buttons excel config
     */
    window.uploadedDataExcelButton = function ($table, filename) {
        return {
            extend: 'excelHtml5',
            text: 'Download',
            filename: filename || undefined,
            title: '',
            exportOptions: {
                format: {
                    header: function (data, columnIdx) {
                        return $.trim(
                            $table.find('thead tr').not('.filter-row').first().find('th').eq(columnIdx).text()
                        );
                    },
                    body: function (data, row, column, node) {
                        var $node = $(node);
                        var links = mediaLinksFromCell($node);
                        if (links.length) {
                            return links.join('\n');
                        }
                        var note = noteFromCell($node);
                        if (note !== null) {
                            return note;
                        }
                        return cleanCellText($node);
                    }
                }
            },
            customizeData: function (data) {
                if (data.headerStructure && data.headerStructure.length > 1) {
                    data.headerStructure = [data.headerStructure[0]];
                }
                if (Array.isArray(data.header) && data.header.length && Array.isArray(data.header[0])) {
                    data.header = data.header[0];
                }
            }
        };
    };
})(window, jQuery);
