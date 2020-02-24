var apiItems = {};
var apiUrl = 'http://' + window.location.hostname;
var pathExt = '/';
var tokenHeader = 'X-AUTH-TOKEN';
var jwtAuth = '';

//var sessionHeader = 'X-USER-SESSION-ID';

$(function () {

    $("input[name=baseUrl]").val(apiUrl + pathExt);

    $.getJSON(apiUrl + "/console/api-list.json?v=" + +Date.now(), function (data) {
//        console.clear();
        var i = 0;
        $.each(data, function (key, val) {
            if (key == 'groups') {
                $.each(val, function (k, v) {
                    var groupName = v.name;
                    var contents = v.contents;
                    var sidebarId = 'apiCollapse_' + i;
                    var cardHtml = '<div class="card">';
                    cardHtml += '<div class="card-header">';
                    cardHtml += '<a class="card-link" data-toggle="collapse" href="#' + sidebarId + '">';
                    cardHtml += groupName;
                    cardHtml += '</a></div>';
                    cardHtml += '<div id="' + sidebarId + '" class="collapse" data-parent="#apiExample">';
                    cardHtml += '<ul class="list-group list-group-' + i + ' list-group-flush">';

                    $.each(contents, function (k, v) {
                        apiItems[v] = {k: i, obj: data[v]};
//                        console.log(apiItems[v]);
                    });
                    cardHtml += '</ul></div></div>';
                    $('#apiExample').append(cardHtml);
                    i++;

                });
            }
        });


    });


    var refreshId = setInterval(function () {
        if (!$.isEmptyObject(apiItems)) {
            $.each(apiItems, function (key, item) {
                var listHtml = '<li class="list-group-item" onclick="callApi(\'' + key + '\')">' + item.obj.title + '</li>';
                $('.list-group-' + item.k).append(listHtml);
            });

            clearInterval(refreshId);
        }
    }, 1000);

    $('#submit').on('click', function () {
        $('#jsonOutput').html('');
        var apiPath = $("input[name=baseUrl]").val() + $("input[name=apiPath]").val();
        var queryParameter = $("input[name=queryParams]").val();

        var authToken = jwtAuth;
        var apiMethod = $('input:radio[name=method]:checked').val();

        let apiUrl = apiPath + (queryParameter.length > 0 ? '?' + queryParameter : '');
        $.ajax({
            type: apiMethod,
            url: apiUrl,
            data: $('#apiForm form').serialize(),
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },
            async: false,
            beforeSend: function (xhr) {
                if (authToken === '' || typeof authToken !== 'undefined') {
                    xhr.setRequestHeader(tokenHeader, authToken);
                }
            },
            success: function (obj, textStatus, request) {
                if (obj.hasOwnProperty('data')) {
                    var data = obj.data;
                    if (data.hasOwnProperty('user_id') && data.hasOwnProperty('user_level')) {
                        $("input[name=userId]").val(data.user_id);
                        $("input[name=userLevel]").val(data.user_level);
                    }
                }
                if (apiPath.includes('login')) {
                    let headers = getResponseHeaders(request);
                    if (typeof headers[tokenHeader] !== 'undefined') {
                        // color is undefined
                        $("textarea[name=authorization]").val(headers[tokenHeader]);
                        jwtAuth = headers[tokenHeader];
                    }

                }

                if (textStatus == 'success') {
                    $("#jsonOutput").css("background-color", "rgb(177, 255, 188)");
                } else {
                    $("#jsonOutput").css("background-color", "rgb(251, 205, 183)");
                }
                var str = JSON.stringify(obj, null, 2);
                $('#jsonOutput').text(syntaxHighlight(str));
                $('#responseHeader').html(request.getAllResponseHeaders().replace(/\n/g, '<br/>'));

            }
        }).fail(function (obj) {
            if (obj.hasOwnProperty('responseJSON')) {
                obj = obj.responseJSON;
            } else if (obj.hasOwnProperty('responseText')) {
                obj = obj.responseText;
            }
            
            var str = JSON.stringify(obj, null, 2);
            $("#jsonOutput").css("background-color", "rgb(251, 205, 183)");
            $('#jsonOutput').text(syntaxHighlight(str));
        });

    });


    $("input[name='server_type']").change(function () {
        if ($(this).is(':checked')) {
            let server = $(this).val();
            $("input[name=baseUrl]").val(server);
        }
    });

});

function callApi(apiName) {
    console.clear();
    if (apiItems.hasOwnProperty(apiName)) {
        $("#apiForm form").html('');

        $('input[name="method"]').prop('checked', false);
        var api = apiItems[apiName];
        var actionPath = api.obj.action;
        if (actionPath.indexOf('login') !== -1) {
            $('textarea[name=authorization], input[name=userLevel], input[name=userId]').val('');
        }

        $("input[name=apiPath]").val(actionPath);
        $("input[name=method][value=" + api.obj.method + "]").prop('checked', true);
        $("input[name=queryParams]").val(api.obj.query);

        var formInputs = null;
        if (api.obj.params !== '') {
            formInputs = api.obj.params.split(",");
            if ($.isArray(formInputs)) {
                let inputHtml = '<div class="form-group row">';
                for (var i = 0; i < formInputs.length; i++) {
                    if (i % 2 == 0 && i != 0) {
                        inputHtml += '</div><div class="form-group row">'
                    }
                    inputHtml += '<label class="col-sm-2 col-form-label">' + ucFirst(formInputs[i]) + '</label>';
                    inputHtml += '<div class="col-sm-4">';
                    inputHtml += '<input type="text" class="form-control" name="' + formInputs[i] + '">';
                    inputHtml += '</div>';
                }
                $(inputHtml).appendTo("#apiForm form");
            }

        }


        /*
         var user_id = parseInt($("input[name=userId]").val());
         var header = $("input[name=authorization]").val();
         var localTime = new Date().getTime() + (new Date().getTimezoneOffset() * 60 * 1000) // Request time in miliseconds UTC
         */
        $('#submit').removeAttr('disabled');
    }
    $("#jsonOutput").css("background-color", "#DDD");
}

function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function syntaxHighlight(json) {
    if (typeof json !== 'string') {
        json = JSON.stringify(json, undefined, 2);
    }
    return json;
}

function ucFirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function getResponseHeaders(jqXHR) {
    let responseHeaders = {};
    let headers = jqXHR.getAllResponseHeaders();
    let value = '';
    headers = headers.split("\n");
    headers.forEach(function (header) {
        header = header.split(": ");
        value = header[1];
        let key = header.shift();
        if (key.length === 0)
            return;
        // chrome60+ force lowercase, other browsers can be different
        key = key.toLowerCase();
        responseHeaders[key] = value.replace(/(\r\n|\n|\r)/gm, "");
        ;
    });

    return responseHeaders;
}

















