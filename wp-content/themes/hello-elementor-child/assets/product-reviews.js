jQuery(function ($) {
    'use strict';

    // Photo input live preview
    $('#bv_review_image').on('change', function () {
        var input = this;
        var $preview = $('#bv-photo-preview');

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $preview.html('<img src="' + e.target.result + '" alt="Xem trước ảnh">').prop('hidden', false);
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            $preview.empty().prop('hidden', true);
        }
    });

    // Lightbox modal for review photos
    $(document).on('click', '.bv-photo-link', function (e) {
        e.preventDefault();
        var fullSrc = $(this).attr('href');
        if (!fullSrc) return;

        var $overlay = $('<div class="bv-photo-lightbox"><div class="bv-lightbox-content"><img src="' + fullSrc + '" alt="Ảnh feedback gốm"><button type="button" class="bv-lightbox-close">&times;</button></div></div>');
        
        $overlay.css({
            position: 'fixed',
            top: 0,
            left: 0,
            width: '100%',
            height: '100%',
            backgroundColor: 'rgba(0,0,0,0.85)',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            zIndex: 99999,
            cursor: 'zoom-out'
        });

        $overlay.find('.bv-lightbox-content').css({
            position: 'relative',
            maxWidth: '90%',
            maxHeight: '90%'
        });

        $overlay.find('img').css({
            maxWidth: '100%',
            maxHeight: '85vh',
            borderRadius: '8px',
            boxShadow: '0 8px 30px rgba(0,0,0,0.5)'
        });

        $overlay.find('.bv-lightbox-close').css({
            position: 'absolute',
            top: '-36px',
            right: 0,
            background: 'none',
            border: 'none',
            color: '#fff',
            fontSize: '32px',
            cursor: 'pointer'
        });

        $('body').append($overlay);

        $overlay.on('click', function () {
            $overlay.remove();
        });
    });
});
