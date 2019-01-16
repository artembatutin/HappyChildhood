$(document).ready(function () {
    var navItems = $('.account-menu a');
    var navListItems = $('.account-menu a');
    var allWells = $('.account-content');
    var allWellsExceptFirst = $('.account-content:not(:first)');

    allWellsExceptFirst.hide();
    navItems.click(function (e) {
        var target = $(this).attr('data-target-id');
        if(target != null) {
            allWells.hide();
            e.preventDefault();
            navListItems.removeClass('active');
            $(this).closest('a').addClass('active');
            $('#' + target).show();
        }
    });
});