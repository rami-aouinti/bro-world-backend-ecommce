import { Controller } from '@hotwired/stimulus';
import TomSelect from 'tom-select';

function parseToArray(value) {
    if (!value) {
        return [];
    }

    if (Array.isArray(value)) {
        return value;
    }

    if (typeof value === 'string') {
        if (value.trim() === '') {
            return [];
        }

        try {
            const parsed = JSON.parse(value);

            if (Array.isArray(parsed)) {
                return parsed;
            }
        } catch (error) {
            return value.split(',').map((item) => item.trim()).filter((item) => item.length > 0);
        }
    }

    return [];
}

function normaliseOption(option) {
    if (typeof option === 'string') {
        return { value: option, label: option };
    }

    if (!option || typeof option !== 'object') {
        return null;
    }

    const value = option.value ?? option.id ?? option.code ?? option.name ?? option.label ?? option.text;

    if (typeof value !== 'string' && typeof value !== 'number') {
        return null;
    }

    const label = option.label ?? option.text ?? option.name ?? String(value);

    return {
        value: String(value),
        label: String(label),
    };
}

export default class extends Controller {
    static values = {
        url: String,
        minCharacters: { type: Number, default: 2 },
        queryParam: { type: String, default: 'query' },
        placeholder: String,
        options: Array,
        selected: Array,
        extraParams: Object,
    };

    initialize() {
        this.abortController = null;
        this.onChange = this.onChange.bind(this);
    }

    connect() {
        this.buildTomSelect();
        this.element.addEventListener('change', this.onChange);
        this.populateInitialSelection();
    }

    disconnect() {
        this.element.removeEventListener('change', this.onChange);
        this.destroyTomSelect();
    }

    urlValueChanged() {
        if (!this.tomSelect) {
            return;
        }

        this.destroyTomSelect();
        this.buildTomSelect();
        this.populateInitialSelection();
    }

    buildTomSelect() {
        const options = this.normalisedOptions();

        this.tomSelect = new TomSelect(this.element, {
            valueField: 'value',
            labelField: 'label',
            searchField: ['label', 'value'],
            options,
            placeholder: this.placeholderValue ?? this.element.getAttribute('placeholder') ?? '',
            load: (query, callback) => this.loadRemoteOptions(query, callback),
            onInitialize: () => {
                this.populateInitialSelection();
            },
        });
    }

    destroyTomSelect() {
        this.abortPendingRequest();

        if (this.tomSelect) {
            this.tomSelect.destroy();
            this.tomSelect = null;
        }
    }

    normalisedOptions() {
        if (this.hasOptionsValue) {
            return parseToArray(this.optionsValue)
                .map(normaliseOption)
                .filter((option) => option !== null);
        }

        return [];
    }

    populateInitialSelection() {
        if (!this.tomSelect) {
            return;
        }

        const selected = this.hasSelectedValue
            ? parseToArray(this.selectedValue)
            : this.element.value;

        const values = parseToArray(selected);

        values.forEach((item) => {
            const option = normaliseOption(item);

            if (!option) {
                return;
            }

            if (!this.tomSelect.options[option.value]) {
                this.tomSelect.addOption(option);
            }

            if (!this.tomSelect.items.includes(option.value)) {
                this.tomSelect.addItem(option.value, false);
            }
        });

        this.tomSelect.refreshItems();
    }

    loadRemoteOptions(query, callback) {
        if (!this.hasUrlValue) {
            callback();
            return;
        }

        if (query.length < this.minCharactersValue) {
            callback();
            return;
        }

        this.abortPendingRequest();

        this.abortController = new AbortController();

        const url = this.buildRequestUrl(query);

        fetch(url, { signal: this.abortController.signal })
            .then((response) => response.json())
            .then((data) => {
                const options = this.normaliseRemoteResponse(data);

                callback(options);
            })
            .catch((error) => {
                if (error.name !== 'AbortError') {
                    console.error('Autocomplete request failed.', error);
                }

                callback();
            });
    }

    buildRequestUrl(query) {
        const url = new URL(this.urlValue, window.location.origin);

        const params = this.hasExtraParamsValue ? this.extraParamsValue : {};

        Object.keys(params || {}).forEach((key) => {
            url.searchParams.set(key, params[key]);
        });

        const queryParam = this.hasQueryParamValue ? this.queryParamValue : 'query';
        url.searchParams.set(queryParam, query);

        return url.toString();
    }

    normaliseRemoteResponse(data) {
        if (!Array.isArray(data)) {
            if (Array.isArray(data.results)) {
                return this.normaliseRemoteResponse(data.results);
            }

            if (Array.isArray(data.items)) {
                return this.normaliseRemoteResponse(data.items);
            }

            return [];
        }

        return data
            .map(normaliseOption)
            .filter((option) => option !== null);
    }

    abortPendingRequest() {
        if (this.abortController) {
            this.abortController.abort();
            this.abortController = null;
        }
    }

    onChange() {
        if (!this.tomSelect) {
            return;
        }

        const values = this.tomSelect.items.slice();

        this.element.value = values.join(',');
    }
}
