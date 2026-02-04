jQuery(function ($) {
    let frame;

    $('.wcbh-upload-banner').on('click', function (e) {
        e.preventDefault();

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Select Brand Banner',
            button: { text: 'Use this banner' },
            multiple: false
        });

        frame.on('select', function () {
            const attachment = frame.state().get('selection').first().toJSON();

            $('#brand_banner_id').val(attachment.id);
            $('.wcbh-banner-preview').html(
                `<img src="${attachment.url}" style="max-width:100%;height:auto;" />`
            );
        });

        frame.open();
    });
});
