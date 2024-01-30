document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.autocomplete_input_useful').forEach(el => {
        new Autocomplete(el, {
            dataApiUrl: el.getAttribute('data-path'),
            maxRows: 30,
            entityAlias: el.getAttribute('data-alias-useful')
        });
    });
});