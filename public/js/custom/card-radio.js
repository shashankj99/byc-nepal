function unclickRadio() {
    $("input:radio").prop("checked", false);
}

function clickRadio(inputElement) {
    $("#" + inputElement).prop("checked", true);
}

function removeActive() {
    $(".card").removeClass("active");
}

function makeActive(element) {
    $("#" + element).addClass("active");
}

$(document).ready(function () {
    $('input:radio').change(function () {
        let radioClicked = $(this).attr('id');
        unclickRadio();
        removeActive();
        clickRadio(radioClicked);
        makeActive(radioClicked);
    });
    $(".card").click(function () {
        let inputElement = $(this).find('input[type=radio]').attr('id');
        unclickRadio();
        removeActive();
        makeActive(inputElement);
        clickRadio(inputElement);
    });
});
