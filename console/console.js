var apiItems = {};
var apiUrl = 'http://' + window.location.hostname;
var pathExt = '/api';
var tokenHeader = 'X-API-TOKEN';
var sessionHeader = 'X-USER-SESSION-ID';

$(function () {

    var browserUrl = document.location.href;
    if (apiUrl != browserUrl && apiUrl.includes('localhost')) {
        apiUrl = browserUrl.replace('/console', '');
        if (apiUrl.substr(apiUrl.length - 1) == '/') {
            apiUrl = apiUrl.slice(0, -1);
        }
    }

    $("input[name=baseUrl]").val(apiUrl);
    $("input[name=apiPath]").val(pathExt);

    $.getJSON(apiUrl + "/console/api-list.json", function (data) {
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
//                alert(item.obj.title);
            });

            clearInterval(refreshId);
        }
    }, 1000);

    $('#submit').on('click', function () {
        $('#jsonOutput').html('');
        var apiPath = $("input[name=baseUrl]").val() + $("input[name=apiPath]").val();
        var queryParameter = $("input[name=queryParams]").val();
        var jsonBody = $("textarea[name=requestBody]").val();
        var checkJson = jsonBody != '' ? isJson(jsonBody) : true;
//        var userId = parseInt($("input[name=userId]").val());
        var sessionToken = $("input[name=sessionToken]").val();
        var apiMethod = $('input:radio[name=method]:checked').val();
        var tokenSignature = $('textarea[name=tokenHash]').val();

        if (checkJson) {
            var xhr = $.ajax({
                url: apiPath + (queryParameter.length > 0 ? '?' + queryParameter : ''),
                type: apiMethod,
                data: jsonBody,
                contentType: 'application/json; charset=utf-8',
                dataType: 'json',
                async: false,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader(tokenHeader, tokenSignature);
                    xhr.setRequestHeader(sessionHeader, sessionToken);
                },
                success: function (obj, textStatus, request) {
                    if (obj.hasOwnProperty('data')) {
                        var data = obj.data;
                        if (data.hasOwnProperty('session_id')) {
                            $("input[name=sessionToken]").val(data.session_id);
                        }
                        if (data.hasOwnProperty('user_info') && data.user_info.hasOwnProperty('id')) {
                            $("input[name=userId]").val(data.user_info.id);
                        }
                    }
//                    if (obj.hasOwnProperty('result_code') && obj.result_code == 0) {
                    if (textStatus == 'success') {
                        $("#jsonOutput").css("background-color", "rgb(177, 255, 188)");
                    } else {
                        $("#jsonOutput").css("background-color", "rgb(251, 205, 183)");
                    }
                    var str = JSON.stringify(obj, null, 2);
                    $('#jsonOutput').text(syntaxHighlight(str));
                    console.log(request.getAllResponseHeaders());
                    $('#responseHeader').html(request.getAllResponseHeaders().replace(/\n/g, '<br/>'));

                },
                error: function (xhr) {
                    $('#responseHeader').html('HTTP-STATUS: ' + xhr.status + '<br>' + xhr.getAllResponseHeaders().replace(/\n/g, '<br>'));
                }
            }).fail(function (jqXHR) {
                if (jqXHR.hasOwnProperty('responseJSON')) {
                    var obj = jqXHR.responseJSON;
                } else if (jqXHR.hasOwnProperty('responseText')) {
                    var obj = jqXHR.responseText;
                }
                
                var str = JSON.stringify(obj, null, 2);
                
                $("#jsonOutput").css("background-color", "rgb(251, 205, 183)");
                $('#jsonOutput').text(syntaxHighlight(str));
            });
//            $('#submit').attr('disabled', 'disabled');
        } else {
            alert('invalid JSON');
        }
    });

});

function callApi(apiName) {
    console.clear();
    if (apiItems.hasOwnProperty(apiName)) {
        $('input[name="method"]').prop('checked', false);
        var api = apiItems[apiName];
        var actionPath = api.obj.action;
        if (actionPath.indexOf('login') !== -1) {
            $('textarea[name=tokenHash], input[name=sessionToken], input[name=userId]').val('');
        }

        $("input[name=apiPath]").val(pathExt + actionPath);
        $("input[name=method][value=" + api.obj.method + "]").prop('checked', true);
        $("input[name=queryParams]").val(api.obj.query);
        $("textarea[name=requestBody]").val(api.obj.json);

        var user_id = parseInt($("input[name=userId]").val());
        var session_token = $("input[name=sessionToken]").val();
        var secret_key = $("input[name=tokenSecret]").val();

        var header = {
            "alg": "HS256",
            "typ": "JWT"
        };
        var localTime = new Date().getTime() + (new Date().getTimezoneOffset() * 60 * 1000) // Request time in miliseconds UTC
        var data = {
            "sessionToken": session_token,
            "reqAt": localTime
        };

        console.log(data);

        var stringifiedHeader = CryptoJS.enc.Utf8.parse(JSON.stringify(header));

        var encodedHeader = base64url(stringifiedHeader);

        var stringifiedData = CryptoJS.enc.Utf8.parse(JSON.stringify(data));
        var encodedData = base64url(stringifiedData);

        var signature = encodedHeader + "." + encodedData;
        signature = CryptoJS.HmacSHA256(signature, secret_key);
        signature = base64url(signature);

        var signature_token = encodedHeader + '.' + encodedData + '.' + signature;
        $('textarea[name=tokenHash]').val(signature_token);
        $('#submit').removeAttr('disabled');

//        console.log(signature_token);
    }
    $("#jsonOutput").css("background-color", "#DDD");
}

function base64url(source) {
    // Encode in classical base64
    var encodedSource = CryptoJS.enc.Base64.stringify(source);

    // Remove padding equal characters
    encodedSource = encodedSource.replace(/=+$/, '');

    // Replace characters according to base64url specifications
    encodedSource = encodedSource.replace(/\+/g, '-');
    encodedSource = encodedSource.replace(/\//g, '_');

    return encodedSource;
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
    if (typeof json != 'string') {
        json = JSON.stringify(json, undefined, 2);
    }
    return json;
}






















