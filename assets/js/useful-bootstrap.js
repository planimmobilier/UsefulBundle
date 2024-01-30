document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.autocomplete_input_useful').forEach(el => {
        new Autocomplete(el, {
            dataApiUrl: el.getAttribute('data-path'),
            maxRows: 30,
            entityAlias: el.getAttribute('data-alias-useful'),
            onSelectItem: (item) => {
                document.querySelectorAll('.programme_id').forEach(el => el.value = item.value);
                document.querySelectorAll('.utilisateur_id').forEach(el => el.value = item.value);
            }
        });
    });
})
/*
jQuery(function () {
    $('.autocomplete_input_useful').typeahead({
        source: function (query, process) {
            return $.get(this.$element.data('path'), {
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
});
 */