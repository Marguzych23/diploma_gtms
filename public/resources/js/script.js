$(document).ready(function () {
    function loadSearchResults() {
        var query = $("#search_input").val();
        var custom_checkbox = $(".custom-checkbox");
        var industry = [];
        var date = $("#date").val();

        for (var i = 0; i < custom_checkbox.length; i++) {
            if ($(custom_checkbox[i]).is(":checked")) {
                industry[i] = $(custom_checkbox[i]).val();
            }
        }
        $.redirect('/competitions', {query, industry, date});
        // $.ajax({
        //     type: 'post',
        //     url: '/competitions',  // <- адрес куда отправлять запрос
        //     data: {query, industry, date}
        // }).done(function (response) {
        //     if (response.code === 'OK') {
        //         // if success
        //     } else {
        //         // if error
        //     }
        // });
    }

    $("#search_input").on("change paste keyup", $.debounce(500, loadSearchResults));
});