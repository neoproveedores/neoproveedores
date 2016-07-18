(function() {
    // TODO: Separar scripts en varios ficheros según contexto
    var body = $('body');

    // Dropdowns
    var initDropdowns = function(element) {
        element.find('select[placeholder]').each(function() {
            var placeholder = $(this).attr('placeholder');
            $(this).prepend('<option value="">' + placeholder + '</option>');
        });
        element.find('.ui.dropdown:not(.remote):not(.add)').dropdown();

        element.find('.ui.remote.dropdown').each(function() {
            $(this).dropdown({
                apiSettings: {
                    url: $(this).data('api') + '?query={query}'
                }
            });
        });

        element.find('.ui.add.dropdown').each(function() {
            $(this).dropdown({
                allowAdditions: true
            });
        });
    };
    initDropdowns(body);

    $('input[data-search]').on('keyup', function(event) {
        var url = $(this).data('search');
        var query = $(this).val();
        var template = '<a href="{url}" class="item">{name}</a>';
        var list = $(this).siblings('.ui.remote.list');
        var keyCode = event.which || event.keyCode;

        if (keyCode !== 13 && query.length >= 2) {
            $.getJSON(url + '?query=' + query, function(items) {
                list.empty();
                for (var i = 0; i < items.length; i++) {
                    var item = items[i];
                    var element = template
                        .replace('{url}', item.url)
                        .replace('{name}', item.name)
                    ;
                    list.append(element);
                }
                if (! list.is(':visible')) {
                    list.transition('slide down');
                }
            });
        }

        if (keyCode === 13) {
            $(this).trigger('blur');
        }
    });
    $('input[data-search]').on('blur', function() {
        var list = $(this).siblings('.ui.remote.list');

        setTimeout(function() {
            if (list.hasClass('visible')) {
                list.transition('slide down', function() {
                    list.empty();
                });
            }
        }, 100);
    });

    var initRatings = function(element) {
        element.find('.ui.rating:not(.evolution)').rating({maxRating: 5});
        element.find('.ui.rating:not(.evolution)').rating('disable');
        element.find('.ui.interactive.rating').rating('enable').rating({
            onRate: function(v) {
                $(this).next('input').val(v);
            }
        });
    };
    initRatings(body);

    $('body').on('click', 'tr[href]', function() {
        var href = $(this).attr('href');
        var table = $(this).parents('.ui.table');
        var modal = $('[data-modal=provider]');

        if (table.hasClass('providers') && modal.length) {
            $.get(href, function(response) {
                modal.find('.content').html(response);
                modal.modal('show');
                initRatings(modal);

                modal.on('click', '.close.button', function(event) {
                    event.preventDefault();
                    modal.modal('hide');
                });
            });
        } else {
            document.location = href;
        }
    });

    // Modals
    $('.ui.modal').modal({dimmerSettings: {variation: 'inverted'}});
    $('[data-initial=modal]').each(function() {
        $('[data-modal="' + $(this).data('target') + '"]').modal('show');
    });
    $('[data-trigger=modal]').on('click', function() {
        $('[data-modal="' + $(this).data('target') + '"]').modal('show');
    });

    // Textarea editor
    var initEditors = function(element) {
        element.find('.editor').each(function() {
            var input = $(this).children('input');
            var tools = $(this).find('.tools');
            var text = $(this).find('.text');
            var placeholder = text.attr('placeholder');
            var editor = new Quill(text[0], {
                theme: 'snow',
                modules: {
                    placeholder: {text: placeholder, style: {color: '#bababa'}}
                }
            });

            editor.addModule('toolbar', {container: tools[0]})
            editor.on('text-change', function() {
                input.val(editor.getHTML());
            });
        });
    };
    initEditors($('body'));

    $('form button:not([type=submit])').on('click', function(event) {
        event.preventDefault();
    });

    // Date picker
    $('.ui.date.input input').each(function() {
        var input = $(this).pickadate({
            format: 'd mmmm yyyy',
            formatSubmit: 'yyyy-mm-dd'
        });

        input.siblings('.ui.label').on('click', function(event) {
            event.stopPropagation();
            input.pickadate('picker').open();
        });
    });

    // Input
    $('.ui.input > input').on('focus', function() {
        $(this).parent().addClass('focus');
    });
    $('.ui.input > input').on('blur', function() {
        $(this).parent().removeClass('focus');
    });

    // Add prototypes
    $('[data-trigger="prototype"]').on('click', function() {
        var target = $(this).data('target');
        var parent = $(this).parents('.ui.grid');
        var prototype = $('[data-prototype='+target+']').html();
        var index = $('[data-index]').length - 1;

        prototype = prototype.replace(/__name__/g, index);
        var element = $(prototype).insertBefore(parent);

        element.find('.ui.interactive.rating').rating('enable');
        initDropdowns(element);
        initUploads(element);
    });

    // Remove Elements
    $('body').on('click', '[data-trigger=remove]', function() {
        $(this).parents('[data-remove]').remove();
    });

    // Counters
    var initCounters = function(element) {
        element.find('[data-behavior=count]').each(function() {
            var counter = $(this);
            var target = $('#' + $(this).data('target'));
            var maxlength = target.attr('maxlength');

            target.on('keyup paste change', function() {
                counter.text(target.val().length + ' / ' + maxlength);
            });
        });
    };
    initCounters($('body'));

    // File uploads
    var initUploads = function(element) {
        element.find('.ui.drop a').on('click', function() {
            $(this).parent().find('input:last').trigger('click');
        });
        element.find('.ui.drop.file').on('change', 'input', function() {
            $(this).parent().removeClass('error');
            $(this).siblings('.selected').removeClass('hide');
        });
        element.find('.ui.drop.files').on('change', 'input', function() {
            var name = $(this).attr('name');
            var index = parseInt(name.match(/\[(\d+)\]/)[1], 10) + 1;
            var parent = $(this).parent();
            var selected = parent.find('.selected');
            var count = parent.find('input').length;

            name = name.replace(/\[\d+\]/, '[' + index + ']');
            $(this).after('<input type="file" name="' + name + '">');

            selected.find('.count').text(count);
            selected.find('.singular, .plural').addClass('hide');
            if (count > 1) {
                selected.find('.plural').removeClass('hide');
            } else {
                selected.find('.singular').removeClass('hide');
            }
            selected.removeClass('hide');
        });
        element.find('.ui.upload.button').on('click', function() {
            $(this).next('input').trigger('click');
        });
        element.find('.ui.upload.picture + input').on('change', function() {
            var button = $(this).siblings('.ui.upload.picture');

            if (this.files && this.files[0]) {
                var reader = new FileReader();

                reader.onload = function(event) {
                    var background = 'url(' + event.target.result + ')';
                    button.css('background-image', background);
                    button.addClass('uploaded');
                }

                reader.readAsDataURL(this.files[0]);
            }
        });
    };
    initUploads(body);

    // Toggle kinds
    $('[data-trigger=toggle]').on('change', function() {
        var kind = $(this).val();

        $('[data-kind]').addClass('hide');
        $('[data-kind=' + kind + ']').removeClass('hide');
    });
    $('.change_role').click(function(){
        $('.roles').removeClass('hide');
    })

    // Form
    $('form').on('submit', function() {
        var valid = true;

        $(this).find('[data-prototype]').remove();

        $(this).find('.ui.drop.required.file').each(function() {
            var value = $(this).find('input').val();
            var file = $(this).next('.ui.file:not(.drop)').length;

            if (! value && ! file) {
                valid = false;

                $(this).addClass('error').popup({
                    content: 'Falta éste fichero por subir',
                    variation: 'error',
                });
            }
        });

        return valid;
    });

    // Chart
    var initCharts = function(element) {
        element.find('.chart').each(function() {
            var tooltip = $(this).find('.evolution.value');
            var values = $(this).attr('data').split(',');
            var data = [];

            $(this).find('svg').remove();

            $.each(values, function(index, value) {
                data.push({
                    short: index,
                    value: value
                });
            });

            var margin = {top: 20, right: 0, bottom: 20, left: 0};
            var width = $(this).width() - margin.left - margin.right;
            var height = $(this).height() - margin.top - margin.bottom;
            var x = d3.scale.ordinal().rangeRoundBands([0, width]);
            var y = d3.scale.linear().range([height, 0]);
            var xAxis = d3.svg.axis()
                .scale(x)
                .orient('bottom')
                .innerTickSize(-height)
                .outerTickSize(0)
                .tickPadding(0)
            ;
            var yAxis = d3.svg.axis()
                .scale(y)
                .orient('left')
                .outerTickSize(0)
                .tickPadding(0)
                .tickValues([0, 1, 2, 3, 4, 5])
                .tickFormat(function(d) { return d; })
            ;
            var svg = d3.select($(this)[0])
                .append('svg')
                .attr('width', width + margin.left + margin.right)
                .attr('height', height + margin.top + margin.bottom)
                .append('g')
            ;
            var line = d3.svg.line()
                .x(function(d) { return x(d.short); })
                .y(function(d) { return y(d.value); })
                .interpolate('monotone')
            ;
            var tooltipPosition = function(el) {
                var parent = $(el).parent().offset();
                var offset = $(el).offset();
                var left = offset.left - parent.left;
                var top = offset.top - parent.top;

                return {top: top, left: left};
            };
            var tooltipInfo = function(index) {
                var info = ratingInfo[index];

                tooltip.popup({
                    title: info.project,
                    content: info.notes,
                    position: 'top center'
                });
            };

            x.domain(data.map(function(d) { return d.short; }));
            y.domain([0, 5]);

            var rb = x.rangeBand() / 2;
            svg.attr(
                'transform',
                'translate('+margin.left+rb+','+margin.top+')'
            );
            svg
                .append('g')
                .attr('class', 'x axis')
                .attr('transform', 'translate(-'+rb+', '+height+')')
                .call(xAxis)
            ;
            svg
                .append('g')
                .attr('class', 'y axis')
                .call(yAxis)
            ;
            svg
                .append('path')
                .attr('d', line(data))
                .attr('class', 'line')
            ;
            svg
                .selectAll('circle')
                .data(data).enter()
                .append('circle')
                .attr('class', 'point')
                .attr('cx', function (d) { return x(d.short); })
            	.attr('cy', function (d) { return y(d.value); })
            	.attr('r', 4)
                .on('mouseover', function(d) {
                    tooltip.text(d.value).css(tooltipPosition(this));
                    tooltipInfo(d.short);
                })
            ;

            tooltip
                .text(values[values.length - 1])
                .css(tooltipPosition($(this).find('circle:last')[0]))
                .show()
            ;
            tooltipInfo(values.length - 1);
        });
    };
    initCharts(body);

    // Filters
    $('form .ui.filters select').on('change', function() {
        $(this).parents('form').submit();
    });

    // Range
    var initRanges = function(element) {
        element.find('.ui.range').each(function() {
            var value = $(this).siblings('.value');
            var inputMin = $(this).siblings('input[data-range=min]');
            var inputMax = $(this).siblings('input[data-range=max]');

            if (inputMin.val() && inputMax.val()) {
                value.removeClass('hide');
            }

            $(this).slider({
                range: true,
                min: $(this).data('min'),
                max: $(this).data('max'),
                values: [inputMin.val(), inputMax.val()],
                slide: function(event, ui) {
                    var min = ui.values[0];
                    var max = ui.values[1];

                    inputMin.val(min);
                    inputMax.val(max);
                    value.find('[data-range=min]').text(min);
                    value.find('[data-range=max]').text(max);
                    value.removeClass('hide');
                }
            });
        });
    };
    initRanges(body);

    // Buscador avanzado
    $('.ui.search .open').on('click', function(event) {
        var search = $(this).parents('.ui.search');
        search.toggleClass('open');
        search.siblings('.ui.advanced.search').transition('slide down');
    });
    $('.ui.advanced.search .top.abilities [data-id]').on('click', function() {
        $(this)
            .parents('.ui.advanced.search')
            .find('[name="abilities[]"]')
            .dropdown('set selected', $(this).data('id'))
        ;
    });
    $('.ui.advanced.search .close').on('click', function(event) {
        var form = $(this).parents('form');

        form.find('input').val('');
        form.find('.ui.dropdown').dropdown('clear');
        form.find('.ui.checkbox').checkbox('uncheck');
        form.find('.ui.range').slider('values', ['', '']);
        form.find('.slider.value').addClass('hide');
    });

    $('.ui.checkbox').checkbox();

    // Async form
    var initAsyncForms = function(element) {
        element.find('form[data-async]').on('submit', function(event) {
            var form = $(this);

            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: $(this).serialize(),
                success: function(data) {
                    var success = form.find('[data-success]');

                    if (success.length) {
                        if (success.data('success') === 'modal') {
                            var target = success.data('target');
                            $('[data-modal="' + target + '"]').modal('show');
                        }
                    } else {
                        var container = $('.async.container');

                        container.html(data);
                        $('.ui.rating:not(.evolution)').rating({maxRating: 5});
                        $('.ui.rating:not(.evolution)').rating('disable');

                        initEditors(container);
                        initCounters(container);
                        initAsyncForms(container);
                    }

                    $('[data-target="invitations"]').addClass('hide');
                    $('.ui.table th .sort').removeClass('asc desc');
                }
            });
            event.preventDefault();
        });
    };
    initAsyncForms($('body'));

    $('.ui.table th .sort').on('click', function() {
        var href = $(this).attr('href');
        var direction = 'desc';
        if ($(this).hasClass('asc')) {
            direction = 'asc';
        }
        if ($(this).hasClass('desc')) {
            direction = 'desc';
        }
        direction = (direction == 'asc') ? 'desc' : 'asc';
        href = href.replace(/direction=(asc|desc)/, 'direction=' + direction);

        $(this).parents('tr').find('.sort').removeClass('asc desc');
        $(this).addClass(direction);
        $(this).attr('href', href);
    });

    $('[data-target="invitations"]').on('click', function() {
        var contacts = $('[data-modal="invitations"] .contacts');

        contacts.empty();

        $('.ui.providers.table .choice input:checked').each(function() {
            var picture = $(this).parents('tr').find('.picture').html();

            contacts.append(picture);
        });
    });

    $('body').on('click', 'a[data-async]', function(event) {
        var that = $(this);

        $.get($(this).attr('href'), function(data) {
            if (that.data('async') === 'append') {
                that.parents('[data-remove]').remove();
                that.remove();
                $('.async.container').append(data);
            } else {
                $('.async.container').html(data);
            }

            $('.ui.rating:not(.evolution)').rating({maxRating: 5});
            $('.ui.rating:not(.evolution)').rating('disable');
        });
        event.preventDefault();
    });

    var openConfirmModal = function(title, message, action, url) {
        var template = '<div class="ui small async modal"><i class="close icon"></i><div class="content"><div class="ui header">{title}<div class="sub header">{message}</div></div></div><div class="actions"><a href="{url}" class="ui grey button">{action}</a><div class="ui close button">Cancelar</div></div></div>';
        var modal = $(template
            .replace('{title}', title)
            .replace('{message}', message)
            .replace('{action}', action)
            .replace('{url}', url)
        );

        modal.appendTo($('body'));
        modal.modal({
            onHidden: function() {
                modal.remove();
            }
        });
        modal.modal('show');
    };
    $('[data-confirm]').on('click', function() {
        var title = $(this).data('title');
        var message = $(this).data('message');
        var action = $(this).text();
        var url = $(this).data('confirm');

        openConfirmModal(title, message, action, url);
    });

    // Choices table
    $('body').on('click', '.ui.choices.table .ui.checkbox', function(event) {
        var table = $(this).parents('.ui.choices.table');

        if (table.hasClass('multiple')) {
            var submit = $(this).parents('form').find('button[type=submit]');
            var checked = table.find('input:checked').length;

            submit.find('a').text(checked);
            if (checked > 0) {
                submit.removeClass('hide');
            } else {
                submit.addClass('hide');
            }
        } else {
            var id = $(this).find('input').val();
            var button = $('[data-trigger=assign]');
            var href = button.data('href').replace('id', id);
            var tr = $(this).parents('tr');

            tr
                .addClass('checked')
                .siblings('tr')
                .removeClass('checked')
                .find('.ui.checkbox')
                .checkbox('uncheck')
            ;
            button.attr('href', href);
            button.removeClass('hide');
        }

        event.stopPropagation();
    });

    var load = function(element) {
        element.load(element.data('load'), function() {
            initEditors(element);
            initCounters(element);
            initAsyncForms(element);
            element.find('.ui.dropdown').dropdown();
        });
    };
    $('[data-load]').each(function(index) {
        var element = $(this);
        load(element);
        if (element.attr('data-reload')) {
            var freq = parseInt(element.attr('data-reload'), 10) * 1000;
            setInterval(function(){ load(element); }, freq);
        }
    });

    // Skills
    $('.ui.skill .author .files').on('click', function(event) {
        $(this).parents('.ui.skill').find('.ui.files').toggleClass('hide');
        event.preventDefault();
    });

    $('.error[data-content]').popup({
        lastResort: true,
        variation: 'error',
        inline: true
    });

    // Sidebar
    $('[data-trigger=sidebar]').on('click', function() {
        $('.ui.sidebar').sidebar('toggle');
    });

    $(window).on('resize', function() {
        initCharts(body);
    });
})();
