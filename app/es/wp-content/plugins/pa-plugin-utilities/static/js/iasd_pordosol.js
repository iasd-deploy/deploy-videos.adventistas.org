(function($){
    var autocompleteList = [];

    var getDateForWidget = function (widget) {
        console.log('GET: ' + widget.data('year')+"-"+widget.data('month')+"-"+widget.data('day'));

        var pds_dt = moment();
        pds_dt.year(widget.data('year'));
        pds_dt.month(widget.data('month'));
        pds_dt.date(widget.data('day'));
        console.log(pds_dt.format("YYYY-MM-DD"));

        return pds_dt;
    }

    var setDateForWidget = function (widget, moment) {
        widget.data('day', moment.date());
        widget.data('month', moment.month());
        widget.data('year', moment.year());
        console.log(moment.format("YYYY-MM-DD"));
    }

    var updateWidgetAjax = function(rawText, textStatus, jqXHR) {
        if(rawText.substring(rawText.length - 1) == '0')
            rawText = rawText.substring(0, rawText.length - 1);

        var data = JSON.parse(rawText);
        var widgetId = data.widget_id;
        var widget = $('#'+widgetId);
        widget.find('.config.open').removeClass('open');
        // widget.find('.friday .hour').html(data.friday.evening.twilight.civil.substring(0, 5));
        // widget.find('.sabbath .hour').html(data.sabbath.evening.twilight.civil.substring(0, 5));
        widget.find('.friday .hour').html(data.friday.evening.sunset.substring(0, 5));
        widget.find('.sabbath .hour').html(data.sabbath.evening.sunset.substring(0, 5));
        
        var pds_dt = getDateForWidget(widget);
        widget.find('.friday .month').html(pds_dt.format("DD/MM"));
        pds_dt.add('day', 1);
        widget.find('.sabbath .month').html(pds_dt.format("DD/MM"));
    }

    var updateWidget = function(widget) {
        var params = {};
        params.action = 'pordosol';
        params.widget_id = widget.attr('id');

        params.pds_la = widget.data('pds_la');
        params.pds_lo = widget.data('pds_lo');
        params.pds_nm = widget.data('pds_nm');

        params.pds_y = widget.data('year');
        params.pds_m = parseInt(widget.data('month')) + 1;
        params.pds_d = widget.data('day');

        widget.find('p.location span').html(params.pds_nm);

        widget.find('.friday .hour').html('<i style="font-size:14px;color:#918a7e;">carregando..</i>');
        widget.find('.sabbath .hour').html('<i style="font-size:14px;color:#918a7e;">carregando..</i>');

        $.get(ajaxurl, params, updateWidgetAjax, 'text');
    }

    var onClick_Next = function() {
        var link = $(this);
        var widget = link.closest('.iasd-widget-sunset');
        var pds_dt = getDateForWidget(widget);

        console.log(pds_dt.format("YYYY-MM-DD"));
        pds_dt.add('d', 1);
        while(pds_dt.weekday() != 5)
            pds_dt.add('d', 1);
        console.log(pds_dt.format("YYYY-MM-DD"));

        setDateForWidget(widget, pds_dt);

        updateWidget(widget);

        return false;
    }

    var onClick_Prev = function() {
        var link = $(this);
        var widget = link.closest('.iasd-widget-sunset');
        var pds_dt = getDateForWidget(widget);

        console.log(pds_dt.format("YYYY-MM-DD"));
        pds_dt.subtract('day', 1);
        while(pds_dt.weekday() != 5) {
            pds_dt.subtract('day', 1);
        }
        console.log(pds_dt.format("YYYY-MM-DD"));

        setDateForWidget(widget, pds_dt);

        updateWidget(widget);

        return false;
    }

    var onClick_Change = function() {
        var btn = $(this);
        var widget = btn.closest('.iasd-widget-sunset');

        widget.data('pds_nm', widget.data('new_pds_nm'));
        widget.data('pds_la', widget.data('new_pds_la'));
        widget.data('pds_lo', widget.data('new_pds_lo'));

        updateWidget(widget);

        return false;
    }

    var onPlaceChanged = function() {
        var autocomplete = this;
        var place = autocomplete.getPlace();

        var pds_nm = "";
        var pds_la = place.geometry.location.lat();
        var pds_lo = place.geometry.location.lng();
        for (var i = 0; i < place.address_components.length; i++) {
            var component = place.address_components[i];
            if(component.types.indexOf("locality") == -1) {
                if(pds_nm == "") {
                    pds_nm = component.long_name;
                } else {
                    pds_nm += ', ' + component.short_name;
                }
            }
        };

        var widget = $(autocompleteList[autocomplete]);
        widget.data('new_pds_nm', pds_nm);
        widget.data('new_pds_la', pds_la);
        widget.data('new_pds_lo', pds_lo);

        return false;
    };

    var onDocument_Ready = function() {
        var inputs = $('.form-control.iasd-pds-find');
        var options = {  types: ['(cities)'] };

        for (var i = 0; i < inputs.length; i++) {
            var input = inputs[i];
            var widget = $(input).closest('.iasd-widget-sunset');
            var pds_autocomplete = new google.maps.places.Autocomplete(input, options);
            google.maps.event.addListener(pds_autocomplete, 'place_changed', onPlaceChanged);
            autocompleteList[pds_autocomplete] = '#'+widget.attr('id');

            widget.find('.nav-next-link').click(onClick_Next);
            widget.find('.nav-prev-link').click(onClick_Prev);
            widget.find('.btn.btn-default').click(onClick_Change);

            updateWidget(widget);
        }

    };

    $('document').ready(onDocument_Ready);



})(jQuery);
