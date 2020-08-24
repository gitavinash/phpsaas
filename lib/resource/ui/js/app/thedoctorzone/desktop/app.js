$(document).ready(function(e) {

    $('#stateID').change(function(e) {
        e.preventDefault();

        var stateValue = $('#stateID').val().split('_');
        var stateID = stateValue[0];

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url : '/api/city/',
            data: {
                'state_id' : stateID
            },
            success: function(data) {
                if(data.status==200) {
                    
                    $('#cityBox').show();
                    
                    $.each(data.cityData, function(index, value) {
                        $('#cityID').append("<option value='"+data.cityData[index]['id']+"_"+data.cityData[index]['name']+"'>"+data.cityData[index]['name']+"</option>")
                    });
                    
                } else {
                    popUpMessage('center', data.msg, data.status);
                }
            },
            error: function(data) {
                console.log(data);
                $('#loginBtn').show();
            }
        })

    });

    $('#cityID').change(function(e) {

        var cityvalue = $('#cityID').val().split('_');
        var cityID = cityvalue[0];
        if(cityID!='') {
            $('#pinCodeBox').show();
        } else {
            $('#pinCodeBox').hide();
        }
    });

    /** Add New Clinic */

    $('#addClinic').click(function(e) {

        e.preventDefault();
        $('#addClinic').hide();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: $('#addNewBuilding').serialize(),
            success: function(data) {
                console.log('success');
                $('#addClinic').show();
            },
            error: function(data) {
                console.log(data);
                $('#addClinic').show();
            }
        })

    });

});