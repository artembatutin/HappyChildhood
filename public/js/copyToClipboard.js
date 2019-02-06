clipboard_btns = $('.clipboard_btn');
clipboard_btns.tooltip({trigger: 'manual'});

clipboard_btns.on('mouseover', function (event) {
    //target.setAttribute('title', 'Copy Link');
    $(this).tooltip('hide').attr('data-original-title', 'Copy Link').tooltip('show');
});
clipboard_btns.on('mouseout', function (event) {
    //target.setAttribute('title', 'Copy Link');
    $(this).tooltip('hide').attr('data-original-title', 'Copy Link');
});
clipboard_btns.on('click', function (event) {
    //target.setAttribute('title', 'Link Copied!');
    $(this).attr('data-original-title', 'Link Copied!').tooltip('show');
});

new ClipboardJS(".clipboard_btn", {
    text: function (trigger) {
        return trigger.nextElementSibling.href;
    }
});