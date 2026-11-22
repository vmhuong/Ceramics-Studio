jQuery(function ($) {
    'use strict';

    $(document).on('click', '.bv-btn-copy', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var textToCopy = '';

        if ($btn.data('copy')) {
            textToCopy = String($btn.data('copy'));
        } else if ($btn.data('target')) {
            var $target = $($btn.data('target'));
            if ($target.length) {
                textToCopy = $target.text().trim();
            }
        }

        if (!textToCopy) return;

        function markCopied() {
            var origText = $btn.html();
            $btn.addClass('is-copied').html('<i class="eicon-check" aria-hidden="true"></i> Đã chép');
            setTimeout(function () {
                $btn.removeClass('is-copied').html(origText);
            }, 2500);
        }

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(textToCopy).then(markCopied).catch(function () {
                fallbackCopy(textToCopy);
            });
        } else {
            fallbackCopy(textToCopy);
        }

        function fallbackCopy(text) {
            var textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-9999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                markCopied();
            } catch (err) {
                console.error('Không thể sao chép tự động', err);
            }
            document.body.removeChild(textArea);
        }
    });
});
