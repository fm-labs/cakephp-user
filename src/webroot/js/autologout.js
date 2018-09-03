(function() {
    console.log("Loading user script: autologout.js");

    setInterval(function() {

        $.getJSON('/user/check-auth', function(data) {
            console.log("CheckAuth", data)
        })
            .fail(function(xhr) {
                console.log("CheckAuth failed", xhr);
            })

    }, 10000);

})();