jQuery(function () {
    autocomplete();
});

function autocomplete() {
    $('.autocomplete_input_useful').typeahead({
        source: function (query, process) {
            return $.get(path_useful, {
                maxRows: 30,
                letters: query,
                entity_alias: this.$element.data('aliasUseful')
            }, function (data) {
                return process($.parseJSON(data));
            });
        },
        matcher: function (item) {
            return true;
        }
    });
}