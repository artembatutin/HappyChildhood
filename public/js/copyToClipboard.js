$(document).ready(function () {
    let clipboard_btns = $('[data-toggle="tooltip"]');
    clipboard_btns.tooltip({
        trigger: 'manual',
        container: 'body'
    });

    clipboard_btns.on('mouseover', function () {
        $(this).attr('data-original-title', 'Copy Link');
    });
    clipboard_btns.on('mouseout', function () {
        $(this).attr('data-original-title', 'Copy Link');
    });
    clipboard_btns.on('click', function () {
        $(this).attr('data-original-title', 'Link Copied!');
    });
});
/*
clipboard_btns = $('[data-toggle="tooltip"]');
clipboard_btns.tooltip({
    //trigger: 'manual',
    container: 'body'
});

clipboard_btns.on('mouseover', function (event) {
    //target.setAttribute('title', 'Copy Link');
    $(this).attr('data-original-title', 'Copy Link');
});
clipboard_btns.on('mouseout', function (event) {
    //target.setAttribute('title', 'Copy Link');
    $(this).attr('data-original-title', 'Copy Link');
});
clipboard_btns.on('click', function (event) {
    //target.setAttribute('title', 'Link Copied!');
    $(this).attr('data-original-title', 'Link Copied!');
});
*/
new ClipboardJS(".clipboard_btn", {
    text: function (trigger) {
        return trigger.nextElementSibling.href;
    }
});