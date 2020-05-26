$(document).ready(function () {
    function saveSettings() {
        var custom_checkbox = $(".custom-checkbox");
        var industries = [];
        var email_subscribe = $("#notifications").is(":checked");

        for (var i = 0; i < custom_checkbox.length; i++) {
            if ($(custom_checkbox[i]).is(":checked")) {
                if ($(custom_checkbox[i]).val() != '') {
                    industries[i] = $(custom_checkbox[i]).val();
                }
            }
        }
        $.ajax({
            type: 'post',
            url: '/profile/data/set',  // <- адрес куда отправлять запрос
            data: {industries, email_subscribe}
        }).done(function (response) {
            if (response.code === 'OK') {
                // if success
            } else {
                // if error
            }
        });
    }

    $("#save-profile").on("click", $.debounce(500, saveSettings));
});