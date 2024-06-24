const DEFAULTS = {
    threshold: 2,
    maximumItems: 6,
    highlightTyped: true,
    highlightClass: 'text-primary',
    label: 'label',
    value: 'value',
    showValue: false,
    showValueBeforeLabel: false,
    dataApiUrl: ''
};

class Autocomplete {
    constructor(field, options) {
        this.field = field;
        this.options = Object.assign({}, DEFAULTS, options);
        this.dropdown = null;

        field.parentNode.classList.add('dropdown');
        field.setAttribute('data-bs-toggle', 'dropdown');
        field.classList.add('dropdown-toggle');

        const dropdown = ce(`<div class="dropdown-menu"></div>`);
        if (this.options.dropdownClass)
            dropdown.classList.add(this.options.dropdownClass);

        field.parentNode.insertBefore(dropdown, field.nextSibling);
        this.items = this.field.nextElementSibling;

        this.dropdown = new bootstrap.Dropdown(field, this.options.dropdownOptions);

        field.addEventListener('click', (e) => {
            if (this.items.childNodes.length === 0 || this.field.value.length < this.options.threshold) {
                e.stopPropagation();
                this.dropdown.hide();
            }
        });

        field.addEventListener('input', () => {
            if (this.options.onInput)
                this.options.onInput(this.field.value);
            this.createItems();
        });
    }

    createItem(lookup, item) {
        let label;
        if (this.options.highlightTyped) {
            const idx = removeDiacritics(item.label)
                .toLowerCase()
                .indexOf(removeDiacritics(lookup).toLowerCase());
            const className = Array.isArray(this.options.highlightClass) ? this.options.highlightClass.join(' ')
                : (typeof this.options.highlightClass == 'string' ? this.options.highlightClass : '');
            label = item.label.substring(0, idx)
                + `<span class="${className}">${item.label.substring(idx, idx + lookup.length)}</span>`
                + item.label.substring(idx + lookup.length, item.label.length);
        } else {
            label = item.label;
        }

        if (this.options.showValue) {
            if (this.options.showValueBeforeLabel) {
                label = `${item.value} ${label}`;
            } else {
                label += ` ${item.value}`;
            }
        }

        return ce(`<button type="button" class="dropdown-item" data-label="${item.label}" data-value="${item.value}">${label}</button>`);
    }

    createItems() {
        const lookup = this.field.value;
        if (lookup.length < this.options.threshold) {
            this.dropdown.hide();
            return 0;
        }

        this.items.innerHTML = '';

        let count = 0;
        if (this.options.dataApiUrl !== '') {
            fetch(this.options.dataApiUrl + '?letters=' + lookup + '&maxRows=' + this.options.maxRows + '&entity_alias=' + this.options.entityAlias, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            }).then(response => response.json()).then((data) => {
                for (let i = 0; i < data.length; i++) {
                    const item = {
                        label: data[i],
                        value: data[i]
                    };
                    if (removeDiacritics(item.label).toLowerCase().indexOf(removeDiacritics(lookup).toLowerCase()) >= 0) {
                        this.items.appendChild(this.createItem(lookup, item));
                        if (this.options.maximumItems > 0 && ++count >= this.options.maximumItems)
                            break;
                    }
                    this.field.nextSibling.querySelectorAll('.dropdown-item').forEach((item) => {
                        item.addEventListener('click', (e) => {
                            let dataLabel = e.currentTarget.getAttribute('data-label');
                            let dataValue = e.currentTarget.getAttribute('data-value');

                            this.field.value = dataLabel;

                            if (this.options.onSelectItem) {
                                this.options.onSelectItem({
                                    value: dataValue,
                                    label: dataLabel
                                });
                            }

                            this.dropdown.hide();
                        })
                    });
                    if (this.items.childNodes.length > 0) {
                        this.dropdown.show();
                    } else {
                        this.dropdown.hide();
                    }
                }
            });
        }
    }
}

/**
 * @param html
 * @returns {Node}
 */
function ce(html) {
    let div = document.createElement('div');
    div.innerHTML = html;
    return div.firstChild;
}

/**
 * @param {String} str
 * @returns {String}
 */
function removeDiacritics(str) {
    return str
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');
}