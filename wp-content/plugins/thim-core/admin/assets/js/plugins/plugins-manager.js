/**
 * Called in tab plugins
 * @since 0.4.0
 */
(function ($, Thim_Plugins) {
    $(document).ready(function () {
        thim_plugins.init();
    });

    function reload() {
        window.location.reload(true);
    }

    var thim_plugins = {

        /**
         * Init functions
         *
         * @since 0.4.0
         */
        init: function () {
            this.filter_search();
            this.onEvent();
            this.count();
            this.initRouting();
        },

        initRouting: function () {
            var hash = window.location.hash;
            var $tab = $('.filter-links a[href="' + hash + '"]');
            if ($tab) {
                $tab.click();
            }
        },

        /**
         * Count each type plugin.
         *
         * @since 1.0.0
         */
        count: function () {
            var all = $('.list-plugins .plugin-card').length;
            var required = $('.list-plugins .plugin-card.required').length;
            var recommended = $('.list-plugins .plugin-card.recommended').length;
            var updates = $('.list-plugins .plugin-card.can-update').length;

            add_count('all', all);
            add_count('required', required);
            add_count('recommended', recommended);
            add_count('updates', updates);

            function add_count(type, number) {
                var $a = $('.filter-links .' + type + ' a');
                if (!$a) {
                    return;
                }
                var text = $a.text();
                $a.text(text.replace('(__)', '(' + number + ')'));
            }

            if (!updates) {
                $('.filter-links .updates').hide();
            }
        },

        /**
         * Filter & search plugins.
         *
         * @since 0.4.0
         */
        filter_search: function () {
            var $wrapper = $('.thim-wrapper .plugin-tab');
            var qsRegex, buttonFilter;
            var $filter_links = $wrapper.find('.filter-links');
            var $plugin_filter = $wrapper.find('#plugin-filter');

            // init isotope
            var $grid = $plugin_filter.find('.list-plugins').isotope({
                filter: function () {
                    var $this = $(this);
                    var searchResult = qsRegex ? $this.find('.name').text().match(qsRegex) : true;
                    var buttonResult = buttonFilter ? $this.is(buttonFilter) : true;
                    return searchResult && buttonResult;
                }
            });

            // click on filter tab
            $filter_links.on('click', 'li', function () {
                $filter_links.find('li a').removeClass('current');
                $(this).find('a').addClass('current');
                buttonFilter = $(this).data('filter');
                $grid.isotope();
            });

            // use value of search field to filter
            var $quicksearch = $('.wp-filter-search').keyup(this.debounce(function () {
                qsRegex = new RegExp($quicksearch.val(), 'gi');
                $grid.isotope();
            }));


        },

        /**
         * debounce so filtering doesn't happen every millisecond
         *
         * @since 0.4.0
         *
         * @param fn
         * @param threshold
         * @returns {debounced}
         */
        debounce: function (fn, threshold) {
            var timeout;
            return function debounced() {
                if (timeout) {
                    clearTimeout(timeout);
                }
                function delayed() {
                    fn();
                    timeout = null;
                }

                timeout = setTimeout(delayed, threshold || 100);
            }
        },

        onEvent: function () {
            $(document).on('click', '.plugin-action-buttons button:not(.updating-message)', function () {
                var $self = $(this);

                $self.addClass('updating-message');
                $self.attr('disabled', true);

                var action = $self.attr('data-action');
                var $plugin = $self.parents('.plugin-action-buttons');
                var slug = $plugin.data('slug');

                Thim_Plugins
                    .request(action, slug)
                    .success(function (response) {
                        if (response.success) {
                            var data = response.data;
                            $self.text(data.text);
                            $self.attr('data-action', data.action);

                            $(document).find('.plugin-card.plugin-card-' + slug).removeClass('can-update');

                            if (data.action == 'reload') {
                                reload();
                            }
                        } else {
                            var arrMessages = response.data.messages;
                            var messages = '';

                            for (var i = 0; i < arrMessages.length; i++) {
                                messages += Thim_Plugins.unescape_html(arrMessages[i]) + "\n";
                            }

                            alert(messages);
                        }
                    })
                    .complete(function () {
                        $self.attr('disabled', false);
                        $self.removeClass('updating-message');
                    });
            });
        }
    }

})(jQuery, Thim_Plugins);