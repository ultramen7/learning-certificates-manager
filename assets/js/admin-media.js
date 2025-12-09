jQuery(document).ready(function ($) {
    var lcmFrame;

    $(document).on('click', '#lcm_select_certificate_button', function (e) {
        e.preventDefault();

        if (lcmFrame) {
            lcmFrame.open();
            return;
        }

        lcmFrame = wp.media({
            title: 'Select or Upload Certificate',
            button: {
                text: 'Use this file',
            },
            multiple: false,
        });

        lcmFrame.on('select', function () {
            var attachment = lcmFrame.state().get('selection').first().toJSON();
            $('#lcm_certificate_file_url').val(attachment.url);
        });

        lcmFrame.open();
    });
});

